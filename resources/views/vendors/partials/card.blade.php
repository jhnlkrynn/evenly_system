{{-- Vendor summary for detail-page sidebars. --}}
<div class="detail-vendor-card">
    <div class="detail-vendor-head">
        <div class="detail-vendor-avatar" aria-hidden="true">{{ $vendor->initials() }}</div>
        <div>
            <strong>{{ $vendor->business_name }}</strong>
            <small>✓ Verified vendor</small>
        </div>
    </div>
    <dl>
        <div>
            <dt>Location</dt>
            <dd>{{ $vendor->locationLabel() }}</dd>
        </div>
        @if ($vendor->contact_number)
            <div>
                <dt>Contact</dt>
                <dd>{{ $vendor->contact_number }}</dd>
            </div>
        @endif
        <div>
            <dt>On Eventlynk since</dt>
            <dd>{{ $vendor->created_at->format('M Y') }}</dd>
        </div>
    </dl>
    @unless ($hideProfileLink ?? false)
        <button type="button" class="detail-side-link" data-href="{{ route('vendors.show', $vendor) }}">View vendor profile</button>
    @endunless
</div>
