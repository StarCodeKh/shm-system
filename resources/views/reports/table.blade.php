<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <title>{{ $title }}</title>
    <style>
        body { font-family: battambang; font-size: 9pt; color: #2b2f3a; }

        .header td { border: none; vertical-align: middle; }
        .org { font-size: 12pt; font-weight: bold; color: #283b6e; line-height: 1.7; }
        .printed { text-align: right; font-size: 8pt; color: #6e7485; line-height: 1.6; }
        .rule { height: 2px; background-color: {{ $color }}; margin: 3mm 0 0; }

        .title { text-align: center; font-size: 16pt; font-weight: bold; color: #283b6e; margin: 6mm 0 4mm; }

        .summary { width: 100%; border-collapse: separate; border-spacing: 3mm 0; margin: 0 -3mm 5mm; }
        .summary td { background-color: #f4f6fb; border-left: 3px solid {{ $color }}; padding: 3mm 4mm; }

        table.data { width: 100%; border-collapse: collapse; }
        table.data th {
            background-color: #283b6e; color: #ffffff; font-weight: bold; font-size: 8.5pt;
            padding: 2.2mm 2mm; text-align: left; border: 0.3mm solid #283b6e;
        }
        table.data td { padding: 1.8mm 2mm; font-size: 8.5pt; border-bottom: 0.2mm solid #dde1ea; }
        table.data tr.alt td { background-color: #f6f7fb; }
        table.data .no { width: 9mm; text-align: center; color: #6e7485; }
        table.data .num { text-align: right; }

        .empty { text-align: center; color: #8a90a0; padding: 10mm 0; }
        .note { margin-top: 3mm; font-size: 8pt; color: #b0413e; }
    </style>
</head>
<body>
    <table class="header" width="100%">
        <tr>
            <td width="20mm"><img src="{{ $logo }}" width="18mm"/></td>
            <td class="org">
                ក្រសួងសេដ្ឋកិច្ចនិងហិរញ្ញវត្ថុ<br/>
                សមាគមសង្គហៈមិត្ត បុគ្គលិក មន្ត្រីរាជការ
            </td>
            <td class="printed">
                កាលបរិច្ឆេទបោះពុម្ព<br/>
                <b>{{ date('d-m-Y H:i') }}</b>
            </td>
        </tr>
    </table>
    <div class="rule"></div>

    <div class="title">{{ $title }}</div>

    <table class="summary">
        <tr>
            @foreach($summary as $label => $value)
                <td>
                    <span style="font-size: 8pt; color: #6e7485;">{{ $label }}</span><br/>
                    <span style="font-size: 15pt; font-weight: bold; color: #283b6e;">{{ number_format($value) }}</span>
                </td>
            @endforeach
        </tr>
    </table>

    <table class="data" repeat_header="1">
        <thead>
            <tr>
                <th class="no">{{ trans('common.No') }}</th>
                @foreach($fields as $field)
                    <th>{{ \Illuminate\Support\Facades\Lang::has('common.'.$field) ? trans('common.'.$field) : \Illuminate\Support\Str::headline($field) }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $row)
                <tr class="{{ $loop->even ? 'alt' : '' }}">
                    <td class="no">{{ $loop->iteration }}</td>
                    @foreach($fields as $field)
                        @php($value = data_get($row, $field))
                        <td class="{{ is_numeric($value) ? 'num' : '' }}">{{ $value }}</td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td class="empty" colspan="{{ count($fields) + 1 }}">មិនមានទិន្នន័យ</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if($truncated)
        <div class="note">បង្ហាញតែ {{ number_format(count($rows)) }} ជួរដំបូងប៉ុណ្ណោះ។ សូមប្រើតម្រងដើម្បីបង្រួមលទ្ធផល។</div>
    @endif
</body>
</html>
