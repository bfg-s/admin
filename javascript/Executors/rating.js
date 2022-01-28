const merge = require('lodash/merge');

module.exports = class extends Executor {

    static __name() {

        return "rating";
    }

    __invoke($options = {}) {

        return $(this.currentTarget).rating({
            theme: 'krajee-fas',
            emptyStar: '<i class="far fa-star"></i>',
            filledStar: '<i class="fas fa-star"></i>',
            clearButton: '<i class="fas fa-minus-circle"></i>'
        }).on('rating:clear', function (event) {
            $(this).val('0');
        });
    }
};
