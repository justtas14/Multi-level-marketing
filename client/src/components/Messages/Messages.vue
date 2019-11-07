<template>
    <div id="error-messages">
        <Error
            v-for="(value, key) in errorMessages"
            v-bind:key="key"
            v-bind:message="value"
        ></Error>
        <Success
            v-for="(value, key) in successMessages"
            v-bind:key="key"
            v-bind:message="value"
        ></Success>
    </div>
</template>

<script>
import './css/Messages.scss';
import Error from './Error.vue';
import Success from './Success.vue';

export default {
    name: 'Messages',
    components: {
        Error,
        Success,
    },
    props: ['errorMessages', 'successMessages'],
    data() {
        return {

        };
    },
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
