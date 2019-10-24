<template>
    <ul class="associate-levelBarList">
        <LevelBarListItem
                v-if="associatesInLevels.length > 0"
                v-bind:key="associatesInLevel.key"
                v-for="associatesInLevel in associatesInLevels"
                v-bind:associatesInLevel="associatesInLevel"
                v-bind:maxLevel="maxLevel"
        />
        <p v-else>Associate doesn't have any children</p>
    </ul>
</template>

<script>
    import '../../css/BusinessShape/BusinessShape.scss'
    import LevelBarListItem from "./LevelBarListItem"

    export default {
        name: "BusinessShape",
        components: {
            LevelBarListItem
        },
        props: ['associatesInLevels'],
        data() {
            return {
                maxLevel: 0
            }
        },
        methods: {

        },
        computed: {

        },
        created() {
            this.maxLevel = Math.max(this.associatesInLevels);
        }
    }
</script>

<style scoped>

</style>