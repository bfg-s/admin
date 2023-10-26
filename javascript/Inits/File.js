const merge = require("lodash/merge");

window.libs['file'] = function ($options = {}) {
    if (!this.target) {

        console.error("Target not fount for Bootstrap Switch!");
        return;
    }

    let $add = {};

    if (this.target.dataset.exts) {

        $add['allowedFileExtensions'] = this.target.dataset.exts.split('|');
    }

    if (this.target.getAttribute('value')) {

        let img = this.target.getAttribute('value');
        img = String(img).indexOf('http') === 0 ? img : (String(img).indexOf('/') === 0 ? img : `/${img}`);
        $add.initialPreview = `<img src="${img}" class="file-preview-image kv-preview-data" style="width:auto;height:auto;max-width:100%;max-height:100%;" />`;
        $add.initialCaption = this.target.getAttribute('value');
    }

    return $(this.target).fileinput(merge({
        'theme': 'explorer-fas',
        overwriteInitial: true,
        showUpload: false
    }, $add, $options)).on('fileclear', (a) => {
        let empty = document.createElement('INPUT');
        empty.setAttribute('type', 'hidden');
        empty.setAttribute('name', a.target.name);
        empty.setAttribute('value', '');
        a.target.parentNode.append(empty);
    }).on('input', (a) => {
        $(`[type="hidden"][name="${a.target.name}"]`).remove();
    });
};
