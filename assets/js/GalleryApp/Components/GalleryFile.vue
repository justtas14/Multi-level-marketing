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
                    <i v-if="constants.downloadable" v-show="hoverImage" class="fas fa-file-download"></i>
                </a>
                <a v-if="constants.uploadable">
                    <i v-show="hoverImage" class="fa fa-upload"></i>
                </a>
            </a>
            <transition name="fade">
                <a href="javascript:;"
                   v-bind:class="{addOpacity : hoverCard}"
                   @click="deleteFile(file.galleryFile.originalName); setInnerDeleteFunction()"
                   class="closeBtn"><i class="material-icons" :class="constants.galleryClasses.closeButtonClasses.closeIcon">close</i>
                </a>
            </transition>
        </div>
        <figcaption id="fileName" class="tooltip">
            {{ file.galleryFile.originalName.length < constants.fileCutLength ? (file.galleryFile.originalName) :
            ((file.galleryFile.originalName).substr(0,constants.fileCutLength-1) + '...')  }}
            <span v-if="file.galleryFile.originalName.length > constants.fileCutLength-1" class="tooltiptext">
                {{ file.galleryFile.originalName }}
            </span>
        </figcaption>
    </div>
</template>
<script>
    import { mapMutations, mapActions } from 'vuex'
    import VLazyImage from "v-lazy-image";
    import defaultFile from '../../../images/defaultFile.png';
    import wordImage from '../../../images/word.png';
    import pdfImage from '../../../images/pdf.png';
    import excelImage from '../../../images/excel.png';
    import EventBus from '../EventBus/EventBus';


    export default {
        name: 'GalleryFile',
        props: ['file', 'imageExtensions', 'gallery', 'constants'],
        data() {
            return {
                fileExtension: this.getFileExtension(),
                hoverCard: false,
                hoverImage: false,
                isDeleted: false
            }
        },
        components: {
            VLazyImage
        },
        methods: {
            deleteFile: function (fileName) {
                EventBus.$emit('delete', fileName);
            },
            setInnerDeleteFunction: function() {
                const params = {
                    fileId: this.file.id,
                    galleryFileId: this.file.galleryFile.id,
                    fileName: this.file.galleryFile.originalName
                };
                this.changeYesFn(() => {
                    this.isDeleted = true;
                    this.deleteRequestFunction(params);
                });
            },
            oneClickFile: function (fileId, fileName) {
                if (!this.isDeleted) {
                    this.$store.commit('gallery/changeModalState', false);
                    EventBus.$emit('oneClickFile', fileId, fileName, this.determineSrc(), this.file.filePath);
                }
            },
            getFileExtension: function () {
                let originalName = this.file.galleryFile.originalName;
                originalName = this.reverse(originalName);
                let fileExtension = this.reverse(originalName.substr(0, originalName.indexOf('.')));
                fileExtension = fileExtension.toLowerCase();
                return fileExtension;
            },
            determineSrc: function () {
                if (this.isImage()) {
                    return this.file.filePath;
                } else if(this.isPDF()) {
                    return pdfImage;
                } else if(this.isDOCX()) {
                    return wordImage;
                } else if(this.isXLSX()) {
                    return excelImage;
                } else {
                    return defaultFile;
                }
            },
            reverse: function(str) {
                return str.split("").reverse().join("");
            },
            isImage: function() {
                return this.imageExtensions.includes(this.fileExtension);
            },
            isPDF: function () {
                return this.fileExtension == 'pdf';
            },
            isDOCX: function() {
                return this.fileExtension == 'docx';
            },
            isXLSX: function() {
                return this.fileExtension == 'xlsx';
            },
            ...mapMutations('gallery', [
                'changeYesFn',
            ]),
            ...mapActions('gallery', [
                'deleteRequestFunction',
            ]),
        }
    }
</script>

<style src="../css/GalleryFile.css" scoped>

</style>