<template>
    <div id="clipboard-app">
        <div class="container">
            <div id="link">{{ copyData }}</div>
            <button v-clipboard="copyData" @success="handleSuccess" @error="handleError">
                <i class="fa fa-clipboard" aria-hidden="true"></i>
                <span v-bind:class="{visibleTooltip : showMessage}" >Copied!</span>
            </button>
        </div>
    </div>
</template>
<script>
    export default {
        name: 'Clipboard',
        props: [],
        components: {

        },
        data() {
            return {
                copyData: 'copy data',
                messageTooltip: null,
                showMessage: false,
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
        created() {

        }
    }
</script>

<style src="../css/clipboard.css"  scoped>

</style>