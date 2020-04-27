module.exports = class extends Executor {

    __invoke () {

        return $(this.target).nestable({
            maxDepth: this.target.dataset.maxDepth ? this.target.dataset.maxDepth : 15
        }).on('change', (e) => {
            let list = $(e.target);

            jax.lte_admin.nestable_save(
                e.target.dataset.model,
                e.target.dataset.maxDepth,
                list.nestable('serialize'),
                e.target.dataset.parent,
            )
        });
    }

    expand () {

        return $(this.target.dataset.target ? this.target.dataset.target : '.dd').nestable('expandAll');
    }

    collapse () {

        return $(this.target.dataset.target ? this.target.dataset.target : '.dd').nestable('collapseAll');
    }

    static __name () {

        return "nestable";
    }
}