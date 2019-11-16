<template>
    <div class="error__block">
        {{ message }}
    </div>
</template>

<script>

export default {
    name: 'Error',
    props: ['message'],
};
</script>

<style lang="scss" scoped>
    @import './css/Error.scss';
</style>
