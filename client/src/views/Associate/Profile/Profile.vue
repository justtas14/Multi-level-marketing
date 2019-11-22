<template>
  <div
    class="admin-contentContainer"
    style="overflow:hidden"
  >
    <div class="card">
      <div class="card-content">
        <span class="card-title">Edit profile</span>
        <form
          name="user_update"
          method="post"
          enctype="multipart/form-data"
        >
          <div class="input-field registration__input--forceBorder">
            <label
              for="user_update_associate_fullName"
              class="required active"
            >Full name</label>
            <input
              type="text"
              id="user_update_associate_fullName"
              name="user_update[associate][fullName]"
              required="required"
              v-model="formData.fullName"
            >
          </div>
          <div class="input-field registration__input--forceBorder">
            <label
              for="user_update_email"
              class="required active"
            >Email</label>
            <input
              type="email"
              id="user_update_email"
              name="user_update[email]"
              required="required"
              v-model="formData.email"
            >
          </div>
          <div class="input-field registration__input--forceBorder">
            <label
              for="user_update_oldPassword"
              class="required"
            >Old password</label>
            <input
              type="password"
              id="user_update_oldPassword"
              name="user_update[oldPassword]"
              required="required"
              v-model="formData.oldPassword"
            >
          </div>
          <div class="input-field registration__input--forceBorder">
            <label for="user_update_newPassword_first">New Password</label>
            <input
              type="password"
              id="user_update_newPassword_first"
              name="user_update[newPassword][first]"
              class="password-field"
              v-model="formData.newPassword"
            >
          </div>
          <div class="input-field registration__input--forceBorder">
            <label for="user_update_newPassword_second">Repeat Password</label>
            <input
              type="password"
              id="user_update_newPassword_second"
              name="user_update[newPassword][second]"
              class="password-field"
              v-model="formData.newPasswordRepeat"
            >
          </div>
          <div class="input-field registration__input--forceBorder">
            <label
              for="user_update_associate_address"
              class="required"
            >Address Line 1</label>
            <input
              type="text"
              id="user_update_associate_address"
              name="user_update[associate][address]"
              required="required"
              v-model="formData.address"
            >
          </div>
          <div class="input-field registration__input--forceBorder">
            <label for="user_update_associate_address2">Address Line 2</label>
            <input
              type="text"
              id="user_update_associate_address2"
              name="user_update[associate][address2]"
              v-model="formData.address2"
            >
          </div>
          <div class="input-field registration__input--forceBorder">
            <label
              for="user_update_associate_city"
              class="required"
            >City</label>
            <input
              type="text"
              id="user_update_associate_city"
              name="user_update[associate][city]"
              required="required"
              v-model="formData.city"
            >
          </div>
          <div class="input-field registration__input--forceBorder">
            <label
              for="user_update_associate_postcode"
              class="required"
            >Postcode</label>
            <input
              type="text"
              id="user_update_associate_postcode"
              name="user_update[associate][postcode]"
              required="required"
              v-model="formData.postcode"
            >
          </div>
          <div class="input-field registration__input--forceSelectDisplay
                registration__input--forceBorder">
            <label
              for="user_update_associate_country"
              class="required"
            >Country</label>
            <Countries v-bind:id="'user_update_associate_country'"
             v-bind:name="'user_update[associate][country]'"
             v-bind:value="formData.country"
             v-model="formData.country"
             >
             </Countries>
          </div>
          <div class="input-field registration__input--forceBorder">
            <label class="required">Mobile phone</label>
            <div id="user_update_associate_mobilePhone">
                <Telephones v-bind:name="'user_update[associate][mobilePhone][country]'"
                v-bind:id="'user_update_associate_mobilePhone_country'"
                v-bind:value="formData.mobilePhoneCountry"
                v-model="formData.mobilePhoneCountry"
                ></Telephones>
                <input
                type="text"
                id="user_update_associate_mobilePhone_number"
                name="user_update[associate][mobilePhone][number]"
                required="required"
                v-model="formData.mobilePhone"
              ></div>
            <Error v-if="this.formErrors && this.formErrors.phoneError"
              v-bind:message="this.formErrors.phoneError"></Error>
          </div>
          <div class="input-field registration__input--forceBorder">
            <label for="user_update_associate_homePhone">Home phone</label>
            <input
              type="text"
              id="user_update_associate_homePhone"
              name="user_update[associate][homePhone]"
              v-model="formData.homePhone"
            >
          </div>
          <div class="input-field">
            <label for="user_update_associate_profilePicture">Profile picture</label>
            <div class="vich-file"><input
                type="file"
                id="user_update_associate_profilePicture"
                name="user_update[associate][profilePicture]"
                @change="readUrl"
              ></div>
            <!-- <div id="profilePicturePreview">
              <img src="">
            </div>

            <div
              id="dragAndDropError"
              class="error__block"
              style="margin-top: 0.25em"
            >
            </div>
            <div id="submitUploadErrorBlock">
            </div> -->
          </div>
          <div class="input-field">
            <label style="position: unset!important;">
              <input
                type="checkbox"
                id="user_update_associate_agreedToEmailUpdates"
                name="user_update[associate][agreedToEmailUpdates]"
                class="filled-in"
                v-model="formData.agreedToEmailUpdates"
              >
              <span>I agree to receive email updates</span>
            </label>
          </div>
          <div class="input-field">
            <label style="position: unset!important;">
              <input
                type="checkbox"
                id="user_update_associate_agreedToTextMessageUpdates"
                name="user_update[associate][agreedToTextMessageUpdates]"
                class="filled-in"
                v-model="formData.agreedToTextMessageUpdates"
              >
              <span>I agree to receive text message updates</span>
            </label>
          </div>
          <div class="input-field">
            <label
              style="position: unset!important;"
              class="required"
            >
              <input
                type="checkbox"
                id="user_update_associate_agreedToTermsOfService"
                name="user_update[associate][agreedToTermsOfService]"
                required="required"
                class="filled-in"
                v-model="formData.agreedToTermsOfService"
              >
              <span>I agree to the terms of service</span>
            </label>
          </div>
          <div class="profile-buttonWrap">
            <div>
                <button
                  type="button"
                  id="user_update_associate_submit"
                  name="user_update[associate][submit]"
                  class="waves-effect waves-light btn"
                  style="background-color:#3ab54a"
                  @click="updateProfile()"
                >Save </button>
              </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import {
    mapActions, mapState,
} from 'vuex';
import Countries from '../../../components/FormFields/Countries.vue';
import Telephones from '../../../components/FormFields/Telephones.vue';
import Error from '../../../components/Messages/Error.vue';

//  :disabled="isLoadingForm || isEmptyRequiredFields()"

export default {
    name: 'Profile',
    components: {
        Countries,
        Telephones,
        Error,
    },
    props: [],
    data() {
        return {
            formData: {
                fullName: '',
                email: '',
                oldPassword: '',
                newPassword: '',
                newPasswordRepeat: '',
                address: '',
                address2: '',
                city: '',
                postcode: '',
                country: '',
                mobilePhoneCountry: '',
                mobilePhone: '',
                homePhone: '',
                profilePicture: null,
                agreedToEmailUpdates: false,
                agreedToTextMessageUpdates: false,
                agreedToTermsOfService: false,
            },
        };
    },
    methods: {
        async updateProfile() {
            const profileFormData = new FormData();
            profileFormData.set('fullName', this.formData.fullName);
            profileFormData.set('email', this.formData.email);
            profileFormData.set('oldPassword', this.formData.oldPassword);
            profileFormData.set('newPassword', this.formData.newPassword);
            profileFormData.set('newPasswordRepeat', this.formData.newPasswordRepeat);
            profileFormData.set('address', this.formData.address);
            profileFormData.set('address2', this.formData.address2);
            profileFormData.set('city', this.formData.city);
            profileFormData.set('postcode', this.formData.postcode);
            profileFormData.set('country', this.formData.country);
            profileFormData.set('mobilePhoneCountry', this.formData.mobilePhoneCountry);
            profileFormData.set('mobilePhone', this.formData.mobilePhone);
            profileFormData.set('homePhone', this.formData.homePhone);
            profileFormData.set('agreedToTextMessageUpdates', this.formData.agreedToTextMessageUpdates);
            profileFormData.set('agreedToSocialMediaUpdates', this.formData.agreedToSocialMediaUpdates);
            profileFormData.set('agreedToTermsOfService', this.formData.agreedToTermsOfService);
            profileFormData.append('profilePicture', this.formData.profilePicture);

          

            const form = document.querySelector('[name=user_update]');
            const formData = new FormData(form);
            console.log(formData);
        },
        readUrl(e) {
            const files = e.target.files || e.dataTransfer.files;
            const [file] = files;
            this.formData.profilePicture = file;
        },
        isEmptyRequiredFields() {
            return this.formData.email === '' || this.formData.fullName === '' || this.formData.oldPassword === ''
             || this.formData.address === '' || this.formData.city === '' || this.formData.postcode === ''
             || this.formData.mobilePhoneCountry === '' || this.formData.mobilePhone === '' || this.formData.agreedToTermsOfService === false;
        },
        ...mapActions('Profile', [
            'home',
            'submitForm',
        ]),
    },
    mounted() {
        this.formData = { ...this.associate, id: undefined, roles: undefined };
    },
    computed: {
        ...mapState('Profile', [
            'isLoadingForm',
            'formErrors',
        ]),
        ...mapState('Security', [
            'associate',
        ]),
    },
    async created() {
        console.log(this.associate);
        await this.home();
    },
};
</script>

<style lang="scss" scoped>
@import "./css/Profile.scss";
</style>
