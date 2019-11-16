<template>
    <li class="associate-levelBarListItem" style="list-style-type:none;">
        <div class="associate-levelBar" :style="{ width: associatesInLevel*100/maxLevel + '%' }">
            Level {{ level }}: <strong>{{ associatesInLevel }}</strong>
        </div>
    </li>
</template>

<script>

export default {
    name: 'LevelBarLIstItem',
    components: {

    },
    props: ['maxLevel', 'level', 'associatesInLevel'],
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

<style lang="scss" scoped>
   @import './css/LevelBarListItem.scss';
</style>
