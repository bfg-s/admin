window.libs['tpl::clearArea'] = function ($to) {
    let area = document.querySelector(`span[data-tpl="${$to}"]`);

    if (area) {

        area.innerHTML = '';
    } else {

        exec("toast:error", `Template area [${$to}] undefined!`);
    }
};

window.libs['tpl::replaceTo'] = function ($tpl, $to = $tpl) {
    window.libs['tpl::copyTo']($tpl, $to, (area) => {
        area.innerHTML = '';
    })
};

window.libs['tpl::copyTo'] = function ($tpl, $to = $tpl, $before = null) {
    let template = document.querySelector(`template[data-tpl="${$tpl}"]`);

    if (template) {

        let area = document.querySelector(`span[data-tpl="${$to}"]`);

        if (area) {

            let cloned = template.content.cloneNode(true);
            let scripts = cloned.querySelectorAll('script');
            if (scripts.length) {
                let pull = (func) => {
                    return typeof func === 'function' ? func(cloned) : () => "";
                };
                var call_pull = "";
                Object.values(scripts).map((script) => {
                    let tpl_script = script.innerText.trim();
                    if (!/^pull.*/.test(tpl_script)) {
                        tpl_script = "pull(function () {" + tpl_script + "});";
                    }
                    tpl_script = "call_pull = " + tpl_script + ";"
                    eval(tpl_script);
                    if (call_pull) {
                        let temp_element = document.createElement('span');
                        temp_element.innerHTML = call_pull;
                        script.replaceWith(...temp_element.children);
                        temp_element = null;
                    } else {
                        script.remove();
                    }
                });
            }

            if ($before) {

                $before(area, cloned)
            }
            area.appendChild(cloned);
        } else {

            exec("toast:error", `Template area [${$to}] undefined!`);
        }
    } else {

        exec("toast:error", `Template [${$tpl}] undefined!`);
    }
};

window.libs['tpl::get_tpl'] = function ($tpl) {
    let template = document.querySelector(`template[data-tpl="${$tpl}"]`);

    if (template) {

        return template.content.cloneNode(true);
    }

    return "";
};
