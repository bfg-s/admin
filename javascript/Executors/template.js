module.exports = class extends Executor {

    static __name() {

        return "tpl";
    }

    clearArea($to) {

        let area = document.querySelector(`span[data-tpl="${$to}"]`);

        if (area) {

            area.innerHTML = '';
        } else {

            "toast:error".exec(`Template area [${$to}] undefined!`);
        }
    }

    replaceTo($tpl, $to = $tpl) {

        let template = document.querySelector(`template[data-tpl="${$tpl}"]`);

        if (template) {

            let area = document.querySelector(`span[data-tpl="${$to}"]`);

            if (area) {

                let cloned = template.content.cloneNode(true);

                area.innerHTML = '';
                area.appendChild(cloned);
            } else {

                "toast:error".exec(`Template area [${$to}] undefined!`);
            }
        } else {

            "toast:error".exec(`Template [${$tpl}] undefined!`);
        }
    }

    copyTo($tpl, $to = $tpl) {

        let template = document.querySelector(`template[data-tpl="${$tpl}"]`);

        if (template) {

            let area = document.querySelector(`span[data-tpl="${$to}"]`);

            if (area) {

                let cloned = template.content.cloneNode(true);

                area.appendChild(cloned);
            } else {

                "toast:error".exec(`Template area [${$to}] undefined!`);
            }
        } else {

            "toast:error".exec(`Template [${$tpl}] undefined!`);
        }
    }
};
