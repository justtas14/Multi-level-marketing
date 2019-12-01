<template>
    <div id="loggingApp">
        <LogTable
            v-bind:logs="logs"
            v-bind:isLoading="isLoading"
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
    data() {
        return {
            isLoading: false,
        };
    },
    methods: {
        async previousPage() {
            const page = null; const
                action = 'subtract';
            this.changePage({ page, action });
            await this.loadLogs();
        },
        async nextPage() {
            const action = 'add'; const
                page = null;
            this.changePage({ page, action });
            await this.loadLogs();
        },
        async specificPage(n) {
            const page = n;
            const action = null;
            this.changePage({
                page,
                action,
            });
            await this.loadLogs();
        },

        async loadLogs() {
            this.isLoading = true;
            await this.findLogs();
            this.isLoading = false;
        },

        ...mapActions('Logs', [
            'findLogs',
        ]),
        ...mapMutations('Logs', [
            'loadData',
            'changePage',
        ]),
    },
    computed: {
        ...mapState('Logs', {
            paginationInfo: 'paginationInfo',
            logs: 'logs',
        }),
    },
    async created() {
        this.isLoading = true;
        this.findLogs();
        this.isLoading = false;
    },
};
</script>

<style lang="scss" scoped>
    @import './css/logging.scss';
</style>
