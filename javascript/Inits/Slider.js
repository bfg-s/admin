window.libs['slider'] = function () {
    if (!this.target) {
        console.error("Target not fount for Bootstrap Switch!");
        return;
    }

    return $(this.target).bootstrapSlider();
};
