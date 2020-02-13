<template>
    <div id="slots__container" ref="container" @scroll="scrollSlot">
    <!-- <div class="middleFocusRuler parentRight"></div>
    <div class="middleFocusRuler parentLeft"></div>
    <div class="middleFocusRuler top"></div>
    <div class="middleFocusRuler bottom"></div>
    <div class="middleFocusRuler left"></div>
    <div class="middleFocusRuler right"></div> -->
    <div v-show="!isLoading" ref="scrollContainer">
         <SlotComponent
            v-for="(associates, index) in getAssociatesInSlots" v-bind:key="index"
            @slot="assignSlot"
            v-bind:associates="associates"
            v-bind:slotNumber="index"
        ></SlotComponent>
    </div>
    </div>
</template>

<script>
import {
    mapActions, mapState, mapGetters, mapMutations,
} from 'vuex';
// import { ContentLoader } from 'vue-content-loader';
import getChildrenFromAddress, { getChildren } from './services/nodeExplorerMethods';

import SlotComponent from './Slot.vue';

export default {
    name: 'Main',
    props: [],
    data() {
        return {
            isLoading: false,
            timer: null,
            slots: [],
            lastContainerY: null,
            firstScroll: true,
            slotAmount: 4,
        };
    },
    components: {
        SlotComponent,
        // ContentLoader,
    },
    computed: {
        getAssociatesInSlots() {
            const associateSlotsArray = [];
            for (let i = 0; i < this.slotAmount; i++) {
                associateSlotsArray.push(this.getSlotAssociates(i));
            }

            return associateSlotsArray;
        },

        ...mapState('NodeExplorer', [
            'focusFirstSlotId',
            'focusSecondSlotId',
            'address',
            'rootAssociate',
        ]),
        ...mapGetters('Security', [
            'getAssociate',
        ]),
    },
    methods: {
        getSlotAssociates(number) {
            if (number === 0) {
                return [];
            }
            if (number === 1) {
                return [this.rootAssociate];
            }
            if ((this.address.length + 1) - number < 0) {
                let addressToAncestor = this.address.slice(0, this.address.length);
                let node = getChildrenFromAddress(addressToAncestor, this.rootAssociate);

                const tempAddress = [...this.address];
                let children = getChildren(node);

                if (children.length > 0) {
                    tempAddress.push(children[0].id);
                    addressToAncestor = tempAddress.slice(0, tempAddress.length);
                    node = getChildrenFromAddress(addressToAncestor, this.rootAssociate);
                    if (node) {
                        children = getChildren(node);
                        return children;
                    }
                }
                return null;
            }

            const addressToAncestor = this.address.slice(
                0, (this.address.length - (this.address.length - number)) - 1,
            );
            const node = getChildrenFromAddress(addressToAncestor, this.rootAssociate);

            return getChildren(node);
        },
        assignSlot(slot) {
            const { container } = this.$refs;
            slot.style.padding = `0 0 0 ${container.offsetWidth / 2 - 50}px`;
            this.slots.push(slot);
        },
        scrollSlot() {
            const { scrollContainer } = this.$refs;
            let rect = scrollContainer.getBoundingClientRect();
            if (this.firstScroll) {
                this.lastContainerY = rect.top;
                this.firstScroll = false;
            }
            if (this.timer !== null) {
                clearTimeout(this.timer);
            }
            this.timer = setTimeout(() => {
                rect = scrollContainer.getBoundingClientRect();
                if (Math.abs(this.lastContainerY - rect.top) < 10) {
                    console.log('same');
                } else if (this.lastContainerY > rect.top) {
                    const addressToAncestor = this.address.slice(0, this.address.length);
                    const node = getChildrenFromAddress(addressToAncestor, this.rootAssociate);
                    this.pushAddress(getChildren(node)[0].id.toString());
                    this.slotAmount++;
                } else {
                    let popCount = Math.round(
                        Math.abs(Math.abs(this.lastContainerY)
                         - Math.abs(rect.top > 0 ? rect.top - 140 : rect.top)) / 140,
                    );
                    if (rect.top > 0) {
                        popCount++;
                    }
                    for (let i = 0; i < popCount; i++) {
                        this.popAddress();
                        this.slotAmount--;
                    }
                }
                this.lastContainerY = rect.top;
            }, 500);
        },
        ...mapActions('NodeExplorer', [
            'getAllAssociates',
        ]),
        ...mapMutations('NodeExplorer', [
            'setAddress',
            'popAddress',
            'pushAddress',
            'setContainerParams',
        ]),
    },
    mounted() {
        const { container, scrollContainer } = this.$refs;
        this.lastContainerY = scrollContainer.getBoundingClientRect().top;
        let rect = container.getBoundingClientRect();
        let containerData = {
            beginX: rect.left,
            middleX: (Math.abs(rect.right - rect.left) / 2),
            width: container.offsetWidth,
        };
        this.setContainerParams(containerData);
        window.addEventListener('resize', () => {
            rect = container.getBoundingClientRect();
            containerData = {
                beginX: rect.left,
                middleX: (Math.abs(rect.right - rect.left) / 2),
                width: container.offsetWidth,
            };
            this.setContainerParams(containerData);
            this.slots.forEach((slot) => {
                slot.style.padding = `0 0 0 ${container.offsetWidth / 2 - 50}px`;
            });
        });
    },

    async created() {
        this.setAddress([this.rootAssociate.id]);
        this.isLoading = true;
        await this.getAllAssociates([]);
        this.isLoading = false;
    },
};
</script>

<style lang="scss" scoped>
    @import './css/Main.scss';
</style>
