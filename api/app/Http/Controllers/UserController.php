<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use App\Models\User;
use App\Http\Requests\UserRequest;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;


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
        \Illuminate\Support\Facades\Log::error('UserController error: ' . $e->getMessage(), [
            'exception' => $e,
        ]);
        return response()->json(['error' => 'An internal server error occurred.'], 500);
    }

    /**
     * Read a claim from decoded Keycloak token when available.
     */
    private function getTokenClaim(string $claim)
    {
        try {
            $guard = auth('api');
            if (!method_exists($guard, 'token')) {
                return null;
            }

            $rawToken = $guard->token();
            if (!is_string($rawToken) || $rawToken === '') {
                return null;
            }

            $decoded = json_decode($rawToken, true);
            if (!is_array($decoded)) {
                return null;
            }

            return $decoded[$claim] ?? null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Build a deterministic UUID-like string from a stable identity value.
     */
    private function buildDeterministicUuid(string $value): string
    {
        $hash = md5(strtolower(trim($value)));

        return substr($hash, 0, 8)
            . '-' . substr($hash, 8, 4)
            . '-' . substr($hash, 12, 4)
            . '-' . substr($hash, 16, 4)
            . '-' . substr($hash, 20, 12);
    }

    /**
     * Resolve current authenticated principal to a local User row.
     * Works with Keycloak claims where id can be UUID/non-numeric.
     */
    private function resolveCurrentLocalUser(): ?User
    {
        $authUser = auth()->user();
        if (!$authUser) {
            return null;
        }

        // If the auth principal is already a persisted local model, use it directly.
        if ($authUser instanceof User && $authUser->exists) {
            return $authUser;
        }

        $authIdentifier = method_exists($authUser, 'getAuthIdentifier') ? $authUser->getAuthIdentifier() : null;
        $authId = $authUser->id ?? $authIdentifier;
        $uuid = $authUser->uuid
            ?? $authUser->sub
            ?? $this->getTokenClaim('sub')
            ?? (!is_numeric((string) $authId) ? $authId : null);
        $email = $authUser->email ?? $this->getTokenClaim('email');
        if (is_string($email) && $email !== '') {
            $email = strtolower(trim($email));
        }
        $preferredUsername = $authUser->preferred_username
            ?? $authUser->username
            ?? $this->getTokenClaim('preferred_username')
            ?? $this->getTokenClaim('username');
        $name = $authUser->name
            ?? $authUser->given_name
            ?? $this->getTokenClaim('name')
            ?? $this->getTokenClaim('given_name')
            ?? 'User';
        $surname = $authUser->surname
            ?? $authUser->family_name
            ?? $this->getTokenClaim('family_name')
            ?? 'Keycloak';

        if (is_numeric((string) $authId)) {
            $user = User::find((int) $authId);
            if ($user) {
                return $user;
            }
        }

        if (!$uuid && $preferredUsername) {
            $uuid = $this->buildDeterministicUuid((string) $preferredUsername);
        }

        if (!$uuid && $email) {
            $uuid = $this->buildDeterministicUuid($email);
        }

        if (!$email && $uuid) {
            $email = sprintf('kc_%s@local.invalid', substr(sha1((string) $uuid), 0, 24));
        }

        if (!$uuid && !$email) {
            return null;
        }

        if ($uuid) {
            $user = User::where('uuid', $uuid)->first();
            if ($user) {
                if (!$user->email && $email) {
                    $user->email = $email;
                    $user->save();
                }

                return $user;
            }
        }

        if ($email) {
            $user = User::where('email', $email)->first();
            if ($user) {
                if (!$user->uuid && $uuid) {
                    $user->uuid = $uuid;
                    $user->save();
                }

                return $user;
            }
        }

        // Create a valid local row with all required columns filled.
        return User::create([
            'email' => $email,
            'name' => $name,
            'surname' => $surname,
            'uuid' => $uuid,
            'password' => bcrypt(Str::random(32)),
        ]);
    }
    public function index()
    {
        try {
            // Return only the authenticated user's profile
            $user = auth()->user();
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            return response()->json($user);
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
    public function show($identifier)
    {
        try {
            $authUser = auth()->user();
            if (!$authUser) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            
            // Users can only view their own profile
            $isNumericId = ctype_digit((string) $identifier);
            if ($isNumericId) {
                if ($authUser->id !== (int) $identifier) {
                    return response()->json(['error' => 'Forbidden: You can only view your own profile'], 403);
                }
            } else {
                if ($authUser->uuid !== $identifier) {
                    return response()->json(['error' => 'Forbidden: You can only view your own profile'], 403);
                }
            }
            
            $user = $isNumericId
                ? User::find($identifier)
                : User::where('uuid', $identifier)->first();
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
                'password' => bcrypt(Str::random(32)),
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
            $authUser = auth()->user();
            if (!$authUser) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            
            $user = User::findOrFail($id);
            
            // Users can only update their own profile
            if ($authUser->id !== $user->id) {
                return response()->json(['error' => 'Forbidden: You can only update your own profile'], 403);
            }
            
            $accessToken = $this->getKeycloakAccessToken();
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
            $authUser = auth()->user();
            if (!$authUser) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            
            // get user for database 
            $user = User::findOrFail($id);
            
            // Users can only delete their own account
            if ($authUser->id !== $user->id) {
                return response()->json(['error' => 'Forbidden: You can only delete your own account'], 403);
            }
            
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

    /**
     * Set the user's preferred location
     */
    public function setPreferredLocation(Request $request)
    {
        try {
            $user = $this->resolveCurrentLocalUser();
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $request->validate([
                'location_id' => 'required|exists:cities_location,id'
            ]);

            $user->preferred_location_id = $request->input('location_id');
            $user->save();

            // Load the relationship to return full location data
            $user->load('preferredLocation');

            return response()->json([
                'message' => 'Preferred location updated successfully',
                'data' => $user
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Get the user's preferred location
     */
    public function getPreferredLocation()
    {
        try {
            $user = $this->resolveCurrentLocalUser();
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $user->load('preferredLocation');

            return response()->json([
                'preferred_location' => $user->preferredLocation
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }
}

