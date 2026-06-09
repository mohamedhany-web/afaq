<script>
function printReport() { window.print(); }
window.addEventListener('beforeprint', () => document.body.classList.add('printing-report'));
window.addEventListener('afterprint', () => document.body.classList.remove('printing-report'));
</script>
<style>
@media print {
    @page { size: A4 portrait; margin: 10mm 8mm; }
    html, body {
        margin: 0 !important; padding: 0 !important;
        background: #fff !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    body.printing-report #sidebar,
    body.printing-report .sidebar-overlay,
    body.printing-report .app-top-header,
    body.printing-report .no-print {
        display: none !important;
    }
    body.printing-report main,
    body.printing-report main > div {
        padding: 0 !important; margin: 0 !important;
        max-width: none !important; background: #fff !important;
    }
    body.printing-report #report-document {
        box-shadow: none !important;
        border: none !important;
        border-radius: 0 !important;
    }
    body.printing-report .report-print-header { display: block !important; }
    body.printing-report table thead { display: table-header-group; }
    body.printing-report tr { break-inside: avoid; page-break-inside: avoid; }
}
.report-print-header { display: none; }
</style>
