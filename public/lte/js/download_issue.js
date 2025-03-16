$(document).ready(function () {
    let selector = $('.downloadIssueAsPdf');
    if (selector.length) {
        selector.on('click', function (e) {
            let filename = $('.filename').val();
            if (filename === '') {
                alert('PDF can not be generated.');
                return;
            }
            downloadAsPDF(filename);
        });
    }
});

function downloadAsPDF(filename) {

    let options = {
        margin: 10,
        filename: filename + '.pdf',
        image: {type: 'jpeg', quality: 0.95},
        html2canvas: {scale: 2, dpi: 192, letterRendering: true, allowTaint: true},
        jsPDF: {unit: 'mm', format: 'a4', orientation: 'portrait', compressPDF: true},
        pagebreak: {mode: 'avoid-all', after: '.html2pdf__pagebreak'}
    };

    let element = document.getElementById('issueContent');
    let result = html2pdf().from(element).set(options).toPdf().get('pdf').then(function (pdf) {

        var totalPages = pdf.internal.getNumberOfPages();

        for (i = 1; i <= totalPages; i++) {
            pdf.setPage(i);
            pdf.setFontSize(9);
            pdf.setTextColor('#ddd');
            pdf.text('Seite ' + i + ' von ' + totalPages, pdf.internal.pageSize.getWidth() - 30, pdf.internal.pageSize.getHeight() - 10);
            pdf.text(filename, pdf.internal.pageSize.getWidth() - 194, pdf.internal.pageSize.getHeight() - 10);
        }
    });

    result.save();
}