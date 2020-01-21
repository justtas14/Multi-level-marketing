<template>
    <div class="slot" ref="slot" @scroll="scrollSlot">
        <div v-for="(associate, index) in associates" v-bind:key="index">
            <Associate v-bind:associate="associate"
            v-bind:associateLenght="associates.length"
            v-bind:index="index"
            v-bind:slotNumber="slotNumber"></Associate>
        </div>
    </div>
</template>

<script>
import Associate from './Associate.vue';

export default {
    name: 'SlotComponent',
    props: ['associates', 'slotNumber'],
    data() {
        return {
            slot: null,
            timer: null,
            focusAssociate: () => {},
        };
    },
    components: {
        Associate,
    },
    computed: {

    },
    mounted() {
        this.slot = this.$refs.slot;
        this.$emit('slot', this.slot);
    },
    methods: {
        scrollSlot() {
            if (this.timer !== null) {
                clearTimeout(this.timer);
            }
            this.timer = setTimeout(() => {
            }, 350);
        },
    },
    created() {

    },
};
</script>

<style lang="scss" scoped>
    @import './css/Slot.scss';
</style>
