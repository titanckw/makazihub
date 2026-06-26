<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\MarketplaceBooking;
use App\Models\MarketplaceServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MarketplaceController extends Controller
{
    public function index()
    {
        $services = MarketplaceServiceProvider::orderBy('sort_order')->orderBy('name')->paginate(20);
        $categories = MarketplaceServiceProvider::$categories;
        return view('manager.marketplace.index', compact('services', 'categories'));
    }

    public function create()
    {
        $categories = MarketplaceServiceProvider::$categories;
        return view('manager.marketplace.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateProvider($request);
        $validated['slug'] = Str::slug($validated['name']) . '-' . Str::random(4);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('marketplace/logos', 'public');
        }

        MarketplaceServiceProvider::create($validated);

        return redirect()->route('manager.marketplace.index')
            ->with('success', 'Service provider added to the marketplace.');
    }

    public function edit(MarketplaceServiceProvider $marketplace)
    {
        $categories = MarketplaceServiceProvider::$categories;
        return view('manager.marketplace.edit', compact('marketplace', 'categories'));
    }

    public function update(Request $request, MarketplaceServiceProvider $marketplace)
    {
        $validated = $this->validateProvider($request, $marketplace->id);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('marketplace/logos', 'public');
        }

        $marketplace->update($validated);

        return redirect()->route('manager.marketplace.index')
            ->with('success', 'Service provider updated.');
    }

    public function destroy(MarketplaceServiceProvider $marketplace)
    {
        $marketplace->delete();
        return redirect()->route('manager.marketplace.index')
            ->with('success', 'Service provider removed.');
    }

    public function bookings()
    {
        $bookings = MarketplaceBooking::with(['tenant.user', 'serviceProvider'])
            ->latest()
            ->paginate(25);

        $statuses = MarketplaceBooking::$statuses;

        return view('manager.marketplace.bookings', compact('bookings', 'statuses'));
    }

    public function updateBooking(Request $request, MarketplaceBooking $booking)
    {
        $request->validate(['status' => ['required', 'in:' . implode(',', array_keys(MarketplaceBooking::$statuses))]]);
        $booking->update(['status' => $request->status]);

        return back()->with('success', 'Booking status updated.');
    }

    private function validateProvider(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'name'          => ['required', 'string', 'max:150'],
            'category'      => ['required', 'in:' . implode(',', array_keys(MarketplaceServiceProvider::$categories))],
            'description'   => ['required', 'string', 'max:1000'],
            'phone'         => ['required', 'string', 'max:20'],
            'whatsapp'      => ['nullable', 'string', 'max:20'],
            'email'         => ['nullable', 'email', 'max:150'],
            'website'       => ['nullable', 'url', 'max:255'],
            'price_label'   => ['nullable', 'string', 'max:100'],
            'base_price'    => ['nullable', 'numeric', 'min:0'],
            'is_active'     => ['boolean'],
            'is_featured'   => ['boolean'],
            'sort_order'    => ['integer', 'min:0'],
            'logo'          => ['nullable', 'image', 'max:2048'],
            'property_id'   => ['nullable', 'exists:properties,id'],
        ]);
    }
}
