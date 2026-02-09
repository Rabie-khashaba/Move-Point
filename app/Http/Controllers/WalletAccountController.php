<?php

namespace App\Http\Controllers;

use App\Exports\WalletAccountsExport;
use App\Models\Representative;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class WalletAccountController extends Controller
{
    public function index(Request $request)
    {
        $query = Representative::with(['governorate', 'location']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('bank_account', 'like', "%{$search}%");
            });
        }

        if ($request->filled('governorate_id')) {
            $query->where('governorate_id', $request->governorate_id);
        }

        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        if ($request->filled('wallet_status')) {
            if ($request->wallet_status === 'with') {
                $query->whereNotNull('bank_account')->where('bank_account', '!=', '');
            } elseif ($request->wallet_status === 'without') {
                $query->where(function ($q) {
                    $q->whereNull('bank_account')->orWhere('bank_account', '');
                });
            }
        }

        // Statistics (based on current filters)
        $statsQuery = clone $query;
        $totalWallets = (clone $statsQuery)->count();
        $withWalletCount = (clone $statsQuery)
            ->whereNotNull('bank_account')
            ->where('bank_account', '!=', '')
            ->count();
        $withoutWalletCount = (clone $statsQuery)
            ->where(function ($q) {
                $q->whereNull('bank_account')->orWhere('bank_account', '');
            })
            ->count();

        $walletAccounts = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        $governorates = \App\Models\Governorate::all();
        $locations = \App\Models\Location::all();

        return view('wallet_accounts.index', compact(
            'walletAccounts',
            'governorates',
            'locations',
            'totalWallets',
            'withWalletCount',
            'withoutWalletCount'
        ));
    }

    public function export(Request $request)
    {
        $fileName = 'wallet_accounts_' . now()->format('Y_m_d_His') . '.xlsx';

        return Excel::download(new WalletAccountsExport($request->all()), $fileName);
    }
}
