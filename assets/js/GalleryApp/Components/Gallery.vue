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
                    v-bind:noTop="noTop"
                />
                <Confirmation
                        @hideConfirmation="hideConfirmation"
                        v-bind:confirm="confirm"
                        v-bind:style="{display: confirm.display}"
                        v-bind:yesClickFn="yesClickFn"
                />
            </div>
        </div>
        <Pagination v-bind:paginationInfo="paginationInfo"/>
    </div>
</template>

<script>
    import Pagination from './GalleryPagination';
    import Confirmation from "../../Confirmation/Components/Confirmation";
    import Notification from './GalleryNotification';
    import GalleryFileList from "./GalleryFileList";
    import { mapMutations } from 'vuex'

    export default {
        name: 'Gallery',
        props: ['files', 'notification', 'paginationInfo',
            'imageExtensions', 'constants', 'confirm', 'yesClickFn', 'noTop'],
        components: {
            Pagination,
            Notification,
            GalleryFileList,
            Confirmation
        },
        data() {
            return {
            }
        },
        mounted() {
        },
        methods: {
            hideConfirmation: function () {
                const confirm = {
                    display: 'none'
                };
                this.changeConfirmation(confirm);
            },
            ...mapMutations('gallery', [
                'changeConfirmation'
            ])
        },
    }
</script>

<style src="../css/Gallery.css" scoped>
</style>
