<template>
    <div id="myModal" class="modal" ref="modalRef" @click="watchForBoundaries">
        <a @click="closeModal" id="closeBtn" class="styles_closeButton__20ID4">
            <i class="material-icons close">
                close
            </i>
            </a>
        <div class="modal-content">
            <div class="modal-header">
                <slot name="header"></slot>
            </div>
            <div class="modal-body">
                <slot name="body"></slot>
            </div>
        </div>
    </div>
</template>

<script>

export default {
    name: 'Modal',
    components: {

    },
    props: [],
    methods: {
        closeModal() {
            this.$emit('closeModal');
        },
        watchForBoundaries(event) {
            if (event.target.getAttribute('id') === this.$refs.modalRef.getAttribute('id')) {
                this.closeModal();
            }
        },
    },
    computed: {

    },
    mounted() {
    },
    created() {
    },
};
</script>

<style lang="scss" scoped>
    @import './css/Modal.scss';
</style>
