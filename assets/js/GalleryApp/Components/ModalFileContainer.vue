<template>
    <div class="modal-header-gallery">
        <span v-if="category=='images'">Select image</span>
        <span v-else>Select file</span>
        <input class="file-upload-input" id="files" type='file'  @change="readUrl"/>
        <label for="files"><i class="fa fa-plus" aria-hidden="true"></i></label>
    </div>
</template>
<script>
    export default {
        name: 'ModalFileContainer',
        props: ['category'],
        data() {
            return {

            }
        },
        methods: {
            readUrl: function (e) {
                this.$emit('readUrl', e);
            }
        }
    }
</script>

<style src="../css/ModalFileContainer.css" scoped>

</style>