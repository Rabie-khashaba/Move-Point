<?php

namespace App\Imports;

use App\Models\DebtSheet;
use App\Models\salary_records1;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\Importable;
use RuntimeException;

class SalaryImport implements ToModel, WithHeadingRow, WithCalculatedFormulas
{
    use Importable;

    protected $month;

    public function __construct($month = null)
    {
        $this->month = $month;
    }

    protected $map = [
        'state' => 'state',
        'star id' => 'star_id',
        'name' => 'name',
        'vehicle type' => 'vehicle_type',
        'contractor' => 'contractor',
        'hub' => 'hub',
        'zone' => 'zone',
        'working days' => 'working_days',
        'delivered cash' => 'delivered_cash',
        'rto' => 'rto',
        'exchange' => 'exchange',
        'crp' => 'crp',
        'pickups stops' => 'pickups_stops',
        'fixed day' => 'fixed_day',
        'variable pkg' => 'variable_pkg',
        'total delivered' => 'total_delivered',
        'guarantee day' => 'guarantee_day',
        'monthly guarantee volume' => 'monthly_guarantee_volume',
        'guarantee volume' => 'guarantee_volume',
        'fixed salary' => 'fixed_salary',
        'variable d r' => 'variable_d_r',
        'exchange variable' => 'exchange_variable',
        'crp variable' => 'crp_variable',
        'pickups variable' => 'pickups_variable',
        'fleet bonus' => 'fleet_bonus',
        'guarantee' => 'guarantee',
        'guarantee deduction' => 'guarantee_deduction',
        'ops bonus' => 'ops_bonus',
        'ops deductions' => 'ops_deductions',
        'fleet deduction' => 'fleet_deduction',
        'fake update' => 'fake_update',
        'total' => 'total',
        'short tag' => 'short_tag',
        'cn' => 'cn',
        'loans' => 'loans',
        'total deduction' => 'total_deduction',
        'net salary' => 'net_salary',
        'amounts on pilots' => 'amounts_on_pilots',
        'salary date' => 'salary_date',
    ];

    private $stop = false;

    private function cleanHeading($h)
    {
        $h = trim(mb_strtolower((string) $h));
        $h = preg_replace('/\s+/', ' ', $h);
        $h = str_replace(['_', '/', '\\', '-', '–', '—'], ' ', $h);
        return trim($h);
    }

    public function model(array $row)
    {
        if ($this->stop) {
            return null;
        }

        $allEmpty = true;
        foreach ($row as $value) {
            if (trim((string) $value) !== '' && $value !== null) {
                $allEmpty = false;
                break;
            }
        }

        if ($allEmpty) {
            $this->stop = true;
            return null;
        }

        $data = [];

        foreach ($row as $heading => $value) {
            $h = $this->cleanHeading($heading);
            if (!isset($this->map[$h])) {
                continue;
            }

            $col = $this->map[$h];

            if (is_string($value)) {
                $v = trim($value);
                $v = str_replace(',', '', $v);
                if (is_numeric($v)) {
                    $v = $v + 0;
                }
            } else {
                $v = $value;
            }

            if ($col === 'salary_date' && is_numeric($v)) {
                $v = date('Y-m-d', strtotime('1899-12-30 +' . $v . ' days'));
            }

            $data[$col] = $v;
        }

        if (empty($data)) {
            return null;
        }

        $data['salary_date'] = $this->month ? ($this->month . '-01') : (date('Y-m') . '-01');

        $this->applyDebtDeductions($data);

        return new salary_records1($data);
    }

    private function applyDebtDeductions(array $data): void
    {
        $starId = isset($data['star_id']) ? trim((string) $data['star_id']) : null;
        if (!$starId) {
            return;
        }

        $shortTag = $this->toNumber($data['short_tag'] ?? 0);
        $creditNote = $this->toNumber($data['cn'] ?? 0);
        $loans = $this->toNumber($data['loans'] ?? 0);

        if ($shortTag <= 0 && $creditNote <= 0 && $loans <= 0) {
            return;
        }

        $debtSheets = DebtSheet::query()
            ->where('star_id', $starId)
            ->where(function ($q) {
                $q->where('shortage', '>', 0)
                    ->orWhere('credit_note', '>', 0)
                    ->orWhere('advances', '>', 0);
            })
            ->orderBy('sheet_date')
            ->orderBy('id')
            ->get();

        if ($debtSheets->isEmpty()) {
            throw new RuntimeException("لا يوجد مديونية لهذا الكود: {$starId}");
        }

        foreach ($debtSheets as $debtSheet) {
            if ($shortTag > 0) {
                $current = $this->toNumber($debtSheet->shortage);
                $applied = min($shortTag, $current);
                $debtSheet->shortage = max(0, $current - $applied);
                $shortTag -= $applied;
            }

            if ($creditNote > 0) {
                $current = $this->toNumber($debtSheet->credit_note);
                $applied = min($creditNote, $current);
                $debtSheet->credit_note = max(0, $current - $applied);
                $creditNote -= $applied;
            }

            if ($loans > 0) {
                $current = $this->toNumber($debtSheet->advances);
                $applied = min($loans, $current);
                $debtSheet->advances = max(0, $current - $applied);
                $loans -= $applied;
            }

            $remainingTotal = $this->toNumber($debtSheet->shortage)
                + $this->toNumber($debtSheet->credit_note)
                + $this->toNumber($debtSheet->advances);

            $debtSheet->status = $remainingTotal <= 0 ? 'سدد' : 'لم يسدد';
            $debtSheet->save();

            if ($shortTag <= 0 && $creditNote <= 0 && $loans <= 0) {
                break;
            }
        }
    }

    private function toNumber($value): float
    {
        if ($value === null || $value === '') {
            return 0.0;
        }

        if (is_numeric($value)) {
            return (float) $value;
        }

        $normalized = str_replace([',', ' '], '', (string) $value);
        return is_numeric($normalized) ? (float) $normalized : 0.0;
    }
}
