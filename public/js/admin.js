/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "../../../bfg-js/bfg-js/bundle/index.js":
/*!**********************************************!*\
  !*** ../../../bfg-js/bfg-js/bundle/index.js ***!
  \**********************************************/
/***/ ((__unused_webpack_module, exports) => {

function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

!function (e, t) {
  for (var n in t) {
    e[n] = t[n];
  }

  t.__esModule && Object.defineProperty(e, "__esModule", {
    value: !0
  });
}(exports, function () {
  "use strict";

  var e = {
    355: function _(e, t) {
      var n,
          r,
          o,
          i,
          a,
          u,
          s = this && this.__assign || function () {
        return (s = Object.assign || function (e) {
          for (var t, n = 1, r = arguments.length; n < r; n++) {
            for (var o in t = arguments[n]) {
              Object.prototype.hasOwnProperty.call(t, o) && (e[o] = t[o]);
            }
          }

          return e;
        }).apply(this, arguments);
      },
          p = this && this.__spreadArrays || function () {
        for (var e = 0, t = 0, n = arguments.length; t < n; t++) {
          e += arguments[t].length;
        }

        var r = Array(e),
            o = 0;

        for (t = 0; t < n; t++) {
          for (var i = arguments[t], a = 0, u = i.length; a < u; a++, o++) {
            r[o] = i[a];
          }
        }

        return r;
      };

      Object.defineProperty(t, "__esModule", {
        value: !0
      }), t["default"] = (n = {}, r = [], o = function o(e, t, r) {
        var o = "on_" + t,
            i = {
          name: e,
          event: t
        };
        return e in n && o in n[e] && n[e][o].map(function (e) {
          var t = e(r, u, i);
          void 0 !== t && (r = t);
        }), "*" in n && o in n["*"] && n["*"][o].map(function (e) {
          var t = e(r, u, i);
          void 0 !== t && (r = t);
        }), r;
      }, i = function i(e) {
        for (var t = [], r = 1; r < arguments.length; r++) {
          t[r - 1] = arguments[r];
        }

        var i = null;

        if (e in n) {
          var a = n[e];
          if (!("data" in a)) return null;
          a.compute && "function" == typeof a.data ? i = a.data.apply(a, p([u], t)) : a.resolve && !a.resolved ? ("function" == typeof a.resolve ? n[e].data = a.resolve.apply(a, p([u], t)) : n[e].data = a.resolve, i = n[e].data = o(e, "resolve", n[e].data)) : i = n[e].data, n[e].resolved = !0, i = o(e, "get", i);
        }

        return i;
      }, a = function a(e, t) {
        return {
          bind: function bind(e, r, i, a) {
            void 0 === i && (i = !1), void 0 === a && (a = !1);
            var u = {
              data: i ? null : r,
              resolve: !!i && r,
              resolved: !1,
              compute: a
            };
            return e in n ? "data" in n[e] ? (u.etc = n[e], n[e] = o(e, "replace", u), delete u.etc) : n[e] = s(s({}, u), n[e]) : n[e] = o(e, "bind", u), t;
          },
          singleton: function singleton(e, n) {
            return t.bind(e, n, !0);
          },
          compute: function compute(e, n) {
            return t.bind(e, n, !1, !0);
          },
          library: function library(e) {
            return "name" in e ? t.bind(e.name, new e(t)) : t;
          },
          inject: function inject(e) {
            e.app = t, t.obj.getMethods(e).map(function (n) {
              var r = n.split(":"),
                  o = null,
                  i = "bind";
              1 === r.length ? (o = r[0], i = "bind") : (o = r[1], i = r[0]), "bind" !== i && "singleton" !== i && "compute" !== i && (i = "bind"), i && o && t[i](o, e[n]);
            });
          },
          get: function get(e) {
            void 0 === e && (e = null);

            for (var t = [], r = 1; r < arguments.length; r++) {
              t[r - 1] = arguments[r];
            }

            return e ? i.apply(void 0, p([e], t)) : n;
          },
          has: function has(e) {
            return e in n;
          },
          forget: function forget(e) {
            return e in n ? (o(e, "forget", n[e].data), delete n[e], !0) : t;
          },
          resolve: function resolve(e) {
            for (var t = [], r = 1; r < arguments.length; r++) {
              t[r - 1] = arguments[r];
            }

            if (e in n) return n[e].resolved = !1, i.apply(void 0, p([e], t));
            throw new Error("Item [" + e + "] not found!");
          },
          on: function on(e, r, o) {
            return "*" === e && (e = ["bind", "get", "resolve", "forget", "replace"]), "string" == typeof r && (r = [r]), "string" == typeof e && (e = [e]), e.forEach(function (e) {
              r.forEach(function (t) {
                e = "on_" + e, t in n ? e in n[t] || (n[t][e] = []) : (n[t] = {}, n[t][e] = []), n[t][e].push(o);
              });
            }), t;
          },
          on_bind: function on_bind(e, t) {
            return this.on("bind", e, t);
          },
          on_get: function on_get(e, t) {
            return this.on("get", e, t);
          },
          on_resolve: function on_resolve(e, t) {
            return this.on("resolve", e, t);
          },
          on_forget: function on_forget(e, t) {
            return this.on("forget", e, t);
          },
          on_replace: function on_replace(e, t) {
            return this.on("replace", e, t);
          },
          register_collection: function register_collection(e) {
            var n = this;
            return e.map(function (e) {
              return n.register(e);
            }), t;
          },
          register: function register(e) {
            var n = new e(t);
            return this.provider(n), n;
          },
          provider: function provider(e) {
            return e.app = t, "boot" in e && r.push(e), "register" in e && "function" == typeof e.register && e.register(), e;
          },
          boot: function boot() {
            return this.execute("boot");
          },
          execute: function execute(e) {
            return "undefined" != typeof Window && document.dispatchEvent(new CustomEvent("bfg:" + e, {
              detail: t
            })), r.forEach(function (n) {
              e in n && "function" == typeof n[e] && n[e](t);
            }), t;
          }
        };
      }, u = new Proxy(function (e) {
        void 0 === e && (e = null);

        for (var t = [], r = 1; r < arguments.length; r++) {
          t[r - 1] = arguments[r];
        }

        return e ? i.apply(void 0, p([e], t)) : n;
      }, {
        get: function get(e, t) {
          var n = a(0, u);
          return t in n ? n[t] : i(t);
        },
        set: function set(e, t, r, o) {
          return !(u.has(t) && n[t].data === r || (a(0, u).bind(t, r), 0));
        },
        has: function has(e, t) {
          return u.has(t);
        }
      }));
    },
    992: function _(e, t) {
      var n = this && this.__spreadArrays || function () {
        for (var e = 0, t = 0, n = arguments.length; t < n; t++) {
          e += arguments[t].length;
        }

        var r = Array(e),
            o = 0;

        for (t = 0; t < n; t++) {
          for (var i = arguments[t], a = 0, u = i.length; a < u; a++, o++) {
            r[o] = i[a];
          }
        }

        return r;
      };

      Object.defineProperty(t, "__esModule", {
        value: !0
      }), t.EventCollect = void 0;

      var r = function () {
        function e() {
          return this.events = {}, e.global = this, new Proxy(function (t) {
            for (var r, o = [], i = 1; i < arguments.length; i++) {
              o[i - 1] = arguments[i];
            }

            return (r = e.global).fire.apply(r, n([t], o));
          }, {
            has: function has(t, n) {
              return "proxy" === n || e.global.has(n);
            },
            get: function get(t, r) {
              return r in e.global ? e.global[r] : function () {
                for (var t, o = [], i = 0; i < arguments.length; i++) {
                  o[i] = arguments[i];
                }

                return (t = e.global).fire.apply(t, n([r], o));
              };
            },
            set: function set(t, n, r, o) {
              return e.global.on(n, r);
            },
            deleteProperty: function deleteProperty(t, n) {
              return e.global.off(n);
            }
          });
        }

        return e.prototype.fire = function (e) {
          for (var t = [], n = 1; n < arguments.length; n++) {
            t[n - 1] = arguments[n];
          }

          return e in this.events && this.events[e].map(function (e) {
            if (e) {
              var n = e.apply(void 0, t);
              void 0 !== n && (t[0] = n);
            }
          }), 0 in t ? t[0] : null;
        }, e.prototype.on = function (e, t) {
          var n = this;
          Array.isArray(e) || (e = [e]), e.map(function (e) {
            e in n.events || (n.events[e] = []);
            var r = !1;
            n.events[e].map(function (e) {
              r || (r = e === t);
            }), "function" != typeof t || r ? Array.isArray(t) && !r && t.map(function (t) {
              "function" == typeof t && n.events[e].push(t);
            }) : n.events[e].push(t);
          });
        }, e.prototype.off = function (e, t) {
          if (e in this.events) {
            if ("function" == typeof t) {
              var n = !1;
              return this.events[e] = this.events[e].filter(function (e) {
                return n = !0, e !== t;
              }), n;
            }

            return delete this.events[e], !0;
          }

          return !1;
        }, e.prototype.has = function (e) {
          return e in this.events;
        }, e;
      }();

      t.EventCollect = r;
    },
    181: function _(e, t) {
      Object.defineProperty(t, "__esModule", {
        value: !0
      }), t.Json = void 0;

      var n = function () {
        function e() {}

        return e.prototype.encode = function (e, t, n) {
          try {
            return JSON.stringify(e, t, n);
          } catch (e) {
            return "";
          }
        }, e.prototype.decode = function (e, t) {
          try {
            return JSON.parse(e, t);
          } catch (e) {
            return {};
          }
        }, e;
      }();

      t.Json = n;
    },
    743: function _(e, t, n) {
      var _r,
          o = this && this.__extends || (_r = function r(e, t) {
        return (_r = Object.setPrototypeOf || {
          __proto__: []
        } instanceof Array && function (e, t) {
          e.__proto__ = t;
        } || function (e, t) {
          for (var n in t) {
            Object.prototype.hasOwnProperty.call(t, n) && (e[n] = t[n]);
          }
        })(e, t);
      }, function (e, t) {
        function n() {
          this.constructor = e;
        }

        _r(e, t), e.prototype = null === t ? Object.create(t) : (n.prototype = t.prototype, new n());
      });

      Object.defineProperty(t, "__esModule", {
        value: !0
      }), t.Kernel = void 0;

      var i = n(238),
          a = n(82),
          u = n(822),
          s = n(565),
          p = n(386),
          c = n(992),
          f = n(181),
          l = n(277),
          d = function (e) {
        function t() {
          return null !== e && e.apply(this, arguments) || this;
        }

        return o(t, e), t.prototype.register = function () {
          var e = this;

          if (this.app.register(l.Request), this.app.bind("start", new Date().getTime()), this.app.bind("env", "production"), this.app.bind("dev", !1), this.app.bind("console", console), this.app.bind("event", new c.EventCollect()), this.app.bind("system", t.sys, !0), this.app.bind("log", new p.Log(this.app)), this.app.bind("version", t.version, !0), this.app.compute("token", function () {
            return e.app.server.token;
          }), this.app.bind("os", t.os, !0), this.app.bind("str", new a.Str()), this.app.bind("obj", new u.Obj(this.app)), this.app.bind("num", new s.Num()), this.app.bind("json", new f.Json()), this.app.bind("is_browser", "browser" === String(this.app.system)), this.app.bind("data", {}), this.app.is_browser) {
            var n = document.getElementById("bfg-page-json");

            if (n) {
              var r = n.innerText,
                  o = this.app.json.decode(r);
              o && this.app.bind("data", o);
            }

            document.dispatchEvent(new CustomEvent("bfg:register", {
              detail: this.app
            }));
          }
        }, t.prototype.boot = function () {}, t.prototype.globalize = function () {
          "browser" === this.app.system ? window.app = this.app : "node" === this.app.system && (globalThis.app = this.app);
        }, t.version = function () {
          return "1.0.0";
        }, t.sys = function () {
          return "undefined" != typeof Window ? "browser" : "node";
        }, t.os = function () {
          if ("node" === t.sys()) return "CLI";
          var e = window.navigator.userAgent,
              n = window.navigator.platform,
              r = null;
          return -1 !== ["Macintosh", "MacIntel", "MacPPC", "Mac68K"].indexOf(n) ? r = "MacOS" : -1 !== ["iPhone", "iPad", "iPod"].indexOf(n) ? r = "iOS" : -1 !== ["Win32", "Win64", "Windows", "WinCE"].indexOf(n) ? r = "Windows" : /Android/.test(e) ? r = "Android" : /Linux/.test(n) && (r = "Linux"), r;
        }, t;
      }(i.ServiceProvider);

      t.Kernel = d;
    },
    386: function _(e, t) {
      var n = this && this.__spreadArrays || function () {
        for (var e = 0, t = 0, n = arguments.length; t < n; t++) {
          e += arguments[t].length;
        }

        var r = Array(e),
            o = 0;

        for (t = 0; t < n; t++) {
          for (var i = arguments[t], a = 0, u = i.length; a < u; a++, o++) {
            r[o] = i[a];
          }
        }

        return r;
      };

      Object.defineProperty(t, "__esModule", {
        value: !0
      }), t.Log = void 0;

      var r = function () {
        function e(t) {
          var r = this;
          return this.app = t, e.glob = this, e.getConsole = function (o, i) {
            return void 0 === i && (i = !1), function () {
              for (var a, u = [], s = 0; s < arguments.length; s++) {
                u[s] = arguments[s];
              }

              if ("!" === u[0]) return function (t) {
                for (var n = [], r = 1; r < arguments.length; r++) {
                  n[r - 1] = arguments[r];
                }

                return e.getConsole(o).apply(void 0, n);
              };

              if (i || t.dev) {
                var p = r.app.console;
                if (o in p) p[o].apply(p, n([e.glob.prompt], u));else if (o in e.glob) return "function" == typeof e.glob[o] ? (a = e.glob)[o].apply(a, u) : e.glob[o];
              }
            };
          }, new Proxy(e.glob.log, {
            get: function get(t, n) {
              return e.getConsole(n);
            },
            has: function has(e, t) {
              return "proxy" === t;
            }
          });
        }

        return e.prototype.log = function () {
          for (var t = [], n = 0; n < arguments.length; n++) {
            t[n] = arguments[n];
          }

          e.getConsole("log").apply(void 0, t);
        }, e.prototype.info = function () {
          for (var t = [], n = 0; n < arguments.length; n++) {
            t[n] = arguments[n];
          }

          e.getConsole("info").apply(void 0, t);
        }, e.prototype.warn = function () {
          for (var t = [], n = 0; n < arguments.length; n++) {
            t[n] = arguments[n];
          }

          e.getConsole("warn").apply(void 0, t);
        }, e.prototype.error = function () {
          for (var t = [], n = 0; n < arguments.length; n++) {
            t[n] = arguments[n];
          }

          e.getConsole("error").apply(void 0, t);
        }, e.prototype.table = function () {
          for (var t = [], n = 0; n < arguments.length; n++) {
            t[n] = arguments[n];
          }

          e.getConsole("table").apply(void 0, t);
        }, e.prototype.clear = function () {
          e.getConsole("clear")();
        }, Object.defineProperty(e.prototype, "prompt", {
          get: function get() {
            return "[" + new Date().toLocaleTimeString("en-US", {
              hour12: !1
            }) + "]:";
          },
          enumerable: !1,
          configurable: !0
        }), e;
      }();

      t.Log = r;
    },
    565: function _(e, t) {
      Object.defineProperty(t, "__esModule", {
        value: !0
      }), t.Num = void 0;

      var n = function () {
        function e() {}

        return e.prototype.isNumber = function (e) {
          return !isNaN(Number(e));
        }, e;
      }();

      t.Num = n;
    },
    822: function _(e, t) {
      var n = this && this.__spreadArrays || function () {
        for (var e = 0, t = 0, n = arguments.length; t < n; t++) {
          e += arguments[t].length;
        }

        var r = Array(e),
            o = 0;

        for (t = 0; t < n; t++) {
          for (var i = arguments[t], a = 0, u = i.length; a < u; a++, o++) {
            r[o] = i[a];
          }
        }

        return r;
      };

      Object.defineProperty(t, "__esModule", {
        value: !0
      }), t.Obj = void 0;

      var r = function () {
        function e(e) {
          this.app = e;
        }

        return e.prototype.getMethods = function (e) {
          var t = [];

          for (var n in e) {
            t.push(n);
          }

          return t;
        }, e.prototype.getElementAttrs = function (e) {
          var t = {};
          return [].slice.call(e.attributes).map(function (e) {
            t[e.name] = e.value;
          }), t;
        }, e.prototype.observer = function (e, t, n) {
          return void 0 === n && (n = !1), n ? new Proxy(e || {}, t || {}) : Proxy.revocable(e || {}, t || {});
        }, e.prototype.has = function (e, t) {
          return String(e).split(".").reduce(function (e, t) {
            return e[t];
          }, t);
        }, e.prototype.get = function (e, t) {
          return String(e).split(".").reduce(function (e, t) {
            return e[t];
          }, t);
        }, e.prototype.set = function (e, t, n) {
          var r = String(e).split("."),
              o = r.length - 1,
              i = n;
          r.some(function (e, n) {
            if (void 0 === e) return !0;
            if (n === o) i[e] = t;else {
              var r = i[e] || {};
              i[e] = r, i = r;
            }
          });
        }, e.prototype.each = function (e, t) {
          var n = Array.isArray(e) ? [] : {};
          return Object.keys(e).map(function (r) {
            return n[r] = t(e[r], r);
          }), n;
        }, e.prototype.get_start_with = function (e, t) {
          var n = this,
              r = null;
          return t = t.replace(/\*/g, "00110011"), Object.keys(e).map(function (o) {
            !r && n.app.str.start_with(e[o].replace(/\*/g, "00110011"), t) && (r = e[o]);
          }), r;
        }, e.prototype.get_end_with = function (e, t) {
          var n = this,
              r = null;
          return Object.keys(e).map(function (o) {
            !r && n.app.str.end_with(e[o], t) && (r = e[o]);
          }), r;
        }, e.prototype.flip = function (e) {
          var t,
              n = {};

          for (t in e) {
            e.hasOwnProperty(t) && (n[e[t]] = t);
          }

          return n;
        }, e.prototype.first_key = function (e) {
          var t = Object.keys(e);
          return 0 in t ? t[0] : null;
        }, e.prototype.last_key = function (e) {
          var t = Object.keys(e),
              n = t.length - 1;
          return n in t ? t[n] : null;
        }, e.prototype.first = function (e) {
          var t = this.first_key(e);
          return t ? e[t] : null;
        }, e.prototype.last = function (e) {
          var t = this.last_key(e);
          return t ? e[t] : null;
        }, e.prototype.merge_recursive = function (e) {
          for (var t, r, o = [], i = 1; i < arguments.length; i++) {
            o[i - 1] = arguments[i];
          }

          if (!o.length) return e;
          var a = o.shift();
          if (this.isObject(e) && this.isObject(a)) for (var u in a) {
            this.isObject(a[u]) ? (e[u] ? e[u] = Object.assign({}, e[u]) : Object.assign(e, ((t = {})[u] = {}, t)), this.merge_recursive(e[u], a[u])) : Object.assign(e, ((r = {})[u] = a[u], r));
          }
          var s = this.merge_recursive.apply(this, n([e], o));
          return s;
        }, e.prototype.isClass = function (e) {
          var t = String(e);
          return "[object Object]" === t && "function" == typeof e || /^class\s.*/.test(t.trim());
        }, e.prototype.isArray = function (e) {
          return Array.isArray(e);
        }, e.prototype.isEmptyObject = function (e) {
          return 0 === Object.keys(e).length;
        }, e.prototype.isObject = function (e) {
          return "[object Object]" === Object.prototype.toString.call(e);
        }, e.prototype.isArrayOrObject = function (e) {
          return Object(e) === e;
        }, e.prototype.dot = function (e, t, n, r, o, i) {
          var a = this;
          return void 0 === t && (t = {}), void 0 === n && (n = []), void 0 === r && (r = !1), void 0 === o && (o = !1), void 0 === i && (i = "."), Object.keys(e).forEach(function (u) {
            var s = a.isArray && r ? "[" + u + "]" : u;

            if (a.isArrayOrObject(e[u]) && (a.isObject(e[u]) && !a.isEmptyObject(e[u]) || a.isArray(e[u]) && !o && 0 !== e[u].length)) {
              if (a.isArray && r) {
                var p = n[n.length - 1] || "";
                return a.dot(e[u], t, n.slice(0, -1).concat(p + s));
              }

              return a.dot(e[u], t, n.concat(s));
            }

            a.isArray && r ? t[n.join(i).concat("[" + u + "]")] = e[u] : t[n.concat(s).join(i)] = e[u];
          }), t;
        }, e;
      }();

      t.Obj = r;
    },
    277: function _(e, t, n) {
      var _r2,
          o = this && this.__extends || (_r2 = function r(e, t) {
        return (_r2 = Object.setPrototypeOf || {
          __proto__: []
        } instanceof Array && function (e, t) {
          e.__proto__ = t;
        } || function (e, t) {
          for (var n in t) {
            Object.prototype.hasOwnProperty.call(t, n) && (e[n] = t[n]);
          }
        })(e, t);
      }, function (e, t) {
        function n() {
          this.constructor = e;
        }

        _r2(e, t), e.prototype = null === t ? Object.create(t) : (n.prototype = t.prototype, new n());
      });

      Object.defineProperty(t, "__esModule", {
        value: !0
      }), t.Request = void 0;

      var i = function (e) {
        function t() {
          return null !== e && e.apply(this, arguments) || this;
        }

        return o(t, e), t.prototype.register = function () {
          var e = this;
          this.app.bind("form_data", function (e) {
            var t = new FormData(),
                n = function n(e, r) {
              if (!e || "object" != _typeof(e) || e instanceof Date || e instanceof File || e instanceof Blob) {
                var o = null === e ? "" : e;
                r && t.append(r, o);
              } else Object.keys(e).forEach(function (t) {
                n(e[t], r ? r + "[" + t + "]" : t);
              });
            };

            return n(e), t;
          }), this.app.bind("request", function (t) {
            return t = t || {}, new Promise(function (n, r) {
              var o = new XMLHttpRequest();
              o.open(t.method || "GET", t.url || window.location.href), o.setRequestHeader("X-CSRF-TOKEN", t.token || e.app.server.token), o.setRequestHeader("X-Requested-With", "XMLHttpRequest"), "object" != _typeof(t.body) || t.body instanceof FormData || (o.setRequestHeader("Content-Type", "application/json"), t.body = e.app.json.encode(t.body)), o.send(t.body), t.headers && Object.keys(t.headers).forEach(function (e) {
                o.setRequestHeader(e, t.headers[e]);
              }), o.onload = function () {
                o.status >= 200 && o.status < 300 ? n({
                  data: e.app.json.decode(o.response),
                  xhr: o,
                  token: o.getResponseHeader("X-CSRF-TOKEN")
                }) : r(o);
              }, o.onerror = function () {
                return r(o);
              };
            });
          });
        }, t;
      }(n(238).ServiceProvider);

      t.Request = i;
    },
    238: function _(e, t) {
      Object.defineProperty(t, "__esModule", {
        value: !0
      }), t.ServiceProvider = void 0;

      t.ServiceProvider = function (e) {
        if (this.app = e, "require" in this && "object" == _typeof(this.require)) for (var t = 0, n = this.require; t < n.length; t++) {
          var r = n[t];
          if (!e.has("ext_" + r)) return e.log.error("Don't have a module [" + r + "]"), {};
        }
        "name" in this && e.bind("ext_" + this.name, !0);
      };
    },
    82: function _(e, t) {
      Object.defineProperty(t, "__esModule", {
        value: !0
      }), t.Str = void 0;

      var n = function () {
        function e() {}

        return e.prototype.to_nodes = function (e) {
          var t = document.createElement("div");
          return t.innerHTML = e.trim(), Object.assign([], t.childNodes);
        }, e.prototype.preg_match_all = function (e, t) {
          for (var n, r = []; null !== (n = e.exec(t));) {
            r.push(n);
          }

          return r;
        }, e.prototype.replace_tags = function (e, t, n) {
          return void 0 === n && (n = "{*}"), n = Array.isArray(n) ? n : n.split("*"), Object.keys(t).map(function (r) {
            e = e.replace(new RegExp(("" + n[0] + r + n[1]).replace(new RegExp("[.\\\\+*?\\[\\^\\]$(){}=!<>|:\\#-]", "g"), "\\$&"), "g"), t[r]);
          }), e;
        }, e.prototype.end_with = function (e, t) {
          return this.is("*" + t, e);
        }, e.prototype.start_with = function (e, t) {
          return this.is(t + "*", e);
        }, e.prototype.contains = function (e, t) {
          return this.is("*" + t + "*", e);
        }, e.prototype.dirname = function (e) {
          return e.replace(/\\/g, "/").replace(/\/[^/]*\/?$/, "");
        }, e.prototype.camel = function (e, t) {
          return void 0 === t && (t = !1), e.replace(/\-|\_/g, " ").replace(/(?:^\w|[A-Z]|\b\w)/g, function (e, n) {
            return t ? e.toUpperCase() : 0 === n ? e.toLowerCase() : e.toUpperCase();
          }).replace(/\s+/g, "");
        }, e.prototype.snake = function (e, t) {
          void 0 === t && (t = "_"), void 0 === t && (t = "-");
          var n = "-" === t ? "_" : "-";
          return (e = e.replace(n, t)).toLowerCase().replace(new RegExp(":", "g"), t).replace(new RegExp("\\s", "g"), t).replace(new RegExp("\\s\\s", "g"), t).replace(new RegExp("[" + t + t + "]+", "g"), t).replace(new RegExp("[^a-z0-9" + t + "\\s]", "g"), "");
        }, e.prototype.translit = function (e) {
          var t = {
            а: "a",
            б: "b",
            в: "v",
            г: "g",
            д: "d",
            е: "e",
            ё: "e",
            ж: "j",
            з: "z",
            и: "i",
            к: "k",
            л: "l",
            м: "m",
            н: "n",
            о: "o",
            п: "p",
            р: "r",
            с: "s",
            т: "t",
            у: "u",
            ф: "f",
            х: "h",
            ц: "c",
            ч: "ch",
            ш: "sh",
            щ: "shch",
            ы: "y",
            э: "e",
            ю: "u",
            я: "ya"
          },
              n = [];
          e = e.replace(/[ъь]+/g, "").replace(/й/g, "i");

          for (var r = 0; r < e.length; ++r) {
            n.push(t[e[r]] || void 0 === t[e[r].toLowerCase()] && e[r] || t[e[r].toLowerCase()].replace(/^(.)/, function (e) {
              return e.toUpperCase();
            }));
          }

          return n.join("");
        }, e.prototype.slug = function (e, t) {
          return void 0 === t && (t = "_"), this.snake(this.translit(e), t);
        }, e.prototype.query_get = function (e) {
          void 0 === e && (e = null);

          for (var t, n = /\+/g, r = /([^&=]+)=?([^&]*)/g, o = function o(e) {
            return decodeURIComponent(e.replace(n, " "));
          }, i = window.location.search.substring(1), a = {}; t = r.exec(i);) {
            a[o(t[1])] = o(t[2]);
          }

          return e ? a[e] : a;
        }, e.prototype.is = function (e, t) {
          return e = e.replace(new RegExp("[.\\\\+*?\\[\\^\\]$(){}=!<>|:\\#-]", "g"), "\\$&").replace(/\\\*/g, ".*"), new RegExp(e + "$", "u").test(t);
        }, e.prototype.trim = function (e, t) {
          t = t ? t.replace(/([\[\]\(\)\.\?\/\*\{\}\+\$\^\:])/g, "$1") : " s ";
          var n = new RegExp("^[" + t + "]+|[" + t + "]+$", "g");
          return e.replace(n, "");
        }, e.prototype.number_format = function (e, t, n, r) {
          var o, i;
          return void 0 === t && (t = 0), void 0 === n && (n = "."), void 0 === r && (r = ","), isNaN(t = Math.abs(t)) && (t = 2), void 0 === n && (n = ","), void 0 === r && (r = "."), (i = (o = parseInt(e = (+e || 0).toFixed(t)) + "").length) > 3 ? i %= 3 : i = 0, (i ? o.substr(0, i) + r : "") + o.substr(i).replace(/(\d{3})(?=\d)/g, "$1" + r) + (t ? n + Math.abs(Number(e) - parseInt(o)).toFixed(t).replace(/-/, "0").slice(2) : "");
        }, e.prototype.http_build_query = function (e, t, n) {
          void 0 === t && (t = null), void 0 === n && (n = null);
          var r = [];
          return null !== e && Object.keys(e).forEach(function (o) {
            var i = o;
            if (t && !isNaN(Number(i)) && (i = t + i), i = encodeURIComponent(i.replace(/[!'()*]/g, escape)), n && (i = n + "[" + i + "]"), "object" == _typeof(e[o])) r.push(i + "=" + JSON.stringify(e[o]));else {
              var a = encodeURIComponent(String(e[o]).replace(/[!'()*]/g, escape));
              r.push(i + "=" + a);
            }
          }), r.join("&");
        }, e;
      }();

      t.Str = n;
    },
    607: function _(e, t, n) {
      var r = this && this.__importDefault || function (e) {
        return e && e.__esModule ? e : {
          "default": e
        };
      };

      Object.defineProperty(t, "__esModule", {
        value: !0
      }), t.ServiceProvider = void 0;
      var o = r(n(355)),
          i = n(743);
      o["default"].has("start") || o["default"].register(i.Kernel), t.app = o["default"];
      var a = n(238);
      Object.defineProperty(t, "ServiceProvider", {
        enumerable: !0,
        get: function get() {
          return a.ServiceProvider;
        }
      }), t["default"] = o["default"];
    }
  },
      t = {};
  return function n(r) {
    if (t[r]) return t[r].exports;
    var o = t[r] = {
      exports: {}
    };
    return e[r].call(o.exports, o, o.exports, n), o.exports;
  }(607);
}());

/***/ }),

/***/ "../../../bfg-js/schema/bundle/index.js":
/*!**********************************************!*\
  !*** ../../../bfg-js/schema/bundle/index.js ***!
  \**********************************************/
/***/ ((__unused_webpack_module, exports) => {

function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

!function (t, e) {
  for (var n in e) {
    t[n] = e[n];
  }

  e.__esModule && Object.defineProperty(t, "__esModule", {
    value: !0
  });
}(exports, function () {
  var t = {
    535: function _(t, e) {
      !function (t, e) {
        for (var n in e) {
          t[n] = e[n];
        }

        e.__esModule && Object.defineProperty(t, "__esModule", {
          value: !0
        });
      }(e, function () {
        "use strict";

        var t = {
          355: function _(t, e) {
            var n,
                r,
                o,
                i,
                a,
                s,
                u = this && this.__assign || function () {
              return (u = Object.assign || function (t) {
                for (var e, n = 1, r = arguments.length; n < r; n++) {
                  for (var o in e = arguments[n]) {
                    Object.prototype.hasOwnProperty.call(e, o) && (t[o] = e[o]);
                  }
                }

                return t;
              }).apply(this, arguments);
            },
                p = this && this.__spreadArrays || function () {
              for (var t = 0, e = 0, n = arguments.length; e < n; e++) {
                t += arguments[e].length;
              }

              var r = Array(t),
                  o = 0;

              for (e = 0; e < n; e++) {
                for (var i = arguments[e], a = 0, s = i.length; a < s; a++, o++) {
                  r[o] = i[a];
                }
              }

              return r;
            };

            Object.defineProperty(e, "__esModule", {
              value: !0
            }), e["default"] = (n = {}, r = [], o = function o(t, e, r) {
              var o = "on_" + e,
                  i = {
                name: t,
                event: e
              };
              return t in n && o in n[t] && n[t][o].map(function (t) {
                var e = t(r, s, i);
                void 0 !== e && (r = e);
              }), "*" in n && o in n["*"] && n["*"][o].map(function (t) {
                var e = t(r, s, i);
                void 0 !== e && (r = e);
              }), r;
            }, i = function i(t) {
              for (var e = [], r = 1; r < arguments.length; r++) {
                e[r - 1] = arguments[r];
              }

              var i = null;

              if (t in n) {
                var a = n[t];
                if (!("data" in a)) return null;
                a.compute && "function" == typeof a.data ? i = a.data.apply(a, p([s], e)) : a.resolve && !a.resolved ? ("function" == typeof a.resolve ? n[t].data = a.resolve.apply(a, p([s], e)) : n[t].data = a.resolve, i = n[t].data = o(t, "resolve", n[t].data)) : i = n[t].data, n[t].resolved = !0, i = o(t, "get", i);
              }

              return i;
            }, a = function a(t, e) {
              return {
                bind: function bind(t, r, i, a) {
                  void 0 === i && (i = !1), void 0 === a && (a = !1);
                  var s = {
                    data: i ? null : r,
                    resolve: !!i && r,
                    resolved: !1,
                    compute: a
                  };
                  return t in n ? "data" in n[t] ? (s.etc = n[t], n[t] = o(t, "replace", s), delete s.etc) : n[t] = u(u({}, s), n[t]) : n[t] = o(t, "bind", s), e;
                },
                singleton: function singleton(t, n) {
                  return e.bind(t, n, !0);
                },
                compute: function compute(t, n) {
                  return e.bind(t, n, !1, !0);
                },
                library: function library(t) {
                  return "name" in t ? e.bind(t.name, new t(e)) : e;
                },
                inject: function inject(t) {
                  t.app = e, e.obj.getMethods(t).map(function (n) {
                    var r = n.split(":"),
                        o = null,
                        i = "bind";
                    1 === r.length ? (o = r[0], i = "bind") : (o = r[1], i = r[0]), "bind" !== i && "singleton" !== i && "compute" !== i && (i = "bind"), i && o && e[i](o, t[n]);
                  });
                },
                get: function get(t) {
                  void 0 === t && (t = null);

                  for (var e = [], r = 1; r < arguments.length; r++) {
                    e[r - 1] = arguments[r];
                  }

                  return t ? i.apply(void 0, p([t], e)) : n;
                },
                has: function has(t) {
                  return t in n;
                },
                forget: function forget(t) {
                  return t in n ? (o(t, "forget", n[t].data), delete n[t], !0) : e;
                },
                resolve: function resolve(t) {
                  for (var e = [], r = 1; r < arguments.length; r++) {
                    e[r - 1] = arguments[r];
                  }

                  if (t in n) return n[t].resolved = !1, i.apply(void 0, p([t], e));
                  throw new Error("Item [" + t + "] not found!");
                },
                on: function on(t, r, o) {
                  return "*" === t && (t = ["bind", "get", "resolve", "forget", "replace"]), "string" == typeof r && (r = [r]), "string" == typeof t && (t = [t]), t.forEach(function (t) {
                    r.forEach(function (e) {
                      t = "on_" + t, e in n ? t in n[e] || (n[e][t] = []) : (n[e] = {}, n[e][t] = []), n[e][t].push(o);
                    });
                  }), e;
                },
                on_bind: function on_bind(t, e) {
                  return this.on("bind", t, e);
                },
                on_get: function on_get(t, e) {
                  return this.on("get", t, e);
                },
                on_resolve: function on_resolve(t, e) {
                  return this.on("resolve", t, e);
                },
                on_forget: function on_forget(t, e) {
                  return this.on("forget", t, e);
                },
                on_replace: function on_replace(t, e) {
                  return this.on("replace", t, e);
                },
                register_collection: function register_collection(t) {
                  var n = this;
                  return t.map(function (t) {
                    return n.register(t);
                  }), e;
                },
                register: function register(t) {
                  var n = new t(e);
                  return this.provider(n), n;
                },
                provider: function provider(t) {
                  return t.app = e, "boot" in t && r.push(t), "register" in t && "function" == typeof t.register && t.register(), t;
                },
                boot: function boot() {
                  return this.execute("boot");
                },
                execute: function execute(t) {
                  return "undefined" != typeof Window && document.dispatchEvent(new CustomEvent("bfg:" + t, {
                    detail: e
                  })), r.forEach(function (n) {
                    t in n && "function" == typeof n[t] && n[t](e);
                  }), e;
                }
              };
            }, s = new Proxy(function (t) {
              void 0 === t && (t = null);

              for (var e = [], r = 1; r < arguments.length; r++) {
                e[r - 1] = arguments[r];
              }

              return t ? i.apply(void 0, p([t], e)) : n;
            }, {
              get: function get(t, e) {
                var n = a(0, s);
                return e in n ? n[e] : i(e);
              },
              set: function set(t, e, r, o) {
                return !(s.has(e) && n[e].data === r || (a(0, s).bind(e, r), 0));
              },
              has: function has(t, e) {
                return s.has(e);
              }
            }));
          },
          992: function _(t, e) {
            var n = this && this.__spreadArrays || function () {
              for (var t = 0, e = 0, n = arguments.length; e < n; e++) {
                t += arguments[e].length;
              }

              var r = Array(t),
                  o = 0;

              for (e = 0; e < n; e++) {
                for (var i = arguments[e], a = 0, s = i.length; a < s; a++, o++) {
                  r[o] = i[a];
                }
              }

              return r;
            };

            Object.defineProperty(e, "__esModule", {
              value: !0
            }), e.EventCollect = void 0;

            var r = function () {
              function t() {
                return this.events = {}, t.global = this, new Proxy(function (e) {
                  for (var r, o = [], i = 1; i < arguments.length; i++) {
                    o[i - 1] = arguments[i];
                  }

                  return (r = t.global).fire.apply(r, n([e], o));
                }, {
                  has: function has(e, n) {
                    return "proxy" === n || t.global.has(n);
                  },
                  get: function get(e, r) {
                    return r in t.global ? t.global[r] : function () {
                      for (var e, o = [], i = 0; i < arguments.length; i++) {
                        o[i] = arguments[i];
                      }

                      return (e = t.global).fire.apply(e, n([r], o));
                    };
                  },
                  set: function set(e, n, r, o) {
                    return t.global.on(n, r);
                  },
                  deleteProperty: function deleteProperty(e, n) {
                    return t.global.off(n);
                  }
                });
              }

              return t.prototype.fire = function (t) {
                for (var e = [], n = 1; n < arguments.length; n++) {
                  e[n - 1] = arguments[n];
                }

                return t in this.events && this.events[t].map(function (t) {
                  if (t) {
                    var n = t.apply(void 0, e);
                    void 0 !== n && (e[0] = n);
                  }
                }), 0 in e ? e[0] : null;
              }, t.prototype.on = function (t, e) {
                var n = this;
                Array.isArray(t) || (t = [t]), t.map(function (t) {
                  t in n.events || (n.events[t] = []);
                  var r = !1;
                  n.events[t].map(function (t) {
                    r || (r = t === e);
                  }), "function" != typeof e || r ? Array.isArray(e) && !r && e.map(function (e) {
                    "function" == typeof e && n.events[t].push(e);
                  }) : n.events[t].push(e);
                });
              }, t.prototype.off = function (t, e) {
                if (t in this.events) {
                  if ("function" == typeof e) {
                    var n = !1;
                    return this.events[t] = this.events[t].filter(function (t) {
                      return n = !0, t !== e;
                    }), n;
                  }

                  return delete this.events[t], !0;
                }

                return !1;
              }, t.prototype.has = function (t) {
                return t in this.events;
              }, t;
            }();

            e.EventCollect = r;
          },
          181: function _(t, e) {
            Object.defineProperty(e, "__esModule", {
              value: !0
            }), e.Json = void 0;

            var n = function () {
              function t() {}

              return t.prototype.encode = function (t, e, n) {
                try {
                  return JSON.stringify(t, e, n);
                } catch (t) {
                  return "";
                }
              }, t.prototype.decode = function (t, e) {
                try {
                  return JSON.parse(t, e);
                } catch (t) {
                  return {};
                }
              }, t;
            }();

            e.Json = n;
          },
          743: function _(t, e, n) {
            var _r,
                o = this && this.__extends || (_r = function r(t, e) {
              return (_r = Object.setPrototypeOf || {
                __proto__: []
              } instanceof Array && function (t, e) {
                t.__proto__ = e;
              } || function (t, e) {
                for (var n in e) {
                  Object.prototype.hasOwnProperty.call(e, n) && (t[n] = e[n]);
                }
              })(t, e);
            }, function (t, e) {
              function n() {
                this.constructor = t;
              }

              _r(t, e), t.prototype = null === e ? Object.create(e) : (n.prototype = e.prototype, new n());
            });

            Object.defineProperty(e, "__esModule", {
              value: !0
            }), e.Kernel = void 0;

            var i = n(238),
                a = n(82),
                s = n(822),
                u = n(565),
                p = n(386),
                c = n(992),
                l = n(181),
                f = n(277),
                d = function (t) {
              function e() {
                return null !== t && t.apply(this, arguments) || this;
              }

              return o(e, t), e.prototype.register = function () {
                var t = this;

                if (this.app.register(f.Request), this.app.bind("start", new Date().getTime()), this.app.bind("env", "production"), this.app.bind("dev", !1), this.app.bind("console", console), this.app.bind("event", new c.EventCollect()), this.app.bind("system", e.sys, !0), this.app.bind("log", new p.Log(this.app)), this.app.bind("version", e.version, !0), this.app.compute("token", function () {
                  return t.app.server.token;
                }), this.app.bind("os", e.os, !0), this.app.bind("str", new a.Str()), this.app.bind("obj", new s.Obj(this.app)), this.app.bind("num", new u.Num()), this.app.bind("json", new l.Json()), this.app.bind("is_browser", "browser" === String(this.app.system)), this.app.bind("data", {}), this.app.is_browser) {
                  var n = document.getElementById("bfg-page-json");

                  if (n) {
                    var r = n.innerText,
                        o = this.app.json.decode(r);
                    o && this.app.bind("data", o);
                  }

                  document.dispatchEvent(new CustomEvent("bfg:register", {
                    detail: this.app
                  }));
                }
              }, e.prototype.boot = function () {}, e.prototype.globalize = function () {
                "browser" === this.app.system ? window.app = this.app : "node" === this.app.system && (globalThis.app = this.app);
              }, e.version = function () {
                return "1.0.0";
              }, e.sys = function () {
                return "undefined" != typeof Window ? "browser" : "node";
              }, e.os = function () {
                if ("node" === e.sys()) return "CLI";
                var t = window.navigator.userAgent,
                    n = window.navigator.platform,
                    r = null;
                return -1 !== ["Macintosh", "MacIntel", "MacPPC", "Mac68K"].indexOf(n) ? r = "MacOS" : -1 !== ["iPhone", "iPad", "iPod"].indexOf(n) ? r = "iOS" : -1 !== ["Win32", "Win64", "Windows", "WinCE"].indexOf(n) ? r = "Windows" : /Android/.test(t) ? r = "Android" : /Linux/.test(n) && (r = "Linux"), r;
              }, e;
            }(i.ServiceProvider);

            e.Kernel = d;
          },
          386: function _(t, e) {
            var n = this && this.__spreadArrays || function () {
              for (var t = 0, e = 0, n = arguments.length; e < n; e++) {
                t += arguments[e].length;
              }

              var r = Array(t),
                  o = 0;

              for (e = 0; e < n; e++) {
                for (var i = arguments[e], a = 0, s = i.length; a < s; a++, o++) {
                  r[o] = i[a];
                }
              }

              return r;
            };

            Object.defineProperty(e, "__esModule", {
              value: !0
            }), e.Log = void 0;

            var r = function () {
              function t(e) {
                var r = this;
                return this.app = e, t.glob = this, t.getConsole = function (o, i) {
                  return void 0 === i && (i = !1), function () {
                    for (var a, s = [], u = 0; u < arguments.length; u++) {
                      s[u] = arguments[u];
                    }

                    if ("!" === s[0]) return function (e) {
                      for (var n = [], r = 1; r < arguments.length; r++) {
                        n[r - 1] = arguments[r];
                      }

                      return t.getConsole(o).apply(void 0, n);
                    };

                    if (i || e.dev) {
                      var p = r.app.console;
                      if (o in p) p[o].apply(p, n([t.glob.prompt], s));else if (o in t.glob) return "function" == typeof t.glob[o] ? (a = t.glob)[o].apply(a, s) : t.glob[o];
                    }
                  };
                }, new Proxy(t.glob.log, {
                  get: function get(e, n) {
                    return t.getConsole(n);
                  },
                  has: function has(t, e) {
                    return "proxy" === e;
                  }
                });
              }

              return t.prototype.log = function () {
                for (var e = [], n = 0; n < arguments.length; n++) {
                  e[n] = arguments[n];
                }

                t.getConsole("log").apply(void 0, e);
              }, t.prototype.info = function () {
                for (var e = [], n = 0; n < arguments.length; n++) {
                  e[n] = arguments[n];
                }

                t.getConsole("info").apply(void 0, e);
              }, t.prototype.warn = function () {
                for (var e = [], n = 0; n < arguments.length; n++) {
                  e[n] = arguments[n];
                }

                t.getConsole("warn").apply(void 0, e);
              }, t.prototype.error = function () {
                for (var e = [], n = 0; n < arguments.length; n++) {
                  e[n] = arguments[n];
                }

                t.getConsole("error").apply(void 0, e);
              }, t.prototype.table = function () {
                for (var e = [], n = 0; n < arguments.length; n++) {
                  e[n] = arguments[n];
                }

                t.getConsole("table").apply(void 0, e);
              }, t.prototype.clear = function () {
                t.getConsole("clear")();
              }, Object.defineProperty(t.prototype, "prompt", {
                get: function get() {
                  return "[" + new Date().toLocaleTimeString("en-US", {
                    hour12: !1
                  }) + "]:";
                },
                enumerable: !1,
                configurable: !0
              }), t;
            }();

            e.Log = r;
          },
          565: function _(t, e) {
            Object.defineProperty(e, "__esModule", {
              value: !0
            }), e.Num = void 0;

            var n = function () {
              function t() {}

              return t.prototype.isNumber = function (t) {
                return !isNaN(Number(t));
              }, t;
            }();

            e.Num = n;
          },
          822: function _(t, e) {
            var n = this && this.__spreadArrays || function () {
              for (var t = 0, e = 0, n = arguments.length; e < n; e++) {
                t += arguments[e].length;
              }

              var r = Array(t),
                  o = 0;

              for (e = 0; e < n; e++) {
                for (var i = arguments[e], a = 0, s = i.length; a < s; a++, o++) {
                  r[o] = i[a];
                }
              }

              return r;
            };

            Object.defineProperty(e, "__esModule", {
              value: !0
            }), e.Obj = void 0;

            var r = function () {
              function t(t) {
                this.app = t;
              }

              return t.prototype.getMethods = function (t) {
                var e = [];

                for (var n in t) {
                  e.push(n);
                }

                return e;
              }, t.prototype.getElementAttrs = function (t) {
                var e = {};
                return [].slice.call(t.attributes).map(function (t) {
                  e[t.name] = t.value;
                }), e;
              }, t.prototype.observer = function (t, e, n) {
                return void 0 === n && (n = !1), n ? new Proxy(t || {}, e || {}) : Proxy.revocable(t || {}, e || {});
              }, t.prototype.has = function (t, e) {
                return String(t).split(".").reduce(function (t, e) {
                  return t[e];
                }, e);
              }, t.prototype.get = function (t, e) {
                return String(t).split(".").reduce(function (t, e) {
                  return t[e];
                }, e);
              }, t.prototype.set = function (t, e, n) {
                var r = String(t).split("."),
                    o = r.length - 1,
                    i = n;
                r.some(function (t, n) {
                  if (void 0 === t) return !0;
                  if (n === o) i[t] = e;else {
                    var r = i[t] || {};
                    i[t] = r, i = r;
                  }
                });
              }, t.prototype.each = function (t, e) {
                var n = Array.isArray(t) ? [] : {};
                return Object.keys(t).map(function (r) {
                  return n[r] = e(t[r], r);
                }), n;
              }, t.prototype.get_start_with = function (t, e) {
                var n = this,
                    r = null;
                return e = e.replace(/\*/g, "00110011"), Object.keys(t).map(function (o) {
                  !r && n.app.str.start_with(t[o].replace(/\*/g, "00110011"), e) && (r = t[o]);
                }), r;
              }, t.prototype.get_end_with = function (t, e) {
                var n = this,
                    r = null;
                return Object.keys(t).map(function (o) {
                  !r && n.app.str.end_with(t[o], e) && (r = t[o]);
                }), r;
              }, t.prototype.flip = function (t) {
                var e,
                    n = {};

                for (e in t) {
                  t.hasOwnProperty(e) && (n[t[e]] = e);
                }

                return n;
              }, t.prototype.first_key = function (t) {
                var e = Object.keys(t);
                return 0 in e ? e[0] : null;
              }, t.prototype.last_key = function (t) {
                var e = Object.keys(t),
                    n = e.length - 1;
                return n in e ? e[n] : null;
              }, t.prototype.first = function (t) {
                var e = this.first_key(t);
                return e ? t[e] : null;
              }, t.prototype.last = function (t) {
                var e = this.last_key(t);
                return e ? t[e] : null;
              }, t.prototype.merge_recursive = function (t) {
                for (var e, r, o = [], i = 1; i < arguments.length; i++) {
                  o[i - 1] = arguments[i];
                }

                if (!o.length) return t;
                var a = o.shift();
                if (this.isObject(t) && this.isObject(a)) for (var s in a) {
                  this.isObject(a[s]) ? (t[s] ? t[s] = Object.assign({}, t[s]) : Object.assign(t, ((e = {})[s] = {}, e)), this.merge_recursive(t[s], a[s])) : Object.assign(t, ((r = {})[s] = a[s], r));
                }
                var u = this.merge_recursive.apply(this, n([t], o));
                return u;
              }, t.prototype.isClass = function (t) {
                var e = String(t);
                return "[object Object]" === e && "function" == typeof t || /^class\s.*/.test(e.trim());
              }, t.prototype.isArray = function (t) {
                return Array.isArray(t);
              }, t.prototype.isEmptyObject = function (t) {
                return 0 === Object.keys(t).length;
              }, t.prototype.isObject = function (t) {
                return "[object Object]" === Object.prototype.toString.call(t);
              }, t.prototype.isArrayOrObject = function (t) {
                return Object(t) === t;
              }, t.prototype.dot = function (t, e, n, r, o, i) {
                var a = this;
                return void 0 === e && (e = {}), void 0 === n && (n = []), void 0 === r && (r = !1), void 0 === o && (o = !1), void 0 === i && (i = "."), Object.keys(t).forEach(function (s) {
                  var u = a.isArray && r ? "[" + s + "]" : s;

                  if (a.isArrayOrObject(t[s]) && (a.isObject(t[s]) && !a.isEmptyObject(t[s]) || a.isArray(t[s]) && !o && 0 !== t[s].length)) {
                    if (a.isArray && r) {
                      var p = n[n.length - 1] || "";
                      return a.dot(t[s], e, n.slice(0, -1).concat(p + u));
                    }

                    return a.dot(t[s], e, n.concat(u));
                  }

                  a.isArray && r ? e[n.join(i).concat("[" + s + "]")] = t[s] : e[n.concat(u).join(i)] = t[s];
                }), e;
              }, t;
            }();

            e.Obj = r;
          },
          277: function _(t, e, n) {
            var _r2,
                o = this && this.__extends || (_r2 = function r(t, e) {
              return (_r2 = Object.setPrototypeOf || {
                __proto__: []
              } instanceof Array && function (t, e) {
                t.__proto__ = e;
              } || function (t, e) {
                for (var n in e) {
                  Object.prototype.hasOwnProperty.call(e, n) && (t[n] = e[n]);
                }
              })(t, e);
            }, function (t, e) {
              function n() {
                this.constructor = t;
              }

              _r2(t, e), t.prototype = null === e ? Object.create(e) : (n.prototype = e.prototype, new n());
            });

            Object.defineProperty(e, "__esModule", {
              value: !0
            }), e.Request = void 0;

            var i = function (t) {
              function e() {
                return null !== t && t.apply(this, arguments) || this;
              }

              return o(e, t), e.prototype.register = function () {
                var t = this;
                this.app.bind("form_data", function (t) {
                  var e = new FormData(),
                      n = function n(t, r) {
                    if (!t || "object" != _typeof(t) || t instanceof Date || t instanceof File || t instanceof Blob) {
                      var o = null === t ? "" : t;
                      r && e.append(r, o);
                    } else Object.keys(t).forEach(function (e) {
                      n(t[e], r ? r + "[" + e + "]" : e);
                    });
                  };

                  return n(t), e;
                }), this.app.bind("request", function (e) {
                  return e = e || {}, new Promise(function (n, r) {
                    var o = new XMLHttpRequest();
                    o.open(e.method || "GET", e.url || window.location.href), o.setRequestHeader("X-CSRF-TOKEN", e.token || t.app.server.token), o.setRequestHeader("X-Requested-With", "XMLHttpRequest"), "object" != _typeof(e.body) || e.body instanceof FormData || (o.setRequestHeader("Content-Type", "application/json"), e.body = t.app.json.encode(e.body)), o.send(e.body), e.headers && Object.keys(e.headers).forEach(function (t) {
                      o.setRequestHeader(t, e.headers[t]);
                    }), o.onload = function () {
                      o.status >= 200 && o.status < 300 ? n({
                        data: t.app.json.decode(o.response),
                        xhr: o,
                        token: o.getResponseHeader("X-CSRF-TOKEN")
                      }) : r(o);
                    }, o.onerror = function () {
                      return r(o);
                    };
                  });
                });
              }, e;
            }(n(238).ServiceProvider);

            e.Request = i;
          },
          238: function _(t, e) {
            Object.defineProperty(e, "__esModule", {
              value: !0
            }), e.ServiceProvider = void 0, e.ServiceProvider = function (t) {
              if (this.app = t, "require" in this && "object" == _typeof(this.require)) for (var e = 0, n = this.require; e < n.length; e++) {
                var r = n[e];
                if (!t.has("ext_" + r)) return t.log.error("Don't have a module [" + r + "]"), {};
              }
              "name" in this && t.bind("ext_" + this.name, !0);
            };
          },
          82: function _(t, e) {
            Object.defineProperty(e, "__esModule", {
              value: !0
            }), e.Str = void 0;

            var n = function () {
              function t() {}

              return t.prototype.to_nodes = function (t) {
                var e = document.createElement("div");
                return e.innerHTML = t.trim(), Object.assign([], e.childNodes);
              }, t.prototype.preg_match_all = function (t, e) {
                for (var n, r = []; null !== (n = t.exec(e));) {
                  r.push(n);
                }

                return r;
              }, t.prototype.replace_tags = function (t, e, n) {
                return void 0 === n && (n = "{*}"), n = Array.isArray(n) ? n : n.split("*"), Object.keys(e).map(function (r) {
                  t = t.replace(new RegExp(("" + n[0] + r + n[1]).replace(new RegExp("[.\\\\+*?\\[\\^\\]$(){}=!<>|:\\#-]", "g"), "\\$&"), "g"), e[r]);
                }), t;
              }, t.prototype.end_with = function (t, e) {
                return this.is("*" + e, t);
              }, t.prototype.start_with = function (t, e) {
                return this.is(e + "*", t);
              }, t.prototype.contains = function (t, e) {
                return this.is("*" + e + "*", t);
              }, t.prototype.dirname = function (t) {
                return t.replace(/\\/g, "/").replace(/\/[^/]*\/?$/, "");
              }, t.prototype.camel = function (t, e) {
                return void 0 === e && (e = !1), t.replace(/\-|\_/g, " ").replace(/(?:^\w|[A-Z]|\b\w)/g, function (t, n) {
                  return e ? t.toUpperCase() : 0 === n ? t.toLowerCase() : t.toUpperCase();
                }).replace(/\s+/g, "");
              }, t.prototype.snake = function (t, e) {
                void 0 === e && (e = "_"), void 0 === e && (e = "-");
                var n = "-" === e ? "_" : "-";
                return (t = t.replace(n, e)).toLowerCase().replace(new RegExp(":", "g"), e).replace(new RegExp("\\s", "g"), e).replace(new RegExp("\\s\\s", "g"), e).replace(new RegExp("[" + e + e + "]+", "g"), e).replace(new RegExp("[^a-z0-9" + e + "\\s]", "g"), "");
              }, t.prototype.translit = function (t) {
                var e = {
                  а: "a",
                  б: "b",
                  в: "v",
                  г: "g",
                  д: "d",
                  е: "e",
                  ё: "e",
                  ж: "j",
                  з: "z",
                  и: "i",
                  к: "k",
                  л: "l",
                  м: "m",
                  н: "n",
                  о: "o",
                  п: "p",
                  р: "r",
                  с: "s",
                  т: "t",
                  у: "u",
                  ф: "f",
                  х: "h",
                  ц: "c",
                  ч: "ch",
                  ш: "sh",
                  щ: "shch",
                  ы: "y",
                  э: "e",
                  ю: "u",
                  я: "ya"
                },
                    n = [];
                t = t.replace(/[ъь]+/g, "").replace(/й/g, "i");

                for (var r = 0; r < t.length; ++r) {
                  n.push(e[t[r]] || void 0 === e[t[r].toLowerCase()] && t[r] || e[t[r].toLowerCase()].replace(/^(.)/, function (t) {
                    return t.toUpperCase();
                  }));
                }

                return n.join("");
              }, t.prototype.slug = function (t, e) {
                return void 0 === e && (e = "_"), this.snake(this.translit(t), e);
              }, t.prototype.query_get = function (t) {
                void 0 === t && (t = null);

                for (var e, n = /\+/g, r = /([^&=]+)=?([^&]*)/g, o = function o(t) {
                  return decodeURIComponent(t.replace(n, " "));
                }, i = window.location.search.substring(1), a = {}; e = r.exec(i);) {
                  a[o(e[1])] = o(e[2]);
                }

                return t ? a[t] : a;
              }, t.prototype.is = function (t, e) {
                return t = t.replace(new RegExp("[.\\\\+*?\\[\\^\\]$(){}=!<>|:\\#-]", "g"), "\\$&").replace(/\\\*/g, ".*"), new RegExp(t + "$", "u").test(e);
              }, t.prototype.trim = function (t, e) {
                e = e ? e.replace(/([\[\]\(\)\.\?\/\*\{\}\+\$\^\:])/g, "$1") : " s ";
                var n = new RegExp("^[" + e + "]+|[" + e + "]+$", "g");
                return t.replace(n, "");
              }, t.prototype.number_format = function (t, e, n, r) {
                var o, i;
                return void 0 === e && (e = 0), void 0 === n && (n = "."), void 0 === r && (r = ","), isNaN(e = Math.abs(e)) && (e = 2), void 0 === n && (n = ","), void 0 === r && (r = "."), (i = (o = parseInt(t = (+t || 0).toFixed(e)) + "").length) > 3 ? i %= 3 : i = 0, (i ? o.substr(0, i) + r : "") + o.substr(i).replace(/(\d{3})(?=\d)/g, "$1" + r) + (e ? n + Math.abs(Number(t) - parseInt(o)).toFixed(e).replace(/-/, "0").slice(2) : "");
              }, t.prototype.http_build_query = function (t, e, n) {
                void 0 === e && (e = null), void 0 === n && (n = null);
                var r = [];
                return null !== t && Object.keys(t).forEach(function (o) {
                  var i = o;
                  if (e && !isNaN(Number(i)) && (i = e + i), i = encodeURIComponent(i.replace(/[!'()*]/g, escape)), n && (i = n + "[" + i + "]"), "object" == _typeof(t[o])) r.push(i + "=" + JSON.stringify(t[o]));else {
                    var a = encodeURIComponent(String(t[o]).replace(/[!'()*]/g, escape));
                    r.push(i + "=" + a);
                  }
                }), r.join("&");
              }, t;
            }();

            e.Str = n;
          },
          607: function _(t, e, n) {
            var r = this && this.__importDefault || function (t) {
              return t && t.__esModule ? t : {
                "default": t
              };
            };

            Object.defineProperty(e, "__esModule", {
              value: !0
            }), e.ServiceProvider = void 0;
            var o = r(n(355)),
                i = n(743);
            o["default"].has("start") || o["default"].register(i.Kernel), e.app = o["default"];
            var a = n(238);
            Object.defineProperty(e, "ServiceProvider", {
              enumerable: !0,
              get: function get() {
                return a.ServiceProvider;
              }
            }), e["default"] = o["default"];
          }
        },
            e = {};
        return function n(r) {
          if (e[r]) return e[r].exports;
          var o = e[r] = {
            exports: {}
          };
          return t[r].call(o.exports, o, o.exports, n), o.exports;
        }(607);
      }());
    },
    906: function _(t, e) {
      "use strict";

      Object.defineProperty(e, "__esModule", {
        value: !0
      }), e.Components = void 0;

      var n = function () {
        function t(t) {
          this.app = t, this.items = {};
        }

        return t.prototype.get = function (t) {
          return this.items[t];
        }, t.prototype.all = function () {
          return this.items;
        }, t.prototype.register = function (t, e) {
          return this.items[t] = e, this.app.event.has("register_component") && this.app.event.register_component(t, e), this;
        }, t.prototype["new"] = function (t) {
          t && "name" in t && this.app.components.register(t.name, t);
        }, t.prototype.has = function (t) {
          return t in this.items;
        }, t.prototype.names = function () {
          return Object.keys(this.items);
        }, t;
      }();

      e.Components = n;
    },
    665: function _(t, e) {
      "use strict";

      Object.defineProperty(e, "__esModule", {
        value: !0
      });

      var n = function () {
        function t() {}

        return t.prototype.is_schema_root_element = function (t) {
          return t && "er" in t.dataset;
        }, t.prototype.is_schema_child_element = function (t) {
          return t && "ec" in t.dataset;
        }, t.prototype.is_schema_element = function (t) {
          return this.is_schema_child_element(t) || this.is_schema_root_element(t);
        }, t.prototype.get_schema_rules = function (t) {
          if (this.is_schema_element(t)) {
            var e = "";
            this.is_schema_child_element(t) ? e = t.dataset.ec : this.is_schema_root_element(t) && (e = t.dataset.er);
            var n = t.dataset.a ? this.json.decode(t.dataset.a) : {},
                r = t.getAttribute("class");
            return r && (n["class"] = r), {
              id: e,
              e: String(e).split("#")[0],
              a: n,
              c: t.dataset.c ? this.json.decode(t.dataset.c) : {},
              v: t.dataset.v ? this.json.decode(t.dataset.v) : {},
              m: t.dataset.m ? this.json.decode(t.dataset.m) : []
            };
          }

          return null;
        }, t.prototype.get_element_dataset = function (t, e) {
          void 0 === e && (e = null);
        }, t;
      }();

      e["default"] = n;
    },
    171: function _(t, e) {
      "use strict";

      Object.defineProperty(e, "__esModule", {
        value: !0
      }), e.Schema = void 0;

      var n = function () {
        function t(t) {
          this.app = t;
        }

        return t.prototype.rules = function (t) {
          return this.app.get_schema_rules(t);
        }, t.prototype.build = function (t, e) {
          var n = this,
              r = document.createElement(t.nodeName),
              o = this.app.schema.apply_content(Object.assign([], t.childNodes));
          return this.app.obj.each(e.a, function (t, e) {
            r.setAttribute(e, t);
          }), this.app.obj.each(e.c, function (t, e) {
            var o;
            o = "string" == typeof t ? document.createRange().createContextualFragment(t) : n.build(r, t), r.appendChild(o);
          }), r.append.apply(r, o), r;
        }, t.prototype.apply_content = function (t) {
          var e = this;
          return t.map(function (t) {
            return t.dataset && "schemaChild" in t.dataset || t.dataset && "schemaChildId" in t.dataset && t.dataset.schemaChildId in e.app.data ? e.app.schema.build(t, e.app.schema.rules(t)) : t;
          });
        }, t.prototype.insert = function (t, e) {
          t.parentNode.replaceChild(e, t);
        }, t;
      }();

      e.Schema = n;
    },
    607: function _(t, e, n) {
      "use strict";

      var _r3,
          o = this && this.__extends || (_r3 = function r(t, e) {
        return (_r3 = Object.setPrototypeOf || {
          __proto__: []
        } instanceof Array && function (t, e) {
          t.__proto__ = e;
        } || function (t, e) {
          for (var n in e) {
            Object.prototype.hasOwnProperty.call(e, n) && (t[n] = e[n]);
          }
        })(t, e);
      }, function (t, e) {
        function n() {
          this.constructor = t;
        }

        _r3(t, e), t.prototype = null === e ? Object.create(e) : (n.prototype = e.prototype, new n());
      }),
          i = this && this.__importDefault || function (t) {
        return t && t.__esModule ? t : {
          "default": t
        };
      };

      Object.defineProperty(e, "__esModule", {
        value: !0
      });

      var a = n(535),
          s = n(906),
          u = n(171),
          p = i(n(665)),
          c = function (t) {
        function e() {
          var e = null !== t && t.apply(this, arguments) || this;
          return e.name = "schema", e;
        }

        return o(e, t), e.prototype.register = function () {
          var t = this,
              e = document.querySelectorAll("meta[name]"),
              n = {};
          Object.assign([], e).map(function (t) {
            var e = t.content;
            "null" === String(e).toLowerCase() ? e = null : "true" === String(e).toLowerCase() ? e = !0 : "false" === String(e).toLowerCase() && (e = !1), n[t.name] = e;
          }), this.app.inject(new p["default"]()), this.app.bind("server", n), this.app.bind("head", document.head), this.app.bind("body", document.body), this.app.bind("schema_class", u.Schema), this.app.singleton("schema_build", function () {
            return t.build();
          }), this.app.singleton("schema", function () {
            return new t.app.schema_class(t.app);
          }), this.app.singleton("components", function () {
            return new s.Components(t.app);
          }), this.app.singleton("elements", function () {
            return document.querySelectorAll("[data-er]");
          });
        }, e.prototype.boot = function () {
          this.app.schema_build && this.app.execute("schema_built");
        }, e.prototype.build = function () {
          var t = this,
              e = this.app.elements,
              n = "map" in e ? "map" : "forEach" in e && "forEach";
          return !1 !== n && (e[n](function (e) {
            t.app.schema.insert(e, t.app.schema.build(e, t.app.schema.rules(e)));
          }), !0);
        }, e;
      }(a.ServiceProvider);

      e["default"] = c;
    }
  },
      e = {};
  return function n(r) {
    if (e[r]) return e[r].exports;
    var o = e[r] = {
      exports: {}
    };
    return t[r].call(o.exports, o, o.exports, n), o.exports;
  }(607);
}());

/***/ }),

/***/ "./resources/js/admin.js":
/*!*******************************!*\
  !*** ./resources/js/admin.js ***!
  \*******************************/
/***/ ((__unused_webpack_module, __unused_webpack_exports, __webpack_require__) => {

var app = __webpack_require__(/*! bfg-js */ "../../../bfg-js/bfg-js/bundle/index.js").app;

app.register(__webpack_require__(/*! bfg-schema */ "../../../bfg-js/schema/bundle/index.js").default);
document.dispatchEvent(new CustomEvent("bfg:theme", {
  detail: app
}));
app.provider({
  register: function register() {
    app.bind('dev', "development" === 'development');

    if (true) {
      app.execute('globalize');
    }
  }
});
app.boot();

/***/ }),

/***/ "./resources/css/admin.css":
/*!*********************************!*\
  !*** ./resources/css/admin.css ***!
  \*********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		if(__webpack_module_cache__[moduleId]) {
/******/ 			return __webpack_module_cache__[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = __webpack_modules__;
/******/ 	
/******/ 	// the startup function
/******/ 	// It's empty as some runtime module handles the default behavior
/******/ 	__webpack_require__.x = x => {}
/************************************************************************/
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => Object.prototype.hasOwnProperty.call(obj, prop)
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/jsonp chunk loading */
/******/ 	(() => {
/******/ 		// no baseURI
/******/ 		
/******/ 		// object to store loaded and loading chunks
/******/ 		// undefined = chunk not loaded, null = chunk preloaded/prefetched
/******/ 		// Promise = chunk loading, 0 = chunk loaded
/******/ 		var installedChunks = {
/******/ 			"/public/js/admin": 0
/******/ 		};
/******/ 		
/******/ 		var deferredModules = [
/******/ 			["./resources/js/admin.js"],
/******/ 			["./resources/css/admin.css"]
/******/ 		];
/******/ 		// no chunk on demand loading
/******/ 		
/******/ 		// no prefetching
/******/ 		
/******/ 		// no preloaded
/******/ 		
/******/ 		// no HMR
/******/ 		
/******/ 		// no HMR manifest
/******/ 		
/******/ 		var checkDeferredModules = x => {};
/******/ 		
/******/ 		// install a JSONP callback for chunk loading
/******/ 		var webpackJsonpCallback = (parentChunkLoadingFunction, data) => {
/******/ 			var [chunkIds, moreModules, runtime, executeModules] = data;
/******/ 			// add "moreModules" to the modules object,
/******/ 			// then flag all "chunkIds" as loaded and fire callback
/******/ 			var moduleId, chunkId, i = 0, resolves = [];
/******/ 			for(;i < chunkIds.length; i++) {
/******/ 				chunkId = chunkIds[i];
/******/ 				if(__webpack_require__.o(installedChunks, chunkId) && installedChunks[chunkId]) {
/******/ 					resolves.push(installedChunks[chunkId][0]);
/******/ 				}
/******/ 				installedChunks[chunkId] = 0;
/******/ 			}
/******/ 			for(moduleId in moreModules) {
/******/ 				if(__webpack_require__.o(moreModules, moduleId)) {
/******/ 					__webpack_require__.m[moduleId] = moreModules[moduleId];
/******/ 				}
/******/ 			}
/******/ 			if(runtime) runtime(__webpack_require__);
/******/ 			if(parentChunkLoadingFunction) parentChunkLoadingFunction(data);
/******/ 			while(resolves.length) {
/******/ 				resolves.shift()();
/******/ 			}
/******/ 		
/******/ 			// add entry modules from loaded chunk to deferred list
/******/ 			if(executeModules) deferredModules.push.apply(deferredModules, executeModules);
/******/ 		
/******/ 			// run deferred modules when all chunks ready
/******/ 			return checkDeferredModules();
/******/ 		}
/******/ 		
/******/ 		var chunkLoadingGlobal = self["webpackChunk"] = self["webpackChunk"] || [];
/******/ 		chunkLoadingGlobal.forEach(webpackJsonpCallback.bind(null, 0));
/******/ 		chunkLoadingGlobal.push = webpackJsonpCallback.bind(null, chunkLoadingGlobal.push.bind(chunkLoadingGlobal));
/******/ 		
/******/ 		function checkDeferredModulesImpl() {
/******/ 			var result;
/******/ 			for(var i = 0; i < deferredModules.length; i++) {
/******/ 				var deferredModule = deferredModules[i];
/******/ 				var fulfilled = true;
/******/ 				for(var j = 1; j < deferredModule.length; j++) {
/******/ 					var depId = deferredModule[j];
/******/ 					if(installedChunks[depId] !== 0) fulfilled = false;
/******/ 				}
/******/ 				if(fulfilled) {
/******/ 					deferredModules.splice(i--, 1);
/******/ 					result = __webpack_require__(__webpack_require__.s = deferredModule[0]);
/******/ 				}
/******/ 			}
/******/ 			if(deferredModules.length === 0) {
/******/ 				__webpack_require__.x();
/******/ 				__webpack_require__.x = x => {};
/******/ 			}
/******/ 			return result;
/******/ 		}
/******/ 		var startup = __webpack_require__.x;
/******/ 		__webpack_require__.x = () => {
/******/ 			// reset startup function so it can be called again when more startup code is added
/******/ 			__webpack_require__.x = startup || (x => {});
/******/ 			return (checkDeferredModules = checkDeferredModulesImpl)();
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
/******/ 	// run startup
/******/ 	return __webpack_require__.x();
/******/ })()
;