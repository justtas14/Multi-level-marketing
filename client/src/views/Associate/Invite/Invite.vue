<template>
    <div class="admin-contentContainer">
        <div class="card">
            <div class="card-content">
                <span class="card-title">Invitation</span>
                <div v-if="isLoading" class="Spinner__Container">
                    <div class="lds-dual-ring"/>
                </div>
                <div v-if="this.sent && this.sent.completed === true" style="padding: 20px 0">
                    Invitation has been sent successfully to the "{{ sent.address }}" email address
                </div>
                <a @click="goToRoute()" v-if="this.sent && this.sent.completed === true"
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
                    Below is an invitation link, you can share it with
                    your potential associates, so they can begin their
                    registration flow themselves.
                </div>
                <div class="invitationLinkContainer">
                    <invitationLink v-bind:invitationUrl="this.uniqueAssociateInvitationLink"/>
                    <share v-bind:invitationUrl="this.uniqueAssociateInvitationLink"/>
                </div>
                <div class="barCodeImageContainer">
                    <content-loader v-if="isLoading" height="80">
                        <rect x="160" y="-10" width="80" height="85" />
                    </content-loader>
                    <zoomImage v-else v-bind:imageSrc="imageSrc">
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
        <div v-if="!this.sent" class="card">
            <div class="card-content">
                <span class="card-title">Sent Invitations</span>
                    <div v-if="isLoading"
                    class="Spinner__Container">
                        <div class="lds-dual-ring"/>
                    </div>
                    <RecentInvitations
                        v-else
                        v-bind:invitations="invitations"
                        v-bind:paginationInfo="pagination"
                    >
                    </RecentInvitations>
            </div>
        </div>
    </div>
</template>

<script>
import Vue from 'vue';
import { ContentLoader } from 'vue-content-loader';
import VueQrcode from '@chenfengyuan/vue-qrcode';
import {
    mapActions, mapMutations, mapState, mapGetters,
} from 'vuex';
import Invitation from '../../../components/Invitation/Invitation.vue';
import invitationLink from '../../../components/InvitationLink/invitationLink.vue';
import zoomImage from '../../../components/ZoomImage/ZoomImage.vue';
import share from '../../../components/Share/Share.vue';
import RecentInvitations from '../../../components/RecetInvitations/RecentInvitations.vue';

const SocialSharing = require('vue-social-sharing');

Vue.use(SocialSharing);
Vue.component(VueQrcode.name, VueQrcode);

export default {
    name: 'Invite',
    props: [],
    components: {
        Invitation,
        invitationLink,
        zoomImage,
        share,
        RecentInvitations,
        ContentLoader,
    },
    data() {
        return {
            messageTooltip: null,
            imageSrc: null,
        };
    },
    methods: {
        async goToRoute() {
            this.setNotSent();
        },
        ...mapMutations('Invitation', [
            'setNotSent',
            'changePagination',
        ]),
        ...mapMutations('Sidebar', [
            'setCurrentPath',
        ]),
        ...mapActions('Invitation', [
            'invitationHome',
            'changePage',
            'changePage',
        ]),
        ...mapMutations('Security', [
            'logout',
        ]),
    },
    mounted() {
    },
    computed: {
        ...mapState('Invitation', [
            'isLoading',
            'isLoadingSentInvitations',
            'sent',
            'siteKey',
            'submitLabel',
            'uniqueAssociateInvitationLink',
            'invitations',
            'pagination',
        ]),
        ...mapGetters('Invitation', [

        ]),
    },
    async created() {
        await this.invitationHome();
        const barCodeImage = document.querySelector('.barCodeImage');
        this.imageSrc = barCodeImage.getAttribute('src');
    },
};
</script>

<style lang="scss" scoped>
    @import './css/Invite.scss';
</style>
