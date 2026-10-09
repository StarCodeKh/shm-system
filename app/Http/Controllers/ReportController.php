<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Officer;
use App\Models\OfficerPayable;
use App\Models\OfficerPayment;
use App\Models\OfficerRequest;
use App\Models\OfficerIncentive;
use App\Models\Income;
use App\Models\Expense;
use App\Services\ExcelDocument;
use App\Services\PdfDocument;
use CommonService;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Printable reports: POST /api/v1/report/{type} (PDF) and /{type}/data (paged JSON).
     * To add a report, add an entry here plus its texts in resources/lang/{kh,en}/report.php
     * (report.types.{key}.title / description / summary.*); the list, viewer and PDF pick it up.
     */
    protected function reports(): array
    {
        return [
            'officers' => [
                'permission' => 'list_officer', // also needs config('access.reports')
                'icon' => 'UsersIcon',
                'color' => '#7367F0',
                'field_api' => '/api/v1/officer/payable/get_field',
                'fields' => fn () => Officer::dataFields(),
                'rows' => fn ($filter) => Officer::getData($filter),
                'total' => fn ($filter) => Officer::getTotal($filter),
                // Keys are labelled by report.types.officers.summary.*
                'summary' => fn ($filter) => [
                    'total' => Officer::getTotal($filter),
                    'male' => Officer::getTotalOfficerByGender($filter, 'male'),
                    'female' => Officer::getTotalOfficerByGender($filter, 'female'),
                ],
            ],
            'income' => [
                'permission' => 'list_income', // also needs config('access.reports')
                'icon' => 'TrendingUpIcon',
                'color' => '#28C76F',
                'field_api' => '/api/v1/income/get_field',
                'fields' => fn () => Income::dataFields(),
                'rows' => fn ($filter) => Income::getData($filter),
                'total' => fn ($filter) => Income::getTotal($filter),
            ],
            'expense' => [
                'permission' => 'list_expense', // also needs config('access.reports')
                'icon' => 'TrendingDownIcon',
                'color' => '#EA5455',
                'field_api' => '/api/v1/expense/get_field',
                'fields' => fn () => Expense::dataFields(),
                'rows' => fn ($filter) => Expense::getData($filter),
                'total' => fn ($filter) => Expense::getTotal($filter),
            ],
            'officer_incentive' => [
                'permission' => 'list_officer_incentive', // also needs config('access.reports')
                'icon' => 'GiftIcon',
                'color' => '#FF9F43',
                'field_api' => '/api/v1/officer/incentive/get_field',
                'fields' => fn () => OfficerIncentive::dataFields(),
                'rows' => fn ($filter) => OfficerIncentive::getOfficerReports($filter),
                'total' => fn ($filter) => OfficerIncentive::countOfficerReports($filter),
            ],
            'officers_payment' => [
                'permission' => 'list_officer_payment', // also needs config('access.reports')
                'icon' => 'CheckCircleIcon',
                'color' => '#00CFE8',
                'field_api' => '/api/v1/officer/payment/get_field',
                'fields' => fn () => OfficerPayment::dataFields(),
                'rows' => fn ($filter) => OfficerPayment::getOfficerPaymentReports($filter),
                'total' => fn ($filter) => OfficerPayment::countOfficerPaymentReports($filter),
            ],
            'officers_payable' => [
                'permission' => 'list_officer_payable', // also needs config('access.reports')
                'icon' => 'ClockIcon',
                'color' => '#F35588',
                'field_api' => '/api/v1/officer/payable/get_field',
                'fields' => fn () => OfficerPayable::dataFields(),
                'rows' => fn ($filter) => OfficerPayable::getOfficerPayables($filter),
                'total' => fn ($filter) => OfficerPayable::countOfficerPayables($filter),
            ],
            'officers_request' => [
                'permission' => 'list_officer_request', // also needs config('access.reports')
                'icon' => 'FileTextIcon',
                'color' => '#4A5F9D',
                'field_api' => '/api/v1/officer/request/get_field',
                'fields' => fn () => OfficerRequest::dataFields(),
                'rows' => fn ($filter) => OfficerRequest::getOfficerRequestReports($filter),
                'total' => fn ($filter) => OfficerRequest::countOfficerRequestReports($filter),
            ],
        ];
    }

    /**
     * List the available reports for the report menu page.
     */
    public function types(Request $request)
    {
        $this->useRequestLocale($request);
        if (!CommonService::can('reports')) {
            return response()->json([]);
        }
        $reports = collect($this->reports())->filter(fn ($report) => $this->allowed($report))->map(fn ($report, $key) => [
            'key' => $key,
            'title' => $this->text($key, 'title'),
            'description' => $this->text($key, 'description'),
            'icon' => $report['icon'],
            'color' => $report['color'],
            'field_api' => $report['field_api'],
        ])->values();

        return response()->json($reports);
    }

    /**
     * One page of a report as JSON for the on-screen viewer (fast; the PDF is only built on download/print).
     * Body: { data: search fields, page, limit }. Summary is computed on the first page only.
     */
    public function preview(Request $request, string $type)
    {
        $this->useRequestLocale($request);
        $report = $this->reports()[$type] ?? abort(404);
        if (!$this->allowed($report)) {
            return response()->json(['message' => trans('common.msg_no_permission_access')], 403);
        }
        $sizes = config('report.page_sizes');
        $page = max(1, (int) $request->input('page', 1));
        $limit = (int) $request->input('limit', config('report.default_page_size'));
        $limit = in_array($limit, $sizes, true) ? $limit : config('report.default_page_size');
        $filter = [
            'sort' => 'id',
            'order' => 'asc',
            'page_number' => $page,
            'limit' => $limit,
            'search_field' => $request->input('data') ?: null,
        ];

        $fields = $report['fields']();
        $total = isset($report['total']) ? (int) $report['total']($filter) : null;

        $summary = $page === 1 ? $this->summaryTiles($type, $report, $filter, $total) : null;

        $rows = $report['rows']($filter)->values();

        return response()->json([
            'columns' => $this->columns($fields, $rows),
            'rows' => $rows,
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'summary' => $summary,
        ]);
    }

    /**
     * Render one report as a PDF. Pass download=1 to get it as an attachment.
     */
    public function generate(Request $request, string $type)
    {
        $this->useRequestLocale($request);
        $report = $this->reports()[$type] ?? abort(404);
        if (!$this->allowed($report)) {
            return response()->json(['message' => trans('common.msg_no_permission_access')], 403);
        }
        $data = $this->document($request, $type, $report);

        // Building the PDF is the slow part (Khmer text shaping in mPDF), so keep each result for a few minutes.
        // The key is the report's content and who prints it, so any change builds a fresh PDF.
        $key = 'report_pdf:'.md5(json_encode([$type, App::getLocale(), auth()->id(), $data]));
        $pdf = Cache::remember($key, now()->addMinutes(config('report.pdf_cache_minutes')), fn () => PdfDocument::render($data));

        $filename = $type.'-report-'.date('Y-m-d').'.pdf';
        $disposition = $request->boolean('download') ? 'attachment' : 'inline';

        return response($pdf, 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', $disposition.'; filename="'.$filename.'"');
    }

    /**
     * Download one report as an Excel workbook (same content and layout as the PDF).
     */
    public function excel(Request $request, string $type)
    {
        $this->useRequestLocale($request);
        $report = $this->reports()[$type] ?? abort(404);
        if (!$this->allowed($report)) {
            return response()->json(['message' => trans('common.msg_no_permission_access')], 403);
        }
        $xlsx = ExcelDocument::render($this->document($request, $type, $report));

        return response($xlsx, 200)
            ->header('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->header('Content-Disposition', 'attachment; filename="'.$type.'-report-'.date('Y-m-d').'.xlsx"');
    }

    /** One report's document for PdfDocument / ExcelDocument: every matching row (up to max_rows) */
    protected function document(Request $request, string $type, array $report): array
    {
        $maxRows = (int) config('report.max_rows');
        $filter = [
            'sort' => 'id',
            'order' => 'asc',
            'page_number' => 1,
            'limit' => $maxRows,
            'search_field' => $request->input('data') ?: null,
        ];
        $rows = $report['rows']($filter);

        return [
            'title' => $this->text($type, 'title'),
            'description' => $this->text($type, 'description'),
            'color' => $report['color'],
            'columns' => $this->columns($report['fields'](), $rows),
            'rows' => $rows,
            'summary' => $this->summaryTiles($type, $report, $filter, count($rows)),
            'filters' => $this->filterLabels($request->input('data') ?: []),
            'truncated' => count($rows) >= $maxRows,
        ];
    }

    /**
     * Reports answer in the user's language: the app sends X-Locale (kh | en); Khmer by default.
     */
    protected function useRequestLocale(Request $request): void
    {
        $locale = $request->header('X-Locale', 'kh');
        App::setLocale(in_array($locale, ['kh', 'en'], true) ? $locale : 'kh');
    }

    /** Reports need the report feature permission plus the report's own module permission */
    protected function allowed(array $report): bool
    {
        return CommonService::can('reports') && (empty($report['permission']) || CommonService::can($report['permission']));
    }

    /** report.types.{key}.{part}, falling back to a readable key */
    protected function text(string $key, string $part): string
    {
        $id = "report.types.$key.$part";
        return Lang::has($id) ? trans($id) : ($part === 'title' ? Str::headline($key) : '');
    }

    /** Summary tiles: the report's own counts (labelled from translations) or the row total */
    protected function summaryTiles(string $type, array $report, array $filter, ?int $total): array
    {
        if (!isset($report['summary'])) {
            return [trans('report.total') => $total];
        }
        $labels = [];
        foreach ($report['summary']($filter) as $key => $value) {
            $id = "report.types.$type.summary.$key";
            $labels[Lang::has($id) ? trans($id) : Str::headline($key)] = $value;
        }
        return $labels;
    }

    /**
     * Column definitions shared by the on-screen table and the PDF:
     * label from the `common` translations, numeric = every value on the page is a number
     * (ids, codes, phone numbers and dates excluded), date = name ends in _date / _at or is dob.
     */
    protected function columns(array $fields, $rows): array
    {
        $rows = collect($rows);

        return collect($fields)->map(function ($field) use ($rows) {
            $isDate = (bool) preg_match(config('report.columns.date'), $field);
            $values = $rows->map(fn ($row) => data_get($row, $field))
                ->filter(fn ($value) => $value !== null && $value !== '' && !is_array($value) && !is_object($value));
            $numeric = !$isDate
                && !preg_match(config('report.columns.not_numeric'), $field)
                && $values->isNotEmpty()
                && $values->every(fn ($value) => is_numeric(str_replace(',', '', (string) $value)));

            return [
                'key' => $field,
                'label' => Lang::has('common.'.$field) ? trans('common.'.$field) : Str::headline($field),
                'numeric' => $numeric,
                'date' => $isDate,
            ];
        })->values()->all();
    }

    /**
     * Active filters as "label => value" for the PDF header.
     * Input: [{ field: value }, …] where value may be a { label, value } option.
     */
    protected function filterLabels(array $filters): array
    {
        $labels = [];
        foreach ($filters as $item) {
            foreach ((array) $item as $field => $value) {
                $text = is_array($value) ? ($value['label'] ?? $value['value'] ?? '') : $value;
                if ($text === null || $text === '') continue;
                $label = Lang::has('common.'.$field) ? trans('common.'.$field) : Str::headline($field);
                $labels[$label] = (string) $text;
            }
        }
        return $labels;
    }
}
