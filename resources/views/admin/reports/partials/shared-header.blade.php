@php
    $reportTitle = $title ?? 'Report';
    $reportSubtitle = $subtitle ?? null;
    $start = request('start_date') ?: request('end_date') ?: today()->toDateString();
    $end = request('end_date') ?: $start;
@endphp
<div class="report-header-card">
    @include('components.resort-document-heading')
    <div class="text-center">
        <h2 class="font-bold text-gray-800">{{ $reportTitle }}</h2>
        @if($reportSubtitle)<p>{{ $reportSubtitle }}</p>@endif
        <p class="text-gray-600">Date: {{ \Carbon\Carbon::parse($start)->format('d-m-Y') }}@if($end !== $start) to {{ \Carbon\Carbon::parse($end)->format('d-m-Y') }}@endif</p>
    </div>
</div>
