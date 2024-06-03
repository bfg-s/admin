window.libs['rating'] = function () {
    //console.log(this.target);

    $(this.target).rating({
        theme: 'krajee-fas',
        emptyStar: '<i class="far fa-star"></i>',
        filledStar: '<i class="fas fa-star"></i>',
        clearButton: '<i class="fas fa-minus-circle"></i>'
    }).on('rating:clear', function (event) {
        $(this).val('0');
    });
};
