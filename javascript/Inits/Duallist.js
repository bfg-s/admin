window.libs['duallist'] = function () {
    return $(this.target).bootstrapDualListbox({
        moveOnSelect: this.target.dataset.moveOnSelect ? this.target.dataset.moveOnSelect : false
    });
};
