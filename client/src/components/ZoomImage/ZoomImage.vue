<template>
    <div id="zoomImage">
        <div class="image-center-anchor"></div>
        <transition name="fade">
            <div class="image-background-overlay"
             v-show="fullscreenImg" @click="openFullScreenImage"></div>
        </transition>
        <img
            :src="imageSrc"
            @click="openFullScreenImage"
            class="imageToZoom"
        >
        <transition name="enlarge"
                v-on:before-enter="beforeEnter"
                v-on:enter="enter"
                v-on:before-leave="beforeLeave"
                v-on:leave="leave"
                v-bind:css="false"
        >
            <img
                :src="imageSrc"
                @click="openFullScreenImage"
                v-show="fullscreenImg"
                class="full-screen-img"
            >
        </transition>
    </div>
</template>

<script>
import './css/zoomImage.scss';
import Velocity from 'velocity-animate';

export default {
    name: 'zoomImage',
    props: ['imageSrc'],
    components: {

    },
    data() {
        return {
            fullscreenImg: false,
            centeredImage: null,
            currentImage: null,
            isLeaving: false,
            centerImagePosition: {
                top: 0,
                right: 0,
                bottom: 0,
                left: 0,
            },
            currentImagePosition: {
                top: 0,
                right: 0,
                bottom: 0,
                left: 0,
            },
            errorOfCalc: 100,
        };
    },
    methods: {
        openFullScreenImage() {
            if (!this.isLeaving) {
                this.fullscreenImg = !this.fullscreenImg;
            }
        },

        beforeEnter(el) {
            this.setCordinates(this.centeredImage, this.centerImagePosition);
            this.setCordinates(this.currentImage, this.currentImagePosition);

            const keepAlignedCentered = () => {
                const rect = this.centeredImage.getBoundingClientRect();
                el.style.left = `${rect.left + this.errorOfCalc}px`;
                el.style.top = `${rect.top + this.errorOfCalc}px`;
                el.style.bottom = `${rect.bottom + this.errorOfCalc}px`;
                el.style.right = `${rect.right + this.errorOfCalc}px`;
            };

            window.removeEventListener('resize', keepAlignedCentered);
            window.addEventListener('resize', keepAlignedCentered);

            el.style.scale = 1;
            el.style.left = `${this.currentImagePosition.left}px`;
            el.style.top = `${this.currentImagePosition.top}px`;
            el.style.bottom = `${this.currentImagePosition.bottom}px`;
            el.style.right = `${this.currentImagePosition.right}px`;
        },
        enter(el, done) {
            Velocity(el, {
                scale: 2,
                bottom: this.centerImagePosition.bottom + this.errorOfCalc,
                top: this.centerImagePosition.top + this.errorOfCalc,
                left: this.centerImagePosition.left + this.errorOfCalc,
                right: this.centerImagePosition.right + this.errorOfCalc,
            }, { duration: 200 });
            done();
        },

        beforeLeave() {
            this.isLeaving = true;
            setTimeout(() => {
                this.isLeaving = false;
            }, 500);
            this.setCordinates(this.centeredImage, this.centerImagePosition);
            this.setCordinates(this.currentImage, this.currentImagePosition);
        },
        leave(el, done) {
            Velocity(el, {
                scale: 1,
                left: this.currentImagePosition.left,
                top: this.currentImagePosition.top,
                bottom: this.currentImagePosition.bottom,
                right: this.currentImagePosition.right,
            }, { duration: 200, complete: done });
        },
        // leaveCancelled only available with v-show
        setCordinates(image, imagePositions) {
            const rect = image.getBoundingClientRect();
            const newPosition = imagePositions;
            newPosition.top = rect.top;
            newPosition.right = rect.right;
            newPosition.bottom = rect.bottom;
            newPosition.left = rect.left;
        },
    },
    mounted() {
        const centeredImage = document.querySelector('.image-center-anchor');
        this.centeredImage = centeredImage;
        this.setCordinates(centeredImage, this.centerImagePosition);
        const currentImage = document.querySelector('.imageToZoom');
        this.currentImage = currentImage;
        this.setCordinates(currentImage, this.currentImagePosition);
    },
    created() {

    },
};
</script>

<style scoped>
</style>
