<template>
        <div :class=
        "[isParent ? 'userDetails__container__parentInfo' : 'userDetails__container__mainInfo']"
        >
            <div v-if="isParent"
            class="userDetails_parentTitleContainer addBottom userDetailsTitle"
            >
                <span class="card-title">Sponsor details</span>
                <button @click="goToUserDetails" class="btn">Details</button>
            </div>
            <span v-else class="card-title addBottom userDetailsTitle">Associate details</span>
            <hr class="separation__line">
            <AssociateInfo
                v-bind:associate="associate"
                v-bind:isParent="isParent"
            />
        </div>
</template>

<script>
import AssociateInfo from './AssociateInfo.vue';

export default {
    name: 'Main',
    props: ['associate', 'isParent'],
    components: {
        AssociateInfo,
    },
    data() {
        return {

        };
    },
    methods: {
        goToUserDetails() {
            this.$router.push({ path: `/admin/user/${this.associate.id}` });
        },
    },
    computed: {

    },
    created() {

    },
};
</script>

<style lang="scss" scoped>
    @import './css/Main.scss';
</style>
