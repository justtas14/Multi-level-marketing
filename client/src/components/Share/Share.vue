<template>
    <div class="share">
        <button v-if="navigatorShare"  id="shareButton">
            <i class="fa fa-share-alt" aria-hidden="true"></i>
        </button>
        <div class="socialLinks" v-else>
            <button data-network="facebook" :data-url="invitationUrl"
             class="facebook st-custom-button"><i class="fab fa-facebook"></i>
             </button>
            <button data-network="twitter" :data-url="invitationUrl"
             class="twitter st-custom-button"><i class="fab fa-twitter"></i>
             </button>
            <button data-network="messenger" :data-url="invitationUrl"
             class="messenger st-custom-button"><i class="fab fa-facebook-messenger"></i>
             </button>
            <button data-network="linkedin" :data-url="invitationUrl"
             class="linkedin st-custom-button"><i class="fab fa-linkedin"></i>
             </button>
            <button data-network="whatsapp" :data-url="invitationUrl"
             class="whatsapp st-custom-button"><i class="fab fa-whatsapp"></i>
             </button>
        </div>
    </div>
</template>

<script>

export default {
    name: 'share',
    props: ['invitationUrl'],
    data() {
        return {
            navigatorShare: null,
        };
    },
    methods: {

    },
    mounted() {
        this.navigatorShare = navigator.share;
        console.log(this.navigatorShare);
        if (navigator.share) {
            const shareButton = document.getElementById('shareButton');
            shareButton.addEventListener('click', () => {
                navigator.share({
                    title: 'Share',
                    url: this.invitationUrl,
                }).then(() => {
                    console.log('Thanks for sharing!');
                }).catch(console.error);
            });
        }
    },
};
</script>

<style lang="scss" scoped>
    @import './css/share.scss';
</style>
