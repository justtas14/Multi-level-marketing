import constants from '../constants/constants';
import Parameters from '../../../../parameters';
import defaultFile from '../../../../public/img/defaultFile.png';
import wordImage from '../../../../public/img/word.png';
import pdfImage from '../../../../public/img/pdf.png';
import excelImage from '../../../../public/img/excel.png';

export default class FileSrc {
    constructor(fileName, filePath) {
        this.fileName = fileName;
        this.filePath = filePath;
        this.fileExtension = this.getFileExtension();
    }

    determineSrc() {
        if (this.isImage()) {
            return `${Parameters.API_HOST_URL}${this.filePath}`;
        } if (this.isPDF()) {
            return pdfImage;
        } if (this.isDOCX()) {
            return wordImage;
        } if (this.isXLSX()) {
            return excelImage;
        }
        return defaultFile;
    }

    reverse(str) {
        return str.split('').reverse().join('');
    }

    isImage() {
        return constants.imageExtensions.includes(this.fileExtension);
    }

    isPDF() {
        return this.fileExtension === 'pdf';
    }

    isDOCX() {
        return this.fileExtension === 'docx';
    }

    isXLSX() {
        return this.fileExtension === 'xlsx';
    }

    getFileExtension() {
        let originalNameTemp = this.fileName;
        originalNameTemp = this.reverse(originalNameTemp);
        let fileExtension = this.reverse(originalNameTemp.substr(0, originalNameTemp.indexOf('.')));
        fileExtension = fileExtension.toLowerCase();
        return fileExtension;
    }
}
