<template>
    <div v-show="associates!==null"
    class="slot" ref="slot" @scroll="scrollSlot" :class="{root: (slotNumber === 0)}">
        <div id="firstElement" v-if="slotNumber === 0"><i class="material-icons rootIcon">
            device_hub
        </i></div>
        <div v-else
        v-for="(associate, index) in associates" v-bind:key="associate.id" ref="allAssociates">
            <Associate
            v-bind:associate="associate"
            v-bind:associateLenght="associates.length"
            v-bind:index="index"
            v-bind:slotContainer="$refs.slot"
            v-bind:isFocused="isFocused"
            ></Associate>
        </div>
    </div>
</template>

<script>
import {
    mapMutations, mapState, mapActions,
} from 'vuex';
import Associate from './Associate.vue';

export default {
    name: 'SlotComponent',
    props: ['associates', 'slotNumber'],
    data() {
        return {
            timer: null,
            isFocused: false,
        };
    },
    components: {
        Associate,
    },
    computed: {
        currentFocusedId() {
            return this.address[this.address.length - 1];
        },
        currentAddress() {
            return this.address.length;
        },
        ...mapState('NodeExplorer', [
            'address',
            'rootAssociate',
            'container',
            'minValueX',
            'currentFocusNumber',
        ]),
    },
    watch: {
        currentFocusedId(newValue) {
            const { slot } = this.$refs;
            this.isFocused = false; slot.style.overflowX = 'hidden';
            if (this.associates) {
                this.associates.forEach(async (element) => {
                    if (element.id.toString() === newValue) {
                        // const loadedAssociates = await this.getAssociates(element.id);
                        // console.log(loadedAssociates);
                        this.isFocused = true;
                        this.setCurrentFocusNumber(this.slotNumber);
                        slot.style.overflowX = 'scroll';
                    }
                });
            }
        },
        currentAddress(newValue, oldValue) {
            const { slot } = this.$refs;
            setTimeout(() => {
                if (newValue < oldValue) {
                    if (this.currentFocusNumber < this.slotNumber) {
                        slot.scrollTo(0, 0);
                    }
                }
            }, 25);
        },
    },
    mounted() {
        if (this.associates) {
            const { currentFocusedId } = this;
            this.associates.forEach((element) => {
                if (element.id === currentFocusedId) {
                    this.isFocused = true;
                }
            });
            const { slot } = this.$refs;
            if (this.isFocused) {
                slot.style.overflowX = 'scroll';
            }
            this.$emit('slot', slot);
        }
    },
    methods: {
        focus(id) {
            if (!this.address.includes(id)) {
                if (this.isFocused) {
                    if (this.address.length === 2) {
                        this.setAddress([this.address[0], id.toString()]);
                    } else {
                        this.popAddress();
                        this.pushAddress(id.toString());
                    }
                }
            }
        },
        scrollSlot() {
            if (this.timer !== null) {
                clearTimeout(this.timer);
            }
            this.timer = setTimeout(() => {
                this.setMinValueX(9999);
                const associateArray = this.$refs.allAssociates;
                associateArray.forEach((element) => {
                    const elementId = element.querySelector('#associateId').innerHTML;
                    const rect = element.getBoundingClientRect();
                    const xValue = rect.right - (this.container.beginX + this.container.middleX);
                    if (this.minValueX > xValue && xValue > 0) {
                        this.focus(parseInt(elementId, 10));
                        this.setMinValueX(xValue);
                    }
                });
            }, 300);
        },
        ...mapActions('NodeExplorer', [
            'getAssociates',
        ]),
        ...mapMutations('NodeExplorer', [
            'setMinValueX',
            'pushAddress',
            'setAddress',
            'popAddress',
            'setCurrentFocusNumber',
        ]),
    },
    created() {
    },
};
</script>

<style lang="scss" scoped>
    @import './css/Slot.scss';
</style>
