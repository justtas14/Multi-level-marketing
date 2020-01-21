<template>
    <div v-bind:style="{ 'padding-right': getPadding() }">
        <div v-if="!associate" class="associate empty"></div>
        <div v-else class="associate"
        v-bind:class="{'firstAssociate': (index === 0)}"
        @click="focus">
            <div id="associatePicture">
                <img class="picture"
                        v-if="associate.filePath" :src="getPicture()"
                        v-bind:class="{ focusProfilePicture:
                        (this.address.includes(associate.id.toString())) }">
                    <img class="picture"
                        v-else :src="defaultPicture"
                        v-bind:class="{ focusProfilePicture:
                        (this.address.includes(associate.id.toString())) }">
            </div>
            <div id="associateName">
                <div id="fullName"
                v-bind:class="{ focusAssociate: (this.address.includes(associate.id.toString())) }">
                <p>{{ associate.title }}</p>
                </div>
                <div id="actions">
                    <div id="numberOfChildren">
                        {{ associate.numberOfChildren }}
                    </div>
                    <i class="material-icons actionIcons" @click="moveToMiddle"
                     v-bind:class="{notActiveZoomIn:
                      (this.associate.id === this.rootAssociate.id)}">
                        zoom_in
                    </i>
                    <i class="material-icons actionIcons">
                        open_in_new
                    </i>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import {
    mapState, mapMutations, mapActions,
} from 'vuex';
import defaultPicture from '../../../public/img/profile.jpg';
import Parameters from '../../../parameters';
import getChildrenFromAddress, { getChildren } from './services/nodeExplorerMethods';

export default {
    name: 'Associate',
    props: ['associate', 'slotNumber', 'index', 'associateLenght'],
    data() {
        return {
            defaultPicture,
        };
    },
    components: {
    },
    computed: {
        ...mapState('NodeExplorer', [
            'address',
            'rootAssociate',
            'containerWidth',
        ]),
    },
    methods: {
        getPadding() {
            return (this.index === this.associateLenght - 1 && this.index !== 0)
                ? (`${this.containerWidth / 2}px`) : ('0');
        },
        getPicture() {
            return `${Parameters.API_HOST_URL}${this.associate.filePath}`;
        },
        moveToMiddle() {
            if (this.slotNumber === 'first') {
                if (this.address.length > 2) {
                    this.popAddress();
                    this.popAddress();
                    this.pushAddress(this.associate.id.toString());
                    console.log(this.address);
                }
            }
        },
        focus() {
            if (!this.address.includes(this.associate.id.toString())) {
                if (this.slotNumber === 'first') {
                    this.popAddress();
                    this.popAddress();
                    this.pushAddress(this.associate.id.toString());
                    const addressToAncestor = this.address.slice(0, this.address.length);
                    const node = getChildrenFromAddress(addressToAncestor, this.rootAssociate);
                    if (getChildren(node).length > 0) {
                        this.pushAddress(getChildren(node)[0].id.toString());
                    }
                }
                if (this.slotNumber === 'second') {
                    if (this.address.length === 2) {
                        this.setAddress([this.address[0], this.associate.id.toString()]);
                    } else {
                        this.popAddress();
                        this.pushAddress(this.associate.id.toString());
                    }
                }
                if (this.slotNumber === 'third') {
                    this.pushAddress(this.associate.id.toString());
                }
                console.log(this.address);
            }
        },

        ...mapActions('NodeExplorer', [
        ]),

        ...mapMutations('NodeExplorer', [
            'pushAddress',
            'setAddress',
            'popAddress',
        ]),
    },
    created() {
    },
};
</script>

<style lang="scss" scoped>
    @import './css/Associate.scss';
</style>
