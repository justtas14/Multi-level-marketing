<template>
    <div id="clipboard-app">
        <div class="container">
            <div id="link">{{ copyData}}</div>
            <button v-clipboard="copyData" @success="handleSuccess" @error="handleError">
                <i class="fa fa-clipboard" aria-hidden="true"></i>
                <span v-bind:class="{visibleTooltip : showMessage}" >Copied!</span>
            </button>
            <div v-if="spinner" class="Spinner__Container" v-bind:style="{top: 0, 'z-index': 9999}">
                <div class="lds-dual-ring"/>
            </div>
        </div>
        <img v-if="!spinner" :src="'data:'+qrCode.getContentType+';base64,'+qrCode.generate">
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
            }
        },
        mounted() {
        },
        async created() {
            const res = await axios.get('/associate/link');
            this.copyData = res.data.uniqueAssociateLink;
            this.qrCode = res.data.qrCode;
            this.spinner = false;
            console.log(this.qrCode);
        }
    }
</script>

<style src="../css/clipboard.css"  scoped>
</style>