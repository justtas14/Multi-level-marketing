<template>
    <ModalWrapper>
        <template v-slot:fileInputContainer>
            <ModalFileContainer @readUrl="readUrl" v-bind:category="category"/>
        </template>
        <template v-slot:gallery>
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
    </ModalWrapper>
</template>

<script>
    import Gallery from "./Gallery";
    import ModalWrapper from "./ModalWrapper";
    import ModalFileContainer from "./ModalFileContainer";
    import modalGalleryConst from "../../SinglePageApp/CommonComponents/Components/Gallery/constants/modalGalleryConst";
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
                constants: {},
                noTop: true
            }
        },
        computed: mapState('gallery', {
            currentPage: state => state.paginationInfo.currentPage,
            modalState: 'modalState',
            editorState: 'editor',
            category: 'category',
            yesClickFn: 'yesClickFn',
            files: 'files',
            notification: 'notification',
            paginationInfo: 'paginationInfo',
            imageExtensions: 'imageExtensions',
            confirm: 'confirm'
        }),
        methods: {
            readUrl: function (e) {
                this.readURL(e)
            },
            ...mapActions('gallery', [
                'callDataAxios',
                'readURL',
                'handleFiles',
                'deleteRequestFunction',
            ]),
            ...mapMutations('gallery', [
                'changeModalState',
                'changeCategory',
                'changePage',
                'closeNotification',
                'showNotification',
                'changeConfirmation',
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
                const confirm = {
                    display: 'block',
                    message: 'Are you sure you want to delete ' + fileName + ' file?'
                };
                scope.changeConfirmation(confirm);
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
        },
        created() {
            this.constants = modalGalleryConst;
        }
    }
</script>

<style src="../css/ModalGalleryWrapper.css">
</style>
