module.exports = (tooltip_selector) => {

    $(tooltip_selector).tooltip({placement: 'auto'});
    $(document).on('pjax:start', function(event) {
        $(tooltip_selector).tooltip('dispose');
    });
    document.addEventListener('ljs:nav:error', ({detail}) => {
        $('[data-old-text]').each((i, obj) => {
            obj.removeAttribute('disabled');
            obj.innerHTML = obj.dataset.oldText;
            obj.removeAttribute('data-old-text');
        });
        "toast:error".exec(detail.options);
    });
}