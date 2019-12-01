<template>
    <div>
        <div v-if="isLoading || isLoadingSentInvitations"
            class="Spinner__Container">
                <div class="lds-dual-ring"/>
        </div>
        <table class="mobileTable" v-if="invitations.length > 0">
            <thead>
            <tr>
                <th class="recent_invitation_title">Email</th>
                <th class="recent_invitation_title">Full Name</th>
                <th class="recent_invitation_title">Invitation created</th>
                <th class="recent_invitation_title">Invitation used</th>
                <th class="recent_invitation_title">Resend</th>
            </tr>
            </thead>
            <tbody>
            <InvitationAbout
                v-for="(invitation, key) in invitations"
                :key="key"
                v-bind:invitation="invitation"
                v-bind:isTheSamePage="isTheSamePage"
            >
            </InvitationAbout>
            </tbody>
        </table>
        <Pagination
            @previousPage="previousPage"
            @nextPage="nextPage"
            @specificPage="specificPage"
            v-if="invitations.length > 0"
             v-bind:paginationInfo="paginationInfo"></Pagination>
        <p style="margin: 2em 0" v-else>Associate doesn't have any sent invitations</p>
    </div>
</template>

<script>
import Pagination from '../Pagination/Pagination.vue';
import InvitationAbout from './InvitationAbout.vue';

export default {
    name: 'RecentInvitations',
    components: {
        InvitationAbout,
        Pagination,
    },
    props: ['invitations', 'paginationInfo', 'isTheSamePage', 'isLoading', 'isLoadingSentInvitations'],
    methods: {
        previousPage() {
            this.$emit('previousPage');
        },
        nextPage() {
            this.$emit('nextPage');
        },
        specificPage(n) {
            this.$emit('specificPage', n);
        },
    },
    computed: {
    },
    created() {
    },
};
</script>

<style lang="scss" scoped>
    @import '../../assets/css/mobileTable.scss';
    @import './css/RecentInvitations.scss';
</style>
