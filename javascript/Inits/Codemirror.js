const merge = require("lodash/merge");

function initCodemirror(target, $options = {}) {

    return window.cm = CodeMirror.fromTextArea(target, merge({
        mode: {
            name: "htmlmixed",
            scriptTypes: [
                {matches: /\/x-handlebars-template|\/x-mustache/i, mode: null},
                {matches: /(text|application)\/(x-)?vb(a|script)/i, mode: "vbscript"}
            ]
        },
        lineNumbers: true,
        lineWrapping: true,
        styleActiveLine: true,
        matchBrackets: true,
        continueComments: "Enter",
        extraKeys: {
            "Ctrl-Q": "toggleComment"
        }
    }, $options)).on('change', (cm) => {
        $(cm.getTextArea()).val(cm.getValue());
    });
}

window.libs['codemirror::html'] = function ($options = {}) {
    return initCodemirror(this.target, $options)
};

window.libs['codemirror::js'] = function ($options = {}) {
    return initCodemirror(this.target, merge({
        mode: {name: "javascript", globalVars: true}
    }, $options))
};

window.libs['codemirror::css'] = function ($options = {}) {
    return initCodemirror(this.target, merge({
        mode: {name: "css"}
    }, $options))
};

window.libs['codemirror::md'] = function ($options = {}) {
    return initCodemirror(this.target, merge({
        mode: {name: "text/x-markdown"}
    }, $options))
};
