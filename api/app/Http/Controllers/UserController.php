<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\User;

// ...
class UserController extends Controller
{
    public function createUser(Request $request)
    {
        // Data of the new user to be registered
        $newUserData = [
            'username' => $request->input('username'),
            'email' => $request->input('email'),
            'enabled' => true,
            'firstName' => $request->input('firstName'),
            'lastName' => $request->input('lastName'),
            'credentials' => [
                ['type' => 'password', 'value' => $request->input('password')],
            ],
        ];

        // Obtain an access token from Keycloak
        $tokenResponse = Http::asForm()
            ->post('keycloak:8080/realms/Rentella/protocol/openid-connect/token', [
                'grant_type' => 'client_credentials',
                'client_id' => 'api',
                'client_secret' => 'j26EbD3pXR3w4pkj4OxVVmB6diKkwJ7y',
            ]);

        $accessToken = $tokenResponse->json('access_token');

        // Make the API call to register the new user
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
            'Content-Type' => 'application/json',
        ])->post('http://keycloak:8080/admin/realms/Rentella/users', $newUserData);

        if ($response->status() === 201) {
            //return response()->json(['message' => 'New user created, good job!'], 201);
            $validatedData = $request->validate([
                //'username' => 'required|string|unique:users',
                'email' => 'required|email|unique:users',
                'firstName' => 'required|string',
                'lastName' => 'required|string',
                //'password' => 'required|string',

            ]);
            // Get the UUID from the Location header in Keycloak's response
            $locationHeader = $response->header('Location');
            $uuid = basename($locationHeader); // Extract the UUID from the URL
            $validatedData['uuid'] = $uuid;

            // Create a new user record in the local database
            $user = new User([
                //'username' => $validatedData['username'],
                'email' => $validatedData['email'],
                'name' => $validatedData['firstName'],
                'surname' => $validatedData['lastName'],
                'uuid' => $uuid,
            ]);
            $user->save();
            if ($response->status() === 201) {
                return response()->json(['message' => 'User data saved successfully'], 201);
            } else {
                return response()->json(['error' => 'Error during new user creation in the local database'], $response->status());
            }
        } else {
            return response()->json(['error' => 'Error during new user creation'], $response->status());
        }
    }
}
