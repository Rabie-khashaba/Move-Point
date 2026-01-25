<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\MessageTraining;
use App\Models\Governorate; // Ensure you import the related models
use App\Models\Location;
use App\Models\Representative;
use App\Models\RepresentativeNote;
use Illuminate\Http\Request;

class MessageTrainingController extends Controller
{
    // Display all messages with related government and location data
    public function index(Request $request)
    {

        $query = MessageTraining::with(['government', 'location']);

        // فلترة بالاسم
        if ($request->filled('governorate_name')) {
            $query->whereHas('government', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->governorate_name . '%');
            });
        }

        if ($request->filled('location_name')) {
            $query->whereHas('location', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->location_name . '%');
            });
        }

        $messages = $query->paginate(15)->withQueryString();


        return view('message_training.index', compact('messages'));
    }

    // Show form to create a new message
    public function create()
    {
        $governments = Governorate::all();  // Assuming you have a Governorate model
        $locations = Location::all();  // Assuming you have a Location model
        $companies = Company::all();

        return view('message_training.create', compact('governments', 'locations', 'companies'));
    }

    // Store a new message
    public function store(Request $request)
    {
        //return $request->all();
        try {
            // Validate the request
            $request->validate([
                'type' => 'required|string',
                'government_id' => 'nullable|exists:governorates,id',
                'location_id' => 'nullable|exists:locations,id',
                'company_id' => 'nullable|exists:companies,id',
                'description_training' => 'nullable|string',
                'google_map_url' => 'nullable|url',
                'link_training' => 'nullable|url',
                'description_location' => 'nullable|string',

            ]);

            $data = [
                'type' => $request->type,
                'government_id' => $request->government_id,
                'location_id' => $request->location_id,
                'company_id' => $request->company_id,
            ];

            if ($request->type === 'أونلاين') {
                $data['link_training'] = $request->link_training;
                $data['description_training'] = $request->description_training;
            } elseif ($request->type === 'في المقر') {
                $data['description_location'] = $request->description_location;
                $data['google_map_url'] = $request->google_map_url;
            }

            $exists = MessageTraining::where('government_id', $request->government_id)
                ->where('location_id', $request->location_id)
                ->exists();

            if ($exists) {
                return redirect()->route('messagesTraining.index')->with('error', 'هذا الموقع مع هذه المحافظة موجود بالفعل');
            }

            MessageTraining::create($data);

            // Return success message
            return redirect()->route('messagesTraining.index')->with('success', 'تم إنشاء الرسالة بنجاح');

        } catch (\Exception $e) {
            // Log the exception message (optional)
            \Log::error('Error creating message: ' . $e->getMessage());

            // Return a generic error message
            return redirect()->route('messagesTraining.index')->with('error', 'حدث خطأ أثناء إنشاء الرسالة. يرجى المحاولة مرة أخرى.');
        }
    }

    // Show a single message by ID
    public function show($id)
    {
        $message = MessageTraining::with(['government', 'location'])->find($id);

        if (!$message) {
            return redirect()->route('message_training.index')->with('error', 'الرسالة غير موجودة');
        }

        return view('message_training.show', compact('message'));
    }

    // Show form to edit a message
    public function edit($id)
    {
        $message = MessageTraining::find($id);

        if (!$message) {
            return redirect()->route('messagesTraining.index')->with('error', 'الرسالة غير موجودة');
        }

        $governments = Governorate::all();
        $locations = Location::all();
        $companies = Company::all();

        return view('message_training.edit', compact('message', 'governments', 'locations', 'companies'));
    }

    // Update a message by ID
    public function update(Request $request, $id)
    {
        //return $request;
        //messagesTrainingreturn $request->all();
        $request->validate([
            'government_id' => 'nullable|exists:governorates,id',
            'location_id' => 'nullable|exists:locations,id',
            'company_id' => 'nullable|exists:companies,id',
            'description' => 'nullable|string',
            'google_map_url' => 'nullable|url',
        ]);

        $message = MessageTraining::find($id);

        if (!$message) {
            return redirect()->route('messagesTraining.index')->with('error', 'الرسالة غير موجودة');
        }

        $data = [
            'government_id' => $request->government_id,
            'location_id' => $request->location_id,
            'company_id' => $request->company_id,
        ];

        if ($message->type === 'أونلاين') {
            $data['link_training'] = $request->google_map_url;
            $data['description_training'] = $request->description;
            // نفرغ قيم الاوفلاين لو اتحولت لاونلاين
            $data['description_location'] = null;
            $data['google_map_url'] = null;
        } elseif ($message->type === 'في المقر') {
            $data['description_location'] = $request->description;
            $data['google_map_url'] = $request->google_map_url;
            // نفرغ قيم الاونلاين لو اتحولت لاوفلاين
            $data['link_training'] = null;
            $data['description_training'] = null;
        }

        //return $data;

        $message->update($data);
        return redirect()->route('messagesTraining.index')->with('success', 'تم تحديث الرسالة بنجاح');
    }





    // Delete a message by ID
    public function destroy($id)
    {
        $message = MessageTraining::find($id);

        if (!$message) {
            return redirect()->route('messagesTraining.index')->with('error', 'الرسالة غير موجودة');
        }

        $message->delete();

        return redirect()->route('messagesTraining.index')->with('success', 'تم حذف الرسالة بنجاح');
    }

    // Get messages by government and location (or government only)
    /* public function getMessagesByLocation(Request $request)
    {
        try {
            $governmentId = $request->input('government_id');
            $locationId = $request->input('location_id');
            $type = $request->input('type');

            if (!$governmentId) {
                return response()->json(['error' => 'المحافظة مطلوبة'], 400);
            }

            $query = MessageTraining::where('government_id', $governmentId);

            // If location_id is provided, filter by both government and location
            if ($locationId) {
                $query->where('location_id', $locationId);
            }
            if ($type) {
                $query->where('type', $type);
            }

            $messages = $query->get([
                'id',
                'type', // online / location
                'description_training',
                'link_training',
                'description_location',
                'google_map_url'
            ]);

            return response()->json($messages);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'error' => 'خطأ في تحميل الرسائل',
                    'message' => $e->getMessage(),
                    'line' => $e->getLine()
                ],

                500
            );

        }
    }
 */


    public function getMessagesByLocation(Request $request)
    {
        //return $request ;

        try {
            $governmentId = $request->input('government_id');
            $locationId = $request->input('location_id');
            $companyId = $request->input('company_id');


            if (!$governmentId || !$locationId || !$companyId) {
                return response()->json([]);
            }

            $messages =  MessageTraining::where('government_id', $governmentId)
                ->where('location_id', $locationId)->where('company_id', $companyId)
                ->get();

            return response()->json($messages);



        } catch (\Exception $e) {
            return response()->json(['error' => 'خطأ في تحميل الرسائل'], 500);
        }
    }
    // Get a single message by ID
    public function getMessage($id)
    {
        try {
            $message = MessageTraining::find($id);

            if (!$message) {
                return response()->json(['error' => 'الرسالة غير موجودة'], 404);
            }

            return response()->json($message);
        } catch (\Exception $e) {
            return response()->json(['error' => 'خطأ في تحميل الرسالة'], 500);
        }
    }



    public function saveNote(Request $request, $id)
    {

        try {

            $validated = $request->validate([
                'note' => 'required|string|max:5000',
            ]);

            $representative = Representative::findOrFail($id);

            $note = RepresentativeNote::create([
                'representative_id' => $representative->id,
                'note' => $validated['note'],
                'created_by' => auth()->id(),
            ]);

            $notes = $representative->notes()->with('createdBy:id,name')->get(['id', 'note', 'created_at', 'created_by']);

            return response()->json([
                'success' => true,
                'message' => 'تم إضافة الملاحظة بنجاح',
                'notes' => $notes,

            ]);
        } catch (\Illuminate\Validation\ValidationException $ve) {
            return response()->json([
                'success' => false,
                'message' => 'بيانات غير صالحة',
                'errors' => $ve->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'تعذر حفظ الملاحظة',
            ], 500);
        }
    }
}
