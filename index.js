// TODO: make order buttons gray while form is incomplete

// GLOBAL VARIABLES

const inputs = {
    products: document.querySelectorAll(".product"),
    email: document.querySelector("#email"),
    street: document.querySelector("#street"),
    streetNumber: document.querySelector("#streetnumber"),
    city: document.querySelector("#city"),
    zipcode: document.querySelector("#zipcode"),
}

const errorPs = {
    products: document.querySelector(".error-msg.products"),
    email: document.querySelector(".error-msg.email"),
    street: document.querySelector(".error-msg.street"),
    streetNumber: document.querySelector(".error-msg.streetnumber"),
    city: document.querySelector(".error-msg.city"),
    zipcode: document.querySelector(".error-msg.zipcode"),
}


// FUNCTIONS

const isNum = val => /^\d+$/.test(val);


const removeErrorMsgs = () => {
    errorPs.products.textContent = "";
    errorPs.email.textContent = "";
    errorPs.street.textContent = "";
    errorPs.streetNumber.textContent = "";
    errorPs.city.textContent = "";
    errorPs.zipcode.textContent = "";
}


const formValidation = (event) => {
    const emailInput = inputs.email.value;
    const streetInput = inputs.street.value;
    const streetNumberInput = inputs.streetNumber.value;
    const cityInput = inputs.city.value;
    const zipcodeInput = inputs.zipcode.value;
    let errorEncountered = false;

    removeErrorMsgs();

    let productOrdered = false;
    for (const productInput of inputs.products) {
        if (isNum(productInput.value)
            && productInput.value > 0) {

            productOrdered = true;
        }
    }

    if (!productOrdered) {
        errorPs.products.textContent = "min. 1 order required";
        errorEncountered = true;
    }

    if (!emailInput) {
        errorEncountered = true;
        errorPs.email.textContent = "Field required";
    }
    if (!streetInput) {
        errorEncountered = true;
        errorPs.street.textContent = "Field required";
    }
    if (!streetNumberInput) {
        errorEncountered = true;
        errorPs.streetNumber.textContent = "Field required";
    }
    if (!cityInput) {
        errorEncountered = true;
        errorPs.city.textContent = "Field required";
    }
    if (!zipcodeInput) {
        errorEncountered = true;
        errorPs.zipcode.textContent = "Field required";
    } else if (!isNum(zipcodeInput)
        || zipcodeInput < 0
        || zipcodeInput > 999999) {

        errorEncountered = true;
        errorPs.zipcode.textContent = "Invalid input";
    }

    if (errorEncountered) {
        event.preventDefault();
    }
}


// EVENT LISTENERS

document.querySelector("form").addEventListener("submit", formValidation)
