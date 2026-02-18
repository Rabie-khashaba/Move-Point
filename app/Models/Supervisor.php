<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supervisor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',                    // الاسم ( نص)
        'phone',                   // رقم التليفون ( رقم مقيد بعدد 11 رقم )
        'contact',                 // التواصل ( رقم )
        'governorate_id',          // المحافظة
        'location_id',             // المقر ( المقر المسؤول عنه المشرف )
        'location_name',
        'national_id',             // رقم البطاقة (رقم مقيد ب 14 رقم)
        'salary',                  // المرتب ( رقم)
        'start_date',              // تاريخ بداية العمل (تاريخ)
        'is_active',


        /* 'address_in_card',
        'address',
        'employee_id', */
        'company_id',
        /* 'bank_account', */
        'code',
       /*  'attachments', */


    ];

    protected $casts = [
        'attachments' => 'array',
        'salary' => 'decimal:2',
        'start_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function governorate()
    {
        return $this->belongsTo(Governorate::class, 'governorate_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    // Many-to-many relationship with representatives
    // public function representatives()
    // {
    //     return $this->belongsToMany(Representative::class, 'supervisor_representative')
    //                 ->withTimestamps();
    // }


    public function representatives()
    {
         return $this->belongsToMany(Representative::class,
        'supervisor_representative',
        'user_id',           // foreign key على جدول supervisors
        'representative_id'  // foreign key على جدول representatives
        )->withTimestamps();
    }
    // Transfer logs
    public function transferLogsAsOld()
    {
        return $this->hasMany(SupervisorTransferLog::class, 'old_supervisor_id');
    }

    public function transferLogsAsNew()
    {
        return $this->hasMany(SupervisorTransferLog::class, 'new_supervisor_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function debits(){
        return $this->hasMany(Debt::class);
    }




    /* public static function requiredDocs(): array
    {
        return [
            'البطاقة (وجه أول)',
            'البطاقة (خلف)',
            'فيش',
            'شهادة ميلاد',
            'إيصال الأمانة',
            'رخصة القيادة',
            'رخصة السيارة وجه أول',
            'رخصة السيارة وجه ثاني',
            'إيصال مرافق (غاز أو مياه أو كهرباء)',
            'مرفق بيانات الاستعلام',
        ];
    }


    // رجّع الأوراق اللي مرفوعة
    public function uploadedDocs(): array
    {
        $attachments = $this->attachments ?? [];
        return collect($attachments)->pluck('type')->toArray();
    }

    public function missingDocs(): array
    {
        return array_diff(self::requiredDocs(), $this->uploadedDocs());
    }


    public function getAttachmentsWithUrlsAttribute()
    {
        $attachments = $this->attachments ?? [];

        return collect($attachments)->map(function ($attachment) {
            return [
                'type' => $attachment['type'] ?? 'مرفق غير معروف',
                'path' => $attachment['path'] ?? null,
                'url' => isset($attachment['path'])
                    ? Storage::disk('public')->url($attachment['path'])
                    : null,
            ];
        })->toArray();
    } */

}
