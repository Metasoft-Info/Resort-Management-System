<style>
.report-header-card {
    border: 2px solid #374151;
    border-radius: 10px;
    background: #ffffff;
}

.report-table-container {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    max-width: 100%;
}

.report-table {
    width: 100%;
    min-width: 1100px;
    border-collapse: collapse;
}

.report-table-wide {
    width: 100%;
    min-width: 1300px;
    border-collapse: collapse;
}

@media (max-width: 1024px) {
    .report-table,
    .report-table-wide {
        min-width: 980px;
    }
}

/* Modern shared print style for all reports */
@media print {
    @page {
        size: landscape !important;
        margin: 5mm !important;
    }

    html {
        width: auto !important;
        min-width: 0 !important;
        max-width: none !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    body {
        width: auto !important;
        min-width: 0 !important;
        max-width: none !important;
        margin: 0 !important;
        padding: 0 !important;
        overflow: visible !important;
        height: auto !important;
    }

    .main-content,
    main,
    .content-wrapper,
    .layout-content,
    .container,
    .p-6 {
        height: auto !important;
        min-height: auto !important;
        max-height: none !important;
        overflow: visible !important;
        page-break-before: auto !important;
    }

    * {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }

    body {
        font-size: 11px !important;
        line-height: 1.25 !important;
        color: #000000 !important;
        background: #ffffff !important;
        font-weight: 600 !important;
    }

    .print\:hidden {
        display: none !important;
    }

    .print\:block {
        display: block !important;
    }

    nav,
    header,
    aside,
    footer,
    .pagination,
    .no-print {
        display: none !important;
    }

    .lg\:ml-64 {
        margin-left: 0 !important;
    }

    /* CRITICAL: Override layout containers that clip content in print */
    .h-screen,
    .overflow-hidden,
    .overflow-y-auto,
    .overflow-x-hidden {
        height: auto !important;
        min-height: auto !important;
        max-height: none !important;
        overflow: visible !important;
        overflow-x: visible !important;
        overflow-y: visible !important;
    }

    .flex.h-screen,
    .main-wrapper,
    #mainWrapper,
    main,
    .main-content {
        height: auto !important;
        min-height: auto !important;
        max-height: none !important;
        overflow: visible !important;
        display: block !important;
    }

    .p-6 {
        width: 100% !important;
        max-width: none !important;
        min-height: auto !important;
        box-sizing: border-box !important;
        padding: 4mm 5mm !important;
        margin: 0 !important;
        overflow: visible !important;
    }

    /* Horizontal flow: header at left, table/content at right */
    .print-horizontal-layout {
        display: grid !important;
        grid-template-columns: 68mm 1fr !important;
        column-gap: 4mm !important;
        align-items: start !important;
        width: 100% !important;
    }

    .print-horizontal-layout .report-header-card {
        grid-column: 1 !important;
        margin: 0 !important;
        padding: 2mm !important;
        position: sticky !important;
        top: 0 !important;
    }

    .print-horizontal-layout .print-main-content {
        grid-column: 2 !important;
        width: 100% !important;
        min-width: 0 !important;
    }

    .report-header-card {
        border: 1px solid #374151 !important;
        border-radius: 4px !important;
        margin-bottom: 3mm !important;
    }

    .report-table-container,
    .bg-white.rounded-lg.shadow,
    .bg-white.rounded-xl.shadow-lg {
        overflow: visible !important;
        overflow-x: visible !important;
        overflow-y: visible !important;
        max-width: 100% !important;
        height: auto !important;
        max-height: none !important;
        box-shadow: none !important;
        border-radius: 0 !important;
    }

    .report-table-container::-webkit-scrollbar {
        display: none !important;
        width: 0 !important;
        height: 0 !important;
    }

    .report-table,
    .report-table-wide {
        min-width: 100% !important;
        width: 100% !important;
        table-layout: auto !important;
        border-collapse: collapse !important;
        font-size: 10px !important;
        font-weight: 600 !important;
        color: #000000 !important;
    }

    .report-table th,
    .report-table td,
    .report-table-wide th,
    .report-table-wide td {
        border: 1px solid #000000 !important;
        padding: 2px 4px !important;
        vertical-align: top !important;
        overflow-wrap: anywhere !important;
        word-break: break-word !important;
        white-space: normal !important;
        color: #000000 !important;
        font-weight: 700 !important;
    }

    .report-table th,
    .report-table-wide th {
        background: #d1d5db !important;
        color: #000000 !important;
        font-size: 9px !important;
        font-weight: 800 !important;
        text-transform: uppercase !important;
        letter-spacing: .2px !important;
    }

    .report-table tbody tr:nth-child(even),
    .report-table-wide tbody tr:nth-child(even) {
        background: #f9fafb !important;
    }

    .report-table td.text-right,
    .report-table-wide td.text-right {
        text-align: right !important;
    }

    .report-table td.text-center,
    .report-table-wide td.text-center {
        text-align: center !important;
    }

    .report-table td.whitespace-nowrap,
    .report-table th.whitespace-nowrap,
    .report-table-wide td.whitespace-nowrap,
    .report-table-wide th.whitespace-nowrap {
        white-space: nowrap !important;
    }

    /* Keep tables printable without clipping: allow wrapping in dense reports */
    .report-table td,
    .report-table th,
    .report-table-wide td,
    .report-table-wide th {
        max-width: none !important;
    }

    thead {
        display: table-header-group !important;
    }

    tfoot {
        display: table-footer-group !important;
    }

    tr {
        page-break-inside: avoid !important;
        break-inside: avoid !important;
    }

    h1, h2, h3 {
        page-break-after: avoid !important;
    }

    .bg-white.rounded-lg.shadow,
    .bg-white.rounded-xl.shadow-lg {
        box-shadow: none !important;
        border-radius: 0 !important;
        border: 0 !important;
    }

    /* Report header: pure black, bold, larger */
    .report-header-card h1,
    .report-header-card h2,
    .report-header-card p {
        color: #000000 !important;
        font-weight: 700 !important;
    }

    .report-header-card h1 {
        font-size: 16px !important;
    }

    .report-header-card h2 {
        font-size: 14px !important;
    }

    .report-header-card p {
        font-size: 11px !important;
    }

    /* Signature section: ensure visible, pure black, bold */
    .signature-block {
        display: block !important;
        visibility: visible !important;
        page-break-inside: avoid !important;
    }

    .signature-block p {
        color: #000000 !important;
        font-weight: 700 !important;
        font-size: 10px !important;
    }

    /* Ensure footer and signature are never hidden */
    .signature-block,
    .signature-block *,
    .shared-footer,
    .shared-footer * {
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
    }

    /* Footer: position at bottom of page, always visible */
    .shared-footer {
        margin-top: 4mm !important;
        padding-top: 2mm !important;
        page-break-inside: avoid !important;
        page-break-before: auto !important;
        text-align: center !important;
        width: 100% !important;
    }

    .shared-footer div {
        color: #6b7280 !important;
        font-size: 9px !important;
        font-weight: 500 !important;
    }

    /* All report text: ensure pure black */
    .report-table-container,
    .report-table-container * {
        color: #000000 !important;
    }
}
</style>
