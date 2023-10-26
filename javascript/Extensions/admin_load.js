module.exports = () => {

    let tooltip_selector = '[title]:not([class^="select2"])';

    $(tooltip_selector).tooltip({placement: 'auto'});
    $(document).on('pjax:start', function (event) {
        $(tooltip_selector).tooltip('dispose');
    });
}
