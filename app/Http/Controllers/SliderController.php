<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Slider;

class SliderController extends Controller
{
    public function index()
    {
        $this->authorize('view_sliders');
        $sliders = Slider::orderBy('sort_order')->latest('id')->paginate(20);
        return view('sliders.index', compact('sliders'));
    }

    public function store(Request $request)
    {
        $this->authorize('create_sliders');
        $data = $request->validate([
            'image' => 'required|image|mimes:jpeg,jpg,png,webp|max:5120',
            'sort_order' => 'nullable|integer|min:0',
        ]);
        try {
            $path = $request->file('image')->store('sliders', 'public');
        } catch (\Throwable $e) {
            return back()->withErrors(['image' => 'تعذر حفظ الصورة: ' . $e->getMessage()]);
        }
        Slider::create([
            'image_path' => $path,
            'sort_order' => $data['sort_order'] ?? 0,
        ]);
        return back()->with('success', 'تم إضافة صورة السلايدر');
    }

    public function update(Request $request, Slider $slider)
    {
        $this->authorize('edit_sliders');
        $data = $request->validate([
            'sort_order' => 'required|integer|min:0',
        ]);
        $slider->update(['sort_order' => $data['sort_order']]);
        return back()->with('success', 'تم تحديث الترتيب');
    }

    public function destroy(Slider $slider)
    {
        $this->authorize('delete_sliders');
        // Delete file if exists
        if ($slider->image_path && Storage::disk('public')->exists($slider->image_path)) {
            Storage::disk('public')->delete($slider->image_path);
        }
        $slider->delete();
        return back()->with('success', 'تم حذف الصورة');
    }

    public function image(Slider $slider)
    {
        $rawPath = (string) $slider->image_path;

        if (empty($rawPath)) {
            abort(404, 'صورة السلايدر غير موجودة');
        }

        // Check if path is already a full URL
        if (filter_var($rawPath, FILTER_VALIDATE_URL)) {
            // It's already a full URL, redirect directly
            return redirect($rawPath);
        }

        // Normalize common prefixes for storage paths
        $relative = ltrim($rawPath, '/');
        $storageRelative = $relative;
        if (str_starts_with($storageRelative, 'storage/')) {
            $storageRelative = substr($storageRelative, strlen('storage/'));
        }
        if (str_starts_with($storageRelative, 'public/')) {
            $storageRelative = substr($storageRelative, strlen('public/'));
        }

        // Check if file exists in storage
        try {
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($storageRelative)) {
                $storageUrl = asset('storage/app/public/' . $storageRelative);
                return redirect($storageUrl);
            }
        } catch (\Throwable $e) {
            // ignore
        }

        // Check direct storage path
        $direct = storage_path('app/public/' . $storageRelative);
        if (is_file($direct)) {
            $storageUrl = asset('storage/app/public/' . $storageRelative);
            return redirect($storageUrl);
        }

        // Check public path variants
        $publicCandidate = public_path($relative);
        if (is_file($publicCandidate)) {
            $storageUrl = asset($relative);
            return redirect($storageUrl);
        }

        abort(404, 'صورة السلايدر غير موجودة');
    }

}


