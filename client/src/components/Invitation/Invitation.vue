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
                    <Error v-if="this.formErrors && this.formErrors.invalidFullName"
                     v-bind:message="this.formErrors.invalidFullName"></Error>
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
                </div>
            </div>
            <Recaptcha v-if="siteKey" v-bind:siteKey="siteKey" ref="recaptcha"/>
            <div class="invitation-buttonWrap">
                <button
                    id="invitation_submit"
                    name="invitation[submit]"
                    class="waves-effect waves-light btn"
                    :disabled="invitationEmail.length === 0 ||
                        fullName.length === 0 || isLoadingForm || this.verifyResponseKey===null"
                    type="button"
                    style="background-color: #3ab54a"
                    @click="sendInvitation"
                >
                    {{ submitLabel }}
                </button>
                <div class="progress" v-if="isLoadingForm">
                    <div class="indeterminate"></div>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
import {
    mapActions, mapState, mapMutations,
} from 'vuex';
import Recaptcha from '../Recaptcha/Recaptcha.vue';
import Messages from '../Messages/Messages.vue';
import Error from '../Messages/Error.vue';


export default {
    name: 'Invitation',
    components: {
        Recaptcha,
        Messages,
        Error,
    },
    props: ['siteKey', 'submitLabel'],
    data() {
        return {
            isLoadingForm: false,
            invitationEmail: '',
            fullName: '',
        };
    },
    methods: {
        async sendInvitation() {
            const payload = {
                formData: {
                    invitationEmail: this.invitationEmail,
                    fullName: this.fullName,
                    verifyResponseKey: this.verifyResponseKey,
                },
                dependencies: this.dependencies,
            };
            this.isLoadingForm = true;
            await this.submitInvitationForm(payload);
            this.isLoadingForm = false;
            this.$refs.recaptcha.$refs.recaptcha.reset();
            this.changeRecaptchaKey(null);
        },
        ...mapActions('Invitation', [
            'submitInvitationForm',
        ]),
        ...mapMutations('Invitation', [
            'changeRecaptchaKey',
        ]),
    },
    computed: {
        ...mapState('Invitation', [
            'formErrors',
            'verifyResponseKey',
        ]),
    },
    created() {

    },
};
</script>

<style lang="scss" scoped>
    @import './css/Invitation.scss';
</style>
