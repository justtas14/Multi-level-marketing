<template>
    <div id="clipboard-app">
        <span class="card-title invitationLinkTitle">Invitation link</span>
        <div class="invitationAboutText">
            Below is an invitation link, you can share it with your potential associates, so they can begin their
            registration flow themselves.
        </div>
        <div class="invitationLinkContainer">
            <invitationLink v-bind:invitationUrl="invitationUrl"/>
            <share v-bind:invitationUrl="invitationUrl"/>
        </div>
        <div class="barCodeImageContainer">
            <zoomImage v-bind:imageSrc="imageSrc">
            </zoomImage>
        </div>
        <qrcode
            :value="invitationUrl"
            :options="{ width: 200 }"
            tag="img"
            class="barCodeImage"
        ></qrcode>
    </div>
</template>

<script>
    import Vue from 'vue';
    import VueQrcode from '@chenfengyuan/vue-qrcode';
    import invitationLink from "./invitationLink";
    import zoomImage from "./zoomImage";
    import share from "./share";
    import '../css/clipboard.scss';

    Vue.component(VueQrcode.name, VueQrcode);

    export default {
        name: 'Clipboard',
        props: ['invitationUrl'],
        components: {
            invitationLink,
            share,
            zoomImage
        },
        data() {
            return {
                messageTooltip: null,
                imageSrc: null
            }
        },
        methods: {

        },
        mounted() {
            const barCodeImage = document.querySelector('.barCodeImage');
            this.imageSrc = barCodeImage.getAttribute('src');
        },
        created() {
        }
    }
</script>