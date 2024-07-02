const morphdom = require('morphdom').default;

window.libs['load::content'] = function (name) {
    axios.post(window.load_content + location.search, {name: name, _load_content: true})
        .then((response) => {
            if (response.data) {
                morphdom(this.target, response.data.content ? `<div>${response.data.content}</div>` : "<div></div>");
                window.resetInits(this.target);
                window.updateInits(this.target);
                window.updateToolTips();
            }
        });
};
