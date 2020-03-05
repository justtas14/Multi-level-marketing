<template>
    <div v-show="associates!==null"
    class="slot" ref="slot" :class="{root: (slotNumber === 0)}"
        @mousedown="lock"
        @touchStart="lock"
        @mouseup="move"
        @touchend="move"
        @touchmove="touchMove"
    >
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
                v-bind:focus="focus"
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
            x0: null,
            y0: null,
            currentFocus: {
                index: 0,
                id: 0,
            },
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
            this.isFocused = false;
            // slot.style.overflowX = 'hidden';
            if (this.associates) {
                this.associates.forEach(async (element, index) => {
                    if (element.id.toString() === newValue) {
                        this.isFocused = true;
                        this.setCurrentFocusNumber(this.slotNumber);
                        this.currentFocus = {
                            index,
                            id: element.id.toString(),
                        };
                        // slot.style.overflowX = 'scroll';
                    }
                });
            }
        },
        currentAddress(newValue, oldValue) {
            const { slot } = this.$refs;
            setTimeout(() => {
                if (newValue < oldValue) {
                    if (this.currentFocusNumber < this.slotNumber) {
                        slot.style.transform = 'translateX(0px)';
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
                // slot.style.overflowX = 'scroll';
            }
            this.$emit('slot', slot);
        }
    },
    methods: {
        unify(e) {
            return e.changedTouches ? e.changedTouches[0] : e;
        },
        lock(e) {
            this.y0 = this.unify(e).clientY;
            this.x0 = this.unify(e).clientX;
        },
        move(e) {
            if ((this.y0 || this.y0 === 0) && (this.x0 === 0 || this.x0) && this.isFocused) {
                const dy = this.unify(e).clientY - this.y0;
                const dx = this.unify(e).clientX - this.x0;
                if (Math.abs(dx) > Math.abs(dy)) {
                    const s = Math.sign(dx);
                    if (s === -1) {
                        if (this.currentFocus.index < this.associates.length - 1) {
                            this.$refs.slot.style.transform = `translateX(${(-100 * (this.currentFocus.index + 1))}px)`;
                            this.focus(this.associates[this.currentFocus.index + 1].id);
                            console.log('right', this.associates[this.currentFocus.index + 1]);
                        }
                    } else if (s === 1) {
                        if (this.currentFocus.index > 0) {
                            this.$refs.slot.style.transform = `translateX(${(-100 * (this.currentFocus.index - 1))}px)`;
                            this.focus(this.associates[this.currentFocus.index - 1].id);
                            console.log('left');
                        }
                    }
                }
                this.y0 = null;
                this.x0 = null;
            }
        },
        touchMove(e) {
            e.preventDefault();
        },
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
        // scrollSlot() {
        //     if (this.timer !== null) {
        //         clearTimeout(this.timer);
        //     }
        //     this.timer = setTimeout(() => {
        //         this.setMinValueX(9999);
        //         const associateArray = this.$refs.allAssociates;
        //         associateArray.forEach((element) => {
        //             const elementId = element.querySelector('#associateId').innerHTML;
        //             const rect = element.getBoundingClientRect();
        //             const xValue = rect.right - (this.container.beginX + this.container.middleX);
        //             if (this.minValueX > xValue && xValue > 0) {
        //                 this.focus(parseInt(elementId, 10));
        //                 this.setMinValueX(xValue);
        //             }
        //         });
        //     }, 300);
        // },
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
