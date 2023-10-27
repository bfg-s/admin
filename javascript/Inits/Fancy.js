require('@fancyapps/fancybox');

window.libs['fancy::img'] = function (img, opts = null) {
    return $.fancybox.open({
        src: img,
    }, opts);
};
