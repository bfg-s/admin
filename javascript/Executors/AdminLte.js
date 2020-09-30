'use strict';

class AdminLte extends Executor {

    add_relation_tpl (area) {

        let id = (new Date).getTime(),
            tpl = "tpl::get_tpl".exec(area),
            zone = document.querySelector(`span[data-tpl="${area}"]`);

        tpl.children[0].innerHTML = tpl.children[0].innerHTML.replace(/\{\_\_id\_\_\}/g, id);

        zone.appendChild(tpl);
    }

    drop_relation_tpl () {

        this.jq.parents('.template_container').remove();
    }

    drop_relation (field_name) {
        let container = this.jq.parents('.template_container');
        container.find('.control_relation').hide();
        container.find('.template_content').hide();
        container.find('.return_relation').show();
        container.prepend(field_name);
    }

    return_relation () {
        let container = this.jq.parents('.template_container');
        container.find('.control_relation').show();
        container.find('.template_content').show();
        container.find('.return_relation').hide();
        container.find('.delete_field').remove();
    }

    static __name () {
    
        return "lte";
    }
}

module.exports = AdminLte;
