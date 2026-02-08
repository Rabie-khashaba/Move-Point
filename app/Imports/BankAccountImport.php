<?php

namespace App\Imports;

use App\Models\Bank;
use App\Models\BankAccount;
use App\Models\Representative;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BankAccountImport implements ToCollection, WithHeadingRow, WithCalculatedFormulas
{
    use Importable;

    protected array $failures = [];
    protected int $imported = 0;
    protected int $skipped = 0;

    protected array $map = [
        'code' => 'code',
        'الكود' => 'code',
        'كود' => 'code',
        'account owner name' => 'account_owner_name',
        'account_owner_name' => 'account_owner_name',
        'اسم صاحب الحساب' => 'account_owner_name',
        'اسم صاحب الحساب البنكي' => 'account_owner_name',
        'bank id' => 'bank_id',
        'bank_id' => 'bank_id',
        'معرف البنك' => 'bank_id',
        'رقم البنك' => 'bank_id',
        'bank' => 'bank_name',
        'bank name' => 'bank_name',
        'bank_name' => 'bank_name',
        'اسم البنك' => 'bank_name',
        'البنك' => 'bank_name',
        'account number' => 'account_number',
        'account_number' => 'account_number',
        'رقم الحساب' => 'account_number',
        'رقم الحساب البنكي' => 'account_number',
    ];

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $data = $this->mapRow($row);

            if ($this->isEmptyRow($data)) {
                continue;
            }

            $code = $data['code'] ?? null;
            if ($code === null || $code === '') {
                $this->failures[] = [
                    'row' => $index + 2,
                    'errors' => ['الكود مطلوب'],
                ];
                $this->skipped++;
                continue;
            }

            $representative = Representative::where('code', $code)->first();
            if (!$representative) {
                $this->failures[] = [
                    'row' => $index + 2,
                    'errors' => ['الكود غير موجود'],
                ];
                $this->skipped++;
                continue;
            }

            $bankId = null;
            if (isset($data['bank_id']) && $data['bank_id'] !== null && $data['bank_id'] !== '') {
                $bankId = (int) $data['bank_id'];
                if (!Bank::where('id', $bankId)->exists()) {
                    $this->failures[] = [
                        'row' => $index + 2,
                        'errors' => ['معرف البنك غير موجود'],
                    ];
                    $this->skipped++;
                    continue;
                }
            } else {
                $bankName = $data['bank_name'] ?? null;
                if ($bankName !== null && $bankName !== '') {
                    $bank = Bank::where('name', $bankName)->first();
                    if (!$bank) {
                        $this->failures[] = [
                            'row' => $index + 2,
                            'errors' => ['اسم البنك غير موجود'],
                        ];
                        $this->skipped++;
                        continue;
                    }
                    $bankId = $bank->id;
                }
            }

            $accountOwner = $data['account_owner_name'] ?? null;
            $accountNumber = $data['account_number'] ?? null;

            $status = ($bankId && $accountOwner && $accountNumber)
                ? 'يمتلك حساب'
                : 'لا يمتلك حساب';

            BankAccount::updateOrCreate(
                ['representative_id' => $representative->id],
                [
                    'bank_id' => $bankId,
                    'status' => $status,
                    'account_owner_name' => $accountOwner,
                    'account_number' => $accountNumber,
                ]
            );

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

            if ($column === 'account_number') {
                $value = $this->normalizeAccountNumber($value);
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

    private function normalizeAccountNumber($value): ?string
    {
        if ($value === null) {
            return null;
        }

        if (is_string($value)) {
            $value = trim($value);
            if ($value === '') {
                return null;
            }
            $value = str_replace([' ', "\t", "\n", "\r", ','], '', $value);
            if (preg_match('/^[+-]?\d+(\.\d+)?e[+-]?\d+$/i', $value)) {
                return sprintf('%.0f', (float) $value);
            }
            return $value;
        }

        if (is_int($value)) {
            return (string) $value;
        }

        if (is_float($value)) {
            return sprintf('%.0f', $value);
        }

        return (string) $value;
    }
}
