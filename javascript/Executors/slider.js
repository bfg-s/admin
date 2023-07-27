module.exports = class extends Executor {

    static __name() {

        return "slider";
    }

    __invoke($options = {}) {

        if (!this.target) {

            ljs._error("Target not fount for Bootstrap Switch!");
            return;
        }

        return $(this.target).bootstrapSlider();
    }
};
