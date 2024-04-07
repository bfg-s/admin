window.libs['nestable'] = function () {
    let result = $(this.target).nestable({
        maxDepth: this.target.dataset.maxDepth ? this.target.dataset.maxDepth : 15
    }).on('change', async (e) => {
        let list = $(e.target);
        NProgress.start();
        const token = exec('token');
        axios.post(e.target.dataset.route, {
            _token: token,
            model: e.target.dataset.model,
            depth: e.target.dataset.maxDepth,
            data: list.nestable('serialize'),
            parent_field: e.target.dataset.parent,
            order_field: e.target.dataset.orderField,
        }).then(data => {
            exec(data.data);
        }).finally(d => {
            NProgress.done();
        });
    });

    if (this.collapsed) {
        $(this.target).nestable('collapseAll');
    }

    return result;
};

window.libs['nestable::expand'] = function () {
    this.collapsed = false;
    return $(this.target.dataset.target ? this.target.dataset.target : '.dd').nestable('expandAll');
};

window.libs['nestable::collapse'] = function () {
    this.collapsed = false;
    return $(this.target.dataset.target ? this.target.dataset.target : '.dd').nestable('collapseAll');
};
