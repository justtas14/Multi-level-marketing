<template>
    <div class="admin-contentContainer">
        <div class="card">
            <div class="card-content">
                <span class="card-title">Invitation</span>
                <div v-if="isLoading" class="Spinner__Container user__search__spiner" v-bind:style="{top: 0, 'z-index': 9999}">
                    <div class="lds-dual-ring"/>
                </div>
                <div v-if="this.sent && this.sent.completed === true" style="padding: 20px 0">
                    Invitation has been sent successfully to the "{{ sent.address }}" email address
                </div>
                <a @click="goToRoute('invite')" v-if="this.sent && this.sent.completed === true"
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
                <div v-if="isLoading" class="Spinner__Container user__search__spiner" v-bind:style="{top: 0, 'z-index': 9999}">
                    <div class="lds-dual-ring"/>
                </div>
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
        <div v-if="!this.sent" class="card">
            <div class="card-content">
                <span class="card-title">Sent Invitations</span>
                    <div v-if="isLoading || isLoadingSentInvitations" class="Spinner__Container user__search__spiner" v-bind:style="{top: 0, 'z-index': 9999}">
                        <div class="lds-dual-ring"/>
                    </div>
                    <RecentInvitations
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
    import VueQrcode from '@chenfengyuan/vue-qrcode';
    import { mapActions, mapMutations, mapState, mapGetters } from 'vuex';
    import Invitation from "../../../Components/Invitation/Invitation";
    import invitationLink from '../../../Components/InvitationLink/invitationLink';
    import zoomImage from "../../../Components/ZoomImage/ZoomImage";
    import share from "../../../Components/Share/Share";
    import RecentInvitations from "../../../Components/RecetInvitations/RecentInvitations";
    import EventBus from "../../../Components/Pagination/EventBus/EventBus";
    import './css/Invite.scss'

    Vue.component(VueQrcode.name, VueQrcode);

    export default {
        name: "Invite",
        props: [],
        components: {
            Invitation,
            invitationLink,
            zoomImage,
            share,
            RecentInvitations
        },
        data() {
            return {
                messageTooltip: null,
                imageSrc: null
            }
        },
        methods: {
            async goToRoute(path) {
                this.setNotSent();
                this.setCurrentPath(path);
                await this.invitationHome();
            },
            ...mapMutations('Invitation', [
                'setNotSent',
                'changePagination'
            ]),
            ...mapMutations('Sidebar', [
                'setCurrentPath'
            ]),
            ...mapActions('Invitation', [
                'invitationHome',
                'changePage',
                'changePage'
            ]),
        },
        mounted() {
            const barCodeImage = document.querySelector('.barCodeImage');
            this.imageSrc = barCodeImage.getAttribute('src');

            EventBus.$on('previousPage', async () => {
                const page = null, action = 'subtract';
                this.changePagination({page, action});
                await this.changePage();
            });
            EventBus.$on('nextPage', async () => {
                const action = 'add', page = null;
                this.changePagination({page, action});
                await this.changePage();
            });
            EventBus.$on('page', async (page) =>  {
                const action = null;
                this.changePagination({page, action});
                await this.changePage();
            });
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
                'pagination'
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