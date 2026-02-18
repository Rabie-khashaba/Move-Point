<?php

namespace App\Imports;

use App\Services\LeadService;
use App\Models\Lead;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class LeadImport implements ToCollection, WithHeadingRow, WithCalculatedFormulas
{
    use Importable;

    protected LeadService $service;
    protected int $moderatorId;
    protected array $failures = [];
    protected int $imported = 0;
    protected int $skipped = 0;
    protected array $duplicatePhones = [];

    protected array $map = [
        'name' => 'name',
        'phone' => 'phone',
        'governorate id' => 'governorate_id',
        'governorate_id' => 'governorate_id',
        'location id' => 'location_id',
        'location_id' => 'location_id',
        'source id' => 'source_id',
        'source_id' => 'source_id',
        'status' => 'status',
        'notes' => 'notes',
        'assigned to' => 'assigned_to',
        'assigned_to' => 'assigned_to',
        'advertiser id' => 'advertiser_id',
        'advertiser_id' => 'advertiser_id',
        'transportation' => 'transportation',
    ];

    public function __construct(LeadService $service, int $moderatorId)
    {
        $this->service = $service;
        $this->moderatorId = $moderatorId;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $data = $this->mapRow($row);

            if ($this->isEmptyRow($data)) {
                continue;
            }

            $data['moderator_id'] = $this->moderatorId;

            if (!empty($data['phone']) && Lead::where('phone', $data['phone'])->exists()) {
                $this->duplicatePhones[] = $data['phone'];
                $this->failures[] = [
                    'row' => $index + 2,
                    'errors' => ['رقم الهاتف موجود بالفعل'],
                ];
                $this->skipped++;
                continue;
            }



            $validator = Validator::make($data, [
                'name' => 'required|string|max:255',
                'phone' => 'required|digits:11|unique:leads,phone',
                'governorate_id' => 'required|exists:governorates,id',
                'location_id' => 'nullable|exists:locations,id',
                'source_id' => 'required|exists:sources,id',
                'status' => ['nullable', \Illuminate\Validation\Rule::in(['متابعة', 'لم يرد', 'غير مهتم', 'عمل مقابلة', 'مقابلة', 'مفاوضات', 'مغلق', 'خسر', 'جديد', 'قديم'])],
                'notes' => 'nullable|string',
                'assigned_to' => 'nullable|exists:users,id',
                'advertiser_id' => 'nullable|exists:advertisers,id',
                'transportation' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                $this->failures[] = [
                    'row' => $index + 2,
                    'errors' => $validator->errors()->all(),
                ];
                $this->skipped++;
                continue;
            }

            try {
                $this->service->create($data);
                $this->imported++;
            } catch (\Throwable $e) {
                $this->failures[] = [
                    'row' => $index + 2,
                    'errors' => [$e->getMessage()],
                ];
                $this->skipped++;
            }
        }
    }

    public function getFailures(): array
    {
        return $this->failures;
    }

    public function getImportedCount(): int
    {
        return $this->imported;
    }

    public function getSkippedCount(): int
    {
        return $this->skipped;
    }

    public function getDuplicatePhones(): array
    {
        return array_values(array_unique($this->duplicatePhones));
    }

    private function cleanHeading($heading): string
    {
        $heading = trim(mb_strtolower((string) $heading));
        $heading = preg_replace('/\s+/', ' ', $heading);
        $heading = str_replace(['_', '/', '\\', '-'], ' ', $heading);
        return trim($heading);
    }

    private function mapRow($row): array
    {
        $data = [];
        foreach ($row as $heading => $value) {
            $key = $this->cleanHeading($heading);
            if (!isset($this->map[$key])) {
                continue;
            }

            $column = $this->map[$key];
            $value = is_string($value) ? trim($value) : $value;
            $value = $value === '' ? null : $value;

            if ($column === 'phone') {
                $value = $this->normalizePhone($value);
            }

            $data[$column] = $value;
        }

        return $data;
    }

    private function isEmptyRow(array $data): bool
    {
        if (empty($data)) {
            return true;
        }

        foreach ($data as $value) {
            if ($value !== null && trim((string) $value) !== '') {
                return false;
            }
        }

        return true;
    }

    private function normalizePhone($value): ?string
    {
        if ($value === null) {
            return null;
        }

        $digits = preg_replace('/\D+/', '', (string) $value);
        if ($digits === '') {
            return null;
        }

        if (strlen($digits) === 10) {
            return '0' . $digits;
        }

        return $digits;
    }
}