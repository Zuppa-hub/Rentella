<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Http\Requests\OrdersFilterRequest;
use App\Models\BeachZone;
use App\Models\Order;
use App\Models\Umbrella;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class OrdersController extends Controller
{
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
     * Resolve authenticated principal (Keycloak/local) to a persisted local User row.
     */
    private function resolveCurrentLocalUser(): ?User
    {
        $authUser = auth()->user();
        if (!$authUser) {
            return null;
        }

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

        return User::create([
            'email' => $email,
            'name' => $name,
            'surname' => $surname,
            'uuid' => $uuid,
            'password' => bcrypt(Str::random(32)),
        ]);
    }

    public function checkAvailability(\Illuminate\Http\Request $request)
    {
        $user = $this->resolveCurrentLocalUser();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validator = Validator::make($request->all(), [
            'zone_id' => 'required|exists:beach_zones,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $startDate = date('Y-m-d 00:00:00', strtotime($data['start_date']));
        $endDate = date('Y-m-d 23:59:59', strtotime($data['end_date']));

        $zone = BeachZone::findOrFail($data['zone_id']);
        $umbrellaIds = Umbrella::where('zone_id', $zone->id)->pluck('id');
        $totalUmbrellas = $umbrellaIds->count();

        if ($totalUmbrellas === 0) {
            return response()->json([
                'available' => false,
                'total_umbrellas' => 0,
                'available_umbrellas' => 0,
                'first_available_umbrella_id' => null,
                'price_id' => $zone->price_id,
            ]);
        }

        $bookedUmbrellaIds = Order::whereIn('umbrella_id', $umbrellaIds)
            ->where(function ($query) use ($startDate, $endDate) {
                $query->where('start_date', '<=', $endDate)
                    ->where('end_date', '>=', $startDate);
            })
            ->pluck('umbrella_id')
            ->unique();

        $availableUmbrellaIds = $umbrellaIds->diff($bookedUmbrellaIds)->values();

        return response()->json([
            'available' => $availableUmbrellaIds->isNotEmpty(),
            'total_umbrellas' => $totalUmbrellas,
            'available_umbrellas' => $availableUmbrellaIds->count(),
            'first_available_umbrella_id' => $availableUmbrellaIds->first(),
            'price_id' => $zone->price_id,
        ]);
    }

    public function createFromZone(\Illuminate\Http\Request $request)
    {
        $user = $this->resolveCurrentLocalUser();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validator = Validator::make($request->all(), [
            'zone_id' => 'required|exists:beach_zones,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'price_id' => 'nullable|exists:prices,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $startDate = date('Y-m-d 00:00:00', strtotime($data['start_date']));
        $endDate = date('Y-m-d 23:59:59', strtotime($data['end_date']));

        return DB::transaction(function () use ($data, $startDate, $endDate, $user) {
            $zone = BeachZone::findOrFail($data['zone_id']);

            $umbrellaIds = Umbrella::where('zone_id', $zone->id)
                ->lockForUpdate()
                ->pluck('id');

            if ($umbrellaIds->isEmpty()) {
                return response()->json([
                    'error' => 'No umbrellas available in this zone',
                ], 422);
            }

            $bookedUmbrellaIds = Order::whereIn('umbrella_id', $umbrellaIds)
                ->where(function ($query) use ($startDate, $endDate) {
                    $query->where('start_date', '<=', $endDate)
                        ->where('end_date', '>=', $startDate);
                })
                ->lockForUpdate()
                ->pluck('umbrella_id')
                ->unique();

            $availableUmbrellaId = $umbrellaIds->diff($bookedUmbrellaIds)->first();

            if (!$availableUmbrellaId) {
                return response()->json([
                    'error' => 'No availability for selected dates',
                ], 409);
            }

            $order = Order::create([
                'umbrella_id' => $availableUmbrellaId,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'user_id' => $user->id,
                'price_id' => $data['price_id'] ?? $zone->price_id,
            ]);

            return response()->json([
                'order' => $order,
                'zone_id' => $zone->id,
                'umbrella_id' => $availableUmbrellaId,
            ], 201);
        });
    }

    public function index(OrdersFilterRequest $request)
    {
        $user = $this->resolveCurrentLocalUser();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        $result = [];
        $now = now();
        $active = null;
        if ($request->has('active')) {
            $active = filter_var($request->input('active'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        }
        
        // Always filter by authenticated user's ID
        $orders = Order::with(['price', 'umbrella', 'umbrella.beachzone.beach', 'umbrella.beachzone.beach.location'])
            ->where('user_id', $user->id);

        if ($request->has('active') && $active !== null) {
            if ($active === true) {
                // active orders
                $orders->where('end_date', '>', $now);
            } elseif ($active === false) {
                // non active orders
                $orders->where('end_date', '<=', $now);
            }
        }
        $orders = $orders->get();
        foreach ($orders as $order) {
            $zone = $order->umbrella->beachzone;
            $result[] = [
                'orders' => $order,
                'name' => $zone->beach->name,
            ];
        }
        return response()->json($result);
    }

    public function show($id)
    {
        $user = $this->resolveCurrentLocalUser();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        $order = Order::findOrFail($id);
        
        // Users can only view their own orders
        if ($user->id !== $order->user_id) {
            return response()->json(['error' => 'Forbidden: You can only view your own orders'], 403);
        }
        
        return response()->json($order);
    }

    public function store(OrderRequest $request)
    {
        $user = $this->resolveCurrentLocalUser();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $data = $request->validated();
        $data['user_id'] = $user->id;

        return response()->json(Order::create($data), 201);
    }

    public function update(OrderRequest $request, $id)
    {
        $user = $this->resolveCurrentLocalUser();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $order = Order::findOrFail($id);
        if ($user->id !== $order->user_id) {
            return response()->json(['error' => 'Forbidden: You can only update your own orders'], 403);
        }

        $data = $request->validated();
        $data['user_id'] = $user->id;

        $order->update($data);
        return response()->json($order, 200);
    }

    public function destroy($id)
    {
        $user = $this->resolveCurrentLocalUser();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $order = Order::findOrFail($id);
        if ($user->id !== $order->user_id) {
            return response()->json(['error' => 'Forbidden: You can only delete your own orders'], 403);
        }

        $order->delete();
        return response()->json(null, 204);
    }
}
