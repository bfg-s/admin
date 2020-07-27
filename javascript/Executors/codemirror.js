const merge = require('lodash/merge');

module.exports = class extends Executor {

    html ($options = {}) {

        return window.cm = CodeMirror.fromTextArea(this.currentTarget, merge({
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

    js () {

        return this.html({
            mode: {name: "javascript", globalVars: true}
        });
    }

    css () {

        return this.html({
            mode: 'css'
        });
    }

    md () {

        return this.html({
            mode: 'text/x-markdown'
        });
    }

    static __name () {
    
        return "codemirror";
    }
};