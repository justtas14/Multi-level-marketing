<template>
    <section id="gallerySection"
             @drop="handleDrop"
             @dragenter.prevent.stop="addImageDropping"
             @dragover.prevent.stop="addImageDropping"
             @dragleave.prevent.stop="removeImageDropping"
             @drop.prevent.stop="removeImageDropping"
    >
        <div v-if="spinner" class="Spinner__Container" v-bind:style="{top: 0, 'z-index': 9999}">
            <div class="lds-dual-ring"/>
        </div>
        <input class="file-upload-input-gallery" multiple type='file'/>
        <div v-if="!spinner" v-bind:style="{display: (files.length == 0) ? 'block' : 'none'}" id="uploadImageIcon">
            <i class="fas fa-file-download"></i>
        </div>
        <div v-if="!spinner" class="image-upload-box"
             v-bind:class="{ 'image-dropping': imageDroppingClass }"
             v-bind:style="{border: (files.length == 0 && imageDroppingClass == false) ?
             '4px dashed #AAAAAA' : '5px dashed #ffffff'}"
        >
        </div>
        <transition-group name="component-fade" tag="p"
              :id="constants.galleryClasses.galleryId"
              v-bind:class="{'noFileClass' : (files.length == 0)}"
        >
        <figure v-bind:key="file.id"
                v-for="file in files"
                class="imageContainer"
        >
                <GalleryFile
                    v-bind:file="file"
                    v-bind:imageExtensions="imageExtensions"
                    v-bind:constants="constants"
                />
        </figure>
        </transition-group>
    </section>
</template>
<script>
    import './css/GalleryFileList.scss'
    import GalleryFile from './GalleryFile.vue';
    import EventBus from './EventBus/EventBus';
    import { mapMutations, mapState } from 'vuex'

    export default {
        name: 'GalleryFileList',
        props: ['files', 'imageExtensions', 'constants'],
        components: {
            GalleryFile
        },
        data() {
            return {
                imageDroppingClass: false,
            }
        },
        computed: mapState('gallery', {
            spinner: 'spinner'
        }),
        methods: {
            handleDrop: function (event) {
                EventBus.$emit('handleDrop', event);
            },

            addImageDropping: function () {
                this.imageDroppingClass = true;
            },
            removeImageDropping: function () {
                this.imageDroppingClass = false;
            },
            ...mapMutations('gallery', [
                'setSpinnerState'
            ])
        },
        mounted() {
            this.setSpinnerState(true);
        }
    }
</script>

<style scoped>

</style>