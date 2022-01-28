module.exports = {

    showLoading() {

        let width = $('.os-content').width(),
            container = $(ljs.cfg("pjax-container") ?? document.body),
            image = $('<img />').attr('src', '/lte-admin/img/loader.gif').css({
                position: 'absolute',
                width: 200,
                height: 200,
                left: `calc(50% - 100px)`,
                top: 'calc(50% - 100px)',
                zIndex: 1
            }),
            image_container = $('<div></div>').css({
                position: 'absolute',
                top: 0,
                bottom: 0,
                left: 0,
                right: 0,
                opacity: 0.7,
                background: '#FFF',
                zIndex: 0
            }).attr('id', 'loading-spinner');
        container.append(image_container.append(image));
    },

    hideLoader() {

    }
};
