<?php

namespace App\Http\Controllers;

use App\Models\DeliveryDeposit;
use App\Models\Representative;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DeliveryDepositController extends Controller
{
    public function index()
    {
        $this->authorize('view_delivery_deposits');

        $deposits = DeliveryDeposit::with(['representative.governorate', 'representative.company'])
        ->when(request('search'), function($query, $search) {
                $query->whereHas('representative', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%");
                });
            })
            ->when(request('representative_id'), function($query, $repId) {
                $query->where('representative_id', $repId);
            })
            ->when(request('status'), function($query, $status) {
                $query->where('status', $status);
            })
            ->when(request('governorate_id'), function($query, $governorateId) {
                $query->whereHas('representative', function($q) use ($governorateId) {
                    $q->where('governorate_id', $governorateId);
                });
            })
            ->when(request('date_from'), function($query, $date) {
                $query->whereDate('created_at', '>=', $date);
            })
            ->when(request('date_to'), function($query, $date) {
                $query->whereDate('created_at', '<=', $date);
            })
            ->when(!request()->filled('date_from') && !request()->filled('date_to'), function($query) {
                $query->whereDate('created_at', now()->toDateString());
            })
            ->latest()
            ->paginate(20);

        $governorates = \App\Models\Governorate::all();
        $representatives = Representative::active()->orderBy('name')->get();
        return view('delivery-deposits.index', compact('deposits', 'governorates', 'representatives'));
    }

    public function create()
    {
        $this->authorize('create_delivery_deposits');

        $representatives = Representative::active()->with('governorate')->get();
        return view('delivery-deposits.create', compact('representatives'));
    }

    public function store(Request $request)
    {
        $this->authorize('create_delivery_deposits');

        $validated = $request->validate([
            'representative_id' => 'required|exists:representatives,id',
            'amount' => 'nullable|numeric|min:0',
            'orders_count' => 'nullable|integer|min:0',
            'notes' => 'nullable|string|max:500',
            'receipt_image' => 'required|mimes:jpeg,png,jpg,pdf|max:2048',
        ]);

        // Handle receipt image upload
        if ($request->hasFile('receipt_image')) {
            $path = $request->file('receipt_image')->store('delivery-receipts', 'public');
            $validated['receipt_image'] = $path;
            $validated['status'] = 'delivered';
            $validated['delivered_at'] = now();
        }

        DeliveryDeposit::create($validated);

        $message = $request->hasFile('receipt_image')
            ? 'تم إضافة إيداع التسليم مع الإيصال بنجاح!'
            : 'تم إضافة إيداع التسليم بنجاح!';

        return redirect()->route('delivery-deposits.index')
            ->with('success', $message);
    }

    public function show($id)
    {
        $this->authorize('view_delivery_deposits');

        $deposit = DeliveryDeposit::with(['representative.governorate'])->findOrFail($id);
        return view('delivery-deposits.show', compact('deposit'));
    }

    public function edit($id)
    {
        $this->authorize('edit_delivery_deposits');

        $deposit = DeliveryDeposit::findOrFail($id);
        $representatives = Representative::active()->with('governorate')->get();
        return view('delivery-deposits.edit', compact('deposit', 'representatives'));
    }

    public function update(Request $request, $id)
    {
        $this->authorize('edit_delivery_deposits');

        $deposit = DeliveryDeposit::findOrFail($id);

        $validated = $request->validate([
            'representative_id' => 'required|exists:representatives,id',
            'amount' => 'nullable|numeric|min:0',
            'orders_count' => 'nullable|integer|min:0',
            'status' => 'nullable|in:pending,delivered,not_delivered',
            'notes' => 'nullable|string|max:500'
        ]);

        if ($validated['status'] === 'delivered' && $deposit->status !== 'delivered') {
            $validated['delivered_at'] = now();
        }

        $deposit->update($validated);

        return redirect()->route('delivery-deposits.index')
            ->with('success', 'تم تحديث إيداع التسليم بنجاح!');
    }

    public function updateReceipt(Request $request, $id)
    {
        $this->authorize('edit_delivery_deposits');

        $deposit = DeliveryDeposit::findOrFail($id);

        $validated = $request->validate([
            'receipt_image' => 'required|image'
        ]);

        // Delete old image if exists
        if ($deposit->receipt_image) {
            Storage::disk('public')->delete($deposit->receipt_image);
        }

        // Store new image
        $path = $request->file('receipt_image')->store('delivery-receipts', 'public');

        $deposit->update([
            'receipt_image' => $path,
            'status' => 'delivered',
            'delivered_at' => now()
        ]);

        return redirect()->route('delivery-deposits.index')
            ->with('success', 'تم رفع إيصال الإيداع وتحديث الحالة بنجاح!');
    }

    public function markAsDelivered($id)
    {
        $this->authorize('edit_delivery_deposits');

        $deposit = DeliveryDeposit::findOrFail($id);

        if ($deposit->status === 'delivered') {
            return back()->with('error', 'تم تسليم هذا الإيداع مسبقاً');
        }

        $deposit->update([
            'status' => 'delivered',
            'delivered_at' => now()
        ]);

        return redirect()->route('delivery-deposits.index')
            ->with('success', 'تم تحديث حالة الإيداع إلى "تم التسليم" بنجاح!');
    }

    public function markAsNotDelivered($id)
    {
        $this->authorize('edit_delivery_deposits');

        $deposit = DeliveryDeposit::findOrFail($id);

        $deposit->update([
            'status' => 'not_delivered',
            'delivered_at' => null
        ]);

        return redirect()->route('delivery-deposits.index')
            ->with('success', 'تم تحديث حالة الإيداع إلى "لم يسلم" بنجاح!');
    }

    public function destroy($id)
    {
        $this->authorize('delete_delivery_deposits');

        $deposit = DeliveryDeposit::findOrFail($id);

        // Delete receipt image if exists
        if ($deposit->receipt_image) {
            Storage::disk('public')->delete($deposit->receipt_image);
        }

        $deposit->delete();

        return redirect()->route('delivery-deposits.index')
            ->with('success', 'تم حذف إيداع التسليم بنجاح!');
    }
    public function showReceipt($id)
    {
        $this->authorize('view_delivery_deposits');

        $deposit = DeliveryDeposit::findOrFail($id);

        if (!$deposit->receipt_image) {
            return redirect()->back()->with('error', 'لا يوجد إيصال لهذا الإيداع.');
        }

        return view('delivery-deposits.receipt', compact('deposit'));
    }


    public function export(Request $request)
    {
        $this->authorize('view_delivery_deposits');

        $deposits = DeliveryDeposit::with(['representative.governorate'])
            ->when(request('status'), function($query, $status) {
                $query->where('status', $status);
            })
            ->when(request('date_from'), function($query, $date) {
                $query->whereDate('created_at', '>=', $date);
            })
            ->when(request('date_to'), function($query, $date) {
                $query->whereDate('created_at', '<=', $date);
            })
            ->get();

        $filename = "delivery_deposits_" . now()->format('Y-m-d') . ".csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];

        $callback = function() use ($deposits) {
            $file = fopen('php://output', 'w');

            // Add BOM for Arabic text
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, ['اسم المندوب', 'رقم الهاتف', 'المحافظة', 'المبلغ', 'الحالة', 'تاريخ الإنشاء', 'تاريخ التسليم']);

            foreach ($deposits as $deposit) {
                fputcsv($file, [
                    $deposit->representative->name ?? 'غير محدد',
                    $deposit->representative->phone ?? 'غير محدد',
                    $deposit->representative->governorate->name ?? 'غير محدد',
                    $deposit->amount,
                    $deposit->status_text,
                    $deposit->receipt_image,
                    $deposit->created_at->format('Y-m-d'),
                    $deposit->delivered_at ? $deposit->delivered_at->format('Y-m-d') : 'لم يسلم'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
