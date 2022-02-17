// TODO: make order buttons gray while form is incomplete
const productInputFields = document.querySelectorAll(".product");
const emailInputField = document.querySelector("#email");
const streetInputField = document.querySelector("#street");
const streetNumberInputField = document.querySelector("#streetnumber");
const cityInputField = document.querySelector("#city");
const zipcodeInputField = document.querySelector("#zipcode");

const productsErrorMsg = document.querySelector(".error-msg.products");
const emailErrorMsg = document.querySelector(".error-msg.email");
const streetErrorMsg = document.querySelector(".error-msg.street");
const streetNumberErrorMsg = document.querySelector(".error-msg.streetnumber");
const cityErrorMsg = document.querySelector(".error-msg.city");
const zipcodeErrorMsg = document.querySelector(".error-msg.zipcode");

const isNum = val => /^\d+$/.test(val);

const form = document.querySelector("form");
form.addEventListener("submit", event => {
    const emailInput = emailInputField.value;
    const streetInput = streetInputField.value;
    const streetNumberInput = streetNumberInputField.value;
    const cityInput = cityInputField.value;
    const zipcodeInput = zipcodeInputField.value;
    let errorEncountered = false;

    zipcodeErrorMsg.textContent = "";
    cityErrorMsg.textContent = "";
    streetNumberErrorMsg.textContent = "";
    streetErrorMsg.textContent = "";
    emailErrorMsg.textContent = "";

    let productOrdered = false;
    for (const productInput of productInputFields) {
        if (isNum(productInput.value)
            && productInput.value > 0) {

            productOrdered = true;
        }
    }

    if (!productOrdered) {
        productsErrorMsg.textContent = "min. 1 order required";
        errorEncountered = true;
    }

    if (!emailInput) {
        errorEncountered = true;
        emailErrorMsg.textContent = "Field required";
    }
    if (!streetInput) {
        errorEncountered = true;
        streetErrorMsg.textContent = "Field required";
    }
    if (!streetNumberInput) {
        errorEncountered = true;
        streetNumberErrorMsg.textContent = "Field required";
    }
    if (!cityInput) {
        errorEncountered = true;
        cityErrorMsg.textContent = "Field required";
    }
    if (!zipcodeInput) {
        errorEncountered = true;
        zipcodeErrorMsg.textContent = "Field required";
    } else if (!isNum(zipcodeInput)
        || zipcodeInput < 0
        || zipcodeInput > 999999) {

        errorEncountered = true;
        zipcodeErrorMsg.textContent = "Invalid input";
    }

    if (errorEncountered) {
        event.preventDefault();
    }
})
