{{-- ServicePriceCalc: line items + total for a booking's selected services. --}}
@php
    $decorPrice = $services['decoration'] ? 5000 : 0;
    $servingPrice = $services['serving'] * 750;
    $dishPrice = $services['dishwashing'] * 500;
@endphp
<div style="font-size: 13px; color: #3d2510;">
    @if ($services['decoration'])
        <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
            <span>Decoration</span>
            <span style="font-weight: 600;">₱5,000</span>
        </div>
    @endif
    @if ($services['serving'] > 0)
        <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
            <span>Serving Staff ({{ $services['serving'] }} staff)</span>
            <span style="font-weight: 600;">₱{{ number_format($servingPrice) }}</span>
        </div>
    @endif
    @if ($services['dishwashing'] > 0)
        <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
            <span>Dishwashing ({{ $services['dishwashing'] }} staff)</span>
            <span style="font-weight: 600;">₱{{ number_format($dishPrice) }}</span>
        </div>
    @endif
    <div style="border-top: 1px solid #e8dfd0; padding-top: 8px; margin-top: 4px; display: flex; justify-content: space-between;">
        <span style="font-weight: 700;">TOTAL</span>
        <span style="font-weight: 700; color: #1a110a;">₱{{ number_format($decorPrice + $servingPrice + $dishPrice) }}</span>
    </div>
</div>
