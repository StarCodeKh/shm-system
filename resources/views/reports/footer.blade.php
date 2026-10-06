{{-- Footer: organisation footer text from the settings, report title, page counter --}}
<table width="100%" style="font-family: {{ config('pdf.default_font') }}; font-size: 7pt; color: #8a90a0; border-top: 0.2mm solid #e3e5ec;">
    <tr>
        <td width="40%" style="padding-top: 2mm;">{{ $organization['footer'] ?: $organization['name'] }}</td>
        <td width="30%" style="padding-top: 2mm; text-align: center; color: {{ $color ?? '#8a90a0' }};">{{ $title ?? '' }}</td>
        <td width="30%" style="padding-top: 2mm; text-align: right;">{{ trans('report.page') }} {PAGENO} / {nbpg}</td>
    </tr>
</table>
