<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Governorate; // Ensure you import the related models
use App\Models\Location;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    // Display all messages with related government and location data
    public function index(Request $request)
    {

            $query = Message::with(['government', 'location']);

            // فلترة بالاسم
            if ($request->filled('governorate_name')) {
                $query->whereHas('government', function($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->governorate_name . '%');
                });
            }

            if ($request->filled('location_name')) {
                $query->whereHas('location', function($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->location_name . '%');
                });
            }

            $messages = $query->paginate(15)->withQueryString();

            return view('messages.index', compact('messages'));
    }

    // Show form to create a new message
    public function create()
    {
        $governments = Governorate::all();  // Assuming you have a Governorate model
        $locations = Location::all();  // Assuming you have a Location model

        return view('messages.create', compact('governments', 'locations'));
    }

    // Store a new message
   public function store(Request $request)
    {
        try {
            // Validate the request
            $request->validate([
                'government_id' => 'required|exists:governorates,id',
                'location_id' => 'required|exists:locations,id',
                'description' => 'required|string',
                'google_map_url' => 'required|url',
            ]);



            $exists = Message::where('government_id', $request->government_id)
                ->where('location_id', $request->location_id)
                ->exists();

            if ($exists) {
                return redirect()->route('messages.index')->with('error',  'هذا الموقع مع هذه المحافظة موجود بالفعل');
            }

            // Create the message
            Message::create($request->all());

            // Return success message
            return redirect()->route('messages.index')->with('success', 'تم إنشاء الرسالة بنجاح');

        } catch (\Exception $e) {
            // Log the exception message (optional)
            \Log::error('Error creating message: ' . $e->getMessage());

            // Return a generic error message
            return redirect()->route('messages.index')->with('error', 'حدث خطأ أثناء إنشاء الرسالة. يرجى المحاولة مرة أخرى.');
        }
    }

    // Show a single message by ID
    public function show($id)
    {
        $message = Message::with(['government', 'location'])->find($id);

        if (!$message) {
            return redirect()->route('messages.index')->with('error', 'الرسالة غير موجودة');
        }

        return view('messages.show', compact('message'));
    }

    // Show form to edit a message
    public function edit($id)
    {
        $message = Message::find($id);

        if (!$message) {
            return redirect()->route('messages.index')->with('error', 'الرسالة غير موجودة');
        }

        $governments = Governorate::all();
        $locations = Location::all();

        return view('messages.edit', compact('message', 'governments', 'locations'));
    }

    // Update a message by ID
    public function update(Request $request, $id)
    {
        $request->validate([
            'government_id' => 'required|exists:governorates,id',
            'location_id' => 'required|exists:locations,id',
            'description' => 'required|string',
            'google_map_url' => 'required|url',
        ]);

        $message = Message::find($id);

        if (!$message) {
            return redirect()->route('messages.index')->with('error', 'الرسالة غير موجودة');
        }

        $message->update($request->all());

        return redirect()->route('messages.index')->with('success', 'تم تحديث الرسالة بنجاح');
    }

    // Delete a message by ID
    public function destroy($id)
    {
        $message = Message::find($id);

        if (!$message) {
            return redirect()->route('messages.index')->with('error', 'الرسالة غير موجودة');
        }

        $message->delete();

        return redirect()->route('messages.index')->with('success', 'تم حذف الرسالة بنجاح');
    }

    // Get messages by government and location (or government only)
    public function getMessagesByLocation(Request $request)
    {
        try {
            $governmentId = $request->input('government_id');
            $locationId = $request->input('location_id');

            if (!$governmentId) {
                return response()->json(['error' => 'المحافظة مطلوبة'], 400);
            }

            $query = Message::where('government_id', $governmentId);

            // If location_id is provided, filter by both government and location
            if ($locationId) {
                $query->where('location_id', $locationId);
            }

            $messages = $query->get(['id', 'description']);

            return response()->json($messages);
        } catch (\Exception $e) {
            return response()->json(['error' => 'خطأ في تحميل الرسائل'], 500);
        }
    }

    // Get a single message by ID
    public function getMessage($id)
    {
        try {
            $message = Message::find($id);

            if (!$message) {
                return response()->json(['error' => 'الرسالة غير موجودة'], 404);
            }

            return response()->json($message);
        } catch (\Exception $e) {
            return response()->json(['error' => 'خطأ في تحميل الرسالة'], 500);
        }
    }
}
