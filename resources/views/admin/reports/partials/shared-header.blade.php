@php
    $reportTitle = $title ?? 'Report';
    $reportSubtitle = $subtitle ?? null;
@endphp

<div class="report-header-card shadow-sm p-2 mb-2">
    <div class="text-center border-b border-gray-300 pb-1 mb-1">
        @if(!empty($resortInfo) && $resortInfo->header_logo)
            <img src="{{ asset('storage/' . $resortInfo->header_logo) }}" alt="{{ $resortInfo->resort_name ?? 'Resort' }}" class="h-9 mx-auto mb-0.5">
            <h1 class="text-base font-bold text-gray-800 tracking-wide leading-tight">TUFAN RESORT</h1>
        @else
            <h1 class="text-base font-bold text-gray-800 tracking-wide leading-tight">{{ $resortInfo->resort_name ?? 'TUFAN RESORT' }}</h1>
        @endif

        <p class="text-[11px] italic text-gray-700 leading-tight">It's Institution of Tufan Company Limited</p>

        @if(!empty($resortInfo) && $resortInfo->address)
            <p class="text-[11px] text-gray-600 leading-tight">{{ $resortInfo->address }}</p>
        @endif

        <p class="text-[10px] text-gray-500 leading-tight">
            @if(!empty($resortInfo))
                @if($resortInfo->email)E-mail: {{ $resortInfo->email }}@endif
                @if($resortInfo->email && $resortInfo->phone) | @endif
                @if($resortInfo->phone)Phone: {{ $resortInfo->phone }}@endif
            @else
                E-mail: info@tufanconventionresort.com | Phone: 01958-216728
            @endif
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
