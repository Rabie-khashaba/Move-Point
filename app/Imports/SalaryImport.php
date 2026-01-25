<?php

namespace App\Imports;

use App\Models\salary_records1;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas; // ✅ مهم
use Maatwebsite\Excel\Concerns\Importable;

class SalaryImport implements ToModel, WithHeadingRow, WithCalculatedFormulas
{
    use Importable;

    protected $month;

    public function __construct($month = null)
    {
        $this->month = $month;
    }

    protected $map = [
        'state'                       => 'state',
        'star id'                     => 'star_id',
        'name'                        => 'name',
        'vehicle type'                => 'vehicle_type',
        'contractor'                  => 'contractor',
        'hub'                         => 'hub',
        'zone'                        => 'zone',
        'working days'                => 'working_days',
        'delivered cash'              => 'delivered_cash',
        'rto'                         => 'rto',
        'exchange'                    => 'exchange',
        'crp'                         => 'crp',
        'pickups stops'               => 'pickups_stops',
        'fixed day'                   => 'fixed_day',
        'variable pkg'                => 'variable_pkg',
        'total delivered'             => 'total_delivered',
        'guarantee day'               => 'guarantee_day',
        'monthly guarantee volume'    => 'monthly_guarantee_volume',
        'guarantee volume'            => 'guarantee_volume',
        'fixed salary'                => 'fixed_salary',
        'variable d r'                => 'variable_d_r',
        'exchange variable'           => 'exchange_variable',
        'crp variable'                => 'crp_variable',
        'pickups variable'            => 'pickups_variable',
        'fleet bonus'                 => 'fleet_bonus',
        'guarantee'                   => 'guarantee',
        'guarantee deduction'         => 'guarantee_deduction',
        'ops bonus'                   => 'ops_bonus',
        'ops deductions'              => 'ops_deductions',
        'fleet deduction'             => 'fleet_deduction',
        'fake update'                 => 'fake_update',
        'total'                       => 'total',
        'short tag'                   => 'short_tag',
        'cn'                          => 'cn',
        'loans'                       => 'loans',
        'total deduction'             => 'total_deduction',
        'net salary'                  => 'net_salary',
        'amounts on pilots'           => 'amounts_on_pilots',
        'salary date'                 => 'salary_date',
    ];



    private $stop = false;

    private function cleanHeading($h)
    {
        $h = trim(mb_strtolower((string)$h));
        $h = preg_replace('/\s+/', ' ', $h);
        $h = str_replace(['_', '/', '\\', '-', '–', '—'], ' ', $h);
        return trim($h);
    }

    public function model(array $row)
    {
        if ($this->stop) return null;

        // ✅ وقف عند أول صف فاضي
        $allEmpty = true;
        foreach ($row as $value) {
            if (trim($value) !== '' && $value !== null) {
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
            if (!isset($this->map[$h])) continue;

            $col = $this->map[$h];

            // ✅ تنظيف القيم وتحويل المعادلة لرقم
            if (is_string($value)) {
                $v = trim($value);
                $v = str_replace(',', '', $v); // إزالة الفواصل
                if (is_numeric($v)) {
                    $v = $v + 0; // تحويل لرقم
                }
            } else {
                $v = $value;
            }


            // ✅ تحويل Excel serial date لتاريخ Y-m-d
            if ($col === 'salary_date' && is_numeric($v)) {
                $v = date('Y-m-d', strtotime('1899-12-30 +' . $v . ' days'));
            }

            $data[$col] = $v;
        }

        if (empty($data)) return null;

        // حفظ الشهر المحدد في salary_date (أول يوم من الشهر)
        if ($this->month) {
            // استخدام أول يوم من الشهر المحدد
            $data['salary_date'] = $this->month . '-01';
        } else {
            // إذا لم يتم تحديد شهر، استخدم الشهر الحالي
            $data['salary_date'] = date('Y-m') . '-01';
        }

        return new salary_records1($data);
    }
}
