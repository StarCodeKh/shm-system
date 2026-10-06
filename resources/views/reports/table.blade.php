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
        $soft = $tint($color, 0.88);
        $softer = $tint($color, 0.95);
    @endphp
    <style>
        body { font-family: {{ config('pdf.default_font') }}; font-size: 8.5pt; color: #2c2e45; }

        /* Header */
        .head td { border: none; vertical-align: middle; }
        .org { font-size: 10.5pt; font-weight: bold; color: #2c2e45; line-height: 1.6; }
        .org small { font-size: 8pt; font-weight: normal; color: #8a8ca5; }
        .meta { text-align: right; font-size: 7.5pt; color: #8a8ca5; line-height: 1.7; }
        .meta b { color: #2c2e45; }
        .rule { height: 1.2mm; background-color: {{ $color }}; margin-top: 3mm; }

        /* Title */
        .title { font-size: 15pt; font-weight: bold; color: #2c2e45; margin-top: 6mm; }
        .subtitle { font-size: 8.5pt; color: #8a8ca5; margin-top: 1mm; }

        /* Active filters */
        .filters { margin-top: 3mm; }
        .chip {
            background-color: #f3f4f7; color: #4b4d68; font-size: 7.5pt;
            padding: 1mm 2.5mm; border-radius: 2mm;
        }
        .chip b { color: #2c2e45; }

        /* Summary tiles */
        .summary { width: 100%; border-collapse: separate; border-spacing: 2.5mm 0; margin: 5mm -2.5mm 5mm; }
        .summary td { background-color: {{ $softer }}; padding: 0; vertical-align: top; }

        /* Data table */
        table.data { width: 100%; border-collapse: collapse; margin-top: 2mm; }
        table.data th {
            background-color: {{ $soft }}; color: #2c2e45; font-size: 7.8pt; font-weight: bold;
            padding: 2.6mm 2.2mm; text-align: left;
        }
        table.data td { padding: 2mm 2.2mm; font-size: 8pt; color: #3f4157; border-bottom: 0.2mm solid #eceef4; }
        table.data tr.alt td { background-color: #f7f8fa; }
        table.data .no { width: 9mm; text-align: center; color: #8a8ca5; }
        table.data .num { text-align: right; }
        table.data .date { white-space: nowrap; color: #6b6d7a; }
        table.data .muted { color: #b0b2bd; }
        table.data tfoot td { font-weight: bold; color: #2c2e45; background-color: {{ $soft }}; padding-top: 2.6mm; padding-bottom: 2.6mm; }

        .empty { text-align: center; color: #8a8ca5; padding: 12mm 0; }
        .note {
            margin-top: 4mm; padding: 2.5mm 4mm; font-size: 7.8pt; color: #9a5b0a;
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
    @endphp

    {{-- Header --}}
    <table class="head" width="100%">
        <tr>
            <td width="17mm"><img src="{{ $organization['logo'] }}" width="14mm"/></td>
            <td class="org">
                {{ $organization['name'] }}
                @if($organization['system'])<br/><small>{{ $organization['system'] }}</small>@endif
            </td>
            <td class="meta">
                {{ trans('report.printed_at') }} <b>{{ date('d-m-Y H:i') }}</b><br/>
                @if($printedBy) {{ trans('report.printed_by') }} <b>{{ $printedBy }}</b><br/> @endif
                {{ trans('report.rows') }} <b>{{ number_format(count($rows)) }}</b>
            </td>
        </tr>
    </table>
    <div class="rule"></div>

    {{-- Title --}}
    <div class="title">{{ $title }}</div>
    @if(!empty($description))
        <div class="subtitle">{{ $description }}</div>
    @endif

    {{-- Active filters --}}
    @if(!empty($filters))
        <div class="filters">
            @foreach($filters as $label => $value)
                <span class="chip">{{ $label }}: <b>{{ $value }}</b></span>&nbsp;
            @endforeach
        </div>
    @endif

    {{-- Summary --}}
    @if(!empty($summary))
        <table class="summary">
            <tr>
                @foreach($summary as $label => $value)
                    <td>
                        {{-- accent strip + inline-styled spans: mPDF ignores font sizes on divs inside cells --}}
                        <table width="100%" style="border-collapse: collapse;">
                            <tr><td style="background-color: {{ $color }}; height: 1mm; padding: 0;"></td></tr>
                            <tr><td style="padding: 2.5mm 4mm 3mm;">
                                <span style="font-size: 7.5pt; color: #8a8ca5;">{{ $label }}</span><br/>
                                <span style="font-size: 16pt; font-weight: bold; color: #2c2e45;">{{ is_numeric($value) ? number_format($value) : $value }}</span>
                            </td></tr>
                        </table>
                    </td>
                @endforeach
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
                    <td class="empty" colspan="{{ count($columns) + 1 }}">{{ trans('report.no_data') }}</td>
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
</body>
</html>
