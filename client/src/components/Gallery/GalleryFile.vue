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
            <a class="fileDownload" @click="oneClickFile(file.id, file.galleryFile.originalName)">
                <div class="overlay-effect" v-bind:class="{hoverImageEffect : hoverImage}"></div>
                <v-lazy-image
                        v-bind:src="determineSrc()"
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
import './css/GalleryFile.scss';
import { mapMutations, mapActions } from 'vuex';
import VLazyImage from 'v-lazy-image';
import defaultFile from '../../../public/img/defaultFile.png';
import wordImage from '../../../public/img/word.png';
import pdfImage from '../../../public/img/pdf.png';
import excelImage from '../../../public/img/excel.png';
import EventBus from './EventBus/EventBus';

export default {
    name: 'GalleryFile',
    props: ['file', 'imageExtensions', 'gallery', 'constants'],
    data() {
        return {
            fileExtension: this.getFileExtension(),
            hoverCard: false,
            hoverImage: false,
            isDeleted: false,
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
        oneClickFile(fileId, fileName) {
            if (!this.isDeleted) {
                this.$store.commit('gallery/changeModalState', false);
                EventBus.$emit('oneClickFile', fileId, fileName, this.determineSrc(), this.file.filePath);
            }
        },
        getFileExtension() {
            let { originalName } = this.file.galleryFile;
            originalName = this.reverse(originalName);
            let fileExtension = this.reverse(originalName.substr(0, originalName.indexOf('.')));
            fileExtension = fileExtension.toLowerCase();
            return fileExtension;
        },
        determineSrc() {
            if (this.isImage()) {
                return this.file.filePath;
            } if (this.isPDF()) {
                return pdfImage;
            } if (this.isDOCX()) {
                return wordImage;
            } if (this.isXLSX()) {
                return excelImage;
            }
            return defaultFile;
        },
        reverse(str) {
            return str.split('').reverse().join('');
        },
        isImage() {
            return this.imageExtensions.includes(this.fileExtension);
        },
        isPDF() {
            return this.fileExtension === 'pdf';
        },
        isDOCX() {
            return this.fileExtension === 'docx';
        },
        isXLSX() {
            return this.fileExtension === 'xlsx';
        },
        ...mapMutations('gallery', [
            'changeYesFn',
            'changeConfirmation',
        ]),
        ...mapActions('gallery', [
            'deleteRequestFunction',
        ]),
    },
};
</script>

<style scoped>

</style>
