const merge = require('lodash/merge');

module.exports = class extends Executor {

    __invoke ($options = {}) {

        if (!this.target) {

            ljs._error("Target not fount for Bootstrap Switch!");
            return ;
        }

        let $add = {};

        if (this.target.dataset.exts) {

            $add['allowedFileExtensions'] = this.target.dataset.exts.split('|');
        }

        if (this.target.getAttribute('value')) {

            $add.initialPreview = `<img src="/${this.target.getAttribute('value')}" class="file-preview-image kv-preview-data" style="width:auto;height:auto;max-width:100%;max-height:100%;" />`;
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
    }

    static __name () {
    
        return "file";
    }
};