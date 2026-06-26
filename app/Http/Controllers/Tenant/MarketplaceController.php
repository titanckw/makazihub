<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\MarketplaceBooking;
use App\Models\MarketplaceServiceProvider;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MarketplaceController extends Controller
{
    private function getTenant(): Tenant
    {
        return Tenant::where('user_id', Auth::id())->firstOrFail();
    }

    /**
     * Marketplace listing — filterable by category.
     */
    public function index(Request $request)
    {
        $tenant = $this->getTenant();

        $query = MarketplaceServiceProvider::active()
            ->forProperty($tenant->property_id ?? null)
            ->orderBy('is_featured', 'desc')
            ->orderBy('sort_order')
            ->orderBy('name');

        if ($request->filled('category')) {
            $query->category($request->category);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('description', 'like', "%{$request->search}%");
            });
        }

        $services  = $query->get();
        $categories = MarketplaceServiceProvider::$categories;

        // Count per category for filter badges
        $categoryCounts = MarketplaceServiceProvider::active()
            ->forProperty($tenant->property_id ?? null)
            ->selectRaw('category, count(*) as total')
            ->groupBy('category')
            ->pluck('total', 'category');

        return view('tenant.marketplace.index', compact('services', 'categories', 'categoryCounts'));
    }

    /**
     * Show a single service provider's detail page.
     */
    public function show(MarketplaceServiceProvider $service)
    {
        abort_unless($service->is_active, 404);

        $tenant = $this->getTenant();

        // Check tenant's past bookings with this provider
        $myBookings = MarketplaceBooking::where('tenant_id', $tenant->id)
            ->where('service_provider_id', $service->id)
            ->latest()
            ->take(5)
            ->get();

        return view('tenant.marketplace.show', compact('service', 'myBookings'));
    }

    /**
     * Store a booking request.
     */
    public function book(Request $request, MarketplaceServiceProvider $service)
    {
        abort_unless($service->is_active, 404);

        $validated = $request->validate([
            'preferred_date' => ['nullable', 'date', 'after:now'],
            'notes'          => ['nullable', 'string', 'max:500'],
            'contact_phone'  => ['required', 'string', 'max:20'],
        ]);

        $tenant = $this->getTenant();

        MarketplaceBooking::create([
            'tenant_id'           => $tenant->id,
            'service_provider_id' => $service->id,
            'preferred_date'      => $validated['preferred_date'] ?? null,
            'notes'               => $validated['notes'] ?? null,
            'contact_phone'       => $validated['contact_phone'],
            'status'              => 'pending',
        ]);

        return redirect()
            ->route('tenant.marketplace.show', $service)
            ->with('success', "Booking request sent to {$service->name}! They will contact you shortly.");
    }

    /**
     * Tenant's own booking history.
     */
    public function myBookings()
    {
        $tenant = $this->getTenant();

        $bookings = MarketplaceBooking::with('serviceProvider')
            ->where('tenant_id', $tenant->id)
            ->latest()
            ->paginate(15);

        return view('tenant.marketplace.my-bookings', compact('bookings'));
    }
}
