<template>
    <div id="app">
      <Sidebar v-if="this.isAuthenticated && this.getAssociate"></Sidebar>
      <router-view :key="$route ? $route.fullPath: ''"></router-view>
    </div>
</template>

<script>
import './assets/css/Main.scss';
import './assets/css/admin.scss';
import './assets/css/associate.scss';
import './assets/css/header.scss';
import './assets/css/hamburgers.scss';

import {
    mapActions, mapMutations, mapState, mapGetters,
} from 'vuex';
import Sidebar from './sidebar/Components/Sidebar.vue';
import Parameters from '../parameters';

export default {
    name: 'Main',
    components: {
        Sidebar,
    },
    props: [],
    data() {
        return {
        };
    },
    methods: {
        ...mapActions('Security', [
            'setCookie',
            'loadAssociate',
        ]),
        ...mapActions('Sidebar', [
            'configurationApi',
        ]),
        ...mapMutations('Security', [
            'authenticatingSuccess',
            'logoutAction',
            'setLogout',
        ]),
    },
    mounted() {
    },
    computed: {
        ...mapGetters('Security', [
            'isAdmin',
            'getAssociate',
            'isAuthenticated',
        ]),
        ...mapState('Security', [
            'isLoading',
            'logout',
        ]),
    },
    async created() {
        await this.configurationApi();

        const urlString = window.location.href;
        const url = new URL(urlString);
        const token = url.searchParams.get('token');

        if (token) {
            this.authenticatingSuccess(token);
            await this.setCookie();
            await this.loadAssociate();
            if (this.isAdmin) {
                this.$router.push({ path: '/admin' });
            } else {
                this.$router.push({ path: '/associate' });
            }
        }
        if (!this.isAuthenticated) {
            console.log('came back');
            window.location.href = `${Parameters.API_HOST_URL}/authenticateFlow?redirect_uri=${url.origin}&client_id=${Parameters.CLIENT_ID}`;
        }
    },
};
</script>
