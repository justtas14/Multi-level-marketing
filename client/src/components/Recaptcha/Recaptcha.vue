<template>
    <div class="recaptcha-container">
        <div class="g-recaptcha" :data-sitekey="siteKey"></div>
    </div>
</template>

<script>

export default {
    name: 'Recaptcha',
    props: ['siteKey'],
    methods: {

    },
};

</script>

<style lang="scss" scoped>
    @import './css/Recaptcha.scss';
</style>
