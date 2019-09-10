import Vue from 'vue';
import Confirmation from "./Components/Confirmation";

let created = false;
let confirmation;

export const confirmationFun = (el, confirm, yesClickFn) => {
    if (!created) {
        confirmation = new Vue({
            el: el,
            data: {
                confirm: confirm,
                yesClickFn: yesClickFn
            },
            methods: {
                hideConfirmation() {
                    confirm.display = 'none';
                },
                showConfirmation() {
                    confirm.display = 'block';
                }
            },
            template: '<Confirmation v-bind:style="{display: confirm.display}" ' +
                'v-bind:confirm="confirm"' +
                ' v-bind:yesClickFn="yesClickFn"' +
                ' @hideConfirmation="hideConfirmation"/>',
            components: {
                Confirmation
            },
            created() {
                created = true;
            }
        });
    } else {
        confirmation.showConfirmation();
    }
};


