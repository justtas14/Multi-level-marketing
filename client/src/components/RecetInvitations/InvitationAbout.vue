<template>
    <tr class="associate-container">
        <td class="associate-overflow invitationAbout">{{ invitation.email }}</td>
        <td class="associate-overflow invitationAbout">{{ invitation.fullName  }}</td>
        <td class="associate-overflow invitationAbout"> {{ formatDate(invitation.created) }}</td>
        <td class="invitationAbout" v-if="invitation.used">Yes</td>
        <td class="invitationAbout" v-else>No</td>
        <td class="invitationAbout">
            <a @click="sendInvitationId" class="btn">
                <div v-if="isResendBtnLoading===true && pressedBtnInvId === invitation.id"
                class="Spinner__Container resendBtnSpinnerContainer">
                    <div class="lds-dual-ring resendBtnSpinner"/>
                </div>
                Resend Invitation
            </a>
         </td>
    </tr>
</template>

<script>
import '../../assets/css/mobileTable.scss';
import './css/InvitationAbout.scss';
import {
    mapActions,
    mapState,
} from 'vuex';

export default {
    name: 'InvitationAbout',
    props: ['invitation', 'dependencies'],
    data() {
        return {
            pressedBtnInvId: null,
        };
    },
    methods: {
        formatDate(date) {
            const d = new Date(date * 1000);
            return `${(`0${d.getDate()}`).slice(-2)}-${(`0${d.getMonth() + 1}`).slice(-2)}-${d.getFullYear()}`;
        },
        async sendInvitationId() {
            if (!this.isResendBtnLoading) {
                this.pressedBtnInvId = this.invitation.id;
                const payload = {
                    params: {
                        invitationId: this.invitation.id,
                    },
                    dependencies: this.dependencies,
                };
                await this.resendInvitation(payload);
            }
        },
        ...mapActions('Invitation', [
            'resendInvitation',
        ]),
    },
    computed: {
        ...mapState('Invitation', [
            'isResendBtnLoading',
        ]),
    },
    created() {

    },
};
</script>

<style scoped>

</style>
