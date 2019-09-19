<template>
    <div class="container">
        <div id="link">{{ invitationUrl }}</div>
        <button id="clip-boardBtn" v-clipboard="invitationUrl" @success="handleSuccess" @error="handleError">
            <i class="fa fa-clipboard" aria-hidden="true"></i>
            <span class="copyPopUp" v-bind:class="{visibleTooltip : showMessage}" >Copied!</span>
        </button>
    </div>
</template>

<script>
    import '../css/link.scss';

    export default {
        props: ['invitationUrl'],
        name: "invitationLink",
        data() {
          return {
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
            },
        }
    }
</script>