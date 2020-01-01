<template>
    <div class="recaptcha-container">
        <VueRecaptcha
        style="transform:scale(0.77);
        -webkit-transform:scale(0.77);
        transform-origin:0 0;-webkit-transform-origin:0 0;"
        ref="recaptcha"
        :loadRecaptchaScript="true"
        @verify="onVerify"
        @expired="onExpired"
        :sitekey="siteKey">
        </VueRecaptcha>
    </div>
</template>

<script>
import {
    mapMutations,
} from 'vuex';
import VueRecaptcha from 'vue-recaptcha';

export default {
    name: 'Recaptcha',
    props: ['siteKey'],
    components: {
        VueRecaptcha,
    },
    methods: {
        onVerify(response) {
            this.changeRecaptchaKey(response);
            console.log(`Verify: ${response}`);
        },
        onExpired() {
            this.changeRecaptchaKey(null);
            console.log('Expired');
        },
        ...mapMutations('Invitation', [
            'changeRecaptchaKey',
            'recaptchaReset',
        ]),
    },
    created() {
        this.recaptchaReset(() => this.$refs.recaptcha.reset());
    },
};

</script>

<style lang="scss" scoped>
    @import './css/Recaptcha.scss';
</style>
