<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use App\Models\User;
use App\Http\Requests\UserRequest;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Http\JsonResponse;


/* The `class UserController extends Controller` is defining a PHP class called `UserController` that
extends the `Controller` class. This means that the `UserController` class inherits all the
properties and methods from the `Controller` class, allowing it to use and override those methods as
needed. */
class UserController extends Controller
{
    protected $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    // Private method to obtain Keycloak access token
    private function getKeycloakAccessToken()
    {
        $tokenResponse = Http::asForm()
            ->post($this->config->get('app.keycloak.get_token_realm_uri'), [
                'grant_type' => $this->config->get('app.keycloak.grant_type'),
                'client_id' => $this->config->get('app.keycloak.client_id'),
                'client_secret' => $this->config->get('app.keycloak.client_secret'),
            ]);

        return $tokenResponse->json('access_token');
    }
    private function handleException(\Exception $e): JsonResponse
    {
        return response()->json(['error' => $e->getMessage()], 500);
    }
    public function index()
    {
        try {
            return response()->json(User::all());
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * The function retrieves a user with a specific UID and returns a JSON response containing the
     * user's information, or an empty JSON response with a 404 status code if the user is not found.
     * 
     * @param uid The parameter "uid" is the unique identifier of the user that we want to retrieve
     * from the database.
     * 
     * @return a JSON response. If a user with the given UID is found, the function will return a JSON
     * response containing the user's information. If no user is found, it will return an empty JSON
     * response with a status code of 404. If an exception occurs during the execution of the function,
     * it will be caught and handled by the `handleException` method.
     */
    public function show($uuid)
    {
        try {
            $user = User::where('uuid', $uuid)->first();

            if ($user) {
                return response()->json($user);
            } else {
                return response()->json([], 404);
            }
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }


    // Create a new user and handle errors
    public function store(UserRequest $request)
    {
        try {
            $accessToken = $this->getKeycloakAccessToken();
            // Make the API call to register the new user
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ])->post($this->config->get('app.keycloak.get_token_uri'),  $request->all());

            //error cheking
            if ($response->status() != 201) {
                return response()->json($response->status());
            }

            // Get the UUID from the Location header in Keycloak's response
            $locationHeader = $response->header('Location');
            $uuid = basename($locationHeader); // Extract the UUID from the URL

            // Create a new user record in the local database
            $user = User::create([
                'email' => $request->input('email'),
                'name' => $request->input('firstName'),
                'surname' => $request->input('lastName'),
                'uuid' => $uuid,
            ]);
            return response()->json(['message' => 'User data saved successfully', 'data' => User::findOrFail($user->id)], 201);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    // Update an existing user and handle errors
    public function update(UserRequest $request, $id)
    {
        try {
            $accessToken = $this->getKeycloakAccessToken();
            $user = User::findOrFail($id);
            // Rest of the code for updating the user
            $updateUserUri = $this->config->get('app.keycloak.keycloak_user_update_url');
            // call keycloak server to update user 
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ])->put("$updateUserUri/{$user->uuid}", $request->all());
            //return $response;
            // update user data in database 
            $user->update([
                'email' => $request->input('email'),
                'name' => $request->input('firstName'),
                'surname' => $request->input('lastName'),
            ]);
            return response()->json(['message' => 'User data updated successfully', 'data' => User::findOrFail($id)]);
            // Handle specific error cases here, e.g., user not found
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    // Delete an existing user and handle errors
    public function destroy($id)
    {
        try {
            // delete user from database and from keycloak server
            // get user for database 
            $user = User::findOrFail($id);
            // get keycloak token 
            $accessToken = $this->getKeycloakAccessToken();

            // Rest of the code for deleting the user
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ])->delete("keycloak:8080/admin/realms/Rentella/users/{$user->uuid}");


            // update user data in database 
            $user->delete();
            return response()->json(['message' => 'User data removed successfully', $id]);
            // Handle specific error cases here, e.g., user not found
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }
}
