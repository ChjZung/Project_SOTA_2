@extends('layouts.app')
@section('title', 'Avada Vegan Store - 100% Plant-Based & Organic Store')
@section('content')
<main id="main" class="storefront-home clearfix width-100">
    <div class="fusion-row" style="max-width:100%;">
        <section id="content" class="full-width">
            <div id="post-5" class="post-5 page type-page status-publish hentry">
                <div class="post-content">
                    @include('home.hero')
                    @include('home.benefits')
                    @include('home.categories')
                    @include('home.new-arrivals')
                    @include('home.ready-meals')
                    @include('home.beverages')
                    @include('home.flash-deals')
                    @include('home.best-sellers')
                    @include('home.reviews')
                    @include('home.articles')
                    @include('home.bottom-banner')
                </div>
            </div>
        </section>
    </div>
</main>
@endsection
