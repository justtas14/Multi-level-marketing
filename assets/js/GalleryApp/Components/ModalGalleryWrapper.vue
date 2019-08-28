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
            >
                <template v-slot:category></template>
            </Gallery>
        </template>
    </ModalWrapper>
</template>

<script>
    import Vue from 'vue';
    import Gallery from "./Gallery";
    import ModalWrapper from "./ModalWrapper";
    import ModalFileContainer from "./ModalFileContainer";
    import modalGalleryConst from "../constants/modalGalleryConst";
    import { mapActions, mapMutations, mapState } from 'vuex'
    import EventBus from '../EventBus/EventBus';
    import 'v-slim-dialog/dist/v-slim-dialog.css';
    import SlimDialog from 'v-slim-dialog';

    Vue.use(SlimDialog);

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
            notification: 'notification',
            paginationInfo: 'paginationInfo',
            imageExtensions: 'imageExtensions'
        }),
        methods: {
            showConfirmation(message) {
                const options = {title: 'Confirmation',  okLabel: 'Yes', cancelLabel: 'No', size: 'sm'};
                this.$dialogs.confirm(message, options)
                    .then(res => {
                        if (res.ok) {
                            this.yesClickFn();
                        }
                    })
            },
            readUrl: function (e) {
                const params = {
                    e: e,
                    confirmation: this.showConfirmation
                };
                this.readURL(params)
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
                scope.showConfirmation('Are you sure you want to delete ' + fileName + ' file?');
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
