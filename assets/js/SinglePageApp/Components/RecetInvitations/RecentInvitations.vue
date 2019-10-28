<template>
    <div>
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
                v-for="invitation in invitations"
                v-bind:invitation="invitation"
            >
            </InvitationAbout>
            </tbody>
        </table>
        <Pagination v-bind:paginationInfo="paginationInfo"></Pagination>
        <p style="margin: 2em 0" v-else>Associate doesn't have any sent invitations</p>
    </div>
</template>

<script>
    import EventBus from "../Pagination/EventBus/EventBus";
    import '../../CommonCss/mobileTable.scss';
    import './css/RecentInvitations.scss';
    import Pagination from "../Pagination/Pagination";
    import InvitationAbout from "./InvitationAbout";

    export default {
        name: "RecentInvitations",
        components: {
            InvitationAbout,
            Pagination
        },
        props: ['invitations', 'paginationInfo']
    }
</script>

<style scoped>

</style>