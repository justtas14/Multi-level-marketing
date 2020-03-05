<template>
    <div id="DragAndDrop">
        <div id="profilePicturePreview" v-bind:style="{display: isInserted ? 'block' : 'none'}">
            <img ref="img" src="">
        </div>
        <div class="profile-image-upload-box"
             @drop="handleDrop"
             @dragenter.prevent.stop="addImageDropping"
             @dragover.prevent.stop="addImageDropping"
             @dragleave.prevent.stop="removeImageDropping"
             @drop.prevent.stop="removeImageDropping"
             v-bind:class="{ 'image-dropping': imageDroppingClass }"
             v-bind:style="{border: (imageDroppingClass == false) ?
         '4px dashed #AAAAAA' : '5px dashed #ffffff', display: (isInserted ? 'none' : 'block')}"
        >
            <input class="file-upload-input-gallery"
            type='file' accept="image/*" @change="handleFiles(this.files)"/>
            <div id="profileUploadImageIcon">
                <i class="fas fa-file-download"></i>
            </div>
        </div>
        <Error v-if="dragAndDropError" v-bind:message="dragAndDropError"/>
    </div>

</template>
<script>
import {
    mapMutations,
} from 'vuex';
import Error from '../Messages/Error.vue';

export default {
    name: 'DragNDrop',
    props: ['originalProfilePictureUpload'],
    components: {
        Error,
    },
    data() {
        return {
            imageDroppingClass: false,
            isInserted: false,
            dragAndDropError: '',
        };
    },
    methods: {
        handleDrop(event) {
            const dt = event.dataTransfer;
            const { files } = dt;
            this.handleFiles(files);
        },
        handleFiles(files) {
            const imageMimeTypes = ['image/png', 'image/jpeg', 'image/webp'];
            const fileLength = files.length;
            if (fileLength === 1) {
                const file = files[fileLength - 1];
                if (imageMimeTypes.includes(file.type)) {
                    this.originalProfilePictureUpload.files = files;
                    this.dragAndDropError = '';
                    this.appendFile(file);
                    return true;
                }
                this.dragAndDropError = 'Only images are allowed';
                this.originalProfilePictureUpload.value = '';
                this.isInserted = false;
                return false;
            }
            this.isInserted = false;
            this.dragAndDropError = 'Only one file allowed';
            this.originalProfilePictureUpload.value = '';
            return false;
        },
        appendFile(file) {
            const reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onloadend = () => {
                const { img } = this.$refs;
                img.src = reader.result;
                this.isInserted = true;
                this.setProfilePicture(file);
            };
            return true;
        },
        addImageDropping() {
            this.imageDroppingClass = true;
        },
        removeImageDropping() {
            this.imageDroppingClass = false;
        },

        ...mapMutations('Profile', [
            'setProfilePicture',
        ]),
    },
    created() {
    },
};
</script>

<style lang="scss" scoped>
    @import './css/DragNDrop.scss';
</style>
