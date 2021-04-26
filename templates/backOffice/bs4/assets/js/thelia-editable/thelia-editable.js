class TheliaEditable {

    constructor(element, options) {
        this.element = element;
        this.defaultOptions = {
            placement: 'left',
            html: true,
            sanitize: false,    //by default, popover html content doesn't accept <form> tag
            content:
                '<form action="' + options.url + '" class="form-inline">'
                    + '<div class="input-group">'
                        + '<input type="text" name="' + options.positionInputName + '" class="form-control">'
                        + '<span class="editable-clear-x"></span>'
                        + '<div class="input-group-append">'
                            + '<button type="submit" class="btn btn-primary"><i class="fas fa-check fa-fw"></i></button>'
                            + '<button type="button" class="btn btn-secondary editable-cancel"><i class="fas fa-times fa-fw"></i></button>'
                        + '</div>'
                    + '<input type="hidden" name="' + options.idInputName + '" value="' + options.idInputValue + '">'
                    + this.additionalInputsHtml(options.additionalInputs)
                + '</form>'
        }
        this.options = Object.assign(this.defaultOptions, options);
    }

    additionalInputsHtml(additionalInputs) {
        let html = '';
        for (let key in additionalInputs) {
            html += '<input type="hidden" name="' + key + '" value="' + additionalInputs[key] + '">';
        }
        return html;
    }

    initPopover() {
        //instanciate popover
        const popover = $(this.element).popover(this.options);

        popover.on('shown.bs.popover', () => {
            const popoverHtml = popover.data('bs.popover').tip;
            $('html').on('click', (event) => {
                //check if click must dismiss the popover
                if (event.target !== this.element && (
                    event.target === popoverHtml.querySelector('button.editable-cancel')
                    || event.target === popoverHtml.querySelector('button.editable-cancel i')
                    || !$.contains(popoverHtml, event.target)
                )) {
                    popover.popover('hide');
                    $('html').off(event);
                // input content clearing
                } else if (event.target === popoverHtml.querySelector('.editable-clear-x')) {
                    popoverHtml.querySelector('input').value = '';
                }
            });
        })
    }
}