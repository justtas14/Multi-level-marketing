import Vue from 'vue';
import DragNDrop from "./Components/DragNDrop";

const dragNdrop = new Vue({
    el: '#DragAndDrop',
    data: {
        profilePicturePreview: null,
        originalProfilePictureUpload: null,
        dragAndDropError: null,
        submitUploadErrorBlock: null
    },
    methods: {
        instantiateSelectors(profilePicturePreview, originalProfilePictureUpload, dragAndDropError, submitUploadErrorBlock) {
            this.profilePicturePreview = profilePicturePreview;
            this.originalProfilePictureUpload = originalProfilePictureUpload;
            this.dragAndDropError = dragAndDropError;
            this.submitUploadErrorBlock = submitUploadErrorBlock;
        }
    },
    template: '<DragNDrop v-bind:profilePicturePreview="profilePicturePreview" ' +
        'v-bind:originalProfilePictureUpload="originalProfilePictureUpload"' +
        'v-bind:dragAndDropError="dragAndDropError"' +
        'v-bind:submitUploadErrorBlock="submitUploadErrorBlock"/>',
    components: {
        DragNDrop
    }
});

window.dragNdrop = dragNdrop;