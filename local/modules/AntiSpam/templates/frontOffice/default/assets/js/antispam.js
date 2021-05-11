$(function() {
    // refresh question label
    document.querySelector('#question_refresh').addEventListener('click', function() {
        $('#question-label-container').load(this.dataset.href + ' #question-label-content');
    });
});

// Apply validation
$('#form-contact').validate({
    errorPlacement: function(error, element) {
        if (element.is('#questionAnswer')) {
            // change default location of error message, because it causes conflict with input-group-addon
            error.appendTo(element.parents('div.form-group.group-question'));
        } else {
            // default behavior for other fields
            error.insertAfter(element);
        }
    },
    // same code as in thelia.js
    highlight: function (element) {
        $(element).closest('.form-group').addClass('has-error');
    },
    unhighlight: function (element) {
        $(element).closest('.form-group').removeClass('has-error');
    },
    errorElement: 'span',
    errorClass: 'help-block'
});