<template>
    <Gallery
            v-bind:yesClickFn="yesClickFn"
            v-bind:files="files"
            v-bind:notification="notification"
            v-bind:paginationInfo="paginationInfo"
            v-bind:constants="constants"
            v-bind:confirm="confirm"
             @previousPage="previousPage"
             @nextPage="nextPage"
             @page="specificPage"
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
import './css/GalleryWrapper.scss';
import { mapActions, mapMutations, mapState } from 'vuex';
import Gallery from './Gallery.vue';
import CategoryMenu from './GalleryMenu.vue';
import galleryConst from './constants/galleryConst';
import EventBus from './EventBus/EventBus';


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
        };
    },
    computed: {
        ...mapState('Gallery', {
            currentPage: state => state.paginationInfo.currentPage,
            category: 'category',
            yesClickFn: 'yesClickFn',
            files: 'files',
            confirm: 'confirm',
            notification: 'notification',
            paginationInfo: 'paginationInfo',
            imageExtensions: 'imageExtensions',
        }),
    },
    methods: {
        previousPage() {
            const page = null; const
                action = 'subtract';
            this.changePage({ page, action });
            this.callDataAxios();
        },
        nextPage() {
            const page = null;
            const action = 'add';
            this.changePage({ page, action });
            this.callDataAxios();
        },
        specificPage(n) {
            const page = n;
            const action = null;
            this.changePage({
                page,
                action,
            });
            this.callDataAxios();
        },

        readUrl(e) {
            this.readURL(e);
        },
        categorizeFiles(category) {
            this.changeCategory(category);
            const page = 1; const
                action = null;
            this.changePage({ page, action });
            this.callDataAxios();
        },
        ...mapActions('Gallery', [
            'callDataAxios',
            'readURL',
            'handleFiles',
            'deleteRequestFunction',
        ]),
        ...mapMutations('Gallery', [
            'changeCategory',
            'changePage',
            'closeNotification',
            'showNotification',
            'changeConfirmation',
            'changeYesFn',
            'changeFilesPerPage',
        ]),
        ...mapMutations('Security', [
            'logout',
        ]),
    },
    mounted() {
        const scope = this;
        EventBus.$on('handleDrop', (event) => {
            const dt = event.dataTransfer;
            const { files } = dt;
            scope.handleFiles(files);
        });
        EventBus.$on('delete', (fileName) => {
            const confirm = {
                message: `Are you sure you want to delete ${fileName} file?`,
                display: 'block',
            };
            scope.changeConfirmation(confirm);
        });
        EventBus.$on('showNotification', (msg) => {
            scope.showNotification(msg);
        });
        EventBus.$on('closeNotification', () => {
            scope.closeNotification();
        });
        EventBus.$on('select', () => {});
        EventBus.$on('insertFileToForm', () => {});
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
};
</script>

<style lang="scss" scoped>
</style>
