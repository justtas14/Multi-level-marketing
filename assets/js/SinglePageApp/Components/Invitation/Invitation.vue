<template>
    <div>
<!--        <Messages></Messages>-->
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
                    <!--                <Error v-if=""></Error>-->
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
                    <!--                <Error v-if=""></Error>-->
                </div>
            </div>
            <Recaptcha v-bind:siteKey="siteKey"/>
            <div class="invitation-buttonWrap">
                <button
                        id="invitation_submit"
                        name="invitation[submit]"
                        class="waves-effect waves-light btn"
                        :disabled="login.length === 0 || password.length === 0 || isLoading"
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