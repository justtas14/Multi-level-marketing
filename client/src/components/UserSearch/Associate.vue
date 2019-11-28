<template>
    <tr class="associate-container">
        <td class="associate-profilePictureContainer">
            <div class="userDetails__picture__container mobileUserPictureContainer">
                <img class="userDetails__picture mobileUserPicture"
                     v-if="associate.filePath" :src="getPicture()" />
                <img class="userDetails__picture mobileUserPicture"
                     v-else :src="defaultPicture" />
            </div>
        </td>
        <td class="associate-itemContainer user__search__fullName">
            <div class="userDetails__picture__container">
                <img class="userDetails__picture"
                     v-if="associate.filePath" :src="getPicture()" />
                <img class="userDetails__picture"
                     v-else :src="defaultPicture" />
            </div>
            <div class="fullName">
                <span class="valueTitle">Full name:</span>
                {{ associate.fullName }}
            </div>
        </td>
        <td class="associate-itemContainer email">
            <span class="valueTitle">Email:</span>
            {{ associate.email }}
        </td>
        <td class="associate-phoneContainer"
         v-bind:style="{'text-align' : associate.mobilePhone ? 'left' : 'center' }">
            <span class="valueTitle">Mobile phone:</span>
            {{ associate.mobilePhone || '-'}}
        </td>
        <td class="associate-itemLevelContainer">
            <span class="valueTitle">Level:</span>
            {{ associate.level }}
        </td>
        <td class="associate-itemDateContainer">
            <span class="valueTitle">Join date:</span>
            {{ dateString }}
        </td>
        <td class="associate-itemActionContainer">
            <a @click="onClick" class="btn">{{ mainActionLabel }}</a>
        </td>
        <Confirmation
                @hideConfirmation="hideConfirmation"
                      v-bind:confirm="confirm"
                      v-bind:style="{display: confirm.display}"
                      v-bind:yesClickFn="yesClickFn"
                v-bind:addPicture="changeParentSVG"
        />
    </tr>
</template>

<script>
import defaultPicture from '../../../public/img/profile.jpg';
import Confirmation from '../Confirmation/Confirmation.vue';
import changeParentSVG from '../../../public/img/geneology.svg';
import Parameters from '../../../parameters';

export default {
    name: 'Associate',
    props: ['associate', 'mainActionLabel', 'mainAction'],
    components: {
        Confirmation,
    },
    data() {
        return {
            dateString: '',
            defaultPicture,
            changeParentSVG,
            yesClickFn() {},
            confirm: {
                display: 'none',
                message: '',
            },
        };
    },
    methods: {
        getPicture() {
            return `${Parameters.API_HOST_URL}${this.associate.filePath}`;
        },
        showConfirmation(message) {
            this.confirm.message = message;
            this.confirm.display = 'block';
            this.yesClickFn = () => {
                this.mainAction.fun(this.associate.id);
                this.confirm.display = 'none';
            };
        },
        hideConfirmation() {
            this.confirm.display = 'none';
        },
        onClick() {
            if (this.mainAction.confirm) {
                this.showConfirmation(this.mainAction.confirm.message(this.associate.fullName));
            } else {
                this.mainAction.fun(this.associate.id);
            }
        },
    },
    created() {
        const d = new Date(this.associate.joinDate);
        this.dateString = `${(`0${d.getDate()}`).slice(-2)}-${(`0${d.getMonth() + 1}`).slice(-2)}-${d.getFullYear()}`;
    },

};
</script>

<style lang="scss" scoped>
    @import './css/Associate.scss';
</style>
