<template>
    <div class="userDetails__mainContainer">
        <div :class=
        "[isParent ? 'userDetails__container__parentInfo' : 'userDetails__container__mainInfo']"
        >
            <div v-if="isParent"
            class="userDetails_parentTitleContainer addBottom userDetailsTitle"
            >
                <span class="card-title">Sponsor details</span>
                <a @click="goToUserDetails" class="btn">Details</a>
            </div>
            <span v-else class="card-title addBottom userDetailsTitle">Associate details</span>
            <hr class="separation__line">
            <div class="userDetails__container">
                <AssociateInfo
                    v-bind:associate="associate"
                    v-bind:changeParent="changeParent"
                    v-bind:deleteAssociate="deleteAssociate"
                />
            </div>
        </div>
    </div>
</template>

<script>
import './css/Main.scss';
import AssociateInfo from './AssociateInfo.vue';

export default {
    name: 'Main',
    props: ['associate', 'deleteAssociate', 'changeParent', 'isParent'],
    components: {
        AssociateInfo,
    },
    data() {

    },
    methods: {
        goToUserDetails() {

        },
    },
    computed: {

    },
    created() {

    },
};
</script>

<style scoped>

</style>
