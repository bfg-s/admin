
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