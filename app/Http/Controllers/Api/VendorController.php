<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\VendorResource;
use App\Models\Vendor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class VendorController extends Controller
{
    /**
     * Display a listing of vendors (with search & pagination).
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Vendor::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->boolean('active_only')) {
            $query->where('is_active', true);
        }

        $vendors = $query->latest()->paginate($request->integer('per_page', 15));

        return VendorResource::collection($vendors);
    }

    /**
     * Store a newly created vendor.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'string', 'email', 'max:255'],
            'address' => ['nullable', 'string'],
            'bank_name' => ['nullable', 'string', 'max:255'],
            'account_title' => ['nullable', 'string', 'max:255'],
            'account_number' => ['nullable', 'string', 'max:50'],
            'iban' => ['nullable', 'string', 'max:50'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $vendor = Vendor::create($validated);

        return (new VendorResource($vendor))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified vendor.
     */
    public function show(Vendor $vendor): VendorResource
    {
        return new VendorResource($vendor);
    }

    /**
     * Update the specified vendor.
     */
    public function update(Request $request, Vendor $vendor): VendorResource
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'string', 'email', 'max:255'],
            'address' => ['nullable', 'string'],
            'bank_name' => ['nullable', 'string', 'max:255'],
            'account_title' => ['nullable', 'string', 'max:255'],
            'account_number' => ['nullable', 'string', 'max:50'],
            'iban' => ['nullable', 'string', 'max:50'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $vendor->update($validated);

        return new VendorResource($vendor);
    }

    /**
     * Remove the specified vendor.
     */
    public function destroy(Vendor $vendor): JsonResponse
    {
        $vendor->delete();

        return response()->json([
            'message' => 'Vendor deleted successfully',
        ]);
    }
}
