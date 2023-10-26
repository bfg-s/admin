window.libs['table_list::checkChildCheckboxes'] = function () {
    let obj = this.target;
    $(obj).parents('.card').find('.action-selector').each((i, o) => {
        o.checked = true;
        $(o).trigger('change');
    });
};

window.libs['table_list::uncheckChildCheckboxes'] = function () {
    let obj = this.target;
    $(obj).parents('.card').find('.action-selector').each((i, o) => {
        o.checked = false;
        $(o).trigger('change');
    });
};

window.libs['table_list::invertChildCheckboxes'] = function () {
    let obj = this.target;
    $(obj).parents('.card').find('.action-selector').each((i, o) => {
        o.checked = !o.checked;
        if (o.name !== obj.name) {
            $(o).trigger('change');
        }
    });
};

window.libs['table_list::invertByCheckChildCheckboxes'] = function () {
    let obj = this.target;
    $(obj).parents('.card').find('.action-selector').each((i, o) => {
        o.checked = !o.checked;
        $(o).trigger('change');
    });
};

window.libs['table_list::primaryChange'] = function () {
    let obj = this.target;
    let has = false;
    $(`.select_${obj.dataset.table}`).each((i, o) => {
        if (obj !== o && o.checked) {
            has = o.checked;
        }
    });
    if (!has && !obj.checked) {
        $(`.global_select_${obj.dataset.table}`).each((i, o) => {
            o.checked = false;
        });
    }
};
