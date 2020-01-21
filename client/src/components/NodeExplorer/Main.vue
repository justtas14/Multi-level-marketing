<template>
    <div class="card">
        <div class="card-content">
            <div id="slots__container" ref="container">
                <content-loader v-if="isLoading" :speed='4' :height='300'>
                        <rect rx="5" ry="5" :width='400' :height='260' />
                </content-loader>
            <div v-else>
                <SlotComponent
                @slot="assignSlot"
                v-bind:associates="firstSlotAssociates"
                v-bind:slotNumber="'first'"
                 ></SlotComponent>
                <SlotComponent
                @slot="assignSlot"
                v-bind:associates="secondSlotAssociates"
                v-bind:slotNumber="'second'"
                 ></SlotComponent>
                <SlotComponent
                @slot="assignSlot"
                v-bind:associates="thirdSlotAssociates"
                v-bind:slotNumber="'third'"
                ></SlotComponent>
            </div>
                <div class="arrow" @click="goUp"
                 v-bind:style="{display: (this.address.length > 2) ? 'block' : 'none'}">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import {
    mapActions, mapState, mapGetters, mapMutations,
} from 'vuex';
import { ContentLoader } from 'vue-content-loader';
import getChildrenFromAddress, { getChildren } from './services/nodeExplorerMethods';

import SlotComponent from './Slot.vue';

export default {
    name: 'Main',
    props: [],
    data() {
        return {
            isLoading: false,
            container: null,
            slots: [],
        };
    },
    components: {
        SlotComponent,
        ContentLoader,
    },
    computed: {
        ...mapState('NodeExplorer', [
            'focusFirstSlotId',
            'focusSecondSlotId',
            'address',
            'rootAssociate',
        ]),
        ...mapGetters('Security', [
            'getAssociate',
        ]),
        firstSlotAssociates() {
            if (this.address.length <= 2) {
                return [this.rootAssociate];
            }
            const addressToAncestor = this.address.slice(0, this.address.length - 2);
            const node = getChildrenFromAddress(addressToAncestor, this.rootAssociate);

            return getChildren(node);
        },
        secondSlotAssociates() {
            if (this.address.length <= 1) {
                return [];
            }
            const addressToAncestor = this.address.slice(0, this.address.length - 1);
            const node = getChildrenFromAddress(addressToAncestor, this.rootAssociate);

            return getChildren(node);
        },
        thirdSlotAssociates() {
            if (this.address.length <= 1) {
                return [];
            }
            const addressToAncestor = this.address.slice(0, this.address.length);
            const node = getChildrenFromAddress(addressToAncestor, this.rootAssociate);

            return getChildren(node);
        },
    },
    methods: {
        assignSlot(slot) {
            slot.style.padding = `0 0 0 ${this.container.offsetWidth / 2 - 50}px`;
            this.slots.push(slot);
        },
        goUp() {
            this.popAddress();
        },
        ...mapActions('NodeExplorer', [
            'getAssociates',
        ]),
        ...mapMutations('NodeExplorer', [
            'setAddress',
            'popAddress',
            'setContainerWidth',
            'setContainerX',
        ]),
    },
    mounted() {
        this.container = this.$refs.container;
        this.setContainerWidth(this.container.offsetWidth);
        let rect = this.container.getBoundingClientRect();
        this.setContainerX(Math.abs(rect.right - rect.left) / 2);
        window.addEventListener('resize', () => {
            rect = this.container.getBoundingClientRect();
            this.setContainerX((rect.right - rect.left) / 2);
            this.setContainerWidth(this.container.offsetWidth);
            this.slots.forEach((slot) => {
                slot.style.padding = `0 0 0 ${this.container.offsetWidth / 2 - 50}px`;
            });
        });
        if (Object.keys(this.rootAssociate.children).length > 0) {
            this.setAddress([this.rootAssociate.id, Object.keys(this.rootAssociate.children)[0]]);
        } else {
            this.setAddress([this.rootAssociate.id]);
        }
    },
    async created() {
        this.isLoading = true;
        await this.getAssociates([]);
        this.isLoading = false;
    },
};
</script>

<style lang="scss" scoped>
    @import './css/Main.scss';
</style>
