<template>
    <div class="cardContent"
         @mouseover="hoverCard = true"
         @mouseleave="hoverCard = false"
         v-bind:class="{hoverCard : hoverCard}"
    >
        <div class="closeContainer"
             @mouseover="hoverImage = true"
             @mouseleave="hoverImage = false"
        >
            <a class="fileDownload" @click="oneClickFile()">
                <div class="overlay-effect" v-bind:class="{hoverImageEffect : hoverImage}"></div>
                <v-lazy-image
                    :src="fileSrc.determineSrc()"
                    src-placeholder=""
                    class="gallery__img"
                    v-bind:alt="file.galleryFile.originalName">
                </v-lazy-image>
                <a v-if="constants.downloadable">
                    <i v-if="constants.downloadable" v-show="hoverImage"
                    class="fas fa-file-download"></i>
                </a>
                <a v-if="constants.uploadable">
                    <i v-show="hoverImage" class="fa fa-upload"></i>
                </a>
            </a>
            <transition name="fade">
                <a href="javascript:;"
                   v-bind:class="{addOpacity : hoverCard}"
                   @click="deleteFile(file.galleryFile.originalName); setInnerDeleteFunction()"
                   class="closeBtn"><i class="material-icons"
                    :class="constants.galleryClasses.closeButtonClasses.closeIcon">close</i>
                </a>
            </transition>
        </div>
        <figcaption id="fileName" class="tooltip">
            {{ cutName() }}
            <span v-if="file.galleryFile.originalName.length > constants.fileCutLength-1"
                 class="tooltiptext">
                {{ file.galleryFile.originalName }}
            </span>
        </figcaption>
    </div>
</template>
<script>
import { mapMutations, mapActions, mapState } from 'vuex';
import VLazyImage from 'v-lazy-image';
import EventBus from './EventBus/EventBus';
import FileSrc from './Services/fileSrc';

export default {
    name: 'GalleryFile',
    props: ['file', 'gallery', 'constants'],
    data() {
        return {
            hoverCard: false,
            hoverImage: false,
            isDeleted: false,
            fileSrc: null,
        };
    },
    components: {
        VLazyImage,
    },
    methods: {
        cutName() {
            return ((this.file.galleryFile.originalName.length < this.constants.fileCutLength)
                ? (this.file.galleryFile.originalName)
                : (`${this.file.galleryFile.originalName.substr(0, (this.constants.fileCutLength - 1))}...`));
        },
        deleteFile(fileName) {
            EventBus.$emit('delete', fileName);
        },
        setInnerDeleteFunction() {
            const params = {
                fileId: this.file.id,
                galleryFileId: this.file.galleryFile.id,
                fileName: this.file.galleryFile.originalName,
            };
            this.changeYesFn(async () => {
                this.changeConfirmation({ display: 'none' });
                this.isDeleted = true;
                const isDeleted = await this.deleteRequestFunction(params);
                if (isDeleted) {
                    EventBus.$emit('checkDeleted', params.fileId);
                } else {
                    this.isDeleted = false;
                }
            });
        },
        oneClickFile() {
            if (!this.isDeleted) {
                this.clickFile(
                    this.file.id,
                    this.file.galleryFile.originalName,
                    this.fileSrc.determineSrc(),
                    this.file.filePath,
                );
            }
        },
        ...mapMutations('Gallery', [
            'changeYesFn',
            'changeConfirmation',
        ]),
        ...mapActions('Gallery', [
            'deleteRequestFunction',
        ]),
    },
    computed: {
        ...mapState('Gallery', [
            'clickFile',
        ]),
    },
    created() {
        const fileSrc = new FileSrc(this.file.galleryFile.originalName, this.file.filePath);
        this.fileSrc = fileSrc;
    },
};
</script>

<style lang="scss" scoped>
    @import './css/GalleryFile.scss';
</style>
