<template>
    <form class="auth-form login-form" @submit.prevent.stop="submit">
        <div class="email mb-3">
            <label class="sr-only" for="signin-email">Email</label>
            <input id="signin-email" name="signin-email" type="email" class="form-control signin-email" placeholder="Email address" required="required">
        </div>
        <div class="password mb-3">
            <label class="sr-only" for="signin-password">Password</label>
            <input id="signin-password" name="signin-password" type="password" class="form-control signin-password" placeholder="Password" required="required">
            <div class="extra mt-3 row justify-content-between">
                <div class="col-6">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="" id="RememberPassword">
                        <label class="form-check-label" for="RememberPassword">
                            Remember me
                        </label>
                    </div>
                </div>
                <div class="col-6">
                    <div class="forgot-password text-right">
                        <a href="reset-password.html">Forgot password?</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center">
            <button type="submit" @click="submit" class="btn app-btn-primary btn-block theme-btn mx-auto">Log In</button>
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

            };
        },
        async mounted () {
            console.log(await this.submit());
        },
        computed: {},
        watch: {},
        methods: {}
    }
</script>f