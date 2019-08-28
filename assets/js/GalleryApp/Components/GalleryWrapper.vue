<template>
    <Gallery
            v-bind:yesClickFn="yesClickFn"
            v-bind:files="files"
            v-bind:confirm="confirm"
            v-bind:notification="notification"
            v-bind:paginationInfo="paginationInfo"
            v-bind:imageExtensions="imageExtensions"
            v-bind:constants="constants"
    >
        <template v-slot:category>
            <CategoryMenu
                @readUrl="readUrl"
                v-bind:category="category"
                v-on:categorise-files="categorizeFiles"
            />
        </template>
    </Gallery>
</template>

<script>
    import Gallery from "./Gallery";
    import CategoryMenu from './GalleryMenu';
    import galleryConst from "../constants/galleryConst";
    import { mapActions, mapMutations, mapState } from 'vuex'
    import EventBus from '../EventBus/EventBus';

    export default {
        name: 'GalleryWrapper',
        props: [],
        components: {
            CategoryMenu,
            Gallery,
        },
        data() {
            return {
                constants: {},
            }
        },
        computed: mapState('gallery', {
              currentPage: state => state.paginationInfo.currentPage,
              category: 'category',
              yesClickFn: 'yesClickFn',
              files: 'files',
              confirm: 'confirm',
              notification: 'notification',
              paginationInfo: 'paginationInfo',
              imageExtensions: 'imageExtensions',
         }),
        methods: {
            readUrl: function (e) {
                this.readUrl(e)
            },
            categorizeFiles: function (category) {
                this.changeCategory(category);
                this.callDataAxios();
            },
            ...mapActions('gallery', [
                'callDataAxios',
                'readUrl',
                'handleFiles',
                'deleteRequestFunction',
            ]),
            ...mapMutations('gallery', [
                'changeCategory',
                'changePage',
                'closeNotification',
                'showNotification',
                'hideConfirmation',
                'showConfirm',
                'changeYesFn',
                'changeFilesPerPage'
            ])
        },
        mounted () {
            const scope = this;
            EventBus.$on('handleDrop', function (event) {
                const dt = event.dataTransfer;
                let files = dt.files;
                scope.handleFiles(files);
            });
            EventBus.$on('delete', function (fileName) {
                scope.showConfirm('Are you sure you want to delete ' + fileName + ' file?');
            });
            EventBus.$on('previousPage', function () {
                const page = null, action = 'subtract';
                scope.changePage({page, action});
                scope.callDataAxios();
            });
            EventBus.$on('nextPage', function () {
                const action = 'add', page = null;
                scope.changePage({page, action});
                scope.callDataAxios();
            });
            EventBus.$on('page', function (page) {
                const action = null;
                scope.changePage({
                    page,
                    action
                });
                scope.callDataAxios();
            });
            EventBus.$on('showNotification', function (msg) {
                scope.showNotification(msg);
            });
            EventBus.$on('closeNotification', function () {
                scope.closeNotification();
            });
            EventBus.$on('hideConfirmation', function () {
                scope.hideConfirmation();
            });
            EventBus.$on('select', function (e) {});
            EventBus.$on('insertFileToForm', function (e) {});
        },
        created() {
            const mql1 = window.matchMedia('(max-width: 1200px)');
            mql1.addListener((e) => {
                if (e.matches) {
                    this.callDataAxios(18);
                } else {
                    this.callDataAxios(20);
                }
            });
            this.constants = galleryConst;
            const w = window.innerWidth;

            if (w > 1200) {
                this.callDataAxios(20);
            } else {
                this.callDataAxios(18);
            }
        },
    }
</script>

<style src="../css/GalleryWrapper.css">
</style>
