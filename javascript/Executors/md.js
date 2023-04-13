const merge = require('lodash/merge');
const unescape = require('lodash/unescape');

module.exports = class extends Executor {

    static __name() {

        return "md";
    }

    simple($options = {}) {

        if (!this.target.id) {

            this.target.id = new Date() / 1
        }

        let data = unescape(this.target.innerHTML);
        this.target.innerText = "";

        return editormd(this.target.id, merge({
            width: "100%",
            height: 300,
            path: '/admin/plugins/',
            pluginPath: '/admin/plugins/editor.md-master/plugins/',
            markdown: data,
            autoFocus: false,
            placeholder: "",
            searchReplace: true,
            toolbarIcons: function () {
                return ['undo', 'redo', '|', 'bold', 'del', 'italic', 'quote', '|',
                    'h1', 'h2', 'h3', 'h4', 'h5', 'h6', '|', 'list-ul', 'list-ol', '|', 'hr', 'link', 'reference-link', 'pagebreak',
                    'image', 'code', 'table', 'datetime', 'html-entities', '||',
                    'goto-line', 'clear', 'search', 'preview', 'watch', 'fullscreen']
            },
            watch: false,
            htmlDecode: "style,script,iframe|on*",
            imageUpload: true,
            imageFormats: ["jpg", "jpeg", "gif", "png", "bmp", "webp"],
            imageUploadURL: ljs.cfg('uploader') + '?_token=' + ljs.token
        }, $options, this.target.dataset));
    }
};
