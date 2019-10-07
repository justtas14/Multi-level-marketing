<template>
    <div id="loggingApp">
        <LogTable
            v-bind:logs="logs"
            v-bind:spinner="spinner"
        ></LogTable>
        <GalleryPagination v-bind:paginationInfo="paginationInfo"/>
    </div>
</template>

<script>
    import '../css/logging.scss';
    import GalleryPagination from "../../GalleryApp/Components/GalleryPagination";
    import LogTable from "./LogTable";
    import { mapActions, mapMutations, mapState } from 'vuex'
    import EventBus from '../EventBus/EventBus';
    import EventBusFromGallery from '../../GalleryApp/EventBus/EventBus';

    export default {
        name: "Logging",
        props: [],
        components: {
            GalleryPagination,
            LogTable
        },
        methods: {
            changePage: function(page) {
                const params = {
                    page
                };
                this.setSpinnerState(true);
                this.findBy(params);
            },
            loadLogs: function(page = null) {
                const params = {};
                if (page) {
                    params['page'] = page;
                }

                this.setSpinnerState(true);
                this.findBy(params);
            },

            ...mapActions('Logs', [
                'findBy',
                'findAll'
            ]),
            ...mapMutations('Logs', [
                'setSpinnerState',
                'loadData',
                'changePage'
            ])
        },
        mounted() {
            EventBusFromGallery.$on('previousPage', () => {
                const page = null, action = 'subtract';
                this.changePage({page, action});
                this.loadLogs(this.paginationInfo.currentPage);
            });
            EventBusFromGallery.$on('nextPage', () => {
                const action = 'add', page = null;
                this.changePage({page, action});
                this.loadLogs(this.paginationInfo.currentPage);
            });
            EventBusFromGallery.$on('page', (page) => {
                const action = null;
                this.changePage({
                    page,
                    action
                });
                this.loadLogs(this.paginationInfo.currentPage);
            });
        },
        computed: {

            ...mapState('Logs', {
                spinner: 'spinner',
                paginationInfo: 'paginationInfo',
                logs: 'logs',
            }),
        },
        async created() {
            this.setSpinnerState(true);
            this.findAll();
        }
    }
</script>

<style scoped>
</style>