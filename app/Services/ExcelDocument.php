<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * The system's standard Excel workbook, the spreadsheet twin of PdfDocument: letterhead, title,
 * info / filter / summary lines, a coloured header row, striped data, SUM totals, signatures,
 * a frozen header with filters, and an A4 print setup. Used by the reports and by every list page.
 */
class ExcelDocument
{
    /**
     * .xlsx bytes for one table document. Takes the same $data as PdfDocument::render():
     * title, columns [{ key, label, numeric, date }], rows; optional description, color, summary, filters, truncated.
     */
    public static function render(array $data): string
    {
        $user = auth()->user();
        $data += ['description' => '', 'color' => config('report.default_color'), 'summary' => [], 'filters' => [], 'truncated' => false];
        $organization = PdfDocument::organization();
        $columns = array_values($data['columns']);
        $color = ltrim($data['color'], '#');
        $moneyPattern = config('report.columns.money');
        $type = PdfDocument::typography(count($columns), config('report.typography.excel_scale'));

        $book = new Spreadsheet;
        $book->getProperties()->setCreator($organization['system'] ?: config('app.name'))->setTitle($data['title']);
        $book->getDefaultStyle()->getFont()->setName(config('report.excel_font'))->setSize($type['td']);
        $sheet = $book->getActiveSheet();
        $sheet->setTitle(self::sheetTitle($data['title']));
        $sheet->setShowGridlines(false);

        $width = count($columns) + 1; // + the "No." column
        $last = Coordinate::stringFromColumnIndex($width);
        $row = 1;
        // One merged line across the table: text, font size, bold, colour, alignment
        $line = function (string $text, float $size = 10, bool $bold = false, string $rgb = '2C2E45', string $align = Alignment::HORIZONTAL_CENTER, ?string $fill = null) use ($sheet, $last, &$row) {
            $range = "A{$row}:{$last}{$row}";
            $sheet->mergeCells($range);
            $sheet->setCellValue("A{$row}", $text);
            $style = $sheet->getStyle($range);
            $style->getFont()->setSize($size)->setBold($bold)->getColor()->setRGB($rgb);
            $style->getAlignment()->setHorizontal($align)->setVertical(Alignment::VERTICAL_CENTER)->setIndent($align === Alignment::HORIZONTAL_LEFT ? 1 : 0);
            if ($fill) {
                $style->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($fill);
            }
            $sheet->getRowDimension($row)->setRowHeight(max(18, $size * 1.9));
            $row++;
        };

        // Letterhead
        if (config('report.national_header')) {
            $line(trans('report.kingdom'), $type['kingdom'], true, '1F2140');
            $line(trans('report.motto'), $type['motto'], true, '1F2140');
        }
        $line($organization['name'], $type['org'], true, '1F2140', Alignment::HORIZONTAL_LEFT);
        if ($organization['system']) {
            $line($organization['system'], $type['system'], false, '8A8CA5', Alignment::HORIZONTAL_LEFT);
        }
        $row++;

        // Title
        $line($data['title'], $type['title'], true, strtoupper($color));
        if ($data['description']) {
            $line($data['description'], $type['subtitle'], false, '8A8CA5');
        }
        $row++;

        // Info, filters, summary
        $info = trans('report.printed_at').':  '.date('d-m-Y H:i')
            .($user ? '      ·      '.trans('report.printed_by').':  '.($user->username ?? $user->email ?? '') : '')
            .'      ·      '.trans('report.records').':  '.number_format(count($data['rows']));
        $line($info, $type['info_value'], false, '4B4D68', Alignment::HORIZONTAL_LEFT, 'F6F7FB');
        $filters = collect($data['filters'])->map(fn ($text, $label) => $label.':  '.$text)->implode('      |      ');
        $line(trans('report.filters').':  '.($filters ?: trans('report.no_filter')), $type['info_value'], false, '4B4D68', Alignment::HORIZONTAL_LEFT, 'F6F7FB');
        if (count($data['summary'])) {
            $summary = collect($data['summary'])->map(fn ($value, $label) => $label.':  '.(is_numeric($value) ? number_format($value) : $value))->implode('      ·      ');
            $line($summary, $type['info_value'], true, '1F2140', Alignment::HORIZONTAL_LEFT, self::tint($color, 0.92));
        }
        $row++;

        // Header row
        $headerRow = $row;
        $sheet->setCellValue("A{$row}", trans('common.No'));
        foreach ($columns as $i => $column) {
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($i + 2).$row, $column['label']);
        }
        $header = $sheet->getStyle("A{$row}:{$last}{$row}");
        $header->getFont()->setBold(true)->setSize($type['th'])->getColor()->setRGB('FFFFFF');
        $header->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB(strtoupper($color));
        $header->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setWrapText(true);
        $sheet->getRowDimension($row)->setRowHeight(round($type['th'] * 2.6, 1));
        $row++;

        // Data rows
        $firstData = $row;
        $widths = array_fill(0, $width, 0);
        $widths[0] = 6;
        $decimals = []; // numeric columns holding fractions get 2 decimals, the others none
        foreach (array_values(collect($data['rows'])->all()) as $n => $item) {
            $sheet->setCellValue("A{$row}", $n + 1);
            foreach ($columns as $i => $column) {
                $value = self::display(data_get($item, $column['key']));
                $cell = Coordinate::stringFromColumnIndex($i + 2).$row;
                if ($value === null || $value === '') {
                    continue;
                }
                if ($column['numeric'] && is_numeric($number = str_replace(',', '', (string) $value))) {
                    $sheet->setCellValue($cell, (float) $number);
                    if (floor((float) $number) != (float) $number) {
                        $decimals[$i] = true;
                    }
                    $value = number_format((float) $number, 2);
                } else {
                    $value = $column['date'] ? substr((string) $value, 0, 10) : (string) $value;
                    $sheet->setCellValueExplicit($cell, $value, DataType::TYPE_STRING);
                }
                $widths[$i + 1] = max($widths[$i + 1], mb_strwidth($value));
            }
            if ($n % 2 === 1) {
                $sheet->getStyle("A{$row}:{$last}{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB(self::tint($color, 0.95));
            }
            $row++;
        }
        $lastData = $row - 1;

        if ($lastData < $firstData) {
            // Empty state
            $sheet->mergeCells("A{$row}:{$last}{$row}");
            $sheet->setCellValue("A{$row}", trans('report.no_data').'  —  '.trans('report.no_data_hint'));
            $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle("A{$row}")->getFont()->setSize($type['empty_hint'])->getColor()->setRGB('8A8CA5');
            $sheet->getStyle("A{$row}:{$last}{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB(self::tint($color, 0.95));
            $sheet->getRowDimension($row)->setRowHeight(36);
            $lastData = $row++;
        } else {
            // Column formats; money columns get a SUM row (summing rates or codes means nothing)
            $sums = [];
            $formats = [];
            foreach ($columns as $i => $column) {
                $letter = Coordinate::stringFromColumnIndex($i + 2);
                $range = "{$letter}{$firstData}:{$letter}{$lastData}";
                if ($column['numeric']) {
                    $formats[$letter] = empty($decimals[$i]) ? '#,##0' : '#,##0.00';
                    $sheet->getStyle($range)->getNumberFormat()->setFormatCode($formats[$letter]);
                    $sheet->getStyle($range)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle("{$letter}{$headerRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    if (preg_match($moneyPattern, $column['key'])) {
                        $sums[$letter] = "=SUM({$range})";
                    }
                }
                if ($column['date']) {
                    $sheet->getStyle($range)->getFont()->getColor()->setRGB('6B6D7A');
                }
            }
            $sheet->getStyle("A{$firstData}:A{$lastData}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("A{$firstData}:A{$lastData}")->getFont()->getColor()->setRGB('8A8CA5');
            $sheet->getStyle("A{$headerRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("A{$firstData}:{$last}{$lastData}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

            if ($sums) {
                $sheet->setCellValue("B{$row}", trans('report.total_row'));
                foreach ($sums as $letter => $formula) {
                    $sheet->setCellValue("{$letter}{$row}", $formula);
                    $sheet->getStyle("{$letter}{$row}")->getNumberFormat()->setFormatCode($formats[$letter]);
                    $sheet->getStyle("{$letter}{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                }
                $total = $sheet->getStyle("A{$row}:{$last}{$row}");
                $total->getFont()->setBold(true)->getColor()->setRGB('1F2140');
                $total->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB(self::tint($color, 0.86));
                $sheet->getRowDimension($row)->setRowHeight(22);
                $row++;
            }
            $sheet->setAutoFilter("A{$headerRow}:{$last}{$lastData}");
        }

        // Thin light grid around the table
        $sheet->getStyle("A{$headerRow}:{$last}".($row - 1))->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('E3E5EC');

        if ($data['truncated']) {
            $row++;
            $line(trans('report.truncated', ['count' => number_format(count($data['rows']))]), $type['note'], false, '9A5B0A', Alignment::HORIZONTAL_LEFT, 'FFF6E6');
        }

        // Signatures: the roles spread over the table width, place and date above the last one
        $roles = collect(config('report.signature.roles', []))->map(fn ($role) => trans('report.signatures.'.$role))->values();
        if ($roles->count()) {
            $row += 2;
            $span = max(1, intdiv($width, $roles->count()));
            foreach ($roles as $i => $label) {
                $from = $i * $span + 1;
                $to = $i === $roles->count() - 1 ? $width : min($width, $from + $span - 1);
                $a = Coordinate::stringFromColumnIndex($from);
                $b = Coordinate::stringFromColumnIndex($to);
                if ($i === $roles->count() - 1) {
                    $sheet->mergeCells("{$a}{$row}:{$b}{$row}");
                    $sheet->setCellValue("{$a}{$row}", PdfDocument::dateLine());
                    $sheet->getStyle("{$a}{$row}")->getFont()->setSize($type['date_line'])->getColor()->setRGB('6B6D7A');
                    $sheet->getStyle("{$a}{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }
                $sheet->mergeCells("{$a}".($row + 1).":{$b}".($row + 1));
                $sheet->setCellValue("{$a}".($row + 1), $label);
                $sheet->getStyle("{$a}".($row + 1))->getFont()->setBold(true)->setSize($type['signature'])->getColor()->setRGB('1F2140');
                $sheet->getStyle("{$a}".($row + 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }
        }

        // Column widths from the content (Khmer glyphs are wide, hence the factor), within limits
        foreach ($columns as $i => $column) {
            $widths[$i + 1] = max($widths[$i + 1], mb_strwidth($column['label']));
        }
        foreach ($widths as $i => $chars) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($i + 1))->setWidth($i === 0 ? 7 : min(55, max(11, $chars * 1.25 + 3)));
        }

        // Freeze the header, print on A4 fitted to the page width with the header repeated
        $sheet->freezePane('A'.($headerRow + 1));
        $setup = $sheet->getPageSetup();
        $setup->setPaperSize(PageSetup::PAPERSIZE_A4)
            ->setOrientation(count($columns) <= config('report.portrait_max_columns') ? PageSetup::ORIENTATION_PORTRAIT : PageSetup::ORIENTATION_LANDSCAPE)
            ->setFitToWidth(1)->setFitToHeight(0)
            ->setRowsToRepeatAtTopByStartAndEnd($headerRow, $headerRow);
        $sheet->getPageMargins()->setTop(0.5)->setBottom(0.6)->setLeft(0.4)->setRight(0.4);
        $sheet->getHeaderFooter()->setOddFooter('&L&'.round($type['footer']).self::footerText($organization['footer'] ?: $organization['name']).'&R&'.round($type['footer']).self::footerText($data['title']).'   '.trans('report.page').' &P / &N');
        $sheet->setSelectedCell('A'.($headerRow + 1));

        ob_start();
        (new Xlsx($book))->save('php://output');
        $book->disconnectWorksheets();

        return ob_get_clean();
    }

    /** Arrays (relations, { label, value } options) shown by their name, like the PDF */
    protected static function display($value)
    {
        if (is_array($value) || is_object($value)) {
            $value = data_get($value, 'title_kh') ?? data_get($value, 'name_kh') ?? data_get($value, 'label') ?? '';
        }

        return $value;
    }

    /** Hex colour mixed with white (0 = colour, 1 = white), as RRGGBB */
    protected static function tint(string $hex, float $amount): string
    {
        if (strlen($hex) !== 6) {
            return 'F6F7FB';
        }
        $mix = fn ($i) => (int) round(hexdec(substr($hex, $i, 2)) + (255 - hexdec(substr($hex, $i, 2))) * $amount);

        return strtoupper(sprintf('%02x%02x%02x', $mix(0), $mix(2), $mix(4)));
    }

    /** Sheet names: max 31 characters, none of : \ / ? * [ ] */
    protected static function sheetTitle(string $title): string
    {
        $title = trim(preg_replace('/[:\\\\\/?*\[\]]/u', ' ', $title));

        return mb_substr($title !== '' ? $title : 'Sheet', 0, 31);
    }

    /** Header/footer codes start with &: escape literal ampersands */
    protected static function footerText(string $text): string
    {
        return str_replace('&', '&&', $text);
    }
}
