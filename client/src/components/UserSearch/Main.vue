<template>
    <div id="user-search">
        <table id="table">
            <thead>
                <SearchBar v-bind:associatesLength="this.associates.length"/>
            </thead>
            <tbody id="associatesWrapper">
                <div v-if="spinner" class="Spinner__Container user__search__spiner"
                 v-bind:style="{top: 0, 'z-index': 9999}">
                    <div class="lds-dual-ring"/>
                </div>
                <tr v-if="this.associates.length === 0 && !this.isStarting"
                 class="associate-notFoundContainer">
                    <td class="associate-notFound">Associates are not found</td>
                </tr>
                <Associate
                    v-bind:key="associate.id"
                    v-for="associate in associates"
                    v-bind:associate="associate"
                    v-bind:mainActionLabel="mainActionLabel"
                    v-bind:mainAction="mainAction"
                />
            </tbody>
        </table>
        <Pagination v-bind:paginationInfo="paginationInfo"/>
    </div>
</template>

<script>
import { mapActions, mapMutations, mapState } from 'vuex';
import Associate from './Associate.vue';
import Pagination from '../Pagination/Pagination.vue';
import SearchBar from './SearchBar.vue';
import { findAll, findBy } from '../../services/AssociateSearchService';
import EventBus from './EventBus/EventBus';
import EventBusFromPagination from '../Pagination/EventBus/EventBus';

export default {
    name: 'Main',
    props: ['mainActionLabel', 'mainAction'],
    components: {
        Associate,
        Pagination,
        SearchBar,
    },
    data() {
        return {
            isStarting: true,
        };
    },
    methods: {
        changePage(page) {
            const params = {
                page,
                nameField: this.props.nameSearch,
                emailField: this.props.emailSearch,
            };
            this.setSpinnerState(true);
            findBy(params, this.token).then((response) => {
                this.loadData(response);
            });
        },
        loadAppropriateAssociates(page = null) {
            const params = {
                nameField: this.nameSearchVal,
                emailField: this.emailSearchVal,
                telephoneField: this.phoneSearchVal,
            };
            if (page) {
                params.page = page;
            }

            this.setSpinnerState(true);
            findBy(params, this.token).then((response) => {
                this.loadData(response);
            });
        },

        ...mapActions('UserSearch', [
        ]),
        ...mapMutations('UserSearch', [
            'setSpinnerState',
            'loadData',
            'updateSearchVal',
            'changePage',
        ]),
    },
    mounted() {
        EventBus.$on('handleSearch', (name, input) => {
            const params = {
                name,
                input,
            };
            this.updateSearchVal(params);
            this.loadAppropriateAssociates();
        });
        EventBusFromPagination.$on('previousPage', () => {
            const page = null; const
                action = 'subtract';
            this.changePage({ page, action });
            this.loadAppropriateAssociates(this.paginationInfo.currentPage);
        });
        EventBusFromPagination.$on('nextPage', () => {
            const action = 'add'; const
                page = null;
            this.changePage({ page, action });
            this.loadAppropriateAssociates(this.paginationInfo.currentPage);
        });
        EventBusFromPagination.$on('page', (page) => {
            const action = null;
            this.changePage({
                page,
                action,
            });
            this.loadAppropriateAssociates(this.paginationInfo.currentPage);
        });
    },
    computed: {
        ...mapState('Security', {
            token: 'token',
        }),
        ...mapState('UserSearch', {
            spinner: 'spinner',
            paginationInfo: 'paginationInfo',
            associates: 'associates',
            nameSearchVal: 'nameSearchVal',
            emailSearchVal: 'emailSearchVal',
            phoneSearchVal: 'phoneSearchVal',
        }),
    },
    created() {
        this.setSpinnerState(true);
        findAll(this.token).then((response) => {
            this.loadData(response);
            this.isStarting = false;
        });
    },

};
</script>

<style lang="scss" scoped>
    @import './css/Main.scss';
</style>
