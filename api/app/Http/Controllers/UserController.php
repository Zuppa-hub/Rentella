<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use App\Models\User;
use App\Http\Requests\CreateUserRequest;

class UserController extends Controller
{
    public function createUser(CreateUserRequest $request)
    {
        // Data of the new user to be registered
        $newUserData = $request->all();

        // Obtain an access token from Keycloak
        $tokenResponse = Http::asForm()
            ->post('keycloak:8080/realms/Rentella/protocol/openid-connect/token', [
                'grant_type' => env('KEYCLOAK_GRANT_TYPE'),
                'client_id' => env('KEYCLOAK_CLIENT_ID'),
                'client_secret' => env('KEYCLOAK_CLIENT_SECRET'),
            ]);

        $accessToken = $tokenResponse->json('access_token');

        // Make the API call to register the new user
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
            'Content-Type' => 'application/json',
        ])->post('http://keycloak:8080/admin/realms/Rentella/users', $newUserData);

        if ($response->status() === 201) {
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
            if ($response->status() === 201) {
                return response()->json(['message' => 'User data saved successfully'], 201);
            }
            if ($response->status() != 201) {
                return response()->json(['error' => 'Error during new user creation in the local database'], $response->status());
            }
        }
        if ($response->status() != 201) {
            return response()->json(['error' => 'Error during new user creation'], $response->status());
        }
    }
}
