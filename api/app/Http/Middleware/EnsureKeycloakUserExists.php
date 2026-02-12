<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
use Keycloak;

class EnsureKeycloakUserExists
{
    public function handle(Request $request, Closure $next)
    {
        try {
            // Se l'utente è autenticato via Keycloak ma non esiste nel DB, crealo
            if ($request->user()) {
                return $next($request);
            }

            // Se arriva il token Keycloak, prova a estrarre i dati
            $token = $request->bearerToken();
            if ($token) {
                try {
                    $keycloak = app('KeycloakGuard');
                    $keycloakUser = $keycloak->user();
                    
                    if ($keycloakUser) {
                        // Verifica se esiste nel DB
                        $user = User::where('email', $keycloakUser->email)->first();
                        
                        if (!$user) {
                            // Crea l'utente se non esiste
                            $user = User::create([
                                'email' => $keycloakUser->email,
                                'name' => $keycloakUser->name ?? $keycloakUser->email,
                                'uuid' => \Illuminate\Support\Str::uuid(),
                                'password' => bcrypt(\Illuminate\Support\Str::random(32)), // Password random
                            ]);
                        }
                    }
                } catch (\Exception $e) {
                    // Ignora errori di parsing del token
                }
            }
        } catch (\Exception $e) {
            // Ignora errori generali
        }

        return $next($request);
    }
}
