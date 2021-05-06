let loadTime;
let submitTime;
// send to the server the duration between page loading and form submission
window.addEventListener('load', () => {
    loadTime = new Date().getTime();
    document.querySelector('#form-contact').addEventListener('submit', (e) => {
        submitTime = new Date().getTime();
        document.querySelector('#form-contact .form_filling_duration').value = submitTime - loadTime;
    });
});
