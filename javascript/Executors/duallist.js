module.exports = class extends Executor {

    __invoke ($options = {}) {

        return $(this.target).bootstrapDualListbox({
            moveOnSelect: this.target.dataset.moveOnSelect ? this.target.dataset.moveOnSelect : false
        });
    }

    static __name () {
    
        return "duallist";
    }
};