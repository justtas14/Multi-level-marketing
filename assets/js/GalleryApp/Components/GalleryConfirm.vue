<template>
    <div id="confirmBox">
        <div class="message">{{confirm.message}}</div>
        <span class="button yes" @click="yesClickFn">Yes</span>
        <span class="button no" @click="hideConfirmation">No</span>
    </div>
</template>
<script>
    import EventBus from '../EventBus/EventBus';

    export default {
        name: 'Confirm',
        props: ['confirm', 'yesClickFn', 'noClickFn'],
        data() {
            return {
            }
        },
        methods: {
            hideConfirmation: function () {
                EventBus.$emit('hideConfirmation');
            }
        }
    }
</script>

<style src="../css/GalleryConfirm.css" scoped>

</style>