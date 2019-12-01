<template>
    <Modal>
        <template v-slot:header>
            <ModalFileContainer @readUrl="readUrl" v-bind:category="category"/>
        </template>
        <template v-slot:body>
            <Gallery
                v-bind:yesClickFn="yesClickFn"
                v-bind:files="files"
                v-bind:notification="notification"
                v-bind:paginationInfo="paginationInfo"
                v-bind:imageExtensions="imageExtensions"
                v-bind:constants="constants"
                v-bind:confirm="confirm"
                v-bind:noTop="noTop"
            >
                <template v-slot:category></template>
            </Gallery>
        </template>
    </Modal>
</template>

<script>
import { mapActions, mapMutations, mapState } from 'vuex';
import Gallery from './Gallery.vue';
import Modal from '../Modal/Modal.vue';
import ModalFileContainer from './ModalFileContainer.vue';
import modalGalleryConst from './constants/modalGalleryConst';
import EventBus from './EventBus/EventBus';


export default {
    name: 'ModalGalleryWrapper',
    props: [],
    components: {
        Gallery,
        Modal,
        ModalFileContainer,
    },
    data() {
        return {
            constants: {},
            noTop: true,
        };
    },
    computed: mapState('Gallery', {
        currentPage: state => state.paginationInfo.currentPage,
        modalState: 'modalState',
        editorState: 'editor',
        category: 'category',
        yesClickFn: 'yesClickFn',
        files: 'files',
        notification: 'notification',
        paginationInfo: 'paginationInfo',
        imageExtensions: 'imageExtensions',
        confirm: 'confirm',
    }),
    methods: {
        readUrl(e) {
            this.readURL(e);
        },
        ...mapActions('Gallery', [
            'callDataAxios',
            'readURL',
            'handleFiles',
            'deleteRequestFunction',
        ]),
        ...mapMutations('Gallery', [
            'changeModalState',
            'changeCategory',
            'changePage',
            'closeNotification',
            'showNotification',
            'changeConfirmation',
            'changeYesFn',
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
                display: 'block',
                message: `Are you sure you want to delete ${fileName} file?`,
            };
            scope.changeConfirmation(confirm);
        });
        EventBus.$on('previousPage', () => {
            const page = null; const
                action = 'subtract';
            scope.changePage({ page, action });
            scope.callDataAxios();
        });
        EventBus.$on('nextPage', () => {
            const action = 'add'; const
                page = null;
            scope.changePage({ page, action });
            scope.callDataAxios();
        });
        EventBus.$on('page', (page) => {
            const action = null;
            scope.changePage({
                page,
                action,
            });
            scope.callDataAxios();
        });
        EventBus.$on('showNotification', (msg) => {
            scope.showNotification(msg);
        });
        EventBus.$on('closeNotification', () => {
            scope.closeNotification();
        });
    },
    created() {
        this.constants = modalGalleryConst;
    },
};
</script>

<style lang="scss" scoped>
    @import './css/ModalGalleryWrapper.scss';
</style>
