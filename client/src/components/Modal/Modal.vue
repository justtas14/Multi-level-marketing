<template>
    <div id="myModal" class="modal">
        <div class="modal-content userSearch">
            <a id="closeBtn" class="styles_closeButton__20ID4">
                <svg class="styles_closeIcon__1QwbI close" xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 36 36">
                    <path d="M28.5 9.62L26.38 7.5 18 15.88 9.62 7.5 7.5 9.62 15.88
                     18 7.5 26.38l2.12 2.12L18 20.12l8.38 8.38 2.12-2.12L20.12 18z">
                    </path>
                </svg>
            </a>
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
import './css/Modal.scss';

export default {
    name: 'Modal',
    components: {

    },
    props: [],
    methods: {

    },
    computed: {

    },
    created() {

    },
};
</script>

<style scoped>

</style>
