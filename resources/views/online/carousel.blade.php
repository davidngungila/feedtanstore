@extends('layouts.app')

@section('page-title', 'Carousel Management')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Carousel Management</h2>
            <a href="{{ route('online.carousel.create') }}" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg font-medium">
                <i class="fas fa-plus mr-2"></i> Add Slide
            </a>
        </div>

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-800 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if($slides->isEmpty())
            <div class="text-center py-12">
                <i class="fas fa-images text-6xl text-gray-300 mb-4"></i>
                <p class="text-gray-500">No slides yet. Add your first slide!</p>
            </div>
        @else
            <div class="grid grid-cols-1 gap-4">
                @foreach($slides as $slide)
                <div class="border rounded-lg p-4 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        @if($slide->image)
                            <div class="w-24 h-16 bg-gray-100 rounded-lg flex items-center justify-center overflow-hidden">
                                <img src="{{ asset('storage/' . $slide->image) }}" alt="{{ $slide->title }}" class="max-h-full max-w-full object-contain">
                            </div>
                        @else
                            <div class="w-24 h-16 bg-gradient-to-r from-gray-200 to-gray-300 rounded-lg flex items-center justify-center">
                                <i class="fas fa-image text-gray-400"></i>
                            </div>
                        @endif
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="font-semibold text-gray-900">{{ $slide->title }}</h3>
                                @if($slide->is_active)
                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Active</span>
                                @else
                                    <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">Inactive</span>
                                @endif
                            </div>
                            @if($slide->subtitle)
                                <p class="text-sm text-gray-500">{{ Str::limit($slide->subtitle, 50) }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('online.carousel.edit', $slide) }}" class="px-3 py-1 text-sm bg-blue-100 text-blue-800 rounded-lg hover:bg-blue-200">
                            <i class="fas fa-edit mr-1"></i> Edit
                        </a>
                        <form action="{{ route('online.carousel.destroy', $slide) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Are you sure you want to delete this slide?')" class="px-3 py-1 text-sm bg-red-100 text-red-800 rounded-lg hover:bg-red-200">
                                <i class="fas fa-trash mr-1"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
