Alpine.data('actionAfterSave', (select, type, lang_to_the_list, lang_add_more, lang_edit_further) => ({

    select: select,
    type: type,
    //lang: {to_the_list: lang_to_the_list, add_more: lang_add_more, edit_further: lang_edit_further},
    to_the_list: lang_to_the_list,
    add_more: lang_add_more,
    edit_further: lang_edit_further,
    chose: select,
    init() {
        const $this = $(this.$el);

        this.$watch('chose', (val) => {
            $("[type='hidden'][name='_after']").val(val);
            $("[type='radio'][name='_after']").attr('checked', false);
            $(`[type='radio'][name='_after'][value='${val}']`).attr('checked', true);
        });
    },

}));
