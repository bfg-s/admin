window.libs['admin::flash_document'] = function (changed_name = null, changed_value = null) {
    return window.libs['flash_document'](changed_name, changed_value);
};

window.libs['admin::drop_relation_tpl'] = function () {
    if (($(this.target).parents('[data-relation-path]').find('.template_container').length - 1) <= 0) {
        let container = $(this.target).parents('[data-relation-path]');
        container.find('.template_empty_container').show();
    }

    $(this.target).parents('.template_container').remove();
};

window.libs['admin::drop_relation'] = function (field_name) {
    let container = $(this.target).parents('.template_container');
    container.find('.control_relation').hide();
    container.find('.template_content').hide();
    container.find('.return_relation').show();
    container.prepend(field_name);
};

window.libs['admin::return_relation'] = function () {
    let container = $(this.target).parents('.template_container');
    container.find('.control_relation').show();
    container.find('.template_content').show();
    container.find('.return_relation').hide();
    container.find('.delete_field').remove();
};

window.libs['admin::add_relation_tpl'] = function (path) {
    let area = `relation_${path}_template`,
        // id = (new Date).getTime(),
        tpl = exec("tpl::get_tpl", area),
        zone = document.querySelector(`span[data-tpl="${area}"]`),
        id = zone ? `new_` + (zone.childElementCount + 1) : (new Date).getTime();

    tpl.children[0].innerHTML = tpl.children[0].innerHTML.replace(/\{\_\_id\_\_\}/g, id);

    //zone.appendChild(tpl);
    //ljs.$nav.readyObject(tpl);
    $(zone).append(tpl.children);
    window.updateInits();
    let container = $(`[data-relation-path='${path}']`);
    container.find('.template_empty_container').hide();
};

window.libs['flash_document'] = function (changed_name = null, changed_value = null) {
    let data = $(":input").serializeArray();
    let lives = $('.__live__');
    if (lives[0]) {
        lives.each((i, o) => data.push({name: '_areas[]', value: o.getAttribute('id')}));
        data.push({name: '_changed_field', value: changed_name});
        data.push({name: '_changed_value', value: changed_value});

        let params = {};

        data.map(({name, value}, key) => {
            if (name.indexOf("q[") !== 0 && name !== '_method')
                params[name] = value;
        });

        axios.post(window.load_lives, {
            _token: exec('token'),
            ...params
        }).then((data => {
            let content = data.data;
            if (typeof content === 'object') {
                Object.keys(content).map((key) => {
                    const q = $(`#${key}`);
                    const live = content[key];
                    if (q[0].dataset.hash !== live.hash) {
                        q.html(live.content);
                        q[0].dataset.hash = live.hash;
                    }
                });
                window.updateInits();
            }
        }));
    }
};

window.libs['custom_save'] = function (model, id, field, inputId) {

    const e = $(`#${inputId}`)[0];

    let val = null;

    setTimeout(() => {

        if (e.nodeName === 'INPUT' && e.getAttribute('type') === 'checkbox') {
            val = !! Number($(`#${inputId}`).parents('.bootstrap-switch-mini').find(`[name="${e.name}"]`).last().val());
        } else if (e.nodeName === 'DIV') {
            val = $(`#${inputId}`).find(':checked').val();
        } else {
            val = $(`#${inputId}`).val();
        }

        axios.post(window.custom_save, {
            _token: exec('token'),
            model: model,
            id: id,
            field_name: field,
            val: val
        }).then(data => {
            exec(data.data);
        });
    }, 100);
};

window.libs['admin::call_callback'] = function (key, parameters) {
    axios.post(window.call_callback, {
        _token: exec('token'), key, parameters
    }).then(data => {
        exec(data.data);
    })
};

window.libs['admin::delete_item'] = function (title, url) {

    exec('alert::confirm', title, () => {
        axios.delete(url).then(data => {
            exec(data.data);
        });
    });
}

const registered_onetime = {};

window.libs['onetime'] = function ($name, $execute, $ms = 100) {

    if (registered_onetime[$name]) {

        clearTimeout(registered_onetime[$name]);
    }

    registered_onetime[$name] = setTimeout(() => {

        if (typeof $execute === 'function') {

            $execute();
        } else {
            exec($execute);
        }

        delete registered_onetime[$name];

    }, $ms);
};

window.libs['admin::model_relation_ordered'] = function (field) {
    $(this.target).sortable({
        revert: true,
        handle: ".handle",
        update: ( event, ui ) => {
            let i = 0;
            event.target.querySelectorAll('.card-body.template_content').forEach(e => {
                e.querySelector('.ordered-field').value = i;
                i++;
            })
        }
    });
};
