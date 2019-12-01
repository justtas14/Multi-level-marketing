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
        ]),
        ...mapActions('Sidebar', [
            'configurationApi',
        ]),
        ...mapMutations('Security', [
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
        ]),
    },
    async created() {
        await this.configurationApi();

        if (!this.isAuthenticated) {
            this.$router.push({ path: '/login' });
        }
    },
};
</script>
