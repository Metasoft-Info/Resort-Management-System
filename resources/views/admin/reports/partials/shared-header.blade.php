@php
    $reportTitle = $title ?? 'রিপোর্ট';
    $reportSubtitle = $subtitle ?? null;
@endphp

<div class="report-header-card shadow-lg p-5 mb-6">
    <div class="text-center border-b border-gray-300 pb-4 mb-3">
        @if(!empty($resortInfo) && $resortInfo->header_logo)
            <img src="{{ asset('storage/' . $resortInfo->header_logo) }}" alt="{{ $resortInfo->resort_name ?? 'Resort' }}" class="h-16 mx-auto mb-2">
        @else
            <h1 class="text-2xl font-bold text-gray-800">{{ $resortInfo->resort_name ?? 'তুফান কনভেনশন রিসোর্ট' }}</h1>
        @endif

        @if(!empty($resortInfo) && $resortInfo->address)
            <p class="text-gray-600 text-sm">{{ $resortInfo->address }}</p>
        @endif
        <p class="text-gray-500 text-xs mt-1">
            @if(!empty($resortInfo))
                @if($resortInfo->phone)Phone: {{ $resortInfo->phone }}@endif
                @if($resortInfo->phone && $resortInfo->email) | @endif
                @if($resortInfo->email)Email: {{ $resortInfo->email }}@endif
            @endif
        </p>
    </div>

    <div class="text-center">
        <h2 class="text-xl font-bold text-gray-800 tracking-wide">{{ $reportTitle }}</h2>
        @if($reportSubtitle)
            <p class="text-sm text-gray-600 mt-1">{{ $reportSubtitle }}</p>
        @endif

        <p class="text-sm text-gray-600 mt-1">
            @if(request('start_date') || request('end_date'))
                তারিখ: {{ request('start_date') ? \Carbon\Carbon::parse(request('start_date'))->format('d-m-Y') : 'শুরু' }}
                থেকে {{ request('end_date') ? \Carbon\Carbon::parse(request('end_date'))->format('d-m-Y') : 'শেষ' }}
            @else
                তারিখ: {{ date('d-m-Y') }}
            @endif
        </p>
    </div>
</div>
