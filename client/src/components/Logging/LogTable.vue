<template>
    <table id="logTable">
        <thead id="logTable-header">
            <th width="70%" class="logHeader message">Message</th>
            <th width="30%" class="logHeader created">Log Created</th>
        </thead>
        <tbody id="associatesWrapper" v-if="logs.length > 0">
            <div v-if="isLoading" class="Spinner__Container user__search__spiner"
                v-bind:style="{top: 0, left: 0, right:0, bottom: 0, 'z-index': 9999}">
                <div class="lds-dual-ring"/>
            </div>
            <Log
                v-bind:key="log.id"
                v-for="log in logs"
                v-bind:log="log"
            />
        </tbody>
        <tbody v-else>
             <tr class="log-container">
                <td v-if="!isLoading" class="log-itemContainer">
                    No logs to display
                </td>
                <td class="log-itemContainer">
                </td>
            </tr>
        </tbody>
    </table>
</template>

<script>
import Log from './Log.vue';

export default {
    name: 'LogTable',
    props: ['logs', 'isLoading'],
    components: {
        Log,
    },
    methods: {

    },
};
</script>

<style lang="scss" scoped>
    @import './css/logTable.scss';
</style>
