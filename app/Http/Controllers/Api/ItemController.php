<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ItemResource;
use App\Models\Item;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ItemController extends Controller
{
    /**
     * Display a listing of items (with search & pagination).
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Item::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('unit', 'like', "%{$search}%");
            });
        }

        if ($request->boolean('active_only')) {
            $query->where('is_active', true);
        }

        $items = $query->latest()->paginate($request->integer('per_page', 15));

        return ItemResource::collection($items);
    }

    /**
     * Store a newly created item.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'unit' => ['nullable', 'string', 'max:50'],
            'current_price' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['previous_price'] = null;
        $validated['average_price'] = $validated['current_price'];

        $item = Item::create($validated);

        return (new ItemResource($item))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified item.
     */
    public function show(Item $item): ItemResource
    {
        return new ItemResource($item);
    }

    /**
     * Update the specified item (with automatic price history calculation).
     */
    public function update(Request $request, Item $item): ItemResource
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'unit' => ['nullable', 'string', 'max:50'],
            'current_price' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $newPrice = (float) $validated['current_price'];
        $oldPrice = (float) $item->current_price;

        // Auto track price history if price changed
        if ($newPrice !== $oldPrice) {
            $validated['previous_price'] = $oldPrice;
            $validated['average_price'] = round(($oldPrice + $newPrice) / 2, 2);
        }

        $item->update($validated);

        return new ItemResource($item);
    }

    /**
     * Remove the specified item.
     */
    public function destroy(Item $item): JsonResponse
    {
        $item->delete();

        return response()->json([
            'message' => 'Item deleted successfully',
        ]);
    }
}
