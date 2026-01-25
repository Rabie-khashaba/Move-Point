<?php

namespace App\Http\Controllers;

use App\Models\Advertiser;
use Illuminate\Http\Request;

class AdvertiserController extends Controller
{
    public function index(Request $request)
    {
        $query = \App\Models\Advertiser::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $advertisers = $query->latest()->paginate(10); // ✅ استخدم paginate

        return view('advertisers.index', compact('advertisers'));
    }

    public function create()
    {
        return view('advertisers.create');
    }

    public function show($id)
    {
        $advertiser = Advertiser::findOrFail($id);
        return view('advertisers.show', compact('advertiser'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        Advertiser::create(['name' => $request->name]);

        return redirect()->route('advertisers.index')->with('success', 'تمت إضافة المعلن بنجاح ✅');
    }

    public function edit(Advertiser $advertiser)
    {
        return view('advertisers.edit', compact('advertiser'));
    }

    public function update(Request $request, Advertiser $advertiser)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $advertiser->update(['name' => $request->name]);

        return redirect()->route('advertisers.index')->with('success', 'تم تحديث المعلن بنجاح ✅');
    }

    public function destroy(Advertiser $advertiser)
    {
        $advertiser->delete();
        return redirect()->route('advertisers.index')->with('success', 'تم حذف المعلن بنجاح 🗑️');
    }
}
