{{-- Footer: thin report-coloured rule, then two aligned columns —
     left: footer text / address / phone · email; right: report title / page counter --}}
@php
    $color = $color ?? '#8a90a0';
    $contact = implode('   ·   ', array_filter([$organization['phone'] ?? '', $organization['email'] ?? '']));
    $line = 'padding: 0 0 0.9mm; line-height: 1.35;';
@endphp
<table width="100%" style="font-family: {{ config('pdf.default_font') }}; font-size: 6.8pt; color: #8a90a0; border-collapse: collapse;">
    <tr>
        <td colspan="2" style="height: 0.35mm; padding: 0; background-color: {{ $color }};"></td>
    </tr>
    <tr><td colspan="2" style="height: 2.2mm; padding: 0;"></td></tr>
    <tr>
        <td width="72%" style="{{ $line }} color: #6b6d7a;">{{ $organization['footer'] ?: $organization['name'] }}</td>
        <td width="28%" style="{{ $line }} text-align: right; color: {{ $color }}; font-weight: bold;">{{ $title ?? '' }}</td>
    </tr>
    <tr>
        <td style="{{ $line }}">{{ $organization['address'] ?? '' }}</td>
        <td style="{{ $line }} text-align: right;">{{ trans('report.page') }} <b style="color: #6b6d7a;">{PAGENO}</b> / {nbpg}</td>
    </tr>
    @if($contact)
        <tr>
            <td style="{{ $line }}">{{ $contact }}</td>
            <td></td>
        </tr>
    @endif
</table>
