<template>
    <div id="galleryApp">
        <div class="appContainer">
            <slot name="category"></slot>
            <div class="galleryContainer">
                <GalleryFileList
                    v-bind:files="files"
                    v-bind:imageExtensions="imageExtensions"
                    v-bind:constants="constants"
                />
                <Notification
                    v-bind:message="notification.message"
                    v-bind:style="{display: notification.display}"
                />
            </div>
        </div>
        <Pagination v-bind:paginationInfo="paginationInfo"/>
    </div>
</template>

<script>
    import Pagination from './GalleryPagination';
    import Notification from './GalleryNotification';
    import GalleryFileList from "./GalleryFileList";

    export default {
        name: 'Gallery',
        props: ['files', 'notification', 'paginationInfo',
            'imageExtensions', 'constants'],
        components: {
            Pagination,
            Notification,
            GalleryFileList
        },
        data() {
            return {
            }
        },
        mounted() {
        },
        methods: {

        },
    }
</script>

<style src="../css/Gallery.css" scoped>
</style>
