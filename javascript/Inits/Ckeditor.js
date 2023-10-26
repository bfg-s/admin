window.libs['ckeditor'] = function ($options = {}) {
    if (!this.target) {

        console.log("Target not fount for CKEditor!");

        return;
    }

    if (this.target.dataset.toolbar) {

        $options.toolbar = this.target.dataset.toolbar.split(' ');
    }

    if ($options.plugin === undefined) {

        $options.plugin = [];
    }

    $options.ckfinder = {
        uploadUrl: window.uploader + '?_token=' + exec('token'),
        options: {
            resourceType: 'Images'
        }
    };

    $options.mediaEmbed = {
        previewsInData: true
    };

    $options.image = {
        // You need to configure the image toolbar, too, so it uses the new style buttons.
        toolbar: ['imageTextAlternative', '|', 'imageStyle:alignLeft', 'imageStyle:full', 'imageStyle:alignRight'],
        styles: [
            // This option is equal to a situation where no style is applied.
            'full',

            // This represents an image aligned to the left.
            'alignLeft',

            // This represents an image aligned to the right.
            'alignRight'
        ]
    }

    return ClassicEditor.create(this.target, $options).then((editor) => {


        editor.editing.view.document.on('blur', () => {

            editor.sourceElement.value = editor.getData();
            $(editor.sourceElement).trigger('change');
        });

        // editor.model.document.on('change:data', () => {
        //
        //     editor.changed = true;
        // });
    });
};
