const merge = require('lodash/merge');

module.exports = class extends Executor {

    __invoke () {

        return $(this.currentTarget).bootstrapNumber({
            upClass: this.target.dataset.upClass ? this.target.dataset.upClass : 'success',
            downClass: this.target.dataset.downClass ? this.target.dataset.downClass : 'danger',
            center: this.target.dataset.center ? this.target.dataset.center === 'true' : true,
            upText: this.target.dataset.upText ? this.target.dataset.upText : '<i class="fas fa-plus"></i>',
            downText: this.target.dataset.downText ? this.target.dataset.downText : '<i class="fas fa-minus"></i>',
        });
    }

    static __name () {
    
        return "number";
    }
};