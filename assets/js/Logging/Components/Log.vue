<template>
    <tr class="log-container">
        <td class="log-itemContainer">
            {{ log.message }}
        </td>
        <td class="log-itemContainer">
            {{ dateString }}
        </td>
    </tr>
</template>

<script>
    import '../css/log.scss';

    export default {
        name: "Log",
        props: ['log'],
        data() {
            return {
                dateString: '',
            }
        },
        components: {

        },
        methods: {

        },
        created() {
            const d = new Date(this.log.createdAt);
            this.dateString = ("0" + d.getDate()).slice(-2) + "-" + ("0"+(d.getMonth()+1)).slice(-2) + "-"
                + d.getFullYear() + ' ' + d.getHours() + ':' + d.getMinutes() + ':' + d.getSeconds();

        }
    }
</script>

<style scoped>

</style>