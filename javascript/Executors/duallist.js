module.exports = class extends Executor {

    __invoke ($options = {}) {

        return $(this.target).bootstrapDualListbox({
            moveOnSelect: false
        });
    }

    static __name () {
    
        return "duallist";
    }
};