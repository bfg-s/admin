module.exports = class extends Executor {

    get tab_regexp() {
        return /^#tab-[a-z0-9]{32}-[0-9]+$/;
    }

    static __name() {

        return "tabs";
    }

    __invoke(h = null) {

        let hash = h ? h : location.hash;

        if (!!hash && this.tab_regexp.test(hash)) {

            $(`${hash}-label`).trigger('click');
        }
    }

    tab_button(e) {
        // this.event.preventDefault();
        // history.pushState("", document.title, this.target.href)
    }
};
