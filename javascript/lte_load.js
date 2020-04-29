$('[title]').tooltip();
$(document).on('pjax:start', function(event) {
    $('[title]').tooltip('dispose');
});
document.addEventListener('ljs:nav:error', ({detail}) => {
    $('[data-old-text]').each((i, obj) => {
        obj.removeAttribute('disabled');
        obj.innerHTML = obj.dataset.oldText;
        obj.removeAttribute('data-old-text');
    });
    "toast:error".exec(detail.options);
});