const merge = require("lodash/merge");

function isJson (json) {
    try {
        return JSON.parse(json);
    } catch (e) {
        return false;
    }
}

function isElementVisible(el) {
    const rect = el.getBoundingClientRect();
    const windowHeight = (window.innerHeight || document.documentElement.clientHeight);
    const windowWidth = (window.innerWidth || document.documentElement.clientWidth);

    // Проверяем, не скрыт ли элемент стилями
    const elemStyle = getComputedStyle(el);
    if (elemStyle.display === 'none' || elemStyle.visibility === 'hidden') return false;

    // Проверяем, находится ли элемент в пределах видимой части окна браузера
    return (
        rect.top <= windowHeight &&
        rect.left <= windowWidth &&
        rect.bottom >= 0 &&
        rect.right >= 0
    );
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
                $add.initialPreview.push(`<img src="${img}" data-img class="file-preview-image kv-preview-data" style="width:auto;height:auto;max-width:100%;max-height:100%;" />`);
                $add.initialCaption.push(v);
                $add.initialPreviewCount = $add.initialPreview.length;
            });
        } else {

            img = String(img).indexOf('http') === 0 ? img : (String(img).indexOf('/') === 0 ? img : `/${img}`);
            $add.initialPreview = `<img src="${img}" data-img class="file-preview-image kv-preview-data" style="width:auto;height:auto;max-width:100%;max-height:100%;" />`;
            $add.initialCaption = this.target.getAttribute('value');
            $add.initialPreviewCount = 1;
        }
    }

    return $(this.target).fileinput(merge({
        'theme': 'explorer-fas',
        overwriteInitial: true,
        initialPreviewShowDelete: true,
        deleteUrl: window.delete_ordered_image, // + '?_token=' + exec('token'),
        deleteExtraData: {
            _token: exec('token'),
            id: this.target.dataset.id,
            field: this.target.dataset.field,
            model: this.target.dataset.model,
        },
        showUpload: false,
        cancelLabel: window.langs.cancel,
        browseLabel: window.langs.browse,
    }, $add, $options)).on('fileclear', (a) => {
        let empty = document.createElement('INPUT');
        empty.setAttribute('type', 'hidden');
        empty.setAttribute('name', a.target.name);
        empty.setAttribute('value', '');
        a.target.parentNode.append(empty);
    }).on('input', (a) => {
        $(`[type="hidden"][name="${a.target.name}"]`).remove();
    }).on('filesorted', (a, params) => {
        const fileList = [];
        $(a.target).parents('.file-input').find('.file-preview-frame').each(function () {
            if (! $(this).hasClass('kv-zoom-thumb')) {
                const img = $(this).find('img');
                const src = img.attr('src');
                if (! img[0].dataset.img) {
                    fileList.push(src);
                }
            }
        });

        if (fileList.length > 1 && a.target.dataset.id && a.target.dataset.field && a.target.dataset.model) {
            axios.post(window.save_image_order, {
                _token: exec('token'),
                id: a.target.dataset.id,
                field: a.target.dataset.field,
                model: a.target.dataset.model,
                fileList: fileList,
            }).then((data => {
                exec(data.data);
            }));
        }
    }).on('filedeleted', function(a, key, jqXHR, data) {

        setTimeout(() => {

            const fileList = [];
            $(a.target).parents('.file-input').find('.file-preview-frame').each(function () {
                if (! $(this).hasClass('kv-zoom-thumb')) {
                    const img = $(this).find('img');
                    const src = img.attr('src');
                    if (! img[0].dataset.img) {
                        fileList.push(src);
                    }
                }
            });

            axios.post(window.save_image_order, {
                _token: exec('token'),
                id: a.target.dataset.id,
                field: a.target.dataset.field,
                model: a.target.dataset.model,
                fileList: fileList,
            }).then((data => {
                exec(data.data);
            }));

        }, 400);

    }).on('filedeleteerror', function(event, data, msg) {
        console.log('File delete error');
        // get message
        alert(msg);
    });
};
