{{-- Rating summary + review list for a vendor ($reviews, $rating, $reviewCount, $distribution). --}}
<div class="detail-section-title">
    <h2>Reviews</h2>
    <span>{{ $reviewCount }} {{ Str::plural('review', $reviewCount) }} from completed events</span>
</div>

@if ($reviewCount)
    <div class="detail-card rating-summary" style="margin-bottom: 14px;">
        <div class="rating-summary-score">
            <strong>{{ number_format($rating, 1) }}</strong>
            <x-star-rating :value="$rating" />
            <span>Based on {{ $reviewCount }} {{ Str::plural('review', $reviewCount) }}</span>
        </div>
        <div class="rating-bars">
            @foreach ($distribution as $stars => $count)
                <div>
                    <span>{{ $stars }} ★</span>
                    <i><b style="width: {{ $reviewCount ? round($count / $reviewCount * 100) : 0 }}%;"></b></i>
                    <span>{{ $count }}</span>
                </div>
            @endforeach
        </div>
    </div>

    <div class="review-list">
        @foreach ($reviews as $review)
            <article class="review-item">
                <div class="review-item-head">
                    <div class="reviewer" style="display: flex; align-items: center; gap: 11px;">
                        <span>{{ $review->customer->initials() }}</span>
                        <p>
                            <strong>{{ $review->customer->name }}</strong>
                            <small>{{ $review->booking->event_type }} · {{ $review->created_at->format('M Y') }}</small>
                        </p>
                    </div>
                    <x-star-rating :value="$review->rating" />
                </div>
                @if ($review->comment)
                    <blockquote>“{{ $review->comment }}”</blockquote>
                @endif
            </article>
        @endforeach
    </div>
@else
    <div class="detail-empty">No reviews yet — this vendor’s first completed events will be reviewed here.</div>
@endif
