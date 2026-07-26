@include('admin.reports.partials.signature-section')

<div class="shared-footer mt-4 pt-2 border-t border-gray-300 text-[10px] text-gray-500 text-center">
    <div>Print/Report Time: {{ now()->format('d-m-Y h:i A') }} | Developed by Mir Javed Jeetu | 01811480222</div>
    <div class="mt-0.5">
        @if(!empty($resortInfo))
            {{ $resortInfo->name ?? 'TUFAN RESORT' }} | {{ $resortInfo->phone ?? '01958216727' }}
        @else
            TUFAN RESORT | 01958216727
        @endif
    </div>
</div>
