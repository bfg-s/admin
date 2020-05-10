
$.validator.addMethod( "any-checked", ( value, element, param ) => {

    if (param === true) {

        return !!$(element).find(':checked').length;
    }

    return $(element).find(':checked').length === param;

}, (param) => {

    if (param === true) {

        return 'You must select at least one item.';
    }

    return `You must select at least ${param} items.`;
});

$.validator.addMethod( "confirmation", ( value, element, params ) => {

    let name = element.name,
        confirm_name = name.replace('_confirmation', '')
        confirm_element = document.querySelector(`[name="${confirm_name}"]`);

    if(confirm_name === name) {
        confirm_name = `${name}_confirmation`;
        confirm_element = document.querySelector(`[name="${confirm_name}"]`);
    }

    if (!confirm_element) {
        return false;
    }

    return element.value === confirm_element.value;

}, (params, element) => {

    let name = element.name,
        confirm_name = name.replace('_confirmation', '')
    confirm_element = document.querySelector(`[name="${confirm_name}"]`);

    if(confirm_name === name) {
        confirm_name = `${name}_confirmation`;
        confirm_element = document.querySelector(`[name="${confirm_name}"]`);
    }

    if (!confirm_element) {
        return false;
    }

    return `${element.placeholder} and ${confirm_element.placeholder} must be the same.`;
});