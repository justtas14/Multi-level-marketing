<template>
    <div class="success__block">
        {{ message }}
    </div>
</template>

<script>

export default {
    name: 'Success',
    props: ['message'],
};
</script>

<style lang="scss" scoped>
    @import './css/Success.scss';
</style>
