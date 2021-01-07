(() => {
    var n = {
        851: () => {
            document.addEventListener("bfg:schema_built", (function (n) {
                n.detail.components.new()
            }))
        }, 555: () => {
        }
    }, r = {};

    function e(t) {
        if (r[t]) return r[t].exports;
        var o = r[t] = {exports: {}};
        return n[t](o, o.exports, e), o.exports
    }

    e.m = n, e.x = n => {
    }, e.o = (n, r) => Object.prototype.hasOwnProperty.call(n, r), (() => {
        var n = {304: 0}, r = [[851], [555]], t = n => {
        }, o = (o, a) => {
            for (var l, p, [s, u, h, f] = a, i = 0, c = []; i < s.length; i++) p = s[i], e.o(n, p) && n[p] && c.push(n[p][0]), n[p] = 0;
            for (l in u) e.o(u, l) && (e.m[l] = u[l]);
            for (h && h(e), o && o(a); c.length;) c.shift()();
            return f && r.push.apply(r, f), t()
        }, a = self.webpackChunk = self.webpackChunk || [];

        function l() {
            for (var t, o = 0; o < r.length; o++) {
                for (var a = r[o], l = !0, p = 1; p < a.length; p++) {
                    var s = a[p];
                    0 !== n[s] && (l = !1)
                }
                l && (r.splice(o--, 1), t = e(e.s = a[0]))
            }
            return 0 === r.length && (e.x(), e.x = n => {
            }), t
        }

        a.forEach(o.bind(null, 0)), a.push = o.bind(null, a.push.bind(a));
        var p = e.x;
        e.x = () => (e.x = p || (n => {
        }), (t = l)())
    })(), e.x()
})();