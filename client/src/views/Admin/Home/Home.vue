<template>
    <div class="admin-contentContainer">
        <div class="card">
            <div class="card-content">
                <span class="card-title">Business Shape</span>
                <div v-if="isLoading" class="Spinner__Container">
                    <div class="lds-dual-ring"/>
                </div>
                <BusinessShape
                    v-else
                    v-bind:associatesInLevels="this.getAssociateInLevels"
                    v-bind:levels="this.getLevels"
                    v-bind:maxLevel="this.getMaxLevel"
                >
                </BusinessShape>
                <div class="adminBusinessShape__buttonContainer">
                    <a @click="downloadCsv" class="waves-effect waves-light btn"
                     target="_blank">Download CSV</a>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-content">
                <span class="card-title">Associate Explorer</span>
                <div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import {
    mapActions, mapState, mapGetters, mapMutations,
} from 'vuex';
import BusinessShape from '../../../components/BusinessShape/BusinessShape.vue';
import './css/Home.scss';

export default {
    name: 'AdminHome',
    components: {
        BusinessShape,
    },
    props: [],
    data() {
        return {

        };
    },
    methods: {
        downloadCsv() {
            const dependencies = {
                logout: this.logout,
                router: this.$router,
            };
            this.downloadCSV(dependencies);
        },
        ...mapActions('AdminHome', [
            'adminHomeApi',
        ]),
        ...mapActions('Sidebar', [
            'downloadCSV',
        ]),
        ...mapMutations('Security', [
            'logout',
        ]),
    },
    mounted() {
    },
    computed: {
        ...mapState('AdminHome', [
            'isLoading',
        ]),
        ...mapGetters('AdminHome', [
            'getAssociateInLevels',
            'getLevels',
            'getMaxLevel',
        ]),
    },
    async created() {
        const dependencies = {
            router: this.$router,
            logout: this.logout,
        };
        await this.adminHomeApi(dependencies);
    },
};
</script>

<style scoped>
</style>
