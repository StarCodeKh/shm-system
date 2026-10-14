<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Workbooks in the import templates' format (GET /api/v1/import/template): one sheet named after the table,
 * row 1 = column names (bold, frozen), one record per row. Used by db:export-shm.
 */
class TemplateWorkbook
{
    /** $columns: name => ['type' => 'number' | 'date' | 'text'] (ImportController::templateColumns); text stays text ("01") */
    public static function write(string $path, string $sheetTitle, array $columns, array $rows): void
    {
        $book = new Spreadsheet;
        $sheet = $book->getActiveSheet();
        $sheet->setTitle(substr($sheetTitle, 0, 28));
        $names = array_keys($columns);
        foreach ($names as $c => $name) {
            $letter = Coordinate::stringFromColumnIndex($c + 1);
            $sheet->setCellValue("{$letter}1", $name);
            $sheet->getColumnDimension($letter)->setWidth(max(12, strlen($name) + 4));
        }
        $sheet->getStyle('A1:'.Coordinate::stringFromColumnIndex(max(1, count($names))).'1')->getFont()->setBold(true);
        $sheet->freezePane('A2');

        foreach (array_values($rows) as $r => $row) {
            foreach ($names as $c => $name) {
                $value = $row[$name] ?? null;
                if ($value === null || $value === '') {
                    continue;
                }
                $cell = Coordinate::stringFromColumnIndex($c + 1).($r + 2);
                if ($columns[$name]['type'] === 'number') {
                    $sheet->setCellValueExplicit($cell, self::number($value), DataType::TYPE_NUMERIC);
                } else {
                    $sheet->setCellValueExplicit($cell, (string) $value, DataType::TYPE_STRING);
                }
            }
        }

        if (! is_dir(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }
        (new Xlsx($book))->save($path);
        $book->disconnectWorksheets();
    }

    /** A cell number: whole numbers stay whole (1300000.0000 from a decimal column is written 1300000) */
    private static function number($value): int|float
    {
        $number = is_bool($value) ? (int) $value : $value + 0;

        return is_float($number) && floor($number) === $number && abs($number) < PHP_INT_MAX ? (int) $number : $number;
    }
}
