'use strict';

class Admin extends Executor {

    static __name() {

        return "admin";
    }

    get_selected_radio () {
        if (this.target) {
            console.log($(this.target).find(':checked').val());
            return $(this.target).find(':checked').val();
        }

        return null;
    }

    flash_document(changed_name = null, changed_value = null) {
        let data = $(":input").serializeArray();
        let lives = $('.__live__');
        if (lives[0]) {
            lives.each((i, o) => data.push({name: '_areas[]', value: o.getAttribute('id')}));
            data.push({name: '_changed_field', value: changed_name});
            data.push({name: '_changed_value', value: changed_value});
            const j = jax;
            data.map(({name, value}, key) => {
                if (name.indexOf("q[") !== 0 && name !== '_method')
                    j.param(name, value);
            });
            j.admin.load_lives().then(((content) => {
                if (typeof content === 'object') {
                    Object.keys(content).map((key) => {
                        const q = $(`#${key}`);
                        const live = content[key];
                        if (q[0].dataset.hash !== live.hash) {
                            q.html(live.content);
                            q[0].dataset.hash = live.hash;
                        }
                    });
                }
            }));
        }
    }

    add_relation_tpl(path) {

        let area = `relation_${path}_template`,
            // id = (new Date).getTime(),
            tpl = "tpl::get_tpl".exec(area),
            zone = document.querySelector(`span[data-tpl="${area}"]`),
            id = zone ? `new_` + (zone.childElementCount + 1) : (new Date).getTime();

        tpl.children[0].innerHTML = tpl.children[0].innerHTML.replace(/\{\_\_id\_\_\}/g, id);

        //zone.appendChild(tpl);
        //ljs.$nav.readyObject(tpl);
        $(zone).append(tpl.children);
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

module.exports = Admin;
