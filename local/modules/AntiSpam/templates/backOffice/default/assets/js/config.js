window.addEventListener('load', () => {

    const formFillDurationField = document.querySelector('#form_fill_duration');
    const formFillDurationLimitGroup = document.querySelector('#form_fill_duration_group');

    if (!formFillDurationField.checked) {
        formFillDurationLimitGroup.classList.add('d-none');
    }

    formFillDurationField.addEventListener('change', () => {
        if (formFillDurationField.checked) {
            formFillDurationLimitGroup.classList.remove('d-none');
        } else {
            formFillDurationLimitGroup.classList.add('d-none');
        }
    });
});
