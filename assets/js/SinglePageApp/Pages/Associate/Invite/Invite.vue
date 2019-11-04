<template>
    <div class="admin-contentContainer">
        <div class="card">
            <div class="card-content">
                <span class="card-title">Invitation</span>
                <div v-if="this.sent && this.sent.completed === true" style="padding: 20px 0">
                    Invitation has been sent successfully to the "{{ sent.address }}" email address
                </div>
                <a @click="goToRoute('associate/invite')" v-if="this.sent && this.sent.completed === true"
                   class="waves-effect waves-light btn">Send another one</a>
                <Invitation
                    v-else-if="this.siteKey"
                    v-bind:siteKey="this.siteKey"
                    v-bind:submitLabel="this.submitLabel"
                >
                </Invitation>
            </div>
        </div>
        <div v-if="!this.sent" class="card">
            <div class="card-content">
                <span class="card-title invitationLinkTitle">Invitation link</span>
                <div class="invitationAboutText">
                    Below is an invitation link, you can share it with your potential associates, so they can begin their
                    registration flow themselves.
                </div>
                <div class="invitationLinkContainer">
                    <invitationLink v-bind:invitationUrl="this.uniqueAssociateInvitationLink"/>
                    <share v-bind:invitationUrl="this.uniqueAssociateInvitationLink"/>
                </div>
                <div class="barCodeImageContainer">
                    <zoomImage v-bind:imageSrc="imageSrc">
                    </zoomImage>
                </div>
                <qrcode
                    :value="this.uniqueAssociateInvitationLink"
                    :options="{ width: 200 }"
                    tag="img"
                    class="barCodeImage"
                ></qrcode>
            </div>
        </div>
<!--        {% include 'includes/other/recentInvitations.twig' with {'route': 'associate_invite'}%}-->
<!--        {% endif %}-->
    </div>
</template>

<script>
    import Vue from 'vue';
    import VueQrcode from '@chenfengyuan/vue-qrcode';
    import { mapActions, mapMutations, mapState, mapGetters } from 'vuex';
    import Invitation from "../../../Components/Invitation/Invitation";
    import invitationLink from '../../../Components/InvitationLink/invitationLink';
    import zoomImage from "../../../Components/ZoomImage/ZoomImage";
    import share from "../../../Components/Share/Share";
    import './css/Invite.scss'

    Vue.component(VueQrcode.name, VueQrcode);

    export default {
        name: "Invite",
        props: [],
        components: {
            Invitation,
            invitationLink,
            zoomImage,
            share
        },
        data() {
            return {
                messageTooltip: null,
                imageSrc: null
            }
        },
        methods: {
            goToRoute() {
                this.setCurrentPath(path);
                this.$router.push({path: path});
            },
            ...mapMutations('Invitation', [
            ]),
            ...mapMutations('Sidebar', [
                'setCurrentPath'
            ]),
            ...mapActions('Invitation', [
                'invitationHome'
            ]),
        },
        mounted() {
            const barCodeImage = document.querySelector('.barCodeImage');
            this.imageSrc = barCodeImage.getAttribute('src');
        },
        computed: {
            ...mapState('Invitation', [
                'isLoading',
                'sent',
                'siteKey',
                'submitLabel',
                'uniqueAssociateInvitationLink'
            ]),
            ...mapGetters('Invitation', [

            ]),
        },
        async created() {
            await this.invitationHome();
        }
    }
</script>

<style scoped>

</style>