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

    public function store(UserRequest $request)
    {
        // Obtain an access token from Keycloak
        $tokenResponse = Http::asForm()
            ->post($this->config->get('app.keycloak.get_token_realm_uri'), [
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
    public function index()
    {
        //All users 
        return response()->json(User::all());
    }

    public function show($id)
    {
        // Information related to specific book 
        return response()->json(User::find($id));
    }

    public function update(UserRequest $request, $id)
    {
        // get user for database 
        $user = User::findOrFail($id);
        // get keycloak token 
        $tokenResponse = Http::asForm()
            ->post($this->config->get('app.keycloak.get_token_realm_uri'), [
                'grant_type' => $this->config->get('app.keycloak.grant_type'),
                'client_id' => $this->config->get('app.keycloak.client_id'),
                'client_secret' => $this->config->get('app.keycloak.client_secret'),
            ]);
        $accessToken = $tokenResponse->json('access_token');

        // call keycloak server to update user 
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
            'Content-Type' => 'application/json',
        ])->put("keycloak:8080/admin/realms/Rentella/users/{$user->uuid}", $request->all());


        // update user data in database 
        $user->update([
            'email' => $request->input('email'),
            'name' => $request->input('firstName'),
            'surname' => $request->input('lastName'),
        ]);        
        return response()->json(['message' => 'User data updated successfully', $request->all()]);
    }


    public function destroy($id)
    {
        // delete user from database and from keycloak server
        // get user for database 
        $user = User::findOrFail($id);
        // get keycloak token 
        $tokenResponse = Http::asForm()
            ->post($this->config->get('app.keycloak.get_token_realm_uri'), [
                'grant_type' => $this->config->get('app.keycloak.grant_type'),
                'client_id' => $this->config->get('app.keycloak.client_id'),
                'client_secret' => $this->config->get('app.keycloak.client_secret'),
            ]);
        $accessToken = $tokenResponse->json('access_token');

        // call keycloak server to update user 
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
            'Content-Type' => 'application/json',
        ])->delete("keycloak:8080/admin/realms/Rentella/users/{$user->uuid}");


        // update user data in database 
        $user->delete();        
        return response()->json(['message' => 'User data removed successfully',$id]);
    }
}
