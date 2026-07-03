/**
 * Client-side validatie voor Kniploket Tiko formulieren.
 */
(function () {
    'use strict';

    const POSTCODE_PATTERN = /^[1-9][0-9]{3}\s?[A-Za-z]{2}$/;
    const EMAIL_PATTERN = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const MOBIEL_MIN_DIGITS = 10;

    function normalizeValue(inputElement) {
        return inputElement.value.trim();
    }

    function setClientError(inputElement, message) {
        const existingError = inputElement.parentElement.querySelector('.client-error');

        if (existingError) {
            existingError.remove();
        }

        if (message) {
            inputElement.classList.add('input-error');
            const errorElement = document.createElement('div');
            errorElement.className = 'field-error client-error';
            errorElement.textContent = message;
            inputElement.parentElement.appendChild(errorElement);
            return false;
        }

        inputElement.classList.remove('input-error');
        return true;
    }

    function countDigits(phoneValue) {
        return (phoneValue.match(/\d/g) || []).length;
    }

    function validateKlantForm(formElement) {
        let isValid = true;
        const naamField = formElement.querySelector('#naam');
        const emailField = formElement.querySelector('#contact_email');
        const straatField = formElement.querySelector('#straatnaam');
        const huisnummerField = formElement.querySelector('#huisnummer');
        const postcodeField = formElement.querySelector('#postcode');
        const plaatsField = formElement.querySelector('#plaats');
        const mobielField = formElement.querySelector('#mobiel');

        if (!normalizeValue(naamField)) {
            isValid = setClientError(naamField, 'Naam is verplicht.') && isValid;
        } else {
            setClientError(naamField, '');
        }

        if (!normalizeValue(emailField)) {
            isValid = setClientError(emailField, 'Contact e-mail is verplicht.') && isValid;
        } else if (!EMAIL_PATTERN.test(normalizeValue(emailField))) {
            isValid = setClientError(emailField, 'Voer een geldig e-mailadres in.') && isValid;
        } else {
            setClientError(emailField, '');
        }

        if (!normalizeValue(straatField)) {
            isValid = setClientError(straatField, 'Straatnaam is verplicht.') && isValid;
        } else {
            setClientError(straatField, '');
        }

        if (!normalizeValue(huisnummerField)) {
            isValid = setClientError(huisnummerField, 'Huisnummer is verplicht.') && isValid;
        } else {
            setClientError(huisnummerField, '');
        }

        if (!normalizeValue(postcodeField)) {
            isValid = setClientError(postcodeField, 'Postcode is verplicht.') && isValid;
        } else if (!POSTCODE_PATTERN.test(normalizeValue(postcodeField))) {
            isValid = setClientError(postcodeField, 'Voer een geldige Nederlandse postcode in.') && isValid;
        } else {
            setClientError(postcodeField, '');
        }

        if (!normalizeValue(plaatsField)) {
            isValid = setClientError(plaatsField, 'Plaats is verplicht.') && isValid;
        } else {
            setClientError(plaatsField, '');
        }

        if (!normalizeValue(mobielField)) {
            isValid = setClientError(mobielField, 'Mobiel nummer is verplicht.') && isValid;
        } else if (countDigits(normalizeValue(mobielField)) < MOBIEL_MIN_DIGITS) {
            isValid = setClientError(mobielField, 'Voer een geldig mobiel nummer in.') && isValid;
        } else {
            setClientError(mobielField, '');
        }

        return isValid;
    }

    function validatePostcodeSearchForm(formElement) {
        const postcodeField = formElement.querySelector('#postcode');
        const postcodeValue = normalizeValue(postcodeField);

        if (postcodeValue === '') {
            setClientError(postcodeField, '');
            return true;
        }

        if (!POSTCODE_PATTERN.test(postcodeValue)) {
            setClientError(postcodeField, 'Voer een geldige Nederlandse postcode in.');
            return false;
        }

        setClientError(postcodeField, '');
        return true;
    }

    function berekenBestelproductTotaal(unitPrijs, aantal, korting, btwPercentage) {
        const subtotaal = (unitPrijs * aantal) - korting;
        const btw = subtotaal * (btwPercentage / 100);

        return subtotaal + btw;
    }

    function formatEuro(bedrag) {
        return '€ ' + bedrag.toFixed(2).replace('.', ',');
    }

    function updateBestelproductTotaal(formElement) {
        const aantalField = formElement.querySelector('#aantal');
        const totaalField = formElement.querySelector('#totaal');

        if (!aantalField || !totaalField) {
            return;
        }

        const unitPrijs = parseFloat(formElement.dataset.unitPrijs || '0');
        const korting = parseFloat(formElement.dataset.korting || '0');
        const btw = parseFloat(formElement.dataset.btw || '0');
        const aantal = parseInt(aantalField.value, 10) || 0;

        totaalField.value = formatEuro(berekenBestelproductTotaal(unitPrijs, aantal, korting, btw));
    }

    function validateBestelproductForm(formElement) {
        const aantalField = formElement.querySelector('#aantal');
        const aantalValue = parseInt(normalizeValue(aantalField), 10);
        let isValid = true;

        if (!normalizeValue(aantalField)) {
            isValid = setClientError(aantalField, 'Aantal is verplicht.') && isValid;
        } else if (Number.isNaN(aantalValue) || aantalValue < 1) {
            isValid = setClientError(aantalField, 'Aantal moet minimaal 1 zijn.') && isValid;
        } else {
            setClientError(aantalField, '');
        }

        return isValid;
    }

    document.addEventListener('DOMContentLoaded', function () {
        const klantForm = document.getElementById('klant-edit-form');
        if (klantForm) {
            klantForm.addEventListener('submit', function (event) {
                if (!validateKlantForm(klantForm)) {
                    event.preventDefault();
                }
            });
        }

        const searchForm = document.getElementById('postcode-search-form');
        if (searchForm) {
            searchForm.addEventListener('submit', function (event) {
                if (!validatePostcodeSearchForm(searchForm)) {
                    event.preventDefault();
                }
            });
        }

        const bestelproductForm = document.getElementById('bestelproduct-edit-form');
        if (bestelproductForm) {
            const aantalField = bestelproductForm.querySelector('#aantal');

            updateBestelproductTotaal(bestelproductForm);

            if (aantalField) {
                aantalField.addEventListener('input', function () {
                    updateBestelproductTotaal(bestelproductForm);
                });
            }

            bestelproductForm.addEventListener('submit', function (event) {
                if (!validateBestelproductForm(bestelproductForm)) {
                    event.preventDefault();
                }
            });
        }

        const loginForm = document.getElementById('login-form');
        if (loginForm) {
            loginForm.addEventListener('submit', function (event) {
                const emailField = loginForm.querySelector('#email');
                const passwordField = loginForm.querySelector('#password');
                let isValid = true;

                if (!emailField.value.trim()) {
                    setClientError(emailField, 'E-mailadres is verplicht.');
                    isValid = false;
                } else if (!EMAIL_PATTERN.test(emailField.value.trim())) {
                    setClientError(emailField, 'Voer een geldig e-mailadres in.');
                    isValid = false;
                } else {
                    setClientError(emailField, '');
                }

                if (!passwordField.value.trim()) {
                    setClientError(passwordField, 'Wachtwoord is verplicht.');
                    isValid = false;
                } else if (passwordField.value.trim().length < 6) {
                    setClientError(passwordField, 'Wachtwoord moet minimaal 6 tekens bevatten.');
                    isValid = false;
                } else {
                    setClientError(passwordField, '');
                }

                if (!isValid) {
                    event.preventDefault();
                }
            });
        }
    });
})();
