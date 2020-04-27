'use strict';

class AdminLte extends Executor {

    md () {

        if (!this.target.id) {

            this.target.id = new Date() / 1
        }

        return editormd(this.target.id, {
            width: "100%",
            height: 540,
            path : '/lte-admin/plugins/editor.md-master/lib/',
            markdown : this.target.innerText,
            //codeFold : true,
            //syncScrolling : false,
            saveHTMLToTextarea : true,    // 保存 HTML 到 Textarea
            searchReplace : true,
            toolbarIcons : function() {
                // Or return editormd.toolbarModes[name]; // full, simple, mini
                // Using "||" set icons align right.
                return ['undo', 'redo', '|', 'bold', 'del', 'italic', 'quote', '|',
                    'h1', 'h2', 'h3', 'h4', 'h5', 'h6', '|', 'list-ul', 'list-ol', '|', 'hr', 'link', 'reference-link', 'pagebreak',
                    'image', 'code', 'table', 'datetime', 'html-entities', 'goto-line', 'watch', 'preview',
                    'fullscreen', 'clear', 'search']
            },
            //watch : false,                // 关闭实时预览
            //htmlDecode : "style,script,iframe|on*",            // 开启 HTML 标签解析，为了安全性，默认不开启
            //toolbar  : false,             //关闭工具栏
            //previewCodeHighlight : false, // 关闭预览 HTML 的代码块高亮，默认开启
            // emoji : false,
            //taskList : true,
            //tocm            : true,         // Using [TOCM]
            //tex : true,                   // 开启科学公式TeX语言支持，默认关闭
            //flowChart : true,             // 开启流程图支持，默认关闭
            //sequenceDiagram : true,       // 开启时序/序列图支持，默认关闭,
            //dialogLockScreen : false,   // 设置弹出层对话框不锁屏，全局通用，默认为true
            //dialogShowMask : false,     // 设置弹出层对话框显示透明遮罩层，全局通用，默认为true
            //dialogDraggable : false,    // 设置弹出层对话框不可拖动，全局通用，默认为true
            //dialogMaskOpacity : 0.4,    // 设置透明遮罩层的透明度，全局通用，默认值为0.1
            //dialogMaskBgColor : "#000", // 设置透明遮罩层的背景颜色，全局通用，默认为#fff
            imageUpload : true,
            imageFormats : ["jpg", "jpeg", "gif", "png", "bmp", "webp"],
            imageUploadURL : "./php/upload.php",
            onload : function() {
                console.log('onload', this);
                //this.fullscreen();
                //this.unwatch();
                //this.watch().fullscreen();

                //this.setMarkdown("#PHP");
                //this.width("100%");
                //this.height(480);
                //this.resize("100%", 640);
            }
        });
    }

    static __name () {
    
        return "lte";
    }
}

module.exports = AdminLte;
