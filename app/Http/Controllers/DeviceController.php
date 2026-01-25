<?php


namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function index()
    {
        $devices = Device::latest()->get();
        return view('devices.index', compact('devices'));
    }

    public function create()
    {
        return view('devices.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'phone_number' => 'required|string|unique:devices,phone_number',
            'device_token' => 'required|string|unique:devices,device_token',
        ]);

        Device::create($validated);

        return redirect()->route('devices.index')
            ->with('success', 'تم إضافة الجهاز بنجاح ✅');
    }

    public function edit(Device $device)
    {
        return view('devices.edit', compact('device'));
    }

    public function update(Request $request, Device $device)
    {
        $validated = $request->validate([
            'phone_number' => 'required|string|unique:devices,phone_number,' . $device->id,
            'device_token' => 'required|string|unique:devices,device_token,' . $device->id,
        ]);

        $device->update($validated);

        return redirect()->route('devices.index')
            ->with('success', 'تم تحديث بيانات الجهاز بنجاح ');
    }

    public function destroy(Device $device)
    {
        $device->delete();

        return redirect()->route('devices.index')
            ->with('success', 'تم حذف الجهاز بنجاح ');
    }
}

