<template>
    <div id="error-messages">
        <Error
            v-for="message in errorMessages"
            v-bind:key="key"
            v-bind:message="message"
        ></Error>
        <Success
            v-for="message in successMessages"
            v-bind:key="key"
            v-bind:message="message"
        ></Success>
    </div>
</template>

<script>
    import './css/Messages.scss';
    import Error from "./Error";
    import Success from "./Success";

    export default {
        name: "Messages",
        components: {
            Error,
            Success
        },
        props: ['errorMessages', 'successMessages'],
        data() {
            return {

            }
        },
        methods: {

        },
        computed: {

        },
        created() {

        }
    }
</script>

<style scoped>

</style>