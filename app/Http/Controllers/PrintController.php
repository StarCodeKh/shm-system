<?php

namespace App\Http\Controllers;

use App\Services\ExcelDocument;
use App\Services\PdfDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

/**
 * Print and Excel buttons of the list pages: the rows the user is looking at (already filtered and
 * labelled by the list) laid out as the system's standard document, the same one the reports use.
 */
class PrintController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /** PDF, opened in the browser */
    public function table(Request $request)
    {
        $data = $this->document($request);

        return response(PdfDocument::render($data), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="'.$this->filename($data['title']).'.pdf"');
    }

    /** Excel workbook, downloaded */
    public function excel(Request $request)
    {
        $data = $this->document($request);

        return response(ExcelDocument::render($data), 200)
            ->header('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->header('Content-Disposition', 'attachment; filename="'.$this->filename($data['title']).'.xlsx"');
    }

    /** The validated document: title, columns, rows (plain text, listed columns only), filters, total */
    protected function document(Request $request): array
    {
        $locale = $request->header('X-Locale', 'kh');
        App::setLocale(in_array($locale, ['kh', 'en'], true) ? $locale : 'kh');

        $max = (int) config('report.print_max_rows');
        $input = $request->validate([
            'title' => 'required|string|max:200',
            'description' => 'nullable|string|max:300',
            'color' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'columns' => 'required|array|min:1|max:40',
            'columns.*.key' => 'required|string|max:100',
            'columns.*.label' => 'required|string|max:200',
            'columns.*.numeric' => 'boolean',
            'columns.*.date' => 'boolean',
            'rows' => 'present|array|max:'.$max,
            'rows.*' => 'array',
            'filters' => 'nullable|array|max:20',
            'filters.*' => 'nullable|string|max:300',
            'total' => 'nullable|integer|min:0',
        ]);

        $keys = array_column($input['columns'], 'key');
        $rows = array_map(function ($row) use ($keys) {
            $clean = [];
            foreach ($keys as $key) {
                $value = $row[$key] ?? null;
                $clean[$key] = is_scalar($value) ? (string) $value : null;
            }

            return $clean;
        }, $input['rows']);

        $total = $input['total'] ?? count($rows);

        return [
            'title' => $input['title'],
            'description' => $input['description'] ?? '',
            'color' => $input['color'] ?? config('report.default_color'),
            'columns' => array_map(fn ($column) => [
                'key' => $column['key'],
                'label' => $column['label'],
                'numeric' => (bool) ($column['numeric'] ?? false),
                'date' => (bool) ($column['date'] ?? false),
            ], $input['columns']),
            'rows' => $rows,
            'summary' => [trans('report.total') => $total],
            'filters' => array_filter($input['filters'] ?? [], fn ($text) => $text !== null && $text !== ''),
            'truncated' => $total > count($rows),
        ];
    }

    /** ASCII file name from the title (Khmer titles fall back to "list"), with the date */
    protected function filename(string $title): string
    {
        return (Str::slug($title) ?: 'list').'-'.date('Y-m-d');
    }
}
