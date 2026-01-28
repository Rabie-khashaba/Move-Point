<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SupervisorTransferLog;
use Illuminate\Support\Facades\Storage;

class Representative extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'phone',
        'address',
        'address_in_card',
        'contact',
        'national_id',
        'salary',
        'start_date',
        'company_id',
        'bank_account',
        'code',
        'attachments',
        'inquiry_checkbox',
        'inquiry_data',
        'governorate_id',
        'location_id',
        'home_location',
        'is_active',
        'status',
        'created_by',
        'is_completed',
        'completed_at',
        'missing_documents',
        'deposit_receipt',
        'employee_id',
        'resign_date',
        'unresign_date',
        'unresign_by',
        'converted_to_active_date',
        'converted_active_by',
        'converted_to_notcompleted_date',
        'documents_received',
    ];

    protected $casts = [
        'attachments' => 'array',
        'inquiry_checkbox' => 'boolean',
        'salary' => 'decimal:2',
        'start_date' => 'date',
        'is_active' => 'boolean',
        'status' => 'boolean',
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
        'converted_to_active_date' => 'date',
    ];

    public function UnResignBy()
    {
        return $this->belongsTo(User::class, 'unresign_by');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'user_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function convertedBy()
    {
        return $this->belongsTo(User::class, 'converted_active_by');
    }

    public function convertedActiveBy()
    {
        return $this->belongsTo(User::class, 'converted_active_by');
    }

    public function resignationRequest()
    {
        return $this->hasOne(ResignationRequest::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function governorate()
    {
        return $this->belongsTo(Governorate::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function training()
    {
        return $this->hasOne(RepresentativeTraining::class);
    }

    // Many-to-many relationship with supervisors
    // public function supervisors()
    // {
    //     return $this->belongsToMany(Supervisor::class, 'supervisor_representative')
    //         ->withTimestamps();
    // }


    public function supervisors()
    {
        return $this->belongsToMany(
            User::class,
            'supervisor_representative',
            'representative_id', // pivot table column for this model
            'user_id'            // pivot table column for supervisor.user_id
        )->withTimestamps();
    }

    // Transfer logs
    public function transferLogs()
    {
        return $this->hasMany(SupervisorTransferLog::class);
    }

    // Get current supervisor (most recent assignment)
    public function getCurrentSupervisorAttribute()
    {
        return $this->supervisors()->latest('supervisor_representative.created_at')->first();
    }

    public function advanceRequests()
    {
        return $this->hasMany(AdvanceRequest::class);
    }

    public function debits()
    {
        return $this->hasMany(Debt::class);
    }

    public function deliveryDeposits()
    {
        return $this->hasMany(DeliveryDeposit::class);
    }

    public function salaryRecords()
    {
        return $this->hasMany(SalaryRecord::class);
    }
    public function notes()
    {
        return $this->hasMany(RepresentativeNote::class)->latest();
    }

    public function bankAccounts()
    {
        return $this->hasMany(BankAccount::class);
    }


    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Boot method to automatically process bonuses when representative is created
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($representative) {
            // Process all targets for current month to give bonuses to qualified employees
            RepresentativeTarget::processAllTargets();
        });
    }

    public static function requiredDocs(): array
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
    }





}