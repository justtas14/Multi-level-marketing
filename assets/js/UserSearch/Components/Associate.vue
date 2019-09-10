<template>
    <tr class="associate-container">
        <td class="associate-itemContainer user__search__fullName">
            <div class="userDetails__picture__container">
                <img class="userDetails__picture"
                     v-if="associate.filePath" :src="associate.filePath" />
                <img class="userDetails__picture"
                     v-else :src="defaultPicture" />
            </div>
            <div class="fullName">
                {{ associate.fullName }}
            </div>
        </td>
        <td class="associate-itemContainer">
            {{ associate.email }}
        </td>
        <td class="associate-phoneContainer" v-bind:style="{'text-align' : associate.mobilePhone ? 'left' : 'center' }">
            {{ associate.mobilePhone || '-'}}
        </td>
        <td class="associate-itemLevelContainer">
            {{ associate.level }}
        </td>
        <td class="associate-itemDateContainer">
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
    import defaultPicture from '../../../../public/assets/images/profile.jpg'
    import Confirmation from "../../Confirmation/Components/Confirmation";
    import changeParentSVG from '../../../images/geneology.svg'

    export default {
        name: "Associate",
        props: ['associate', 'mainActionLabel', 'mainAction'],
        components: {
            Confirmation
        },
        data() {
            return {
                dateString: '',
                defaultPicture: defaultPicture,
                changeParentSVG: changeParentSVG,
                yesClickFn: function () {},
                confirm: {
                    display: 'none',
                    message: '',
                },
            }
        },
        methods: {
            showConfirmation(message) {
                this.confirm.message = message;
                this.confirm.display = 'block';
                this.yesClickFn = () => {
                    this.mainAction.fun(this.associate.id);
                    this.confirm.display = 'none';
                }
            },
            hideConfirmation: function () {
                this.confirm.display = 'none';
            },
            onClick: function (e) {
                if (this.mainAction.confirm) {
                    this.showConfirmation(this.mainAction.confirm.message(this.associate.fullName));
                } else {
                    this.mainAction.fun(this.associate.id);
                }
            }
        },
        created() {
            const d = new Date(this.associate.joinDate);
            this.dateString = ("0" + d.getDate()).slice(-2) + "-" + ("0"+(d.getMonth()+1)).slice(-2) + "-" + d.getFullYear();
        }

    }
</script>

<style src="../css/Associate.css" scoped></style>

