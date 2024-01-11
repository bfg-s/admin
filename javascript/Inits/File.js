const merge = require("lodash/merge");

function isJson (json) {
    try {
        return JSON.parse(json);
    } catch (e) {
        return false;
    }
}

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
        let json = isJson(img);

        if (json) {
            img = JSON.parse(img);
            Object.values(img).forEach((v) => {
                const img = String(v).indexOf('http') === 0 ? v : (String(v).indexOf('/') === 0 ? v : `/${v}`);
                $add.initialPreview = $add.initialPreview || [];
                $add.initialCaption = $add.initialCaption || [];
                $add.initialPreview.push(`<img src="${img}" class="file-preview-image kv-preview-data" style="width:auto;height:auto;max-width:100%;max-height:100%;" />`);
                $add.initialCaption.push(v);
                $add.initialPreviewCount = $add.initialPreview.length;
            });
        } else {

            img = String(img).indexOf('http') === 0 ? img : (String(img).indexOf('/') === 0 ? img : `/${img}`);
            $add.initialPreview = `<img src="${img}" class="file-preview-image kv-preview-data" style="width:auto;height:auto;max-width:100%;max-height:100%;" />`;
            $add.initialCaption = this.target.getAttribute('value');
            $add.initialPreviewCount = 1;
        }
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
