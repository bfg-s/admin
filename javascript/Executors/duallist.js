module.exports = class extends Executor {

    static __name() {

        return "duallist";
    }

    __invoke($options = {}) {

        return $(this.target).bootstrapDualListbox({
            moveOnSelect: this.target.dataset.moveOnSelect ? this.target.dataset.moveOnSelect : false
        });
    }
};
