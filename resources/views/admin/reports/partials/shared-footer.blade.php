@include('admin.reports.partials.signature-section')

<div class="mt-4 pt-2 border-t border-gray-300 text-[10px] text-gray-500 text-center">
    <div>প্রিন্ট/রিপোর্ট সময়: {{ now()->format('d-m-Y h:i A') }} | Developed by Mir Javed Jeetu | 01811480222</div>
    <div class="mt-0.5">
        @if(!empty($resortInfo))
            {{ $resortInfo->name ?? 'TUFAN RESORT' }} | {{ $resortInfo->phone ?? '+88 01958-216728' }}
        @else
            TUFAN RESORT | +88 01958-216728
        @endif
    </div>
</div>
