module.exports = {

    register () {

        this.app.bind('url', (path = "") => {
            let uri = this.app.server.home_uri;
            let origin = location.origin;
            if (path !== '') { path = '/' + this.app.str.trim(path, '/'); }
            return origin + '/' + this.app.str.trim(uri, '/') + path
        });
    }
};