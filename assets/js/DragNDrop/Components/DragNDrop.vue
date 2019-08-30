<template>
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
</template>
<script>
    export default {
        name: 'DragNDrop',
        props: ['profilePicturePreview', 'originalProfilePictureUpload', 'dragAndDropError', 'submitUploadErrorBlock'],
        components: {
        },
        data() {
            return {
                imageDroppingClass: false,
                isInserted: false
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
                        this.dragAndDropError.style.display = 'none';
                        this.appendFile(file);
                    } else {
                        this.dragAndDropError.style.display = 'block';
                        this.dragAndDropError.innerHTML = 'Only images are allowed';
                    }
                } else {
                    this.dragAndDropError.style.display = 'block';
                    this.dragAndDropError.innerHTML = 'Only one file allowed';
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

<style src="../css/DragNDrop.css" scoped>

</style>