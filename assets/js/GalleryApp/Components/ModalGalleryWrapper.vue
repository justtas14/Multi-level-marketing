<template>
    <ModalWrapper>
        <template v-slot:fileInputContainer>
            <ModalFileContainer @readUrl="readUrl" v-bind:category="category"/>
        </template>
        <template v-slot:gallery>
            <Gallery
                v-bind:yesClickFn="yesClickFn"
                v-bind:files="files"
                v-bind:confirm="confirm"
                v-bind:notification="notification"
                v-bind:paginationInfo="paginationInfo"
                v-bind:imageExtensions="imageExtensions"
                v-bind:constants="constants"
            >
                <template v-slot:category></template>
            </Gallery>
        </template>
    </ModalWrapper>
</template>

<script>
    import Gallery from "./Gallery";
    import ModalWrapper from "./ModalWrapper";
    import ModalFileContainer from "./ModalFileContainer";
    import modalGalleryConst from "../constants/modalGalleryConst";
    import { mapActions, mapMutations, mapState } from 'vuex'
    import EventBus from '../EventBus/EventBus';

    export default {
        name: 'ModalGalleryWrapper',
        props: [],
        components: {
            Gallery,
            ModalWrapper,
            ModalFileContainer
        },
        data() {
            return {
                constants: {}
            }
        },
        computed: mapState('gallery', {
            currentPage: state => state.paginationInfo.currentPage,
            modalState: 'modalState',
            editorState: 'editor',
            category: 'category',
            yesClickFn: 'yesClickFn',
            files: 'files',
            confirm: 'confirm',
            notification: 'notification',
            paginationInfo: 'paginationInfo',
            imageExtensions: 'imageExtensions'
        }),
        methods: {
            readUrl: function (e) {
                this.readUrl(e)
            },
            ...mapActions('gallery', [
                'callDataAxios',
                'readUrl',
                'handleFiles',
                'deleteRequestFunction',
            ]),
            ...mapMutations('gallery', [
                'changeModalState',
                'changeCategory',
                'changePage',
                'closeNotification',
                'showNotification',
                'hideConfirmation',
                'showConfirm',
                'changeYesFn'
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
            EventBus.$on('setInnerDeleteFunction', async function (params) {
                scope.changeYesFn(() => {
                    scope.deleteRequestFunction(params);
                    EventBus.$emit('checkDeleted', params.fileId);
                });
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
        },
        created() {
            this.constants = modalGalleryConst;
        }
    }
</script>

<style src="../css/ModalGalleryWrapper.css">
</style>
