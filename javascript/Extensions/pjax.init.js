$.pjax.defaults.timeout = 5000;
$.pjax.defaults.maxCacheLength = 0;

let cancelContext;
let tooltip_selector = '[title]:not([class^="select2"])';

$(document).pjax('a:not([target]):not([href^="#"]):not([data-href])', {
    container: '#admin-content'
});

$(document).on('submit', 'form:not([target]):not([tabindex])', (event) => {

    $.pjax.submit(event, '#admin-content');
});

$(document).on('pjax:beforeSend', (event, xhr) => {

    let token = $('[name="csrf-token"]').attr('content');

    if (token) {
        xhr.setRequestHeader('X-CSRF-TOKEN', token);
    }
});

$(document).on('pjax:timeout', (event) => {

    event.preventDefault();

    if (process.env.NODE_ENV === 'development') {
        console.warn("Pjax TimeOut:", event);
    }
});

$(document).on("pjax:popstate", () => {

    $(document).on("pjax:end", (event) => {

        $(event.target).find("script[data-exec-on-popstate]").each(function () {

            $.globalEval(this.text || this.textContent || this.innerHTML || '');
        });
    });
});

$(document).on('pjax:send', (xhr) => {

    NProgress.start();

    document.body.style.cursor = "progress";

    window.Alpine && window.Alpine.deferMutations && window.Alpine.deferMutations();

    if (window.initedVueForPjsxMoveDestroy && Array.isArray(window.initedVueForPjsxMoveDestroy)) {
        for (let i = 0; i <= window.initedVueForPjsxMoveDestroy.length - 1; i++) {
            window.initedVueForPjsxMoveDestroy[i].destroy();
        }
    }
});

$(document).on('pjax:beforeReplace', (a, b, c, d) => {

    if (cancelContext) {
        d.cancelContext(cancelContext);
    }
});

$(document).on('pjax:complete', (xhr, req, status) => {

    document.body.style.cursor = "auto";

    if (status !== 'error' && !document.querySelector('#admin-content')) {

        cancelContext = true;

        window.location.reload();

        return;
    }

    exec('timer::onetime', "tooltip", () => $(tooltip_selector).tooltip({placement: 'auto'}));

    window.Alpine && window.Alpine.flushAndStopDeferringMutations && window.Alpine.flushAndStopDeferringMutations();

    window.updateInits();

    exec('tabs');

    NProgress.done();
});

$(document).on('pjax:error', (xhr, textStatus, error, options) => {

    document.body.style.cursor = "auto";

    NProgress.done();

    $('[data-old-text]').each((i, obj) => {
        obj.removeAttribute('disabled');
        obj.innerHTML = obj.dataset.oldText;
        obj.removeAttribute('data-old-text');
    });
    if (options !== 'abort') {
        exec("toast::error", options);
    }
});
