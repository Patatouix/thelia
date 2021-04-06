/*
*   TypeWatch 2.2
*
*   Examples/Docs: github.com/dennyferra/TypeWatch
*
*   Dual licensed under the MIT and GPL licenses:
*   http://www.opensource.org/licenses/mit-license.php
*   http://www.gnu.org/licenses/gpl.html
*
*   Version with no jQuery
*/

class TypeWatch {

    constructor(options) {
        this.supportedInputTypes = [
            'TEXT', 'TEXTAREA',
            'PASSWORD', 'TEL',
            'SEARCH', 'URL',
            'EMAIL', 'DATETIME',
            'DATE', 'MONTH',
            'WEEK', 'TIME',
            'DATETIME-LOCAL',
            'NUMBER', 'RANGE'
        ];
        this.options = Object.assign({
            wait: 750,
            callback: function() { },
            highlight: true,
            captureLength: 2,
            inputTypes: this.supportedInputTypes
        }, options);
    }

    watchElements(elements) {
        // Watch Each Element
        elements.forEach((e) => {
            this.watchElement(e);
        });
    }

    watchElement(element) {
        const elementType = element.type.toUpperCase();
        if (this.options.inputTypes.indexOf(elementType) >= 0) {

            // Allocate timer element
            let timer = {
                timer: null,
                text: element.value.toUpperCase(),
                cb: this.options.callback,
                el: element,
                wait: this.options.wait
            };

            // Set focus action (highlight)
            if (this.options.highlight) {
                element.addEventListener('focus', (e) => e.target.select());
            }

            // Key watcher / clear and reset the timer
            const startWatch = function(event) {
                let timerWait = timer.wait;
                let overrideBool = false;
                const evtElementType = event.target.type.toUpperCase();

                // If enter key is pressed and not a TEXTAREA and matched inputTypes
                if (typeof event.keyCode != 'undefined' && event.keyCode == 13 && evtElementType != 'TEXTAREA'
                    && this.options.inputTypes.indexOf(evtElementType) >= 0)
                {
                    timerWait = 1;
                    overrideBool = true;
                }

                // Clear timer
                clearTimeout(timer.timer);
                timer.timer = setTimeout(() => this.checkElement(timer, overrideBool), timerWait);

            }.bind(this);

            element.addEventListener('keydown', startWatch);
            element.addEventListener('paste', startWatch);
            element.addEventListener('cut', startWatch);
            element.addEventListener('input', startWatch);
        }
    }

    checkElement(timer, override) {
        const value = timer.el.value;

        // Fire if text >= options.captureLength AND text != saved text OR if override AND text >= options.captureLength
        if ((value.length >= this.options.captureLength && value.toUpperCase() != timer.text)
            || (override && value.length >= this.options.captureLength))
        {
            timer.text = value.toUpperCase();
            timer.cb.call(timer.el, value);
        }
    };
}