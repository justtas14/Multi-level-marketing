<template>
    <div id="clipboard-app">
        <div class="invitationLinkContainer">
            <span class="card-title invitationLinkTitle">Invitation link</span>
            <div class="container">
                <div id="link">{{ copyData}}</div>
                <button id="clip-boardBtn" v-clipboard="copyData" @success="handleSuccess" @error="handleError">
                    <i class="fa fa-clipboard" aria-hidden="true"></i>
                    <span v-bind:class="{visibleTooltip : showMessage}" >Copied!</span>
                </button>
                <button @click="facebookShare" id="facebook-btn">
                    <i class="fab fa-facebook-square"></i>
                </button>
                <button @click="twtterShare" id="twitter-btn">
                    <i class="fab fa-twitter"></i>
                </button>
                <div v-if="spinner" class="Spinner__Container" v-bind:style="{top: 0, 'z-index': 9999}">
                    <div class="lds-dual-ring"/>
                </div>
            </div>
        </div>
        <div class="barCodeImageContainer">
            <img v-if="!spinner" id="bar-code" :src="'data:'+qrCode.contentType+';base64,'+qrCode.generateString">
        </div>
        <meta property="og:title" content="Your title here" />
        <meta property="og:description" content="your description here" />
    </div>
</template>
<script>
    import axios from 'axios';

    export default {
        name: 'Clipboard',
        props: [],
        components: {

        },
        data() {
            return {
                copyData: '',
                qrCode: null,
                messageTooltip: null,
                showMessage: false,
                spinner: true
            }
        },
        methods: {
            handleSuccess(e) {
                this.showMessage = true;
                setTimeout(this.hideMessage, 2000);
            },
            handleError(e) {
                console.log(e);
            },
            hideMessage() {
                this.showMessage = false;
            },
            socialMediaShare() {
                FB.ui({
                    method: 'share_open_graph',
                    action_type: 'og.shares',
                    display: 'popup',
                    action_properties: JSON.stringify({
                        object: {
                            // 'og:title': 'Title to show',
                            'og:description': this.copyData,
                        }
                    })
                }, function(response) {
                    if (response && !response.error_message) {
                        console.log('Posting completed.');
                    } else {
                        console.log('Error while posting.');
                    }
                });
            },
            facebookShare() {
                this.socialMediaShare();
            },
            twtterShare() {

            }
        },
        mounted() {
        },
        async created() {
            const res = await axios.get('/associate/link');
            this.copyData = res.data.uniqueAssociateLink;
            this.qrCode = res.data.qrCode;
            this.spinner = false;




        }
    }
</script>

<style src="../css/clipboard.css"  scoped>
</style>