@extends('layouts.app')

@section('page-title', 'Product Catalog')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <h2 class="text-xl font-bold mb-4" :class="darkMode?'text-white':'text-primary-900'">Product Catalog</h2>
        <p class="text-sm" :class="darkMode?'text-primary-400':'text-gray-600'">Content for Product Catalog page will be here.</p>
    </div>
</div>
@endsection