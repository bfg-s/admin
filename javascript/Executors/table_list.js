'use strict';

class TableList extends Executor {

    checkChildCheckboxes () {

        let obj = this.storage.object;

        $(`[name^="${obj.name}"]`).each((i,o) => {

            if (o.name !== obj.name && o.checked !== obj.checked) {

                o.checked = obj.checked;

                $(o).trigger('change');

            }
        });
    }

    static __name () {
    
        return "table_list";
    }
}

module.exports = TableList;
