<template>
    <div class="error__block">
        {{ message }}
    </div>
</template>

<script>
    import './css/Error.scss';

    export default {
        name: "Error",
        props: ['message']
    }
</script>

<style scoped>

</style>