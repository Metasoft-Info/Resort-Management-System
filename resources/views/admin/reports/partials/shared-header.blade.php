@php
    $reportTitle = $title ?? 'Report';
    $reportSubtitle = $subtitle ?? null;
    $headingName = $headingName ?? ($resortInfo->resort_name ?? 'TUFAN RESORT');
    $headingTagline = $headingTagline ?? "It's Institution of Tufan Company Limited";
    $contactEmail = $contactEmail ?? ($resortInfo->email ?? 'info@tufanconventionresort.com');
    $contactPhone = $contactPhone ?? ($resortInfo->phone ?? '01958216727');
@endphp

<div class="report-header-card shadow-sm p-2 mb-2">
    <div class="text-center border-b border-gray-300 pb-1 mb-1">
        @if(!empty($resortInfo) && $resortInfo->header_logo)
            <img src="{{ asset('storage/' . $resortInfo->header_logo) }}" alt="{{ $headingName }}" class="h-9 mx-auto mb-0.5">
        @endif
        <h1 class="text-base font-bold text-gray-800 tracking-wide leading-tight">{{ $headingName }}</h1>

        <p class="text-[11px] italic text-gray-700 leading-tight">{{ $headingTagline }}</p>

        @if(!empty($resortInfo) && $resortInfo->address)
            <p class="text-[11px] text-gray-600 leading-tight">{{ $resortInfo->address }}</p>
        @endif

        <p class="text-[10px] text-gray-500 leading-tight">
            @if($contactEmail)E-mail: {{ $contactEmail }}@endif
            @if($contactEmail && $contactPhone) | @endif
            @if($contactPhone)Phone: {{ $contactPhone }}@endif
        </p>
    </div>

    <div class="text-center">
        <h2 class="text-sm font-bold text-gray-800 tracking-wide leading-tight">{{ $reportTitle }}</h2>
        @if($reportSubtitle)
            <p class="text-[10px] text-gray-600 leading-tight">{{ $reportSubtitle }}</p>
        @endif

        <p class="text-[10px] text-gray-600 leading-tight">
            @if(request('start_date') || request('end_date'))
                @php
                    $sDate = request('start_date');
                    $eDate = request('end_date');
                @endphp
                @if($sDate && $eDate && $sDate === $eDate)
                    Date: {{ \Carbon\Carbon::parse($sDate)->format('d-m-Y') }}
                @elseif($sDate && !$eDate)
                    Date: {{ \Carbon\Carbon::parse($sDate)->format('d-m-Y') }}
                @elseif(!$sDate && $eDate)
                    Date: {{ \Carbon\Carbon::parse($eDate)->format('d-m-Y') }}
                @else
                    Date: {{ \Carbon\Carbon::parse($sDate)->format('d-m-Y') }}
                    to {{ \Carbon\Carbon::parse($eDate)->format('d-m-Y') }}
                @endif
            @else
                Date: {{ date('d-m-Y') }}
            @endif
        </p>
    </div>
</div>
