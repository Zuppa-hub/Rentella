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

        $authId = $authUser->id ?? null;
        $uuid = $authUser->uuid ?? $authUser->sub ?? (!is_numeric((string) $authId) ? $authId : null);
        $email = $authUser->email ?? null;
        $name = $authUser->name ?? $authUser->given_name ?? 'User';
        $surname = $authUser->surname ?? $authUser->family_name ?? 'Keycloak';

        if (is_numeric((string) $authId)) {
            $user = User::find((int) $authId);
            if ($user) {
                return $user;
            }
        }

        if ($uuid) {
            $user = User::where('uuid', $uuid)->first();
            if ($user) {
                return $user;
            }
        }

        if ($email) {
            $user = User::where('email', $email)->first();
            if ($user) {
                return $user;
            }
        }

        if (!$email) {
            $email = sprintf('kc_%s@local.invalid', Str::random(12));
        }

        if (!$uuid) {
            do {
                $uuid = (string) Str::uuid();
            } while (User::where('uuid', $uuid)->exists());
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
