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
        loadLogs() {
            this.setSpinnerState(true);
            this.findLogs();
        },

        ...mapActions('Logs', [
            'findLogs',
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
            this.loadLogs();
        });
        PaginationEventBus.$on('nextPage', () => {
            const action = 'add'; const
                page = null;
            this.changePage({ page, action });
            this.loadLogs();
        });
        PaginationEventBus.$on('page', (page) => {
            console.log(page);
            const action = null;
            this.changePage({
                page,
                action,
            });
            this.loadLogs();
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
        this.findLogs();
    },
};
</script>

<style lang="scss" scoped>
    @import './css/logging.scss';
</style>
