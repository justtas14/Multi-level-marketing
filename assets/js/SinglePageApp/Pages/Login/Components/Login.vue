<template>
    <div>

    </div>
</template>

<script>
    import '../css/Login.scss'
    import { mapActions, mapMutations, mapState, mapGetters } from 'vuex'

    export default {
        name: "Login",
        components: {
        },
        props: [],
        data() {
            return {
                login: "",
                password: ""
            }
        },
        methods: {
            async performLogin() {
                let payload = {login: this.$data.login, password: this.$data.password},
                    redirect = this.$route.query.redirect;

                await this.login(payload);
                if (!this.hasError) {
                    if (typeof redirect !== "undefined") {
                        this.$router.push({path: redirect});
                    } else {
                        this.$router.push({path: "/home"});
                    }
                }
            },

            ...mapActions('Security', [
                'login'
            ]),
            ...mapMutations('Security', [
            ])
        },
        computed: {
            ...mapGetters('Security', [
                'hasError'
            ]),
            ...mapState('Security', [
                'isAuthenticated',
                'error',
                'isLoading'
            ])
        },
        created() {
            let redirect = this.$route.query.redirect;

            if (this.isAuthenticated) {
                if (typeof redirect !== "undefined") {
                    this.$router.push({path: redirect});
                } else {
                    this.$router.push({path: "/home"});
                }
            }
        }
    }
</script>

<style scoped>

</style>