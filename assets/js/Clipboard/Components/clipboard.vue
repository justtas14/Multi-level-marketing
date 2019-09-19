<template>
    <div id="clipboard-app">
        <span class="card-title invitationLinkTitle">Invitation link</span>
        <div class="invitationAboutText">
            Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
        </div>
        <div class="invitationLinkContainer">
            <invitationLink v-bind:invitationUrl="invitationUrl"/>
            <share v-bind:navigatorShare="navigatorShare" v-bind:invitationUrl="invitationUrl"/>
        </div>
        <div class="barCodeImageContainer">
            <qrcode :value="invitationUrl" :options="{ width: 200 }"></qrcode>
        </div>
    </div>
</template>
<script>
    import Vue from 'vue';
    import VueQrcode from '@chenfengyuan/vue-qrcode';
    import invitationLink from "./invitationLink";
    import share from "./share";
    import '../css/clipboard.scss';

    Vue.component(VueQrcode.name, VueQrcode);

    export default {
        name: 'Clipboard',
        props: ['invitationUrl'],
        components: {
            invitationLink,
            share
        },
        data() {
            return {
                messageTooltip: null,
                navigatorShare: null
            }
        },
        methods: {

        },
        mounted() {
            this.navigatorShare = navigator.share;
            if (navigator.share) {
                const shareButton = document.getElementById('shareButton');
                shareButton.addEventListener('click', event => {
                    navigator.share({
                        title: 'Share',
                        url: this.invitationUrl
                    }).then(() => {
                        console.log('Thanks for sharing!');
                    }).catch(console.error);
                })
            }
        },
        created() {
        }
    }
</script>