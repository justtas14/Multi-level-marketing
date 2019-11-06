<template>
    <div>
        <Messages v-bind:errorMessages="this.formErrors"></Messages>
        <form name="invitation" method="post">
            <div class="invitation-item" >
                <div class="input-field col s12">
                    <input
                            type="text"
                            id="invitation_fullName"
                            name="invitation[fullName]"
                            required="required"
                            v-model="fullName"
                            class="validate"
                    >
                    <label for="invitation_fullName" class="required">Full name</label>
                    <Error v-if="this.formErrors && this.formErrors.invalidFullName" v-bind:message="this.formErrors.invalidFullName"></Error>
                </div>
            </div>
            <div class="invitation-item" >
                <div class="input-field col s12">
                    <input
                            id="invitation_email"
                            name="invitation[email]"
                            required="required"
                            class="validate"
                            v-model="invitationEmail"
                            type="email"
                    >
                    <label for="invitation_email" class="required">Email</label>
                    <Error v-if="this.formErrors && this.formErrors.invalidEmail" v-bind:message="this.formErrors.invalidEmail"></Error>
                </div>
            </div>
            <Recaptcha v-if="siteKey" v-bind:siteKey="siteKey"/>
            <div class="invitation-buttonWrap">
                <button
                        id="invitation_submit"
                        name="invitation[submit]"
                        class="waves-effect waves-light btn"
                        :disabled="invitationEmail.length === 0 || fullName.length === 0 || isFormLoading"
                        type="button"
                        style="background-color: #3ab54a"
                        @click="sendInvitation"
                >
                    {{ submitLabel }}
                </button>
            </div>
        </form>
    </div>
</template>

<script>
    import './css/Invitation.scss'
    import Recaptcha from "../Recaptcha/Recaptcha";
    import Messages from "../Messages/Messages";
    import Error from "../Messages/Error";
    import { mapActions, mapMutations, mapState, mapGetters } from 'vuex';


    export default {
        name: "Invitation",
        components: {
            Recaptcha,
            Messages,
            Error
        },
        props: ['siteKey', 'submitLabel'],
        data() {
            return {
                invitationEmail: '',
                fullName: ''
            }
        },
        methods: {
            sendInvitation: function () {
                let payload = {
                    invitationEmail: this.invitationEmail,
                    fullName: this.fullName
                };
                this.submitInvitationForm(payload);
            },
            ...mapActions('Invitation', [
                'submitInvitationForm'
            ]),
        },
        computed: {
            ...mapState('Invitation', [
                'isFormLoading',
                'formErrors'
            ]),
        },
        created() {

        }
    }
</script>

<style scoped>

</style>