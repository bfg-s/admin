<template>
    <form class="auth-form login-form" @submit.prevent.stop="send">
        <div v-if="error_message" class="alert alert-danger">
            {{error_message}}
        </div>
        <div class="email mb-3">
            <label class="sr-only" for="signin-login">Login</label>
            <input id="signin-login" v-model="form.login" type="text" :class="{'form-control signin-email': true, 'is-invalid': !!errors.login}" placeholder="Login name">
            <div v-if="errors.login" v-for="mess in errors.login" class="invalid-feedback">{{mess}}</div>
        </div>
        <div class="password mb-3">
            <label class="sr-only" for="signin-password">Password</label>
            <input id="signin-password" v-model="form.password" type="password" :class="{'form-control signin-password': true, 'is-invalid': !!errors.password}" placeholder="Password">
            <div v-if="errors.password" v-for="mess in errors.password" class="invalid-feedback">{{mess}}</div>
            <div class="extra mt-3 row justify-content-between">
                <div class="col-12">
                    <div class="form-check">
                        <input class="form-check-input" v-model="form.remember" type="checkbox" value="" id="RememberPassword">
                        <label class="form-check-label" for="RememberPassword">
                            Remember me
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center">
            <button type="submit" class="btn app-btn-primary btn-block theme-btn mx-auto">Log In</button>
        </div>
    </form>
</template>

<script>
    export default {
        name: "aui::servicePages.login.form",
        props: {
            submit: {}
        },
        data () {
            return {
                error_message: null,
                errors: {},
                form: {
                    login: null,
                    password: null,
                    remember: false,
                }
            };
        },
        mounted () {},
        computed: {},
        watch: {},
        methods: {
            send () {
                this.submit(this.form);
            }
        }
    }
</script>f