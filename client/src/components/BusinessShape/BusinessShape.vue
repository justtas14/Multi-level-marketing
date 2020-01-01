<template>
    <ul class="associate-levelBarList">
        <div v-if="this.levels > 0">
            <LevelBarListItem
                v-for="(item, index) in associatesInLevels"
                :key="index"
                v-bind:associatesInLevel="item"
                v-bind:maxLevel="maxLevel"
                v-bind:level="index"
            />
        </div>
        <p style="text-align:center" v-else>Associate doesn't have any children</p>
    </ul>
</template>s

<script>
import LevelBarListItem from './LevelBarListItem.vue';

export default {
    name: 'BusinessShape',
    components: {
        LevelBarListItem,
    },
    props: ['associatesInLevels', 'levels', 'maxLevel'],
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
    @import './css/BusinessShape.scss';
</style>
