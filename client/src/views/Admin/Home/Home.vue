<template>
    <div class="admin-contentContainer">
        <div class="card">
            <div class="card-content">
                <span class="card-title">Business Shape</span>
                <div v-if="isLoading" class="Spinner__Container user__search__spiner" v-bind:style="{top: 0, 'z-index': 9999}">
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
                    <a @click="this.downloadCSV" class="waves-effect waves-light btn" target="_blank">Download CSV</a>
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
    import BusinessShape from "../../../Components/BusinessShape/BusinessShape";
    import './css/Home.scss';
    import { mapActions, mapMutations, mapState, mapGetters } from 'vuex';

    export default {
        name: "AdminHome",
        components: {
            BusinessShape
        },
        props: [],
        data() {
            return {

            }
        },
        methods: {

            ...mapActions('AdminHome', [
                'adminHomeApi'
            ]),
            ...mapActions('Sidebar', [
                'downloadCSV'
            ]),
        },
        mounted() {
        },
        computed: {
            ...mapState('AdminHome', [
                'isLoading'
            ]),
            ...mapGetters('AdminHome', [
                'getAssociateInLevels',
                'getLevels',
                'getMaxLevel'
            ]),
        },
        async created() {
            await this.adminHomeApi();
        }
    }
</script>

<style scoped>

</style>