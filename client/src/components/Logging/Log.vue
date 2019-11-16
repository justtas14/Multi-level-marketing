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
            let hours;
            if (i < 10) {
                hours = `0${i}`;
            }
            return hours;
        },
    },
    created() {
        // TODO use lib moment js
        const d = new Date(this.log.createdAt);
        let m = d.getMinutes();
        let s = d.getSeconds();
        m = this.checkTime(m);
        s = this.checkTime(s);
        this.dateString = `${(`0${d.getDate()}`).slice(-2)}-${(`0${d.getMonth() + 1}`).slice(-2)}-${
            d.getFullYear()} ${d.getHours()}:${m}:${s}`;
    },
};
</script>

<style lang="scss" scoped>
    @import './css/log.scss';
</style>
