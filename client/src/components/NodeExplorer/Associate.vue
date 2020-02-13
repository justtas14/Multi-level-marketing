<template>
    <div v-bind:style="{ 'padding-right': getPadding() }"
      ref="associate">
        <div id="associateId">{{associate.id}}</div>
        <div v-if="!associate" class="associate empty"></div>
        <div v-else class="associate"
        v-bind:class="{'firstAssociate': (index === 0)}">
            <div id="associatePicture">
                <img class="picture"
                        v-if="associate.filePath" :src="getPicture()"
                        v-bind:class="{ focusProfilePicture:
                        isInMiddle() }">
                    <img class="picture"
                        v-else :src="defaultPicture"
                        v-bind:class="{ focusProfilePicture:
                        isInMiddle() }">
            </div>
            <div id="associateName">
                <div id="fullName" v-bind:class="{ focusAssociate:
                        isInMiddle() }">
                <p>{{ associate.title }}</p>
                </div>
                <div id="actions" v-bind:class="{twoColumns:
                      (!this.isFocused)}">
                    <div id="numberOfChildren">
                        {{ associate.numberOfChildren }}
                    </div>
                    <i class="material-icons actionIcons" @click="moveToMiddle"
                     v-bind:class="{notActiveZoomIn:
                      (!this.isFocused)}">
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

export default {
    name: 'Associate',
    props: ['associate', 'isFocused', 'index', 'associateLenght', 'slotContainer'],
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
            'container',
            'minValueX',
        ]),
    },
    methods: {
        isInMiddle() {
            return this.associate.id.toString() === this.address[this.address.length - 1];
        },
        getPadding() {
            return (this.index === this.associateLenght - 1 && this.index !== 0)
                ? (`${this.container.width / 2}px`) : ('0');
        },
        getPicture() {
            return `${Parameters.API_HOST_URL}${this.associate.filePath}`;
        },
        moveToMiddle() {
            if (!this.address.includes(this.associate.id)) {
                const { associate } = this.$refs;
                const associateRect = associate.getBoundingClientRect();
                const slotContainerRect = this.slotContainer.getBoundingClientRect();
                this.slotContainer.scrollTo(
                    this.index * associateRect.width,
                    slotContainerRect.top,
                );
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
