{{-- resources/views/emails/welcome-tenant.blade.php --}}
<x-emails.partials.layout>

  <p class="greeting">Dear {{ $tenant->user->name }},</p>
  <h2>Welcome to MakaziHub! 🏠</h2>
  <p class="subtitle">Your tenancy has been set up. Here's everything you need to get started.</p>

  <div class="success-box">
    ✅ You've been successfully added as a tenant at <strong>{{ $tenant->activeLease?->unit?->property?->name ?? 'your property' }}</strong>.
  </div>

  <div class="card">
    <div class="card-row">
      <span class="card-label">Your Name</span>
      <span class="card-value">{{ $tenant->user->name }}</span>
    </div>
    <div class="card-row">
      <span class="card-label">Email</span>
      <span class="card-value">{{ $tenant->user->email }}</span>
    </div>
    @if($tenant->activeLease)
    <div class="card-row">
      <span class="card-label">Property</span>
      <span class="card-value">{{ $tenant->activeLease->unit->property->name }}</span>
    </div>
    <div class="card-row">
      <span class="card-label">Unit</span>
      <span class="card-value">{{ $tenant->activeLease->unit->unit_number }}</span>
    </div>
    <div class="card-row">
      <span class="card-label">Monthly Rent</span>
      <span class="card-value">KES {{ number_format($tenant->activeLease->monthly_rent, 2) }}</span>
    </div>
    <div class="card-row">
      <span class="card-label">Lease Start</span>
      <span class="card-value">{{ $tenant->activeLease->start_date->format('M d, Y') }}</span>
    </div>
    @if($tenant->activeLease->end_date)
    <div class="card-row">
      <span class="card-label">Lease End</span>
      <span class="card-value">{{ $tenant->activeLease->end_date->format('M d, Y') }}</span>
    </div>
    @endif
    @endif
  </div>

  <p style="font-size:14px; color:#475569; margin-bottom:8px; font-weight:600;">Your Tenant Portal Login:</p>
  <div class="card" style="margin-bottom:20px;">
    <div class="card-row">
      <span class="card-label">Portal URL</span>
      <span class="card-value"><a href="{{ url('/login') }}" style="color:#059669;">{{ url('/login') }}</a></span>
    </div>
    <div class="card-row">
      <span class="card-label">Email</span>
      <span class="card-value" style="font-family:monospace;">{{ $tenant->user->email }}</span>
    </div>
    <div class="card-row">
      <span class="card-label">Password</span>
      <span class="card-value" style="color:#D97706;">Use the password set during registration, or reset via the login page.</span>
    </div>
  </div>

  <p style="font-size:14px; color:#475569; margin-bottom:20px; line-height:1.6;">
    Through your tenant portal you can: view all invoices, track payments, download receipts, and monitor your lease details. Rent invoices will be sent to this email automatically each month.
  </p>

  <a href="{{ url('/login') }}" class="btn">Access Tenant Portal</a>

  <hr class="divider">
  <p style="font-size:13px; color:#94A3B8;">If you did not expect this email or believe it was sent in error, please contact your property manager immediately.</p>

</x-emails.partials.layout>
