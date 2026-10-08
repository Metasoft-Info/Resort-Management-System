@php
    $reportTitle = $title ?? 'Report';
    $reportSubtitle = $subtitle ?? null;
    $headingName = $headingName ?? 'Tufan Resort';
    $headingPhone = $contactPhone ?? '01958216728';
    $start = request('start_date') ?: request('end_date') ?: today()->toDateString();
    $end = request('end_date') ?: $start;
@endphp
<div class="report-header-card">
    <div style="text-align:center; margin:0 0 10px; padding:0; color:#111827;">
        <h1 style="font-family:Arial,sans-serif; font-size:22px; line-height:1.2; font-weight:700; margin:0 0 4px;">{{ $headingName }}</h1>
        <p style="font-family:Arial,sans-serif; font-size:14px; line-height:1.3; margin:0;">Mobile: {{ $headingPhone }}</p>
    </div>
    <div class="text-center">
        <h2 class="font-bold text-gray-800">{{ $reportTitle }}</h2>
        @if($reportSubtitle)<p>{{ $reportSubtitle }}</p>@endif
        <p class="text-gray-600">Date: {{ \Carbon\Carbon::parse($start)->format('d-m-Y') }}@if($end !== $start) to {{ \Carbon\Carbon::parse($end)->format('d-m-Y') }}@endif</p>
    </div>
</div>
