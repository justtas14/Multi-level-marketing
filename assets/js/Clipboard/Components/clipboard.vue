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
                    :class="{imgOpened: !fullscreenImg }"
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
                fullscreenImg: false
            }
        },
        methods: {
            openFullScreenImage() {
                this.fullscreenImg = !this.fullscreenImg;
            },
            // --------
            // ENTERING
            // --------

            beforeEnter: function (el) {
                el.style.scale = 1;
            },
            enter: function (el, done) {
                Velocity(el, { scale: 3 }, { duration: 300 });
                Velocity(el, { fontSize: '1em' }, { complete: done });
            },
            afterEnter: function (el) {
                // ...
            },
            enterCancelled: function (el) {
                // ...
            },

            // --------
            // LEAVING
            // --------

            beforeLeave: function (el) {
                // ...
            },
            // the done callback is optional when
            // used in combination with CSS
            leave: function (el, done) {
                // ...
                done()
            },
            afterLeave: function (el) {
                // ...
            },
            // leaveCancelled only available with v-show
            leaveCancelled: function (el) {
                // ...
            }
        },
        mounted() {
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