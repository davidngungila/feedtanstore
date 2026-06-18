<?php

namespace App\Http\Controllers;

use App\Models\CarouselSlide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CarouselController extends Controller
{
    public function index()
    {
        $slides = CarouselSlide::orderBy('order')->get();
        return view('online.carousel', compact('slides'));
    }

    public function create()
    {
        return view('online.carousel-create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string',
            'button_text' => 'nullable|string|max:255',
            'button_url' => 'nullable|url',
            'background_color' => 'nullable|string|max:50',
            'gradient_color' => 'nullable|string|max:50',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'order' => 'nullable|integer',
        ]);

        $data = $validated;
        $data['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('carousel', 'public');
        }

        CarouselSlide::create($data);

        return redirect()->route('online.carousel')->with('success', 'Slide created successfully!');
    }

    public function edit(CarouselSlide $carousel)
    {
        return view('online.carousel-edit', compact('carousel'));
    }

    public function update(Request $request, CarouselSlide $carousel)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string',
            'button_text' => 'nullable|string|max:255',
            'button_url' => 'nullable|url',
            'background_color' => 'nullable|string|max:50',
            'gradient_color' => 'nullable|string|max:50',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'order' => 'nullable|integer',
        ]);

        $data = $validated;
        $data['is_active'] = $request->boolean('is_active', $carousel->is_active);

        if ($request->hasFile('image')) {
            if ($carousel->image) {
                Storage::disk('public')->delete($carousel->image);
            }
            $data['image'] = $request->file('image')->store('carousel', 'public');
        }

        $carousel->update($data);

        return redirect()->route('online.carousel')->with('success', 'Slide updated successfully!');
    }

    public function destroy(CarouselSlide $carousel)
    {
        if ($carousel->image) {
            Storage::disk('public')->delete($carousel->image);
        }
        $carousel->delete();

        return redirect()->route('online.carousel')->with('success', 'Slide deleted successfully!');
    }
}
