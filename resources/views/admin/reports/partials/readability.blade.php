<style>
body .report-page { padding: 16px; }
body .report-page .report-header-card { height: auto !important; min-height: 0 !important; padding: 12px !important; border: 1px solid #d1d5db; border-radius: 12px; margin-bottom: 16px; }
body .report-page .report-header-card h1 { font-size: 24px; }
body .report-page .report-header-card h2 { font-size: 18px; }
body .report-page .report-header-card p { font-size: 14px; }
body .report-page .report-table, body .report-page .report-table-wide { font-size: 13px; line-height: 1.4; }
body .report-page .report-table th, body .report-page .report-table-wide th { font-size: 12px; padding: 9px 7px; }
body .report-page .report-table td, body .report-page .report-table-wide td { padding: 7px; }
body .report-page .grid:not(.print\:hidden) > div > p.font-bold { font-size: 20px; line-height: 1.3; }
body .report-page .grid > div > p.text-xs { font-size: 13px; }
body .report-page .report-summary-grid { grid-template-columns: repeat(auto-fit, minmax(145px, 1fr)); }
@media print {
    @page { size: A4 landscape; margin: 7mm; }
    body .report-page { width: 100% !important; margin: 0 !important; padding: 0 !important; }
    body .report-page .report-header-card { border: 0 !important; padding: 0 0 3mm !important; margin: 0 0 3mm !important; break-inside: avoid; }
    body .report-page .report-header-card h1 { font-size: 17pt !important; }
    body .report-page .report-header-card h2 { font-size: 12pt !important; }
    body .report-page .report-header-card p { font-size: 10pt !important; }
    body .report-page .grid[class*="print:grid-cols"], body .report-page .report-summary-grid { display: grid !important; grid-template-columns: repeat(auto-fit,minmax(28mm,1fr)) !important; gap: 2mm !important; justify-items: stretch !important; margin-bottom: 3mm !important; }
    body .report-page .grid > div { padding: 2mm !important; }
    body .report-page .grid > div > p.font-bold { font-size: 12pt !important; line-height: 1.3 !important; }
    body .report-page .grid > div > p.text-xs { font-size: 9pt !important; }
    body .report-page .p-5 { padding: 0 !important; margin-bottom: 3mm !important; border: 0 !important; }
    body .report-page .report-table, body .report-page .report-table-wide { width:100% !important; min-width:0 !important; font-size:8pt !important; table-layout:auto !important; }
    body .report-page .report-table th, body .report-page .report-table td, body .report-page .report-table-wide th, body .report-page .report-table-wide td { padding: 2px 3px !important; font-size:8pt !important; line-height:1.3 !important; font-weight:400 !important; word-break:normal !important; overflow-wrap:break-word !important; }
    body .report-page .report-table th, body .report-page .report-table-wide th { white-space:normal !important; font-weight:700 !important; text-transform:none !important; letter-spacing:0 !important; }
    body .report-page .report-table td span, body .report-page .report-table-wide td span { font-size:inherit !important; padding:0 !important; }
    body .report-page .report-table thead, body .report-page .report-table-wide thead { display:table-header-group !important; }
    body .report-page .report-table tfoot, body .report-page .report-table-wide tfoot { display:table-row-group !important; }
    body .report-page .signature-block { margin-top:6mm !important; }
    body .report-page .signature-block .grid { display:grid !important; grid-template-columns:repeat(4,1fr) !important; }
    body .report-page .print-horizontal-layout { display:block !important; }
}
</style>
