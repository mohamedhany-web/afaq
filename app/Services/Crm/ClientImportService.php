<?php

namespace App\Services\Crm;

use App\Models\Client;
use App\Models\User;
use App\Services\CrmScopeService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ClientImportService
{
    public const HEADER_ALIASES = [
        'name' => ['الاسم', 'الاسم الكامل', 'اسم', 'name', 'full_name', 'fullname', 'customer', 'client'],
        'phone' => ['الهاتف', 'رقم الهاتف', 'رقم الجوال', 'جوال', 'phone', 'mobile', 'tel', 'telephone', 'رقم التليفون', 'التليفون'],
        'email' => ['البريد', 'البريد الإلكتروني', 'بريد', 'email', 'e-mail', 'mail'],
        'company' => ['الشركة', 'اسم الشركة', 'company', 'company_name', 'organization'],
        'address' => ['العنوان', 'address', 'location', 'المدينة'],
        'notes' => ['ملاحظات', 'notes', 'note', 'تعليقات', 'comments'],
    ];

    public function downloadTemplate(): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setRightToLeft(true);
        $sheet->setTitle('عملاء');

        $headers = ['الاسم', 'الهاتف', 'البريد الإلكتروني', 'الشركة', 'العنوان', 'ملاحظات'];
        foreach ($headers as $i => $header) {
            $col = chr(65 + $i);
            $sheet->setCellValue($col . '1', $header);
            $sheet->getColumnDimension($col)->setWidth(22);
        }

        $headerRange = 'A1:F1';
        $sheet->getStyle($headerRange)->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFF'));
        $sheet->getStyle($headerRange)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('2563EB');
        $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $examples = [
            ['محمد أحمد', '01012345678', 'mohamed@example.com', 'شركة النخبة', 'القاهرة', 'مهتم بشقة 120م'],
            ['', '01098765432', '', '', 'الجيزة', 'بدون اسم — مقبول'],
        ];

        $row = 2;
        foreach ($examples as $example) {
            foreach ($example as $i => $value) {
                $sheet->setCellValue(chr(65 + $i) . $row, $value);
            }
            $row++;
        }

        $sheet->setCellValue('A' . ($row + 1), 'ملاحظة: الهاتف مطلوب. الاسم اختياري — يُولَّد تلقائياً إن تُرك فارغاً.');
        $sheet->mergeCells('A' . ($row + 1) . ':F' . ($row + 1));
        $sheet->getStyle('A' . ($row + 1))->getFont()->setItalic(true)->setSize(10);

        return response()->streamDownload(function () use ($spreadsheet) {
            (new Xlsx($spreadsheet))->save('php://output');
        }, 'crm-leads-template.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function import(UploadedFile $file, User $user, string $duplicateMode = 'skip'): array
    {
        $rows = $this->parseFile($file);
        if ($rows === []) {
            return [
                'imported' => 0,
                'skipped' => 0,
                'updated' => 0,
                'failed' => 0,
                'errors' => [['row' => 0, 'message' => 'الملف فارغ أو لا يحتوي على بيانات صالحة.']],
            ];
        }

        $scope = CrmScopeService::for($user);
        $timeline = app(ClientTimelineService::class);
        $seenPhones = [];

        $result = [
            'imported' => 0,
            'skipped' => 0,
            'updated' => 0,
            'failed' => 0,
            'errors' => [],
        ];

        foreach ($rows as $item) {
            $rowNum = $item['row'];
            $data = $item['data'];

            $phone = trim((string) ($data['phone'] ?? ''));
            if ($phone === '') {
                $result['failed']++;
                $result['errors'][] = ['row' => $rowNum, 'message' => 'رقم الهاتف مطلوب'];
                continue;
            }

            $normalized = self::normalizePhone($phone);
            if ($normalized && isset($seenPhones[$normalized])) {
                $result['skipped']++;
                continue;
            }
            if ($normalized) {
                $seenPhones[$normalized] = true;
            }

            $name = trim((string) ($data['name'] ?? ''));
            if ($name === '') {
                $name = self::fallbackName($phone);
            }

            $email = trim((string) ($data['email'] ?? '')) ?: null;
            if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $result['failed']++;
                $result['errors'][] = ['row' => $rowNum, 'message' => 'بريد إلكتروني غير صالح'];
                continue;
            }

            $existing = $this->findByPhone($scope, $phone, $normalized);

            if ($existing) {
                if ($duplicateMode === 'skip') {
                    $result['skipped']++;
                    continue;
                }

                try {
                    $this->updateExisting($existing, $name, $phone, $email, $data);
                    $result['updated']++;
                } catch (\Throwable $e) {
                    $result['failed']++;
                    $result['errors'][] = ['row' => $rowNum, 'message' => $e->getMessage()];
                }
                continue;
            }

            if ($email && Client::where('email', $email)->exists()) {
                $email = null;
            }

            try {
                $client = Client::create([
                    'name' => $name,
                    'phone' => $phone,
                    'email' => $email,
                    'company_name' => trim((string) ($data['company'] ?? '')) ?: null,
                    'address' => trim((string) ($data['address'] ?? '')) ?: null,
                    'notes' => trim((string) ($data['notes'] ?? '')) ?: null,
                    'status' => 'prospect',
                    'lead_stage' => 'lead',
                    'client_type' => 'individual',
                    'assigned_to' => $user->employee?->id,
                    'created_by' => $user->id,
                ]);

                $timeline->recordLeadCreated($client, $user);
                $result['imported']++;
            } catch (\Throwable $e) {
                $result['failed']++;
                $result['errors'][] = ['row' => $rowNum, 'message' => $e->getMessage()];
            }
        }

        return $result;
    }

    public static function normalizePhone(?string $phone): ?string
    {
        if (!$phone) {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $phone);
        if ($digits === '') {
            return null;
        }

        if (str_starts_with($digits, '20') && strlen($digits) >= 11) {
            $digits = '0' . substr($digits, 2);
        }

        return $digits;
    }

    public static function fallbackName(string $phone): string
    {
        $digits = self::normalizePhone($phone) ?? preg_replace('/\D+/', '', $phone);
        $suffix = $digits ? substr($digits, -4) : Str::random(4);

        return 'عميل ' . $suffix;
    }

    protected function parseFile(UploadedFile $file): array
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $rows = [];

        if (in_array($extension, ['csv', 'txt'], true)) {
            $handle = fopen($file->getRealPath(), 'r');
            if (!$handle) {
                return [];
            }

            $lineNum = 0;
            $headerMap = null;

            while (($line = fgetcsv($handle)) !== false) {
                $lineNum++;
                if ($this->isEmptyRow($line)) {
                    continue;
                }

                if ($headerMap === null) {
                    $headerMap = $this->mapHeaders($line);
                    if ($headerMap === []) {
                        $headerMap = $this->defaultHeaderMap();
                    }
                    if (!isset($headerMap['phone'])) {
                        return [];
                    }
                    if ($this->rowLooksLikeDataNotHeader($line, $headerMap)) {
                        $rows[] = ['row' => $lineNum, 'data' => $this->extractRow($line, $headerMap)];
                    }
                    continue;
                }

                $rows[] = ['row' => $lineNum, 'data' => $this->extractRow($line, $headerMap)];
            }

            fclose($handle);

            return $rows;
        }

        $spreadsheet = IOFactory::load($file->getRealPath());
        $sheet = $spreadsheet->getActiveSheet();
        $matrix = $sheet->toArray(null, true, true, false);

        $headerMap = null;
        foreach ($matrix as $index => $line) {
            $lineNum = $index + 1;
            if ($this->isEmptyRow($line)) {
                continue;
            }

            if ($headerMap === null) {
                $headerMap = $this->mapHeaders($line);
                if ($headerMap === []) {
                    $headerMap = $this->defaultHeaderMap();
                }
                if (!isset($headerMap['phone'])) {
                    return [];
                }
                if ($this->rowLooksLikeDataNotHeader($line, $headerMap)) {
                    $rows[] = ['row' => $lineNum, 'data' => $this->extractRow($line, $headerMap)];
                }
                continue;
            }

            $rows[] = ['row' => $lineNum, 'data' => $this->extractRow($line, $headerMap)];
        }

        return $rows;
    }

    protected function mapHeaders(array $row): array
    {
        $map = [];
        foreach ($row as $index => $cell) {
            $key = $this->matchColumnKey((string) $cell);
            if ($key) {
                $map[$key] = $index;
            }
        }

        return $map;
    }

    protected function defaultHeaderMap(): array
    {
        return ['name' => 0, 'phone' => 1, 'email' => 2, 'company' => 3, 'address' => 4, 'notes' => 5];
    }

    protected function matchColumnKey(string $header): ?string
    {
        $normalized = mb_strtolower(trim($header));
        if ($normalized === '') {
            return null;
        }

        foreach (self::HEADER_ALIASES as $field => $aliases) {
            foreach ($aliases as $alias) {
                if ($normalized === mb_strtolower($alias)) {
                    return $field;
                }
            }
        }

        return null;
    }

    protected function rowLooksLikeDataNotHeader(array $row, array $headerMap): bool
    {
        $phoneIndex = $headerMap['phone'] ?? null;
        if ($phoneIndex === null) {
            return false;
        }

        $phone = trim((string) ($row[$phoneIndex] ?? ''));
        return $phone !== '' && preg_match('/\d/', $phone);
    }

    protected function extractRow(array $row, array $headerMap): array
    {
        $data = [];
        foreach ($headerMap as $field => $index) {
            $data[$field] = isset($row[$index]) ? trim((string) $row[$index]) : '';
        }

        return $data;
    }

    protected function isEmptyRow(array $row): bool
    {
        foreach ($row as $cell) {
            if (trim((string) $cell) !== '') {
                return false;
            }
        }

        return true;
    }

    protected function findByPhone(CrmScopeService $scope, string $phone, ?string $normalized): ?Client
    {
        $query = $scope->clientsQuery()->where(function ($q) use ($phone, $normalized) {
            $q->where('phone', $phone);
            if ($normalized) {
                $q->orWhere('phone', 'like', '%' . substr($normalized, -10));
            }
        });

        return $query->first();
    }

    protected function updateExisting(Client $client, string $name, string $phone, ?string $email, array $data): void
    {
        $updates = [
            'phone' => $phone,
        ];

        if ($name && (str_starts_with($client->name, 'عميل ') || trim($client->name) === '')) {
            $updates['name'] = $name;
        }

        if ($email && !$client->email) {
            if (!Client::where('email', $email)->where('id', '!=', $client->id)->exists()) {
                $updates['email'] = $email;
            }
        }

        foreach (['company' => 'company_name', 'address' => 'address', 'notes' => 'notes'] as $src => $dest) {
            $val = trim((string) ($data[$src] ?? ''));
            if ($val && empty($client->{$dest})) {
                $updates[$dest] = $val;
            }
        }

        $client->update($updates);
    }
}
