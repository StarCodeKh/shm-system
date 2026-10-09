<?php

namespace App\Services;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\Mpdf;

/**
 * The system's standard printed document: letterhead, title, info bar, summary cards, data table,
 * signatures and footer (resources/views/reports/table.blade.php), laid out by mPDF.
 * Used by the PDF reports and by the Print button of every list page.
 */
class PdfDocument
{
    /**
     * PDF bytes for one table document.
     *
     * $data: title, columns [{ key, label, numeric, date }], rows; optional description, color, summary
     * [label => value], filters [label => text], truncated. Letterhead, signatures and the printing
     * user are filled in here.
     */
    public static function render(array $data): string
    {
        $user = auth()->user();
        $data += [
            'description' => '',
            'color' => config('report.default_color'),
            'summary' => [],
            'filters' => [],
            'truncated' => false,
            'printedBy' => $user ? ($user->username ?? $user->email ?? '') : '',
            'moneyPattern' => config('report.columns.money'),
        ];
        $data['organization'] = self::organization();
        $data['nationalHeader'] = (bool) config('report.national_header');
        $data['dateLine'] = self::dateLine();
        $data['signatures'] = collect(config('report.signature.roles', []))
            ->map(fn ($role) => trans('report.signatures.'.$role))->values()->all();

        $data['type'] = self::typography(count($data['columns']));

        $html = View::make('reports.table', $data)->render();

        // Large reports produce a big HTML string; mPDF needs more regex, memory and time to lay it out.
        ini_set('pcre.backtrack_limit', '50000000');
        ini_set('memory_limit', '1024M');
        set_time_limit(300);

        // Narrow tables read better in portrait
        $mpdf = self::make(count($data['columns']) <= config('report.portrait_max_columns') ? 'P' : 'L');
        $mpdf->SetTitle($data['title']);
        $mpdf->SetHTMLFooter(View::make('reports.footer', [
            'title' => $data['title'],
            'color' => $data['color'],
            'organization' => $data['organization'],
            'type' => $data['type'],
        ])->render());

        // mPDF's Khmer shaper emits harmless warnings on some malformed text; don't let them abort the document.
        set_error_handler(fn ($severity, $message, $file) => str_contains($file, '/mpdf/'), E_WARNING | E_NOTICE);
        try {
            $mpdf->WriteHTML($html);

            return $mpdf->Output('', 'S');
        } finally {
            restore_error_handler();
        }
    }

    /**
     * Text sizes for a table with $columns columns, from config report.typography:
     * table text (th, td) shrinks for wide tables; $scale enlarges everything (Excel).
     * Also 'table' = the shrink factor, for cell padding.
     */
    public static function typography(int $columns, float $scale = 1.0): array
    {
        $config = config('report.typography');
        $factor = 1.0;
        foreach ($config['table_scale'] as $max => $value) {
            if ($columns <= $max) {
                $factor = $value;
                break;
            }
        }
        $sizes = [];
        foreach ($config['sizes'] as $role => $size) {
            $size *= (in_array($role, ['th', 'td'], true) ? $factor : 1) * $scale;
            // PDF takes tenths of a point; Excel only shows half points
            $sizes[$role] = $scale == 1.0 ? round($size, 1) : round($size * 2) / 2;
        }
        $sizes['table'] = $factor;

        return $sizes;
    }

    /** Organisation name, logo, footer and contact line from the system settings (configuration table) */
    public static function organization(): array
    {
        static $organization = null;
        if ($organization !== null) {
            return $organization;
        }

        $settings = Settings::all();
        $logo = ltrim((string) ($settings['config_logo'] ?? ''), '/');
        $logoPath = $logo && is_file(public_path($logo)) ? public_path($logo) : public_path(config('report.default_logo'));

        return $organization = [
            'name' => $settings['config_owner'] ?? config('app.name'),
            'system' => $settings['config_name'] ?? '',
            'logo' => $logoPath,
            'footer' => $settings['config_footer'] ?? '',
            // Footer contact lines
            'address' => trim((string) ($settings['config_address'] ?? '')),
            'phone' => trim((string) ($settings['config_telephone'] ?? '')),
            'email' => trim((string) ($settings['config_email'] ?? '')),
        ];
    }

    /** "Place, day month year" above the signatures, in Khmer numerals for Khmer */
    public static function dateLine(): string
    {
        $khmer = App::getLocale() === 'kh';
        $digits = fn ($value) => $khmer ? strtr((string) $value, ['០', '១', '២', '៣', '៤', '៥', '៦', '៧', '៨', '៩']) : (string) $value;
        $months = trans('report.months');

        return trim(trans('report.date_line', [
            'place' => config('report.signature.place') ?: '',
            'day' => $digits(date('d')),
            'month' => is_array($months) ? $months[(int) date('n') - 1] : date('F'),
            'year' => $digits(date('Y')),
        ]), ' ,');
    }

    protected static function make(string $orientation): Mpdf
    {
        $fontDirs = (new ConfigVariables)->getDefaults()['fontDir'];
        $fontData = (new FontVariables)->getDefaults()['fontdata'];

        return new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'orientation' => $orientation,
            'tempDir' => config('pdf.temp_dir'),
            'fontDir' => array_merge($fontDirs, [config('pdf.font_dir')]),
            'fontdata' => $fontData + config('pdf.fonts'),
            'default_font' => config('pdf.default_font'),
            'margin_top' => 12,
            'margin_bottom' => 26, // room for the three-line footer
            'margin_footer' => 7,
            'simpleTables' => true,
        ]);
    }
}
