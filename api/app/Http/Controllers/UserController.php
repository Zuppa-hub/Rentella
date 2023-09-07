<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use App\Models\User;
use App\Http\Requests\CreateUserRequest;
use Illuminate\Contracts\Config\Repository as Config;

class UserController extends Controller
{
    protected $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function createUser(CreateUserRequest $request)
    {
        // Obtain an access token from Keycloak
        $tokenResponse = Http::asForm()
            ->post('keycloak:8080/realms/Rentella/protocol/openid-connect/token', [
                'grant_type' => $this->config->get('app.keycloak.grant_type'),
                'client_id' => $this->config->get('app.keycloak.client_id'),
                'client_secret' => $this->config->get('app.keycloak.client_secret'),
            ]);

        $accessToken = $tokenResponse->json('access_token');

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
        return response()->json(['message' => 'User data saved successfully', $request->all()], 201);
    }
}
