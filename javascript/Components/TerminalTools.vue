<template>
    <div>
        <VueTerminal
             :console-sign="`${admin.name}@${admin.login}$`"
             height="300px"
             :command="cmd"
             :completion="completion"
             ref="terminal">
        </VueTerminal>
    </div>
</template>

<script>

    import VueTerminal from './Terminal/VueTerminal'
    import get from 'lodash/get'

    export default {
        name: 'TerminalTools',
        components: {
            VueTerminal
        },
        props: [],
        $sync: ['admin'],
        data () {
            return {
                admin: {name: '', login: ''},
                args: [],
                commands: ['admin', 'jax', 'exe'],
                command_infos: {

                }
            };
        },
        watch: {
        },
        computed: {
            term () { return this.$refs.terminal.$ptty; },
            $prompt () { return this.term.get_terminal('.prompt'); },
            $input  () { return this.$prompt.find('.input'); }
        },
        mounted () {
            this.commands.map(k => {
                if (`command_${k}` in this) {
                    this.make_command(k, this[`command_${k}`], this.command_infos[k])
                }
            })
        },
        methods: {
            cmd (cmd_opts, name, ...args) {
                return this.command_exe(cmd_opts, 'exe', name, ...args);
            },
            command_cb_wrapper (cb)  {

                return ((std) => {
                    let last = this.term.get_command_option('last'),
                        args = last.split(' ');

                    return cb(std, ...args);
                }).bind(this);
            },
            make_command (name, cb, help = "(empty)")  {

                this.term.register('command', {
                    name: name,
                    method: this.command_cb_wrapper(cb),
                    help: help
                })
            },
            command_admin (std, name, ...params) {

                let result = this.admin

                if (params.length) {
                    let out = {};
                    params.map((k) => out[k] = get(result, k))
                    result = out;
                }

                std.out = `<pre>${JSON.stringify(result,  null, 2)}</pre>`;

                return std;
            },
            command_jax (std, name, jax_path, ...params) {

                if (!jax_path) {
                    std.out = "jax [jax name] ...params";
                }  else {
                    std = false;
                    this.hide();
                    jax.path(jax_path, ...params).then((data) => {
                        this.e(data, true);
                    }).catch((e) => {
                        this.e(e, true);
                    });
                }
                return std;
            },
            command_exe (std, name, command, ...params) {

                if (!command) {
                    std.out = "exe [executor name] ...params";
                } else {
                    this.hide();
                    let result;

                    try {
                        result = ljs.exec(command, [...params], {
                            terminal: {$input: this.$input, prompt: this.$prompt},
                            target: this.$input[0],
                            term: this.term
                        });
                    } catch (e) {
                        result = e;
                    }

                    if (result instanceof Promise) {
                        std.out = "Loading...";
                        result.then((data) => {
                            this.e(data);
                        }).catch((data) => {
                            this.e(data);
                        }).finally(() => {
                            this.show();
                        })
                    } else {
                        if (typeof data  === 'object') {
                            std.out  = `<pre>${JSON.stringify(result,  null, 2)}</pre>`;
                        } else {
                            std.out  = result;
                        }
                        this.show();
                    }
                }

                return std;
            },
            e (data, show = false) {
                if (typeof data  === 'object') {
                    this.term.echo(`<pre>${JSON.stringify(data,  null, 2)}</pre>`);
                } else {
                    this.term.echo(data);
                }
                if (show) {
                    this.show();
                }
            },
            show () {
                this.$input.show();
                this.$input.focus();
            },
            hide () {
                this.$input.hide();
            },
            completion (commands, str) {
                if (str === '') {
                    Object.keys(ljs.executor).map(k => commands.push(k));
                    Object.keys(ljs.executor_lite).map(k => commands.push(k));
                } else {
                    if ( str.indexOf( '::' ) === -1 ) {
                        Object.keys(ljs.executor).map(k => {
                            if ( k.indexOf( str ) === 0 ) {
                                commands.push(k)
                            }
                        });
                        Object.keys(ljs.executor_lite).map(k => {
                            if ( k.indexOf( str ) === 0 ) {
                                commands.push(k)
                            }
                        });
                    } else {
                        let exec = str.split('::');
                        let obj = ljs.executor[exec[0]];
                        if (obj) {
                            Object.getOwnPropertyNames(Object.getPrototypeOf(obj)).map((i) => {
                                if (i !== 'constructor') {
                                    if ( i.indexOf( exec[1] ) === 0 ) {
                                        commands.push(`${exec[0]}::${i}`)
                                    }
                                }
                            });
                        }
                    }
                }
                return commands;
            }
        }
    }
</script>