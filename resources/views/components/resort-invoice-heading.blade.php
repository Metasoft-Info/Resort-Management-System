@php
    $resortInvoiceInfo = $resortInfo ?? null;
    $resortInvoiceAddress = $resortInvoiceInfo?->address ?? 'Kamalnagar, Satkhira Sadar';
    $resortInvoiceEmail = $resortInvoiceInfo?->email ?? 'tufanresort@gmail.com';
    $resortInvoicePhone = trim((string) ($resortInvoiceInfo?->phone ?? '01958216728'));
    $resortInvoicePhoneDigits = preg_replace('/\D+/', '', $resortInvoicePhone);

    if (str_starts_with($resortInvoicePhoneDigits, '880')) {
        $resortInvoicePhoneDigits = substr($resortInvoicePhoneDigits, 2);
    }

    if (preg_match('/^01\d{9}$/', $resortInvoicePhoneDigits)) {
        $resortInvoicePhone = '+88 '.substr($resortInvoicePhoneDigits, 0, 5).'-'.substr($resortInvoicePhoneDigits, 5);
    } elseif ($resortInvoicePhone === '') {
        $resortInvoicePhone = '+88 01958-216728';
    }

    if (isset($invoiceLogoSource)) {
        $resortInvoiceLogo = $invoiceLogoSource !== '' ? $invoiceLogoSource : null;
    } else {
        $resortInvoiceLogo = !empty($resortInvoiceInfo?->header_logo)
            ? asset('storage/'.$resortInvoiceInfo->header_logo)
            : null;
    }
@endphp

<div style="text-align:center; color:#111; font-family:'Times New Roman', Times, serif; margin:0 0 9px; line-height:1.25;">
    @if($resortInvoiceLogo)
        <img src="{{ $resortInvoiceLogo }}" alt="Tufan Resort logo" style="display:block; width:auto; height:22mm; max-width:34mm; object-fit:contain; margin:0 auto 3mm;">
    @else
        <div style="width:22mm; height:22mm; border:1.5px solid #26364a; border-radius:50%; box-sizing:border-box; margin:0 auto 3mm; padding-top:6mm; color:#26364a; font-family:Georgia,serif; font-size:10px; font-weight:bold; letter-spacing:1px; line-height:1.2;">TUFAN<br>RESORT</div>
    @endif
    <h1 style="font-family:Georgia,'Times New Roman',serif; font-size:25px; line-height:1.15; font-weight:bold; letter-spacing:5px; text-transform:uppercase; margin:0 0 6px;">TUFAN RESORT</h1>
    <p style="font-size:15px; line-height:1.25; font-style:italic; margin:0 0 5px;">It's Institution of Tufan Company Limited</p>
    <p style="font-size:14px; line-height:1.25; margin:0 0 4px;">{{ $resortInvoiceAddress }}</p>
    <p style="font-size:13px; line-height:1.25; margin:0;">E-mail: {{ $resortInvoiceEmail }} &nbsp; | &nbsp; Mob. {{ $resortInvoicePhone }}</p>
</div>
