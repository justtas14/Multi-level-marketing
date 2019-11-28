<template>
    <tr class="associate-container">
        <td class="associate-overflow invitationAbout">{{ invitation.email }}</td>
        <td class="associate-overflow invitationAbout">{{ invitation.fullName  }}</td>
        <td class="associate-overflow invitationAbout"> {{ formatDate(invitation.created) }}</td>
        <td class="invitationAbout" v-if="invitation.used">Yes</td>
        <td class="invitationAbout" v-else>No</td>
        <td class="invitationAbout resendBtnContainer">
            <a @click="sendInvitationId" class="btn resendBtn"
             :disabled="isResendBtnLoading===true && pressedBtnInvId === invitation.id">
                Resend Invitation
            </a>
            <div v-if="isResendBtnLoading===true && pressedBtnInvId === invitation.id"
             class="progress">
                    <div class="indeterminate"></div>
            </div>
         </td>
    </tr>
</template>

<script>
import {
    mapActions,
    mapState,
} from 'vuex';

export default {
    name: 'InvitationAbout',
    props: ['invitation', 'isTheSamePage'],
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
                };
                await this.resendInvitation(payload);
                this.pressedBtnInvId = null;
                if (!this.isTheSamePage) {
                    console.log('push');
                    this.$router.push({ path: '/associate/invite' });
                }
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

<style lang="scss" scoped>
    @import '../../assets/css/mobileTable.scss';
    @import './css/InvitationAbout.scss';
</style>
