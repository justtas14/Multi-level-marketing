<template>
    <tr class="associate-container">
        <td class="associate-overflow invitationAbout">{{ invitation.email }}</td>
        <td class="associate-overflow invitationAbout">{{ invitation.fullName  }}</td>
        <td class="associate-overflow invitationAbout"> {{ formatDate(invitation.created) }}</td>
        <td class="invitationAbout" v-if="invitation.used">Yes</td>
        <td class="invitationAbout" v-else>No</td>
        <td class="invitationAbout"><a @click="sendInvitationId"
         class="btn">Resend Invitation</a></td>
    </tr>
</template>

<script>
import '../../assets/css/mobileTable.scss';
import {
    mapActions,
} from 'vuex';

export default {
    name: 'InvitationAbout',
    props: ['invitation'],
    data() {
        return {

        };
    },
    methods: {
        formatDate(date) {
            const d = new Date(date * 1000);
            return `${(`0${d.getDate()}`).slice(-2)}-${(`0${d.getMonth() + 1}`).slice(-2)}-${d.getFullYear()}`;
        },
        async sendInvitationId() {
            console.log(this.invitation.id);
            const payload = {
                invitationId: this.invitation.id,
            };
            await this.resendInvitation(payload);
        },
        ...mapActions('Invitation', [
            'resendInvitation',
        ]),
    },
    computed: {

    },
    created() {

    },
};
</script>

<style scoped>

</style>
