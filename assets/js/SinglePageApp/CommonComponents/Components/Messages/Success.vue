<template>
    <div class="success__block">
        {{ message }}
    </div>
</template>

<script>
    import '../../css/Messages/Success.scss';

    export default {
        name: "Success",
        props: ['message']
    }
</script>

<style scoped>

</style>