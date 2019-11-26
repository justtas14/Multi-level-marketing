<template>
    <div id="loggingApp">
        <LogTable
            v-bind:logs="logs"
            v-bind:spinner="spinner"
        ></LogTable>
        <Pagination
            @previousPage="previousPage"
            @nextPage="nextPage"
            @specificPage="specificPage"
        v-bind:paginationInfo="paginationInfo"/>
    </div>
</template>

<script>
import { mapActions, mapMutations, mapState } from 'vuex';
import Pagination from '../Pagination/Pagination.vue';
import LogTable from './LogTable.vue';

export default {
    name: 'Logging',
    props: [],
    components: {
        Pagination,
        LogTable,
    },
    methods: {
        previousPage() {
            const page = null; const
                action = 'subtract';
            this.changePage({ page, action });
            this.loadLogs();
        },
        nextPage() {
            const action = 'add'; const
                page = null;
            this.changePage({ page, action });
            this.loadLogs();
        },
        specificPage(n) {
            const page = n;
            const action = null;
            this.changePage({
                page,
                action,
            });
            this.loadLogs();
        },

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
