
window.libs['tabs'] = function (h = null) {
    let hash = h ? h : location.hash;

    if (!!hash && /^#tab-[a-z0-9]{32}-[0-9]+$/.test(hash)) {

        $(`${hash}-label`).trigger('click');
    }
};

$(document).on('click', '[role="tablist"] a', (e) => {
    let hash = e.target.getAttribute('href');
    //location.hash = hash;
    history.pushState(null, null, hash);
});
