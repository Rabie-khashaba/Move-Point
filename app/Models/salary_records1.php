<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class salary_records1 extends Model
{

    use HasFactory;
    protected $table = 'salary_records1s';


    protected $fillable = [
        'state',
        'star_id',
        'name',
        'vehicle_type',
        'contractor',
        'hub',
        'zone',
        'working_days',
        'delivered_cash',
        'rto',
        'exchange',
        'crp',
        'pickups_stops',
        'fixed_day',
        'variable_pkg',
        'total_delivered',
        'guarantee_day',
        'monthly_guarantee_volume',
        'guarantee_volume',
        'fixed_salary',
        'variable_d_r',
        'exchange_variable',
        'crp_variable',
        'pickups_variable',
        'fleet_bonus',
        'guarantee',
        'guarantee_deduction',
        'ops_bonus',
        'ops_deductions',
        'fleet_deduction',
        'fake_update',
        'total',
        'short_tag',
        'cn',
        'loans',
        'total_deduction',
        'net_salary',
        'amounts_on_pilots',
        'salary_date',

    ];

    protected $casts = [
        'salary_date' => 'date:Y-m-d',
    ];
}
