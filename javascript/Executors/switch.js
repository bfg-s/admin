module.exports = class extends Executor {

    __invoke ($options = {}) {

        if (!this.target) {

            ljs._error("Target not fount for Bootstrap Switch!");
            return ;
        }

        return $(this.target).bootstrapSwitch({

        }).on('switchChange.bootstrapSwitch', function(event, state) {

            $(event.currentTarget).trigger('change');
            $(event.currentTarget).trigger('mouseup');

            if (!event.currentTarget.checked) {
                let empty = document.createElement('INPUT');
                empty.setAttribute('type', 'hidden');
                empty.setAttribute('name', event.currentTarget.name);
                empty.setAttribute('value', '0');
                event.currentTarget.parentNode.append(empty);
            }

            else {
                $(`[type="hidden"][name="${event.currentTarget.name}"]`).remove();
            }
        });
    }

    static __name () {
    
        return "switch";
    }
};