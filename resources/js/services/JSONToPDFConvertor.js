import html2canvas from 'html2canvas';
import jsPDF from 'jspdf';

function HTMLToPDFConvertor(elementId, fileName) {
    // Get the DOM element to be converted to PDF
    var element = document.getElementById(elementId);

    // Use html2canvas to capture the DOM element as an image
    html2canvas(element).then(function(canvas) {
        var imgData = canvas.toDataURL('image/png');

        // Calculate dimensions for the PDF
        var imgWidth = 210; // A4 size width in mm
        var pageHeight = 297; // A4 size height in mm
        var imgHeight = (canvas.height * imgWidth) / canvas.width;
        var heightLeft = imgHeight;

        // Initialize a new jsPDF instance
        var doc = new jsPDF('p', 'mm');

        // Add the captured image to the PDF
        var position = 0;

        doc.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
        heightLeft -= pageHeight;

        // Add more pages if needed
        while (heightLeft >= 0) {
            position = heightLeft - imgHeight;
            doc.addPage();
            doc.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
            heightLeft -= pageHeight;
        }

        // Save the PDF
        doc.save(fileName);
    });
}

export default HTMLToPDFConvertor;
