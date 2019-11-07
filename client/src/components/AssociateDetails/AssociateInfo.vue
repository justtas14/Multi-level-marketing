<template>
    <div>
        <div class="userDetails__picture__container">
            <img v-if="associate.filePath" class="userDetails__picture" :src="associate.filePath"/>
            <img v-else class="userDetails__picture" :src="defaultPicture" />
            <div class="user__name">{{ associate.fullName }}</div>
            <a v-if="deleteAssociate" id="deleteBtn" class="btn btnAction">Delete user</a>
            <a v-if="changeParent" id="modalBtn" class="btn btnAction">Change parent</a>
        </div>
        <div class="userDetails__info__container" :class="{ 'parent' : changeParent}">
            <ul class="userDetails__info">
                <li class="userDetails__info__item"><b>Full Name</b>: {{ associate.fullName }}</li>
                <li class="userDetails__info__item"><b>Email</b>: {{ associate.email }}</li>
                <li class="userDetails__info__item">
                    <b>Mobile Phone</b>:
                    <span v-if="associate.mobilePhone">{{ associate.mobilePhone }}</span>
                    <span v-else>-</span>
                </li>
                <li class="userDetails__info__item">
                    <b>Home phone</b>:
                    <span v-if="associate.homePhone">{{ associate.homePhone }}</span>
                    <span v-else>-</span>
                </li>
                <hr class="separation__line">
                <li class="userDetails__info__item">
                    <b>Country</b>:
                    <span v-if="associate.country">{{ associate.country }}</span>
                    <span v-else>-</span>
                </li>
                <li class="userDetails__info__item">
                    <b>City</b>:
                    <span v-if="associate.city">{{ associate.city }}</span>
                    <span v-else>-</span>
                </li>
                <li class="userDetails__info__item">
                    <b>Address</b>:
                    <span v-if="associate.address">{{ associate.address }}</span>
                    <span v-else>-</span>
                </li>
                <li class="userDetails__info__item">
                    <b>Second address</b>:
                    <span v-if="associate.address2">{{ associate.address2 }}</span>
                    <span v-else>-</span>
                </li>
                <hr class="separation__line">
                <li class="userDetails__info__item">
                    <b>Post code</b>:
                    <span v-if="associate.postCode">{{ associate.postCode }}</span>
                    <span v-else>-</span>
                </li>
                <li class="userDetails__info__item">
                    <b>Date of birth</b>: {{ associate.dateOfBirth }}
                </li>
                <li class="userDetails__info__item"><b>Join Date</b>: {{ associate.joinDate }}</li>
                <li class="userDetails__info__item"><b>Level</b>: {{ associate.level }}</li>
                <hr class="separation__line">
                <li class="userDetails__info__item">
                    <b>Is agreed to email updates </b>:
                    <span v-if="associate.agreedToEmailUpdates">Yes</span>
                    <span v-else>No</span>
                </li>
                <li class="userDetails__info__item">
                    <b>Is agreed to text message updates </b>:
                    <span v-if="associate.agreedToTextMessageUpdates">Yes</span>
                    <span v-else>No</span>
                </li>
                <li class="userDetails__info__item">
                    <b>Is agreed to social media updates </b>:
                    <span v-if="associate.agreedToSocialMediaUpdates">Yes</span>
                    <span v-else>No</span>
                </li>
                <li class="userDetails__info__item">
                    <b>Is agreed to terms of services </b>:
                    <span v-if="associate.agreedToTermsOfService">Yes</span>
                    <span v-else>No</span>
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
import defaultPicture from '../../../public/img/profile.jpg';

export default {
    name: 'AssociateInfo',
    props: ['associate', 'changeParent', 'deleteAssociate'],
    components: {

    },
    data() {
        return {
            defaultPicture,
        };
    },
    methods: {

    },
    computed: {

    },
    created() {

    },
};
</script>

<style scoped>

</style>
