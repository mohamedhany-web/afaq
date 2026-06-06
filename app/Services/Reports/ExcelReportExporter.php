<?php

namespace App\Services\Reports;

use App\Helpers\SettingsHelper;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExcelReportExporter
{
    public function download(array $payload, string $filename): StreamedResponse
    {
        $spreadsheet = $this->buildSpreadsheet($payload);
        $safeName = preg_replace('/[^\p{L}\p{N}_\-]+/u', '_', $filename) ?: 'report';

        return response()->streamDownload(function () use ($spreadsheet) {
            (new Xlsx($spreadsheet))->save('php://output');
        }, $safeName . '.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function buildSpreadsheet(array $payload): Spreadsheet
    {
        $theme = $this->hexToRgb(SettingsHelper::getThemeColor('#2563eb'));
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setRightToLeft(true);
        $sheet->setTitle(mb_substr($payload['sheet_title'] ?? 'تقرير', 0, 31));

        $row = 1;
        $company = SettingsHelper::getCompanyName() ?: SettingsHelper::getSystemName();
        $lastCol = max(1, count($payload['columns'] ?? []));

        $sheet->mergeCells($this->range(1, $row, $lastCol, $row));
        $sheet->setCellValue('A' . $row, $company);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $row++;

        $sheet->mergeCells($this->range(1, $row, $lastCol, $row));
        $sheet->setCellValue('A' . $row, $payload['title'] ?? 'تقرير');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $row++;

        $meta = 'تاريخ الاستخراج: ' . ($payload['generated_at'] ?? now()->format('Y-m-d H:i'));
        if (!empty($payload['period_label'])) {
            $meta .= '  |  الفترة: ' . $payload['period_label'];
        }
        $sheet->mergeCells($this->range(1, $row, $lastCol, $row));
        $sheet->setCellValue('A' . $row, $meta);
        $sheet->getStyle('A' . $row)->getFont()->setSize(10)->getColor()->setRGB('666666');
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $row += 2;

        if (!empty($payload['summary'])) {
            foreach ($payload['summary'] as $item) {
                $sheet->setCellValue('A' . $row, $item['label'] ?? '');
                $sheet->setCellValue('B' . $row, $this->formatValue($item['value'] ?? ''));
                $sheet->getStyle('A' . $row)->getFont()->setBold(true);
                $row++;
            }
            $row++;
        }

        $columns = $payload['columns'] ?? [];
        $headerRow = null;
        if ($columns) {
            $colIndex = 1;
            foreach ($columns as $col) {
                $cell = $this->cell($colIndex, $row);
                $sheet->setCellValue($cell, $col['label'] ?? $col['key']);
                $colIndex++;
            }
            $headerRange = $this->range(1, $row, count($columns), $row);
            $sheet->getStyle($headerRange)->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => sprintf('%02x%02x%02x', $theme[0], $theme[1], $theme[2])],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]],
            ]);
            $sheet->getRowDimension($row)->setRowHeight(22);
            $headerRow = $row;
            $row++;

            $dataRowIndex = 0;
            foreach ($payload['rows'] ?? [] as $dataRow) {
                $colIndex = 1;
                foreach ($columns as $col) {
                    $key = $col['key'];
                    $value = is_array($dataRow) ? ($dataRow[$key] ?? '') : '';
                    $sheet->setCellValue($this->cell($colIndex, $row), $this->formatValue($value, $col['type'] ?? null));
                    $colIndex++;
                }
                if ($dataRowIndex % 2 === 1) {
                    $sheet->getStyle($this->range(1, $row, count($columns), $row))->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F8FAFC']],
                    ]);
                }
                $row++;
                $dataRowIndex++;
            }

            $tableRange = $this->range(1, $headerRow, count($columns), $row - 1);
            $sheet->getStyle($tableRange)->getBorders()->getAllBorders()
                ->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('E2E8F0');

            for ($c = 1; $c <= count($columns); $c++) {
                $sheet->getColumnDimensionByColumn($c)->setAutoSize(true);
            }
        }

        if ($headerRow !== null) {
            $sheet->freezePane('A' . ($headerRow + 1));
        }

        return $spreadsheet;
    }

    protected function formatValue(mixed $value, ?string $type = null): mixed
    {
        if ($value === null || $value === '') {
            return '—';
        }
        if ($type === 'money' && is_numeric($value)) {
            return number_format((float) $value, 2);
        }
        if ($type === 'number' && is_numeric($value)) {
            return number_format((float) $value, 0);
        }
        if ($type === 'percent' && is_numeric($value)) {
            return number_format((float) $value, 1) . '%';
        }

        return $value;
    }

    protected function cell(int $col, int $row): string
    {
        return \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . $row;
    }

    protected function range(int $colStart, int $rowStart, int $colEnd, int $rowEnd): string
    {
        return $this->cell($colStart, $rowStart) . ':' . $this->cell($colEnd, $rowEnd);
    }

    /** @return array{0:int,1:int,2:int} */
    protected function hexToRgb(string $hex): array
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }

        return [
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2)),
        ];
    }
}
