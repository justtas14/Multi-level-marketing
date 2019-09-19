<template>
    <div class="share">
        <button v-if="navigatorShare"  id="shareButton">
            <i class="fa fa-share-alt" aria-hidden="true"></i>
        </button>
        <div class="socialLinks" v-else>
            <button data-network="facebook" :data-url="invitationUrl" class="facebook st-custom-button"><i class="fab fa-facebook"></i></button>
            <button data-network="twitter" :data-url="invitationUrl" class="twitter st-custom-button"><i class="fab fa-twitter"></i></button>
            <button data-network="messenger" :data-url="invitationUrl" class="messenger st-custom-button"><i class="fab fa-facebook-messenger"></i></button>
            <button data-network="linkedin" :data-url="invitationUrl" class="linkedin st-custom-button"><i class="fab fa-linkedin"></i></button>
            <button data-network="whatsapp" :data-url="invitationUrl" class="whatsapp st-custom-button"><i class="fab fa-whatsapp"></i></button>
        </div>
    </div>
</template>

<script>
    import '../css/share.scss';

    export default {
        name: "share",
        props: ['invitationUrl', 'navigatorShare'],
        methods: {

        }
    }
</script>
