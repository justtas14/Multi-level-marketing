<template>
    <div class="recaptcha-container">
        <div class="g-recaptcha" :data-sitekey="siteKey"></div>
    </div>
</template>

<script>
    import './css/Recaptcha.scss';

    export default {
        name: "Recaptcha",
        props: ['siteKey']
    }
</script>

<style scoped>

</style>