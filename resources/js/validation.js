/**
 * Client-side validatie voor Kniploket Tiko formulieren.
 */
(function () {
    'use strict';

    const POSTCODE_PATTERN = /^[1-9][0-9]{3}[A-Za-z]{2}$/;
    const EMAIL_PATTERN = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const STRAATNAAM_PATTERN = /^(?:[1-9]\d*e\s+)?[A-Za-zÀ-ÿ'][A-Za-zÀ-ÿ\s'\-\.]{1,147}$/u;
    const PLAATS_PATTERN = /^[A-Za-zÀ-ÿ][A-Za-zÀ-ÿ\s'\-\.]{1,98}$/u;
    const HUISNUMMER_PATTERN = /^\d{1,5}[a-zA-Z]{0,2}$/;
    const MOBIEL_PATTERN = /^(06[\s\-]?(\d[\s\-]?){8}|\+31[\s\-]?6[\s\-]?(\d[\s\-]?){8})$/;
    const FALLBACK_DUTCH_PLAATSEN = ['utrecht', 'amsterdam', 'rotterdam', 'den haag'];
    const FALLBACK_BLOCKED_WORDS = ['israel', 'israël', 'palestina', 'duitsland', 'frankrijk'];
    const FALLBACK_STRAAT_SUFFIXES = [
        'straat', 'steeg', 'laan', 'weg', 'gracht', 'singel', 'plein', 'pad', 'dreef',
        'boulevard', 'park', 'ring', 'dijk', 'kade', 'hof', 'veld', 'akker', 'dam', 'wal',
        'haven', 'markt', 'sluis', 'poort', 'buurt', 'gaard', 'tuin', 'brug', 'baan',
        'zoom', 'wetering', 'plantsoen',
    ];

    function normalizeValue(inputElement) {
        return inputElement.value.trim();
    }

    function normalizePostcode(postcodeValue) {
        return postcodeValue.trim().toUpperCase().replace(/\s+/g, '');
    }

    function getDutchPlaatsen() {
        const configuredPlaatsen = window.KNIPLOKET?.dutchPlaatsen;

        if (Array.isArray(configuredPlaatsen) && configuredPlaatsen.length > 0) {
            return configuredPlaatsen;
        }

        return FALLBACK_DUTCH_PLAATSEN;
    }

    function getStraatSuffixes() {
        const configuredSuffixes = window.KNIPLOKET?.straatSuffixes;

        if (Array.isArray(configuredSuffixes) && configuredSuffixes.length > 0) {
            return configuredSuffixes.map((suffix) => suffix.toLowerCase());
        }

        return FALLBACK_STRAAT_SUFFIXES;
    }

    function getBlockedAddressWords() {
        const configuredWords = window.KNIPLOKET?.blockedAddressWords;

        if (Array.isArray(configuredWords) && configuredWords.length > 0) {
            return configuredWords.map((word) => word.toLowerCase());
        }

        return FALLBACK_BLOCKED_WORDS;
    }

    function isBlockedAddressWord(value) {
        return getBlockedAddressWords().includes(value.toLowerCase());
    }

    function containsInvalidStraatnaamDigits(straatnaamValue) {
        if (!/\d/u.test(straatnaamValue)) {
            return false;
        }

        if (/^\d+e\s+.+/u.test(straatnaamValue)) {
            const naamZonderOrdinal = straatnaamValue.replace(/^\d+e\s+/u, '');

            return /\d/u.test(naamZonderOrdinal);
        }

        return true;
    }

    function containsRepeatedCharacters(straatnaamValue) {
        return /(.)\1{2,}/u.test(straatnaamValue);
    }

    function hasValidDutchStreetEnding(straatnaamValue) {
        const normalized = straatnaamValue.toLowerCase().trim();

        if (/^laan van [\p{L}\s'\-\.]+$/u.test(normalized)) {
            return true;
        }

        return getStraatSuffixes().some((suffix) => normalized.endsWith(suffix));
    }

    function isValidDutchStraatnaam(straatnaamValue) {
        if (!STRAATNAAM_PATTERN.test(straatnaamValue)) {
            return false;
        }

        if (containsInvalidStraatnaamDigits(straatnaamValue)) {
            return false;
        }

        if (containsRepeatedCharacters(straatnaamValue)) {
            return false;
        }

        if (!hasValidDutchStreetEnding(straatnaamValue)) {
            return false;
        }

        return !isBlockedAddressWord(straatnaamValue);
    }

    function isValidDutchPlaats(plaatsValue) {
        if (!PLAATS_PATTERN.test(plaatsValue)) {
            return false;
        }

        if (isBlockedAddressWord(plaatsValue)) {
            return false;
        }

        return getDutchPlaatsen().includes(plaatsValue.toLowerCase());
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

    function validateDutchMobiel(mobielValue) {
        return MOBIEL_PATTERN.test(mobielValue);
    }

    function formatDateYmd(dateObject) {
        const year = dateObject.getFullYear();
        const month = String(dateObject.getMonth() + 1).padStart(2, '0');
        const day = String(dateObject.getDate()).padStart(2, '0');

        return `${year}-${month}-${day}`;
    }

    function isValidMedewerkerGeboortedatum(dateValue) {
        const minAge = window.KNIPLOKET?.medewerkerMinAge ?? 15;
        const maxAge = window.KNIPLOKET?.medewerkerMaxAge ?? 100;
        const today = new Date();
        const youngestAllowed = new Date(today);
        youngestAllowed.setFullYear(youngestAllowed.getFullYear() - minAge);
        const oldestAllowed = new Date(today);
        oldestAllowed.setFullYear(oldestAllowed.getFullYear() - maxAge);

        const maxDate = formatDateYmd(youngestAllowed);
        const minDate = formatDateYmd(oldestAllowed);

        return dateValue >= minDate && dateValue <= maxDate;
    }

    function validateDutchAddressFields(formElement, isValid) {
        const straatField = formElement.querySelector('#straatnaam');
        const huisnummerField = formElement.querySelector('#huisnummer');
        const postcodeField = formElement.querySelector('#postcode');
        const plaatsField = formElement.querySelector('#plaats');
        const mobielField = formElement.querySelector('#mobiel');

        const straatnaamValue = normalizeValue(straatField);
        if (!straatnaamValue) {
            isValid = setClientError(straatField, 'Straatnaam is verplicht.') && isValid;
        } else if (!isValidDutchStraatnaam(straatnaamValue)) {
            isValid = setClientError(straatField, 'Voer een geldige Nederlandse straatnaam in (bijv. Oudegracht of Winkel van Sinkelstraat).') && isValid;
        } else {
            setClientError(straatField, '');
        }

        if (!normalizeValue(huisnummerField)) {
            isValid = setClientError(huisnummerField, 'Huisnummer is verplicht.') && isValid;
        } else if (!HUISNUMMER_PATTERN.test(normalizeValue(huisnummerField))) {
            isValid = setClientError(huisnummerField, 'Voer een geldig huisnummer in (bijv. 88 of 12A).') && isValid;
        } else {
            setClientError(huisnummerField, '');
        }

        const normalizedPostcode = normalizePostcode(normalizeValue(postcodeField));
        if (!normalizedPostcode) {
            isValid = setClientError(postcodeField, 'Postcode is verplicht.') && isValid;
        } else if (!POSTCODE_PATTERN.test(normalizedPostcode)) {
            isValid = setClientError(postcodeField, 'Voer een geldige Nederlandse postcode in (bijv. 3512AB).') && isValid;
        } else {
            setClientError(postcodeField, '');
        }

        const plaatsValue = normalizeValue(plaatsField);
        if (!plaatsValue) {
            isValid = setClientError(plaatsField, 'Plaats is verplicht.') && isValid;
        } else if (!isValidDutchPlaats(plaatsValue)) {
            isValid = setClientError(plaatsField, 'Voer een geldige Nederlandse plaatsnaam in (bijv. Utrecht).') && isValid;
        } else {
            setClientError(plaatsField, '');
        }

        if (!normalizeValue(mobielField)) {
            isValid = setClientError(mobielField, 'Mobiel nummer is verplicht.') && isValid;
        } else if (!validateDutchMobiel(normalizeValue(mobielField))) {
            isValid = setClientError(
                mobielField,
                'Voer een geldig Nederlands mobiel nummer in (bijv. 06XXXXXXXX of +31 6 XXXXXXXX).'
            ) && isValid;
        } else {
            setClientError(mobielField, '');
        }

        return isValid;
    }

    function validateKlantForm(formElement) {
        let isValid = true;
        const naamField = formElement.querySelector('#naam');
        const emailField = formElement.querySelector('#contact_email');

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

        return validateDutchAddressFields(formElement, isValid);
    }

    function validateMedewerkerForm(formElement) {
        let isValid = true;
        const voornaamField = formElement.querySelector('#voornaam');
        const achternaamField = formElement.querySelector('#achternaam');
        const emailField = formElement.querySelector('#contact_email');
        const specialisatieField = formElement.querySelector('#specialisatie');
        const geboortedatumField = formElement.querySelector('#geboortedatum');

        if (!normalizeValue(voornaamField)) {
            isValid = setClientError(voornaamField, 'Voornaam is verplicht.') && isValid;
        } else {
            setClientError(voornaamField, '');
        }

        if (!normalizeValue(achternaamField)) {
            isValid = setClientError(achternaamField, 'Achternaam is verplicht.') && isValid;
        } else {
            setClientError(achternaamField, '');
        }

        if (!normalizeValue(emailField)) {
            isValid = setClientError(emailField, 'Contact e-mail is verplicht.') && isValid;
        } else if (!EMAIL_PATTERN.test(normalizeValue(emailField))) {
            isValid = setClientError(emailField, 'Voer een geldig e-mailadres in.') && isValid;
        } else {
            setClientError(emailField, '');
        }

        if (!normalizeValue(specialisatieField)) {
            isValid = setClientError(specialisatieField, 'Specialisatie is verplicht.') && isValid;
        } else {
            setClientError(specialisatieField, '');
        }

        if (!normalizeValue(geboortedatumField)) {
            isValid = setClientError(geboortedatumField, 'Geboortedatum is verplicht.') && isValid;
        } else if (!isValidMedewerkerGeboortedatum(normalizeValue(geboortedatumField))) {
            isValid = setClientError(
                geboortedatumField,
                'Voer een realistische geboortedatum in (medewerker moet tussen 15 en 100 jaar oud zijn).'
            ) && isValid;
        } else {
            setClientError(geboortedatumField, '');
        }

        return validateDutchAddressFields(formElement, isValid);
    }

    function validatePostcodeSearchForm(formElement) {
        const postcodeField = formElement.querySelector('#postcode');
        const postcodeValue = normalizeValue(postcodeField);

        if (postcodeValue === '') {
            setClientError(postcodeField, '');
            return true;
        }

        const normalizedPostcode = normalizePostcode(postcodeValue);
        if (!POSTCODE_PATTERN.test(normalizedPostcode)) {
            setClientError(postcodeField, 'Voer een geldige Nederlandse postcode in (bijv. 3512AB).');
            return false;
        }

        setClientError(postcodeField, '');
        return true;
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
            const editPostcodeField = klantForm.querySelector('#postcode');
            if (editPostcodeField) {
                editPostcodeField.addEventListener('blur', function () {
                    editPostcodeField.value = normalizePostcode(editPostcodeField.value);
                });
            }

            klantForm.addEventListener('submit', function (event) {
                if (!validateKlantForm(klantForm)) {
                    event.preventDefault();
                }
            });
        }

        const medewerkerForm = document.getElementById('medewerker-edit-form');
        if (medewerkerForm) {
            const editPostcodeField = medewerkerForm.querySelector('#postcode');
            if (editPostcodeField) {
                editPostcodeField.addEventListener('blur', function () {
                    editPostcodeField.value = normalizePostcode(editPostcodeField.value);
                });
            }

            medewerkerForm.addEventListener('submit', function (event) {
                if (!validateMedewerkerForm(medewerkerForm)) {
                    event.preventDefault();
                }
            });
        }

        const searchForm = document.getElementById('postcode-search-form');
        if (searchForm) {
            const searchPostcodeField = searchForm.querySelector('#postcode');
            if (searchPostcodeField) {
                searchPostcodeField.addEventListener('blur', function () {
                    searchPostcodeField.value = normalizePostcode(searchPostcodeField.value);
                });
            }

            searchForm.addEventListener('submit', function (event) {
                if (!validatePostcodeSearchForm(searchForm)) {
                    event.preventDefault();
                }
            });
        }

        const bestelproductForm = document.getElementById('bestelproduct-edit-form');
        if (bestelproductForm) {
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
