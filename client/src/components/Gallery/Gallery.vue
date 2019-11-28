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
        <Pagination
         @previousPage="previousPage"
          @nextPage="nextPage"
           @specificPage="specificPage"
        v-bind:paginationInfo="paginationInfo"
        />
    </div>
</template>

<script>
import { mapMutations } from 'vuex';
import Pagination from '../Pagination/Pagination.vue';
import Confirmation from '../Confirmation/Confirmation.vue';
import Notification from './GalleryNotification.vue';
import GalleryFileList from './GalleryFileList.vue';

export default {
    name: 'Gallery',
    props: ['files', 'notification', 'paginationInfo',
        'imageExtensions', 'constants', 'confirm', 'yesClickFn', 'noTop'],
    components: {
        Pagination,
        Notification,
        GalleryFileList,
        Confirmation,
    },
    data() {
        return {
        };
    },
    mounted() {
    },
    methods: {
        previousPage() {
            this.$emit('previousPage');
        },
        nextPage() {
            this.$emit('nextPage');
        },
        specificPage(n) {
            this.$emit('page', n);
        },

        hideConfirmation() {
            const confirm = {
                display: 'none',
            };
            this.changeConfirmation(confirm);
        },
        ...mapMutations('Gallery', [
            'changeConfirmation',
        ]),
    },
    created() {
    },
};
</script>

<style lang="scss" scoped>
    @import './css/Gallery.scss';
</style>
