<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <title>{{ $title }}</title>
    {{-- mPDF supports a CSS subset: layout uses tables, colours come from the report ($color).
         simpleTables is on for speed, so cell borders are ignored: separation uses backgrounds. --}}
    @php
        // Mix the report colour with white: tint(0.88) gives a soft background of the same hue
        $tint = function ($hex, $amount) {
            $hex = ltrim($hex, '#');
            if (strlen($hex) !== 6) return '#f6f7fb';
            $mix = fn ($i) => (int) round(hexdec(substr($hex, $i, 2)) + (255 - hexdec(substr($hex, $i, 2))) * $amount);
            return sprintf('#%02x%02x%02x', $mix(0), $mix(2), $mix(4));
        };
        $soft = $tint($color, 0.86);
        $softer = $tint($color, 0.94);
        // Moul is a Khmer display face; English headings stay in the body font
        $heading = app()->getLocale() === 'kh' ? 'moul' : config('pdf.default_font');
        $headingWeight = $heading === 'moul' ? 'normal' : 'bold';
    @endphp
    <style>
        body { font-family: {{ config('pdf.default_font') }}; font-size: {{ $type['body'] }}pt; color: #2c2e45; }
        table { border-collapse: collapse; }
        td { vertical-align: top; }

        /* Letterhead: national motto centred, organisation block on the left */
        .kingdom { font-family: {{ $heading }}; font-weight: {{ $headingWeight }}; font-size: {{ $type['kingdom'] }}pt; color: #1f2140; text-align: center; }
        .motto { font-family: {{ $heading }}; font-weight: {{ $headingWeight }}; font-size: {{ $type['motto'] }}pt; color: #1f2140; text-align: center; }
        .org-name { font-family: {{ $heading }}; font-weight: {{ $headingWeight }}; font-size: {{ $type['org'] }}pt; color: #1f2140; text-align: center; line-height: 1.5; }
        .org-system { font-size: {{ $type['system'] }}pt; color: #8a8ca5; text-align: center; }

        /* Title */
        .title { font-family: {{ $heading }}; font-weight: {{ $headingWeight }}; font-size: {{ $type['title'] }}pt; color: {{ $color }}; text-align: center; }
        .subtitle { font-size: {{ $type['subtitle'] }}pt; color: #8a8ca5; text-align: center; }

        /* Info bar */
        .info td { background-color: #f6f7fb; padding: 2.4mm 4mm; vertical-align: middle; }
        .info .label { font-size: {{ $type['info_label'] }}pt; color: #8a8ca5; }
        .info .value { font-size: {{ $type['info_value'] }}pt; font-weight: bold; color: #2c2e45; }

        /* Data table */
        table.data { width: 100%; margin-top: 4mm; }
        table.data th {
            background-color: {{ $color }}; color: #ffffff; font-size: {{ $type['th'] }}pt; font-weight: bold;
            padding: {{ round(2.8 * $type['table'], 2) }}mm {{ round(2.2 * $type['table'], 2) }}mm; text-align: left; vertical-align: middle;
        }
        table.data td { padding: {{ round(2.1 * $type['table'], 2) }}mm {{ round(2.2 * $type['table'], 2) }}mm; font-size: {{ $type['td'] }}pt; color: #3f4157; vertical-align: middle; }
        table.data tr.alt td { background-color: {{ $softer }}; }
        table.data .no { width: 9mm; text-align: center; color: #8a8ca5; }
        table.data th.no { color: #ffffff; }
        table.data .num { text-align: right; }
        table.data .date { white-space: nowrap; color: #6b6d7a; }
        table.data .muted { color: #b0b2bd; }
        table.data tfoot td {
            font-weight: bold; color: #1f2140; background-color: {{ $soft }};
            padding-top: 2.8mm; padding-bottom: 2.8mm;
        }

        .note {
            margin-top: 4mm; padding: 2.5mm 4mm; font-size: {{ $type['note'] }}pt; color: #9a5b0a;
            background-color: #fff6e6; border-left: 1mm solid #ff9f43;
        }
    </style>
</head>
<body>
    @php
        // Number formatting matches the on-screen viewer: thousands separators, up to 2 decimals
        $format = function ($value) {
            $n = (float) str_replace(',', '', (string) $value);
            return number_format($n, floor($n) == $n ? 0 : 2);
        };
        $display = function ($value) {
            if (is_array($value) || is_object($value)) {
                $value = data_get($value, 'title_kh') ?? data_get($value, 'name_kh') ?? data_get($value, 'label') ?? '';
            }
            return $value;
        };
        $numericTotals = [];
        foreach ($columns as $column) {
            // Only money columns get a total (summing rates or counts of codes means nothing)
            if ($column['numeric'] && preg_match($moneyPattern, $column['key'])) {
                $numericTotals[$column['key']] = collect($rows)->sum(fn ($row) => (float) str_replace(',', '', (string) data_get($row, $column['key'])));
            }
        }
        $summary = $summary ?? [];
    @endphp

    {{-- Letterhead --}}
    @if($nationalHeader)
        <div class="kingdom">{{ trans('report.kingdom') }}</div>
        <div class="motto">{{ trans('report.motto') }}</div>
        {{-- the short double rule under the motto --}}
        <table align="center" style="margin-top: 1.5mm;">
            <tr>
                <td style="width: 9mm; height: 0.5mm; background-color: #1f2140;"></td>
                <td style="width: 2mm;"></td>
                <td style="width: 3mm; height: 0.5mm; background-color: {{ $color }};"></td>
                <td style="width: 2mm;"></td>
                <td style="width: 9mm; height: 0.5mm; background-color: #1f2140;"></td>
            </tr>
        </table>
    @endif

    <table width="100%" style="margin-top: {{ $nationalHeader ? '-4mm' : '0' }};">
        <tr>
            <td width="78mm" style="padding: 0;">
                {{-- one row each, so the gaps are exact: logo · 2.5mm · name · 1mm · system --}}
                <table width="100%">
                    <tr><td style="text-align: center; padding: 0 0 2.5mm;"><img src="{{ $organization['logo'] }}" width="17mm"/></td></tr>
                    <tr><td class="org-name" style="padding: 0;">{{ $organization['name'] }}</td></tr>
                    @if($organization['system'])
                        <tr><td class="org-system" style="padding: 1mm 0 0;">{{ $organization['system'] }}</td></tr>
                    @endif
                </table>
            </td>
            <td></td>
        </tr>
    </table>

    {{-- Title, with a short accent bar under it --}}
    <div class="title" style="margin-top: 5mm;">{{ $title }}</div>
    @if(!empty($description))
        <div class="subtitle" style="margin-top: 0.5mm;">{{ $description }}</div>
    @endif
    <table align="center" style="margin-top: 2mm;">
        <tr><td style="width: 18mm; height: 0.8mm; background-color: {{ $color }};"></td></tr>
    </table>

    {{-- Info bar: when, who, how many, which filters --}}
    <table class="info" width="100%" style="margin-top: 5mm;">
        <tr>
            <td width="4mm" style="padding: 0; background-color: {{ $color }};"></td>
            <td width="22%">
                <span class="label">{{ trans('report.printed_at') }}</span><br/>
                <span class="value">{{ date('d-m-Y H:i') }}</span>
            </td>
            <td width="18%">
                <span class="label">{{ trans('report.printed_by') }}</span><br/>
                <span class="value">{{ $printedBy ?: '—' }}</span>
            </td>
            <td width="15%">
                <span class="label">{{ trans('report.records') }}</span><br/>
                <span class="value">{{ number_format(count($rows)) }}</span>
            </td>
            <td>
                <span class="label">{{ trans('report.filters') }}</span><br/>
                @if(!empty($filters))
                    @foreach($filters as $label => $value)
                        <span style="font-size: {{ $type['info_label'] }}pt; color: #6b6d7a;">{{ $label }}:</span>
                        <span class="value">{{ $value }}</span>@if(!$loop->last)<span style="color: #c4c6d0;">&nbsp;&nbsp;|&nbsp;&nbsp;</span>@endif
                    @endforeach
                @else
                    <span class="value">{{ trans('report.no_filter') }}</span>
                @endif
            </td>
        </tr>
    </table>

    {{-- Summary cards: fixed width, so one card doesn't stretch across the page --}}
    @if(count($summary))
        <table width="100%" style="margin-top: 4mm;">
            <tr>
                @foreach($summary as $label => $value)
                    <td width="46mm" style="padding: 0;">
                        {{-- inline-styled spans: mPDF ignores font sizes on divs inside cells --}}
                        <table width="100%" style="background-color: {{ $softer }};">
                            <tr>
                                <td width="1.2mm" style="background-color: {{ $color }}; padding: 0;"></td>
                                <td style="padding: 2.6mm 4mm 3mm;">
                                    <span style="font-size: {{ $type['summary_label'] }}pt; color: #6b6d7a;">{{ $label }}</span><br/>
                                    <span style="font-size: {{ $type['summary_value'] }}pt; font-weight: bold; color: #1f2140;">{{ is_numeric($value) ? number_format($value) : $value }}</span>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td width="3mm" style="padding: 0;"></td>
                @endforeach
                <td></td>
            </tr>
        </table>
    @endif

    {{-- Data --}}
    <table class="data" repeat_header="1">
        <thead>
            <tr>
                <th class="no">{{ trans('common.No') }}</th>
                @foreach($columns as $column)
                    <th class="{{ $column['numeric'] ? 'num' : '' }}">{{ $column['label'] }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $row)
                <tr class="{{ $loop->even ? 'alt' : '' }}">
                    <td class="no">{{ $loop->iteration }}</td>
                    @foreach($columns as $column)
                        @php($value = $display(data_get($row, $column['key'])))
                        @if($value === null || $value === '')
                            <td class="muted">—</td>
                        @elseif($column['numeric'])
                            <td class="num">{{ $format($value) }}</td>
                        @elseif($column['date'])
                            <td class="date">{{ \Illuminate\Support\Str::limit((string) $value, 10, '') }}</td>
                        @else
                            <td>{{ $value }}</td>
                        @endif
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($columns) + 1 }}" style="background-color: {{ $softer }}; padding: 10mm 0; text-align: center;">
                        <span style="font-size: {{ $type['empty'] }}pt; font-weight: bold; color: #4b4d68;">{{ trans('report.no_data') }}</span><br/>
                        <span style="font-size: {{ $type['empty_hint'] }}pt; color: #8a8ca5;">{{ trans('report.no_data_hint') }}</span>
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if(count($rows) && count($numericTotals))
            <tfoot>
                <tr>
                    <td class="no"></td>
                    @foreach($columns as $column)
                        @if(array_key_exists($column['key'], $numericTotals))
                            <td class="num">{{ $format($numericTotals[$column['key']]) }}</td>
                        @elseif($loop->first)
                            <td>{{ trans('report.total_row') }}</td>
                        @else
                            <td></td>
                        @endif
                    @endforeach
                </tr>
            </tfoot>
        @endif
    </table>

    @if($truncated)
        <div class="note">{{ trans('report.truncated', ['count' => number_format(count($rows))]) }}</div>
    @endif

    {{-- Signatures: place and date above the last role, room to sign under each --}}
    @if(count($signatures))
        <table width="100%" style="margin-top: 8mm; page-break-inside: avoid;" autosize="1">
            <tr>
                @foreach($signatures as $role)
                    <td width="{{ round(100 / count($signatures), 2) }}%" style="text-align: center; font-size: {{ $type['date_line'] }}pt; color: #6b6d7a;">
                        {{ $loop->last ? $dateLine : '' }}
                    </td>
                @endforeach
            </tr>
            <tr>
                @foreach($signatures as $role)
                    <td style="text-align: center; padding-top: 1mm;">
                        <span style="font-family: {{ $heading }}; font-weight: {{ $headingWeight }}; font-size: {{ $type['signature'] }}pt; color: #1f2140;">{{ $role }}</span>
                    </td>
                @endforeach
            </tr>
            <tr>
                @foreach($signatures as $role)
                    <td style="height: 22mm;"></td>
                @endforeach
            </tr>
        </table>
    @endif
</body>
</html>
