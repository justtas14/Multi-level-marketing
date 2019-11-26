<template>
    <div class="share">
        <button v-if="navigatorShare"  id="shareButton">
            <i class="fa fa-share-alt" aria-hidden="true"></i>
        </button>
        <div class="socialLinks" v-else>
            <social-sharing :url="invitationUrl"
            title="The Progressive JavaScript Framework"
            description="Intuitive, Fast and Composable MVVM for building interactive interfaces."
            inline-template>
                <div>
                    <network network="email">
                        <i class="fa fa-envelope socialIcons"></i>
                    </network>
                    <network network="facebook">
                        <i class="fab fa-facebook socialIcons"></i>
                    </network>
                    <network network="linkedin">
                        <i class="fab fa-linkedin socialIcons"></i>
                    </network>
                    <network network="reddit">
                        <i class="fab fa-reddit socialIcons"></i>
                    </network>
                    <network network="skype">
                        <i class="fab fa-skype socialIcons"></i>
                    </network>
                    <network network="twitter">
                        <i class="fab fa-twitter socialIcons"></i>
                    </network>
                    <network network="whatsapp">
                        <i class="fab fa-whatsapp socialIcons"></i>
                    </network>
                </div>
            </social-sharing>
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
