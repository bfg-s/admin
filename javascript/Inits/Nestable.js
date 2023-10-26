window.libs['nestable'] = function () {
    let result = $(this.target).nestable({
        maxDepth: this.target.dataset.maxDepth ? this.target.dataset.maxDepth : 15
    }).on('change', (e) => {
        let list = $(e.target);

        jax.admin.nestable_save(
            e.target.dataset.model,
            e.target.dataset.maxDepth,
            list.nestable('serialize'),
            e.target.dataset.parent,
            e.target.dataset.orderField,
        )
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
