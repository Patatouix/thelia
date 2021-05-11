$(function() {
    document.querySelector('#question_refresh').addEventListener('click', function() {
        $('#question-label-container').load(this.dataset.href + ' #question-label-content');
    });
});