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
        size: A4 landscape;
        margin: 6mm;
    }

    html,
    body {
        height: auto !important;
        overflow: visible !important;
    }

    .main-content,
    main,
    .content-wrapper,
    .layout-content,
    .container,
    .p-6 {
        height: auto !important;
        max-height: none !important;
        overflow: visible !important;
    }

    * {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }

    body {
        font-size: 9px !important;
        line-height: 1.25 !important;
        color: #111827 !important;
        background: #ffffff !important;
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

    .p-6 {
        padding: 2mm !important;
    }

    .report-header-card {
        border: 1px solid #374151 !important;
        border-radius: 4px !important;
        margin-bottom: 3mm !important;
    }

    .report-table-container {
        overflow: visible !important;
        overflow-x: visible !important;
        overflow-y: visible !important;
        max-width: 100% !important;
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
        font-size: 8px !important;
    }

    .report-table th,
    .report-table td,
    .report-table-wide th,
    .report-table-wide td {
        border: 1px solid #6b7280 !important;
        padding: 2px 4px !important;
        vertical-align: top !important;
        overflow-wrap: anywhere !important;
        word-break: break-word !important;
        white-space: normal !important;
    }

    .report-table th,
    .report-table-wide th {
        background: #e5e7eb !important;
        color: #111827 !important;
        font-size: 7.5px !important;
        font-weight: 700 !important;
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
}
</style>
