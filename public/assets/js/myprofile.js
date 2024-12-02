function saveAsPDF() {
    const doc = new jsPDF();

    doc.setFontSize(20);
    const title = translations.title;
    const titleWidth = doc.getStringUnitWidth(title) * doc.internal.getFontSize() / doc.internal.scaleFactor;
    const x = (doc.internal.pageSize.getWidth() - titleWidth) / 2;
    doc.text(title, x, 20);

    const codes = document.querySelectorAll('.list-group-item label');
    let y = 30;

    codes.forEach(function (code) {
        const text = code.innerText.trim();
        doc.text(text, 10, y);
        y += 10;
    });

    doc.save('recovery_codes.pdf');
}

function printCodes() {
    const title = translations.title;
    const content = document.getElementById("contentToPrint").innerHTML;
    const printWindow = window.open('', '_blank');
    printWindow.document.open();
    printWindow.document.write(`
        <html>
            <head>
                <title>${title}</title>
            </head>
            <body>
                ${content}
            </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.print();
    printWindow.close();
}

function copyAllCodes() {
    const codes = document.querySelectorAll('.list-group-item label');
    const codesText = '';
    codes.forEach(function (code) {
        codesText += code.innerText.trim() + '\n'
    });
    codesText = codesText.trim();
    navigator.clipboard.writeText(codesText).then(function () {
        alert("{{ __('labels.texts.copied_codes') }}");
    }, function (err) {
        console.error(err);
    });
}
