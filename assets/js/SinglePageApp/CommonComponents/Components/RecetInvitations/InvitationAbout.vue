<template>
    <tr class="associate-container">
        <td class="associate-overflow invitationAbout">{{ invitation.email }}</td>
        <td class="associate-overflow invitationAbout">{{ invitation.fullName  }}</td>
        <td class="associate-overflow invitationAbout">{{ invitation.created }}</td>
        <td class="invitationAbout" v-if="invitation.used">Yes</td>
        <td class="invitationAbout" v-else>No</td>
        <td class="invitationAbout"><a @click="sendInvitationId" class="btn">Resend Invitation</a></td>
    </tr>
</template>

<script>
    import '../../css/CommonCss/mobileTable.scss';

    export default {
        name: "InvitationAbout",
        props: ['invitation'],
        data() {
            return {

            }
        },
        methods: {
            sendInvitationId: function() {
                // href="{{ path('associate_invite', {invitationId: invitation.id}) }}"
            }
        },
        computed: {

        },
        created() {

        }
    }
</script>

<style scoped>

</style>