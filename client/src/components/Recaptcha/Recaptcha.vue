<template>
    <div class="recaptcha-container">
        <VueRecaptcha
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
        ]),
    },
};

</script>

<style lang="scss" scoped>
    @import './css/Recaptcha.scss';
</style>
