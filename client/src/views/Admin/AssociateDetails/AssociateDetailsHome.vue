<template>
    <div class="admin-contentContainer" style="overflow:hidden">
        <div class="card">
            <div class="card-content">
                <Error v-if="this.formError"
              v-bind:message="this.formError"></Error>
                    <Success v-if="this.formSuccess.type === 'parent'"
              v-bind:message="this.formSuccess.message"></Success>
                <div class="userDetails__mainContainer">
                    <Main
                        v-bind:associate="stateAssociate"
                        v-bind:isParent="false"
                    >
                    </Main>
                    <Main
                        v-if="associateParent"
                        v-bind:associate="associateParent"
                        v-bind:isParent="true"
                    >
                    </Main>
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
                    v-bind:associatesInLevels="associatesInLevels"
                    v-bind:levels="levels"
                    v-bind:maxLevel="maxLevel"
                ></BusinessShape>
            </div>
        </div>
        <div class="card" v-if="!checkEndPrelaunch">
            <div class="card-content">
                <span class="card-title">Sent Invitations</span>
                    <RecentInvitations
                        v-bind:invitations="invitations"
                        v-bind:paginationInfo="pagination"
                        v-bind:isLoading="isLoading"
                        v-bind:isLoadingSentInvitations="isLoadingSentInvitations"
                        v-bind:isTheSamePage="false"
                        @previousPage="previousPage"
                        @nextPage="nextPage"
                        @specificPage="specificPage"
                    >
                    </RecentInvitations>
            </div>
        </div>
    </div>
</template>

<script>
import {
    mapState, mapActions, mapMutations, mapGetters,
} from 'vuex';
import Main from '../../../components/AssociateDetails/Main.vue';
import RecentInvitations from '../../../components/RecetInvitations/RecentInvitations.vue';
import BusinessShape from '../../../components/BusinessShape/BusinessShape.vue';
import Error from '../../../components/Messages/Error.vue';
import Success from '../../../components/Messages/Success.vue';

export default {
    name: 'AssociateDetailsHome',
    components: {
        Main,
        RecentInvitations,
        BusinessShape,
        Error,
        Success,
    },
    props: [],
    data() {
        return {
            currentAssociateId: null,
            isLoading: false,
            isLoadingSentInvitations: false,
        };
    },
    methods: {
        async nextPage() {
            const action = 'add'; const
                page = null;
            await this.appendPageFormDataAndChange(page, action);
        },
        async previousPage() {
            const page = null; const
                action = 'subtract';
            await this.appendPageFormDataAndChange(page, action);
        },
        async specificPage(n) {
            const page = n;
            const action = null;
            await this.appendPageFormDataAndChange(page, action);
        },

        async appendPageFormDataAndChange(page, action) {
            this.changePagination({ page, action });
            const formData = new FormData();
            formData.append('associateId', this.associateUrlId);
            formData.append('page', this.pagination.currentPage);
            this.isLoadingSentInvitations = true;
            await this.associateInfo(formData);
            this.isLoadingSentInvitations = false;
        },

        ...mapMutations('AssociateDetails', [
            'setAssociateUrlId',
            'changePagination',
        ]),
        ...mapActions('AssociateDetails', [
            'associateInfo',
        ]),
    },
    mounted() {
    },
    computed: {
        ...mapGetters('Sidebar', [
            'checkEndPrelaunch',
        ]),
        ...mapState('AssociateDetails', [
            'associateUrlId',
            'stateAssociate',
            'associateParent',
            'invitations',
            'formError',
            'formSuccess',
            'associatesInLevels',
            'maxLevel',
            'levels',
            'pagination',
        ]),
    },
    async created() {
        this.setAssociateUrlId(this.$route.params.id);
        const formData = new FormData();
        formData.append('associateId', this.$route.params.id);
        this.isLoading = true;
        await this.associateInfo(formData);
        this.isLoading = false;
    },
};
</script>

<style lang="scss" scoped>
    @import './css/AssociateDetailsHome.scss';
</style>
