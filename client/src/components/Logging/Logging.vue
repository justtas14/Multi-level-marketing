<template>
    <div id="loggingApp">
        <LogTable
            v-bind:logs="logs"
            v-bind:spinner="spinner"
        ></LogTable>
        <Pagination v-bind:paginationInfo="paginationInfo"/>
    </div>
</template>

<script>
import './css/logging.scss';
import { mapActions, mapMutations, mapState } from 'vuex';
import Pagination from '../Pagination/Pagination.vue';
import LogTable from './LogTable.vue';
import PaginationEventBus from '../Pagination/EventBus/EventBus';

export default {
    name: 'Logging',
    props: [],
    components: {
        Pagination,
        LogTable,
    },
    methods: {
        changePage(page) {
            const params = {
                page,
            };
            this.setSpinnerState(true);
            this.findBy(params);
        },
        loadLogs(page = null) {
            const params = {};
            if (page) {
                params.page = page;
            }

            this.setSpinnerState(true);
            this.findBy(params);
        },

        ...mapActions('Logs', [
            'findBy',
            'findAll',
        ]),
        ...mapMutations('Logs', [
            'setSpinnerState',
            'loadData',
            'changePage',
        ]),
    },
    mounted() {
        PaginationEventBus.$on('previousPage', () => {
            const page = null; const
                action = 'subtract';
            this.changePage({ page, action });
            this.loadLogs(this.paginationInfo.currentPage);
        });
        PaginationEventBus.$on('nextPage', () => {
            const action = 'add'; const
                page = null;
            this.changePage({ page, action });
            this.loadLogs(this.paginationInfo.currentPage);
        });
        PaginationEventBus.$on('page', (page) => {
            const action = null;
            this.changePage({
                page,
                action,
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
    },
};
</script>

<style scoped>
</style>
