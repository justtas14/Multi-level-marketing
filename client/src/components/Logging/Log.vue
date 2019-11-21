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

export default {
    name: 'Log',
    props: ['log'],
    data() {
        return {
            dateString: '',
        };
    },
    components: {

    },
    methods: {
        checkTime(i) {
            let time = i;
            if (time < 10) {
                time = `0${i}`;
            }
            return time;
        },
    },
    created() {
        // TODO use lib moment js
        const d = new Date(this.log.createdAt);
        const minutes = this.checkTime(d.getMinutes());
        const seconds = this.checkTime(d.getSeconds());
        this.dateString = `${(`0${d.getDate()}`).slice(-2)}-${(`0${d.getMonth() + 1}`).slice(-2)}-${
            d.getFullYear()} ${d.getHours()}:${minutes}:${seconds}`;
    },
};
</script>

<style lang="scss" scoped>
    @import './css/log.scss';
</style>
