<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
        ...parent::share($request),
        'auth' => [
            'user' => $request->user(),
        ],
        'cartCount' => function () use ($request) {
            if (!$request->user()) return 0;
            return \App\Models\Cart::where('user_id', $request->user()->id)
                ->withCount('items')
                ->first()?->items_count ?? 0;
        },
        'wishlistedIds' => function () use ($request) {
            if (!$request->user()) return [];
            return \App\Models\Wishlist::where('user_id', $request->user()->id)
                ->pluck('product_id')
                ->toArray();
        },
        'csrf_token' => csrf_token(),
    ];
}
}
