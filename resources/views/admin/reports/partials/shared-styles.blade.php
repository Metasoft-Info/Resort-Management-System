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

@media print {
    .report-table,
    .report-table-wide {
        min-width: 100% !important;
        width: 100% !important;
        table-layout: fixed;
    }

    .report-table th,
    .report-table td,
    .report-table-wide th,
    .report-table-wide td {
        white-space: normal !important;
        word-break: break-word;
    }
}
</style>
