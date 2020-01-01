<template>
    <div class="admin-contentContainer">
        <div class="card" v-if="checkEndPrelaunch">
            <div class="card-content">
                <div class="landingContent" v-html="this.getLandingContent">
                </div>
            </div>
        </div>
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
                <rootExplorer v-bind:path="'/api/admin/explorer'"></rootExplorer>
            </div>
        </div>
    </div>
</template>

<script>
import {
    mapActions, mapState, mapGetters, mapMutations,
} from 'vuex';
import BusinessShape from '../../../components/BusinessShape/BusinessShape.vue';
import rootExplorer from '../../../components/RootExplorer/rootExplorer.vue';

export default {
    name: 'AdminHome',
    components: {
        BusinessShape,
        rootExplorer,
    },
    props: [],
    data() {
        return {

        };
    },
    methods: {
        downloadCsv() {
            this.downloadCSV();
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
        ...mapGetters('Sidebar', [
            'checkEndPrelaunch',
            'getLandingContent',
        ]),
    },
    async created() {
        await this.adminHomeApi();
    },
};
</script>

<style lang="scss" scoped>
    @import './css/Home.scss';
</style>
