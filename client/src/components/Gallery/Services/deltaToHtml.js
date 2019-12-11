import { QuillDeltaToHtmlConverter } from 'quill-delta-to-html';

export default class DeltaToHtml {
    constructor(delta, content = null) {
        this.delta = delta;
        this.condent = content;
    }

    deltaToHtml() {
        let quillHTML;
        try {
            this.content = JSON.parse(this.delta);
            const cfg = {
                inlineStyles: true,
                allowBackgroundClasses: true,
            };
            const converter = new QuillDeltaToHtmlConverter(this.content.ops, cfg);
            quillHTML = converter.convert();
        } catch (e) {
            quillHTML = this.delta;
        }
        return quillHTML;
    }
}
