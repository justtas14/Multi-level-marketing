<template>
    <div class="login-container">
        <div class="login-logoContainer">
            <img v-if="this.checkConfigurationMainLogo"
            style="max-width:100%; max-height: 100px" :src="configuration.mainLogoPath" />
            <img v-else style="max-width:100%; max-height: 100px" :src="plumTreeLogo"/>
        </div>
        <div class="login-title">
            Login
        </div>
        <div class="login-inputContainer">
            <form action="" method="post">
                <div class="login-item">
                    <div class="input-field col s12">
                        <label for="email">E-mail</label>
                        <input v-model="email" class="validate" type="text"
                         id="email" name="email"/>
                    </div>
                </div>
                <div class="login-item">
                    <div class="input-field col s12">
                        <label for="password">Password:</label>
                        <input v-model="password" class="validate"
                         type="password" id="password" name="password"/>
                    </div>
                </div>
                <div id="resetPassword">
                    Forgot your password?
                    <a href="#">Click here</a>
                </div>
                <div class="login-buttonWrap">
                    <button
                        class="waves-effect waves-light btn"
                        style="background-color:#3ab54a"
                        type="button"
                        :disabled="email.length === 0 || password.length === 0 || isLoading"
                        @click="performLogin()"
                    >
                    <div v-if="this.isLoading" class="Spinner__Container">
                        <div class="lds-dual-ring buttonSpinner"/>
                    </div>
                        Login
                    </button>
                </div>
            </form>
            <div id="loggingLogs">
                <div class="login-errorMessageContainer" v-if="hasError">
                    <Error v-bind:message="error"></Error>
                </div>
                <div class="login-successMessageContainer" v-if="isLoggedIn">
                    <Success v-bind:message="'Success'"></Success>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import {
    mapActions, mapMutations, mapState, mapGetters,
} from 'vuex';
import plumTreeLogo from '../../../../public/img/plum_tree_logo.png';
import Error from '../../../components/Messages/Error.vue';
import Success from '../../../components/Messages/Success.vue';

export default {
    name: 'Login',
    components: {
        Error,
        Success,
    },
    props: [],
    data() {
        return {
            email: '',
            password: '',
            plumTreeLogo,
        };
    },
    methods: {
        async performLogin() {
            const payload = { login: this.email, password: this.password };
            const { redirect } = this.$route.query;
            await this.login(payload);
            if (!this.hasError) {
                await this.setCookie();
                await this.loadAssociate();
                if (typeof redirect !== 'undefined') {
                    this.$router.push({ path: redirect });
                } else if (this.isAdmin) {
                    this.$router.push({ path: '/admin' });
                } else {
                    this.$router.push({ path: '/associate' });
                }
            }
        },

        ...mapActions('Security', [
            'login',
            'setCookie',
            'loadAssociate',
        ]),
        ...mapMutations('Security', [
        ]),
    },
    computed: {
        ...mapGetters('Security', [
            'hasError',
            'isAdmin',
            'isAuthenticated',
        ]),
        ...mapGetters('Sidebar', [
            'checkConfigurationMainLogo',
        ]),
        ...mapState('Security', [
            'error',
            'isLoading',
            'isLoggedIn',
        ]),
        ...mapState('Sidebar', [
            'configuration',
        ]),
    },
    created() {
        const { redirect } = this.$route.query;

        if (this.isAuthenticated) {
            if (typeof redirect !== 'undefined') {
                this.$router.push({ path: redirect });
            } else if (this.isAdmin) {
                this.$router.push({ path: '/admin' });
            } else {
                this.$router.push({ path: '/associate' });
            }
        }
    },
};
</script>

<style lang="scss" scoped>
    @import '../css/Login.scss';
</style>
