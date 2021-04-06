// Manage Countries and States form
class AdressState {

    constructor(element) {
        this.stateSelect = element;
        this.stateId = this.stateSelect.value;
        this.countrySelect = document.querySelector(this.stateSelect.dataset.theliaCountry);
        this.stateBlock = document.querySelector(this.stateSelect.dataset.theliaToggle);
        this.stateOptions = Object.assign({}, this.stateSelect.children);
    }

    initialize() {
        this.countrySelect.addEventListener('change', () => this.updateState());
        this.updateState();
    }

    updateState() {
        const countryId = this.countrySelect.value;
        const stateId = this.stateSelect.value;
        let hasStates = false;

        if (stateId !== null && stateId !== '') {
            this.stateId = stateId;
        }

        Array.from(this.stateSelect.children).forEach((e) => e.remove());

        for (let key in this.stateOptions) {
            if (this.stateOptions[key].dataset.country == countryId) {
                this.stateSelect.append(this.stateOptions[key]);
                hasStates = true;
            }
        };

        if (hasStates) {
            // try to select the last state
            this.stateSelect.value = this.stateId;
            this.stateBlock.classList.remove('hidden');
        } else {
            this.stateBlock.classList.add('hidden');
        }
    }
}

document.querySelectorAll("[data-thelia-state]").forEach((e) => {
    const adressState = new AdressState(e);
    adressState.initialize();
})