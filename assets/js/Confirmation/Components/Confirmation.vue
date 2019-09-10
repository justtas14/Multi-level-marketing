<template>
    <div class="v-dialogs">
        <div class="v-dialog active v-dialog-sm">
            <div class="v-dialog-overlay" @click="hideConfirmation"></div>
            <div class="v-dialog-container">
                <div class="v-dialog-header"><a aria-label="Fechar" @click="hideConfirmation" class="v-dialog-btn v-dialog-btn-close"></a>
                    <div class="v-dialog-title">Confirmation</div>
                </div>
                <div class="v-dialog-body">
                    <div v-if="addPicture" class="confirmImageContainer">
                        <img svg-inline src="../../../images/geneology.svg" class="confirmImage" alt="svg" />
                        <div class="content">
                            {{confirm.message}}
                        </div>
                    </div>
                    <div v-else class="content">
                        {{confirm.message}}
                    </div>
                </div>
                <div class="v-dialog-footer"><a class="v-dialog-btn v-dialog-btn-danger" @click="hideConfirmation">No</a><a
                        class="v-dialog-btn success" @click="yesClickFn">Yes</a></div>
            </div>
        </div>
    </div>
</template>
<script>
    import 'v-slim-dialog/dist/v-slim-dialog.css'
    import '../css/Confirmation.css';

    export default {
        name: 'Confirmation',
        props: ['confirm', 'yesClickFn', 'addPicture'],
        components: {

        },
        data() {
            return {
            }
        },
        methods: {
            hideConfirmation: function () {
                this.$emit('hideConfirmation');
            }
        },
        created() {
        }
    }
</script>

<style>

</style>