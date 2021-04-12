class BootstrapEditable {

    constructor(element, options) {
        this.element = element;
        this.defaultOptions = {
            placement: 'left',
            html: true,
            sanitize: false,    //by default, popover html content doesn't accept <form> tag
            content:
                '<div class="editableform-loading" style="display: none;"></div>'
                + '<form class="form-inline editableform" action="' + options.url + '">'
                    + '<div class="form-group">'
                        + '<div class="editable-input" style="position: relative;">'
                            + '<input type="text" name="' + options.positionInputName + '" class="form-control input-mini" style="padding-right: 24px;">'
                            + '<input type="hidden" name="' + options.idInputName + '" value="' + options.idInputValue + '">'
                            + this.additionalInputsHtml(options.additionalInputs)
                            + '<span class="editable-clear-x"></span>'
                        + '</div>'
                        + '<button type="submit" class="btn btn-primary btn-sm editable-submit"><i class="fas fa-check"></i></button>'
                    + '<button type="button" class="btn btn-secondary btn-sm editable-cancel"><i class="fas fa-times"></i></button>'
                    + '</div>'

                    + '<div class="editable-error-block help-block" style="display: none;"></div>'
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
                }
            });
        })
    }
}