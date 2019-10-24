<template>
    <div id="DragAndDrop">
        <div id="profilePicturePreview">
            <img src="">
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
            <input class="file-upload-input-gallery" type='file' accept="image/*" onchange="handleFiles(this.files)"/>
            <div id="profileUploadImageIcon">
                <i class="fas fa-file-download"></i>
            </div>
        </div>
        <Error v-if="dragAndDropError" v-bind:message="dragAndDropError"/>
    </div>

</template>
<script>
    import '../../css/DragNDrop/DragNDrop.scss';
    import Error from "../Messages/Error";

    export default {
        name: 'DragNDrop',
        props: ['originalProfilePictureUpload', 'submitUploadErrorBlock'],
        components: {
            Error
        },
        data() {
            return {
                imageDroppingClass: false,
                isInserted: false,
                dragAndDropError: ''
            }
        },
        methods: {
            handleDrop: function (event) {
                const dt = event.dataTransfer;
                let files = dt.files;
                this.handleFiles(files);
            },
            handleFiles: function (files) {
                if (this.submitUploadErrorBlock) {
                    this.submitUploadErrorBlock.style.display = 'none';
                }
                const imageMimeTypes = ['image/png', 'image/jpeg', 'image/webp'];
                const fileLength = files.length;
                if (fileLength === 1) {
                    const file = files[fileLength-1];
                    if (imageMimeTypes.includes(file.type)) {
                        this.originalProfilePictureUpload.files = files;
                        this.dragAndDropError = '';
                        this.appendFile(file);
                    } else {
                        this.dragAndDropError = 'Only images are allowed';
                    }
                } else {
                    this.dragAndDropError = 'Only one file allowed';
                }
            },
            appendFile: function (file) {
                const reader = new FileReader();
                reader.readAsDataURL(file);
                reader.onloadend = () => {
                    const img = this.profilePicturePreview.querySelector('img');
                    img.src = reader.result;
                    this.profilePicturePreview.style.display = 'block';
                    this.isInserted = true;
                }
            },
            addImageDropping: function () {
                this.imageDroppingClass = true;
            },
            removeImageDropping: function () {
                this.imageDroppingClass = false;
            },
        },
    }
</script>

<style scoped>
</style>