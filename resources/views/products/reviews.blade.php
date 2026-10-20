<section id="reviews" class="product-reviews-section"><div class="product-reading-width">
    <h2>Product Reviews</h2>
    @if(session('review_saved'))<p class="product-review-notice" role="status">{{ session('review_saved') }}</p>@endif
    @forelse($reviews as $review)
        <article class="product-review"><span class="product-review-avatar" aria-hidden="true">{{ mb_substr($review['name'], 0, 1) }}</span><div><header><strong>{{ $review['name'] }}</strong> <small>— {{ $review['date'] }}</small><span class="product-review-stars" aria-label="{{ $review['rating'] }} out of 5 stars">{{ str_repeat('★', $review['rating']) }}</span></header><p>{{ $review['comment'] }}</p></div></article>
    @empty<p>No reviews yet. Be the first to review {{ $item['name'] }}.</p>@endforelse
    @auth
    <form class="product-review-form" action="{{ route('product.review', ['slug' => $item['slug']]) }}" method="POST">
        @csrf
        <h3>Add or update your review</h3><p>Reviewing as {{ auth()->user()->name }}. Your review will appear after store approval.</p>
        @if($errors->any())<div role="alert" class="product-review-errors">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
        <label>Your rating *<select name="rating" required><option value="">Rate…</option>@for($rating = 5; $rating >= 1; $rating--)<option value="{{ $rating }}" @selected(old('rating') == $rating)>{{ $rating }} {{ $rating === 1 ? 'star' : 'stars' }}</option>@endfor</select></label>
        <label>Your review *<textarea name="comment" rows="6" minlength="10" maxlength="2000" required>{{ old('comment') }}</textarea></label>
        <button type="submit">Submit review</button>
    </form>
    @else<p><a class="commerce-button" href="{{ route('login') }}">Sign in to write a review</a></p>@endauth
</div></section>
