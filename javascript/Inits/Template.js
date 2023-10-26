window.libs['tpl::clearArea'] = function ($to) {
    let area = document.querySelector(`span[data-tpl="${$to}"]`);

    if (area) {

        area.innerHTML = '';
    } else {

        exec("toast::error", `Template area [${$to}] undefined!`);
    }
};

window.libs['tpl::replaceTo'] = function ($tpl, $to = $tpl) {
    let template = document.querySelector(`template[data-tpl="${$tpl}"]`);

    if (template) {

        let area = document.querySelector(`span[data-tpl="${$to}"]`);

        if (area) {

            let cloned = template.content.cloneNode(true);

            area.innerHTML = '';
            area.appendChild(cloned);
        } else {

            exec("toast::error", `Template area [${$to}] undefined!`);
        }
    } else {

        exec("toast::error", `Template [${$tpl}] undefined!`);
    }
};

window.libs['tpl::copyTo'] = function ($tpl, $to = $tpl) {
    let template = document.querySelector(`template[data-tpl="${$tpl}"]`);

    if (template) {

        let area = document.querySelector(`span[data-tpl="${$to}"]`);

        if (area) {

            let cloned = template.content.cloneNode(true);

            area.appendChild(cloned);
        } else {

            exec("toast::error", `Template area [${$to}] undefined!`);
        }
    } else {

        exec("toast::error", `Template [${$tpl}] undefined!`);
    }
};
