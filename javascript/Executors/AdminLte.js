'use strict';

class AdminLte extends Executor {

    static __name() {

        return "lte";
    }

    flash_document(changed_name = null, changed_value = null) {
        let data = $(":input").serializeArray();
        let lives = $('.__live__');
        if (lives[0]) {
            lives.each((i, o) => data.push({name: '_areas[]', value: o.getAttribute('id')}));
            data.map(({name}, key) => {
                if (changed_name && name === changed_name) data[key] = {name, value: changed_value};
            });
            $.get(location.href + "", data, (content) => {
                if (typeof content === 'object') {
                    Object.keys(content).map((key) => {
                        $(`#${key}`).html(content[key]);
                    });
                }
            })
        }
    }

    add_relation_tpl(path) {

        let area = `relation_${path}_template`,
            // id = (new Date).getTime(),
            tpl = "tpl::get_tpl".exec(area),
            zone = document.querySelector(`span[data-tpl="${area}"]`),
            id = zone ? `new_` + (zone.childElementCount + 1) : (new Date).getTime();

        tpl.children[0].innerHTML = tpl.children[0].innerHTML.replace(/\{\_\_id\_\_\}/g, id);

        zone.appendChild(tpl);
        let container = $(`[data-relation-path='${path}']`);
        container.find('.template_empty_container').hide();
    }

    drop_relation_tpl() {

        if ((this.jq.parents('[data-relation-path]').find('.template_container').length - 1) <= 0) {
            let container = this.jq.parents('[data-relation-path]');
            container.find('.template_empty_container').show();
        }

        this.jq.parents('.template_container').remove();
    }

    drop_relation(field_name) {
        let container = this.jq.parents('.template_container');
        container.find('.control_relation').hide();
        container.find('.template_content').hide();
        container.find('.return_relation').show();
        container.prepend(field_name);
    }

    return_relation() {
        let container = this.jq.parents('.template_container');
        container.find('.control_relation').show();
        container.find('.template_content').show();
        container.find('.return_relation').hide();
        container.find('.delete_field').remove();
    }
}

module.exports = AdminLte;
