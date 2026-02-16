<?php

namespace App\Imports;

use App\Models\DebtSheet;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DebtSheetImport implements ToCollection, WithHeadingRow, WithCalculatedFormulas
{
    use Importable;

    protected ?string $month;
    protected string $status;
    protected array $failures = [];
    protected int $imported = 0;
    protected int $skipped = 0;

    protected array $map = [
        'star id' => 'star_id',
        'star_id' => 'star_id',
        'starid' => 'star_id',
        'shortage' => 'shortage',
        'short tag' => 'shortage',
        'short_tag' => 'shortage',
        'credit note' => 'credit_note',
        'credit_note' => 'credit_note',
        'cn' => 'credit_note',
        'advances' => 'advances',
        'advance' => 'advances',
        'loans' => 'advances',
        'السلف' => 'advances',
    ];

    public function __construct(?string $month = null, string $status = 'لم يسدد')
    {
        $this->month = $month;
        $this->status = in_array($status, ['سدد', 'لم يسدد'], true) ? $status : 'لم يسدد';
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $data = $this->mapRow($row);

            if ($this->isEmptyRow($data)) {
                continue;
            }

            $starId = $data['star_id'] ?? null;
            if (!$starId) {
                $this->failures[] = [
                    'row' => $index + 2,
                    'errors' => ['star_id مطلوب'],
                ];
                $this->skipped++;
                continue;
            }

            DebtSheet::create([
                'star_id' => (string) $starId,
                'shortage' => $this->toNumber($data['shortage'] ?? 0),
                'credit_note' => $this->toNumber($data['credit_note'] ?? 0),
                'advances' => $this->toNumber($data['advances'] ?? 0),
                'status' => $this->resolveDebtStatus(
                    $this->toNumber($data['shortage'] ?? 0),
                    $this->toNumber($data['credit_note'] ?? 0),
                    $this->toNumber($data['advances'] ?? 0)
                ),
                'sheet_date' => $this->resolveSheetDate(),
            ]);

            $this->imported++;
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

    private function cleanHeading(string $heading): string
    {
        $heading = trim(mb_strtolower($heading));
        $heading = preg_replace('/\s+/', ' ', $heading);
        $heading = str_replace(['_', '/', '\\', '-'], ' ', $heading);
        return trim($heading);
    }

    private function mapRow($row): array
    {
        $data = [];
        foreach ($row as $heading => $value) {
            $key = $this->cleanHeading((string) $heading);
            if (!isset($this->map[$key])) {
                continue;
            }

            $column = $this->map[$key];
            $data[$column] = is_string($value) ? trim($value) : $value;
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

    private function toNumber($value): float
    {
        if ($value === null || $value === '') {
            return 0;
        }

        if (is_numeric($value)) {
            return (float) $value;
        }

        $normalized = str_replace([',', ' '], '', (string) $value);
        return is_numeric($normalized) ? (float) $normalized : 0;
    }

    private function resolveSheetDate(): string
    {
        if ($this->month && preg_match('/^\d{4}-\d{2}$/', $this->month)) {
            return $this->month . '-01';
        }

        return date('Y-m') . '-01';
    }

    private function resolveDebtStatus(float $shortage, float $creditNote, float $advances): string
    {
        return ($shortage + $creditNote + $advances) <= 0 ? 'سدد' : 'لم يسدد';
    }
}
