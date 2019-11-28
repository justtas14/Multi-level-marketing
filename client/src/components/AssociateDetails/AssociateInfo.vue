<template>
    <div class="userDetails__container">
        <div class="userDetails__picture__container">
            <img v-if="associate.filePath" class="userDetails__picture" :src="getImage()"/>
            <img v-else class="userDetails__picture" :src="defaultPicture" />
            <div class="user__name">{{ associate.fullName }}</div>
            <button
                v-if="!isParent"
                :disabled="isLoadingBtn"
                @click="deleteUser()"
                class="btn btnAction">
                Delete user
            </button>
            <button
                v-else
                @click="changeParent()"
                class="btn btnAction"
                :disabled="isLoadingBtn">
            Change parent
            </button>
            <div class="progress" v-if="isLoadingBtn">
                <div class="indeterminate"></div>
            </div>
        </div>
        <div class="userDetails__info__container" :class="{ 'parent' : isParent}">
            <ul>
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
    <Confirmation
        @hideConfirmation="hideConfirmation"
        v-bind:confirm="confirm"
        v-bind:style="{display: confirm.display}"
        v-bind:yesClickFn="yesClickFn"
    ></Confirmation>
    <Modal
     v-bind:style="{display: modalDisplay}"
     @closeModal="closeModal"
     >
        <template v-slot:header>
             Choose new parent
        </template>
        <template v-slot:body>
            <Main v-bind:mainAction="mainAction" v-bind:mainActionLabel="'Choose'"></Main>
        </template>
    </Modal>

    </div>
</template>

<script>
import {
    mapActions, mapState,
} from 'vuex';
import defaultPicture from '../../../public/img/profile.jpg';
import Parameters from '../../../parameters';
import Confirmation from '../Confirmation/Confirmation.vue';
import Modal from '../Modal/Modal.vue';
import Main from '../UserSearch/Main.vue';

export default {
    name: 'AssociateInfo',
    props: ['associate', 'isParent'],
    components: {
        Confirmation,
        Modal,
        Main,
    },
    data() {
        return {
            defaultPicture,
            confirm: {
                display: 'none',
                message: '',
            },
            modalDisplay: 'none',
            parentConfirm: {
                message: () => {},
            },
            isLoadingBtn: false,
            yesClickFn: async () => {},
            mainAction: {
                confirm: {
                    message: () => {},
                },
            },
        };
    },
    methods: {
        closeModal() {
            this.modalDisplay = 'none';
        },
        hideConfirmation() {
            this.confirm.display = 'none';
        },
        getImage() {
            return `${Parameters.API_HOST_URL}${this.associate.filePath}`;
        },
        changeParent() {
            this.modalDisplay = 'block';
            this.mainAction.confirm.message = associateName => `Are you sure want to change {{ associate.fullName }} parent to ${associateName}?`;
            this.yesClickFn = async () => {

            };
        },
        deleteUser() {
            this.confirm = {
                display: 'block',
                message: `Are you sure you want to delete ${this.associate.fullName}?`,
            };
            this.yesClickFn = async () => {
                this.confirm.display = 'none';
                this.isLoadingBtn = true;
                const formData = new FormData();
                formData.append('deleteAssociateId', this.associate.id);
                formData.append('associateId', this.associateUrlId);
                const res = await this.buttonAction(formData);
                if (res.deleted) {
                    console.log('push', res.formSuccess);
                }
                this.isLoadingBtn = false;
            };
        },

        ...mapActions('AssociateDetails', [
            'buttonAction',
        ]),
    },
    computed: {
        ...mapState('AssociateDetails', [
            'associateUrlId',
        ]),
    },
    created() {

    },
};
</script>

<style lang="scss" scoped>
    @import './css/AssociateInfo.scss'
</style>
