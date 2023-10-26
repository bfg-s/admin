window.libs['bootstrapSwitch'] = function () {
    const $this = $(this.target);
    return $this.bootstrapSwitch({}).on('switchChange.bootstrapSwitch', function (event, state) {

        $(event.currentTarget).trigger('change');
        $(event.currentTarget).trigger('mouseup');

        if (!event.currentTarget.checked) {
            let empty = document.createElement('INPUT');
            empty.setAttribute('type', 'hidden');
            empty.setAttribute('name', event.currentTarget.name);
            empty.setAttribute('value', '0');
            event.currentTarget.parentNode.append(empty);
        } else {
            $(`[type="hidden"][name="${event.currentTarget.name}"]`).remove();
        }

        exec("admin::flash_document");
    });
};
