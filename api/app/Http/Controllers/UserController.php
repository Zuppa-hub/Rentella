<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use App\Models\User;
use App\Http\Requests\UserRequest;
use Illuminate\Contracts\Config\Repository as Config;

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
    public function index()
    {
        //All users 
        return response()->json(User::all());
    }

    public function show($id)
    {
        // Information related to specific user 
        return response()->json(User::find($id));
    }

    // Create a new user and handle errors
    public function store(UserRequest $request)
    {
        $accessToken = $this->getKeycloakAccessToken();
        // Make the API call to register the new user
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
            'Content-Type' => 'application/json',
        ])->post($this->config->get('app.keycloak.get_token_uri'),  $request->all());

        //error cheking
        if ($response->status() != 201) {
            return response()->json(['error' => 'Error during new user creation'], $response->status());
        }

        // Get the UUID from the Location header in Keycloak's response
        $locationHeader = $response->header('Location');
        $uuid = basename($locationHeader); // Extract the UUID from the URL

        // Create a new user record in the local database
        $user = new User([
            'email' => $request->input('email'),
            'name' => $request->input('firstName'),
            'surname' => $request->input('lastName'),
            'uuid' => $uuid,
        ]);
        $user->save();
        return response()->json(['message' => 'User data saved successfully', 'data' => User::findOrFail($user->id)], 201);
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
            return response()->json(['error' => 'Error during user update'], 500);
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
            return response()->json(['error' => 'Error during user deletion'], 500);
        }
    }
}
