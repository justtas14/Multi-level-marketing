<template>
    <div class="admin-contentContainer" style="overflow:hidden">
        <div class="card">
            <div class="card-content">
                <Error v-if="this.formError"
              v-bind:message="this.formError"></Error>
                <div class="userDetails__mainContainer">
                    <Success v-if="this.formSuccess.type === 'parent'"
              v-bind:message="this.formSuccess.message"></Success>
                <div class="userDetails__mainContainer">
                    <Main
                        v-bind:associate="associate"
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
        <div class="card">
            <div class="card-content">
                <span class="card-title">Sent Invitations</span>
                <div v-if="isLoading"
                    class="Spinner__Container">
                        <div class="lds-dual-ring"/>
                    </div>
                    <RecentInvitations
                        v-else
                        v-bind:invitations="invitations"
                        v-bind:paginationInfo="pagination"
                        v-bind:isTheSamePage="false"
                    >
                    </RecentInvitations>
            </div>
        </div>
    </div>
</template>

<script>
import {
    mapState, mapActions, mapMutations,
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
        };
    },
    methods: {
        ...mapMutations('AssociateDetails', [
            'setAssociateUrlId',
        ]),
        ...mapActions('AssociateDetails', [
            'associateInfo',
        ]),
    },
    mounted() {
    },
    computed: {

        ...mapState('AssociateDetails', [
            'associateUrlId',
            'isLoading',
            'associate',
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
        formData.append('associateId', this.associateUrlId);
        await this.associateInfo(formData);
    },
};
</script>

<style lang="scss" scoped>
    @import './css/AssociateDetailsHome.scss';
</style>
