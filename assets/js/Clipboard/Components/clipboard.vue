<template>
    <div id="clipboard-app">
        <div class="invitationLinkContainer">
            <span class="card-title invitationLinkTitle">Invitation link</span>
<!--            <button class="btn">-->
<!--                <div></div>-->
<!--                <div></div>-->
<!--            </button>-->
            <div class="container">
                <div id="link">{{ copyData}}</div>
                <button id="clip-boardBtn" v-clipboard="copyData" @success="handleSuccess" @error="handleError">
                    <i class="fa fa-clipboard" aria-hidden="true"></i>
                    <span v-bind:class="{visibleTooltip : showMessage}" >Copied!</span>
                </button>
                <div class="share">
                    <button id="shareButton">
                        <i class="fa fa-share-alt" aria-hidden="true"></i>
                    </button>
                    <ul class="submenu">
                        <li><div data-network="facebook" :data-url="copyData" class="facebook st-custom-button"><i class="fab fa-facebook"></i></div></li>
                        <li> <div data-network="twitter" :data-url="copyData" class="twitter st-custom-button"><i class="fab fa-twitter"></i></div></li>
                        <li> <div data-network="messenger" :data-url="copyData" class="messenger st-custom-button"><i class="fab fa-facebook-messenger"></i></div></li>
                        <li> <div data-network="linkedin" :data-url="copyData" class="linkedin st-custom-button"><i class="fab fa-linkedin"></i></div></li>
                        <li><div data-network="whatsapp" :data-url="copyData" class="whatsapp st-custom-button"><i class="fab fa-whatsapp"></i></div></li>
                    </ul>
                </div>
                <div v-if="spinner" class="Spinner__Container" v-bind:style="{top: 0, 'z-index': 9999}">
                    <div class="lds-dual-ring"/>
                </div>
            </div>
        </div>
        <div class="barCodeImageContainer">
            <img v-if="!spinner" id="bar-code" :src="'data:'+qrCode.contentType+';base64,'+qrCode.generateString">
        </div>
    </div>
</template>
<script>
    import axios from 'axios';
    import '../css/clipboard.scss';

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
        },
        mounted() {
            if (navigator.share) {
                const shareButton = document.getElementById('shareButton');
                shareButton.addEventListener('click', event => {
                    navigator.share({
                        title: 'Share',
                        url: this.copyData
                    }).then(() => {
                        console.log('Thanks for sharing!');
                    }).catch(console.error);
                })
            } else {

            }
        },
        async created() {
            const res = await axios.get('/associate/link');
            this.copyData = res.data.uniqueAssociateLink;
            this.qrCode = res.data.qrCode;
            this.spinner = false;
        }
    }
</script>