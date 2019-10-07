<template>
    <table id="logTable">
        <thead>
            <th>Message</th>
            <th>Log Created</th>
        </thead>
        <tbody id="associatesWrapper">
        <div v-if="spinner" class="Spinner__Container user__search__spiner" v-bind:style="{top: 0, 'z-index': 9999}">
            <div class="lds-dual-ring"/>
        </div>
        <Log
            v-bind:key="log.id"
            v-for="log in logs"
            v-bind:log="log"
        />
        </tbody>
    </table>
</template>

<script>
    import Log from "./Log";
    import '../css/logTable.scss';

    export default {
        name: "LogTable",
        props: ['logs', 'spinner'],
        components: {
            Log
        },
        methods: {

        }
    }
</script>

<style scoped>

</style>