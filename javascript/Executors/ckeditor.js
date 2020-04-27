module.exports = class extends Executor {

    __invoke ($options = {}) {

        if (!this.target) {

            ljs._error("Target not fount for CKEditor!");

            return ;
        }
        
        if (this.target.dataset.toolbar) {

            $options.toolbar = this.target.dataset.toolbar.split(' ');
        }

        if ($options.plugin === undefined) {

            $options.plugin = [];
        }

        $options.ckfinder = {
            uploadUrl: ljs.cfg('uploader') + '?_token=' + ljs.token,
            options: {
                resourceType: 'Images'
            }
        };

        return ClassicEditor.create( this.target,  $options).then((editor) => {


            editor.editing.view.document.on('blur', () => {

                editor.sourceElement.value = editor.getData();
                $(editor.sourceElement).trigger('change');
            });

            // editor.model.document.on('change:data', () => {
            //
            //     editor.changed = true;
            // });
        });
    }

    static __name () {
    
        return "ckeditor";
    }
};