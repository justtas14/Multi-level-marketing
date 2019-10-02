<template>
    <div id="clipboard-app">
        <span class="card-title invitationLinkTitle">Invitation link</span>
        <div class="invitationAboutText">
            Below is an invitation link, you can share it with your potential associates, so they can begin their
            registration flow themselves.
        </div>
        <div class="invitationLinkContainer">
            <invitationLink v-bind:invitationUrl="invitationUrl"/>
            <share v-bind:navigatorShare="navigatorShare" v-bind:invitationUrl="invitationUrl"/>
        </div>
        <div class="barCodeImageContainer">
            <qrcode
                @click.native="openFullScreenImage"
                class="originalQrCode"
                :class="{}"
                :value="invitationUrl"
                :options="{ width: 200 }"
                tag="img"
            >
            </qrcode>
            <transition name="enlarge"
                v-on:before-enter="beforeEnter"
                v-on:enter="enter"
                v-on:after-enter="afterEnter"
                v-on:enter-cancelled="enterCancelled"

                v-on:before-leave="beforeLeave"
                v-on:leave="leave"
                v-on:after-leave="afterLeave"
                v-on:leave-cancelled="leaveCancelled"
                v-bind:css="false"
            >
                <qrcode
                    @click.native="openFullScreenImage"
                    v-if="fullscreenImg"
                    class="full-screen-img"
                    :class="{imgOpened: fullscreenImg }"
                    :value="invitationUrl"
                    :options="{ width: 200 }"
                    tag="img"
                >
                </qrcode>
            </transition>
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
                navigatorShare: null,
                fullscreenImg: false,
                centeredImage: null,
                currentImage: null,
                centerImagePosition: {
                    top: 0,
                    right: 0,
                    bottom: 0,
                    left: 0
                },
                currentImagePosition:{
                    top: 0,
                    right: 0,
                    bottom: 0,
                    left: 0
                },
                errorOfCalc: 100,
            }
        },
        methods: {

            openFullScreenImage: function () {
                this.fullscreenImg = !this.fullscreenImg;
            },

            beforeEnter: function (el) {
                this.setCordinates(this.centeredImage, this.centerImagePosition);
                this.setCordinates(this.currentImage, this.currentImagePosition);

                window.addEventListener('resize', (e) => {
                    const rect = this.centeredImage.getBoundingClientRect();
                    el.style.left = rect.left + this.errorOfCalc + 'px';
                    el.style.top = rect.top + this.errorOfCalc + 'px';
                    el.style.bottom = rect.bottom + this.errorOfCalc + 'px';
                    el.style.right = rect.right + this.errorOfCalc + 'px';
                });

                el.style.scale = 1;
                el.style.left = this.currentImagePosition.left + 'px';
                el.style.top = this.currentImagePosition.top + 'px';
                el.style.bottom = this.currentImagePosition.bottom + 'px';
                el.style.right = this.currentImagePosition.right + 'px';
            },
            enter: function (el, done) {
                Velocity(el, { scale: 2,
                    bottom: this.centerImagePosition.bottom + this.errorOfCalc,
                    top: this.centerImagePosition.top + this.errorOfCalc,
                    left: this.centerImagePosition.left + this.errorOfCalc,
                    right: this.centerImagePosition.right + this.errorOfCalc

                }, { duration: 200 });
                done();
            },
            afterEnter: function (el) {

            },
            enterCancelled: function (el) {
            },


            beforeLeave: function (el) {
                this.setCordinates(this.centeredImage, this.centerImagePosition);
                this.setCordinates(this.currentImage, this.currentImagePosition);
            },
            leave: function (el, done) {
                Velocity(el, { scale: 1,
                    left: this.currentImagePosition.left,
                    top: this.currentImagePosition.top,
                    bottom: this.currentImagePosition.bottom,
                    right: this.currentImagePosition.right
                }, { duration: 200, complete: done });
            },
            afterLeave: function (el) {
                el.style.position = "absolute";
            },
            // leaveCancelled only available with v-show
            leaveCancelled: function (el) {

            },
            setCordinates: function (image, imagePositions) {
                const rect = image.getBoundingClientRect();
                imagePositions.top = rect.top;
                imagePositions.right = rect.right;
                imagePositions.bottom = rect.bottom;
                imagePositions.left = rect.left;
            }
        },
        mounted() {
            const centeredImage = document.querySelector('.image-center-anchor');
            this.centeredImage = centeredImage;
            this.setCordinates(centeredImage, this.centerImagePosition);
            const currentImage = document.querySelector('.originalQrCode');
            this.currentImage = currentImage;
            this.setCordinates(currentImage, this.currentImagePosition);


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