/**
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * You must not modify, adapt or create derivative works of this source code
 *
 *  @author    Muesli tech team
 *  @copyright 2023 Muesli SASU
 *  @license   GNU General Public License version 3
 */

const aplaceAutocompleteManager = () => {
    const inputNames = {};

    const getValidInputNameInPage = (inputNames) => {
        for (const inputName of inputNames) {
            if (inputName?.length > 0) {
                const inputExists = document.querySelector('[name="' + inputName + '"]');
                if (inputExists) {
                    return inputName;
                }
            }
        };
        return null;
    }
    // address
    const addressInputName = getValidInputNameInPage(aplace_autocomplete_field_address.split(','));
    if (addressInputName) inputNames[addressInputName] = { 'type': 'address' };
    // city
    const cityInputName = getValidInputNameInPage(aplace_autocomplete_field_city.split(','));
    if (cityInputName) inputNames[cityInputName] = { 'type': 'city' };
    // postcode
    const postcodeInputName = getValidInputNameInPage(aplace_autocomplete_field_postcode.split(','));
    if (postcodeInputName) inputNames[postcodeInputName] = { 'type': 'postcode' };
    if (APlaceAutocomplete) {
        new APlaceAutocomplete({
            autoFill: true,
            useIcons: false,
            blinkWhenFilled: true,
            useDefaultStyle: false,
            inputNames,
            countries: aplace_countries_data?.filter((c) => { return c.active === '1' }).map(c => c.iso_code.toLowerCase())?.join(','),
            resultSelectedCallback: (result) => {
                const selectCountry = document.querySelector('[name="' + aplace_autocomplete_field_country + '"]');
                if (selectCountry) {
                    if (result?.address?.country_code) {
                        const country = aplace_countries_data?.find((country) => country.iso_code === result.address.country_code);
                        if (country) {
                            selectCountry.value = country.id_country;
                        }
                    }
                }
                const selectState = document.querySelector('[name="' + aplace_autocomplete_field_state + '"]');
                if (selectState) {
                    if (result?.address?.state_code) {
                        const state = aplace_states_data?.find((state) => state.name === result.address.state);
                        if (state) {
                            selectState.value = state.id_state;
                        }
                    }
                }
            }
        });
    }
}

$(document).ready(() => {
    aplaceAutocompleteManager();
});

if (typeof prestashop !== 'undefined') {
    prestashop.on('updatedAddressForm', () => {
        aplaceAutocompleteManager();
    });
}