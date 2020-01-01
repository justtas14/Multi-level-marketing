<template>
  <div
    class="admin-contentContainer"
    style="overflow:hidden"
  >
    <div class="card" v-if="checkEndPrelaunch">
      <div class="card-content">
          <div class="landingContent" v-html="this.getLandingContent">
          </div>
      </div>
    </div>
    <div class="card">
      <div class="card-content">
        <span class="card-title">Edit profile</span>
        <Success v-if="this.formUpdated" v-bind:message="'Profile Updated!'"></Success>
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
              v-model="formData.associate.fullName"
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
            <Error v-if="this.formErrors && this.formErrors.invalidEmail"
            v-bind:message="this.formErrors.invalidEmail"></Error>
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
            <Error v-if="this.formErrors && this.formErrors.invalidPassword"
              v-bind:message="this.formErrors.invalidPassword"></Error>
          </div>
          <div class="input-field registration__input--forceBorder">
            <label for="user_update_newPassword_first">New Password</label>
            <input
              type="password"
              id="user_update_newPassword_first"
              name="user_update[newPassword][first]"
              class="password-field"
              v-model="formData.newPassword.first"
            >
            <Error v-if="this.formErrors && this.formErrors.first"
              v-bind:message="this.formErrors.first"></Error>
          </div>
          <div class="input-field registration__input--forceBorder">
            <label for="user_update_newPassword_second">Repeat Password</label>
            <input
              type="password"
              id="user_update_newPassword_second"
              name="user_update[newPassword][second]"
              class="password-field"
              v-model="formData.newPassword.second"
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
              v-model="formData.associate.address"
            >
          </div>
          <div class="input-field registration__input--forceBorder">
            <label for="user_update_associate_address2">Address Line 2</label>
            <input
              type="text"
              id="user_update_associate_address2"
              name="user_update[associate][address2]"
              v-model="formData.associate.address2"
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
              v-model="formData.associate.city"
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
              v-model="formData.associate.postcode"
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
             v-bind:value="formData.associate.country"
             v-model="formData.associate.country"
             >
             </Countries>
          </div>
          <div class="input-field registration__input--forceBorder">
            <label class="required">Mobile phone</label>
            <content-loader v-if="isLoading" :height='28' >
                        <rect :width='400' :height='28' />
            </content-loader>
            <div v-else id="user_update_associate_mobilePhone">
                <Telephones v-bind:name="'user_update[associate][mobilePhone][country]'"
                v-bind:id="'user_update_associate_mobilePhone_country'"
                v-bind:value="formData.associate.mobilePhone.country"
                v-model="formData.associate.mobilePhone.country"
                ></Telephones>
                <input
                type="text"
                id="user_update_associate_mobilePhone_number"
                name="user_update[associate][mobilePhone][number]"
                required="required"
                v-model="formData.associate.mobilePhone.number"
              ></div>
            <Error v-if="this.formErrors && this.formErrors.phoneError"
              v-bind:message="this.formErrors.phoneError"></Error>
            <Error v-if="this.formErrors && this.formErrors.mobilePhone"
            v-bind:message="this.formErrors.mobilePhone"></Error>
          </div>
          <div class="input-field registration__input--forceBorder">
            <label for="user_update_associate_homePhone">Home phone</label>
            <input
              type="text"
              id="user_update_associate_homePhone"
              name="user_update[associate][homePhone]"
              v-model="formData.associate.homePhone"
            >
          </div>
          <div class="input-field">
            <label for="user_update_associate_profilePicture">Profile picture</label>
            <div class="vich-file"><input
                type="file"
                id="user_update_associate_profilePicture"
                name="user_update[associate][profilePicture]"
                @change="readUrl"
                ref="originalProfilePictureUpload"
              ></div>
              <DragAndDrop
               v-bind:originalProfilePictureUpload="this.$refs.originalProfilePictureUpload"
               >
               </DragAndDrop>
            <Error v-if="this.formErrors && this.formErrors.profilePictureError"
              v-bind:message="this.formErrors.profilePictureError"></Error>
          </div>
          <div class="input-field">
            <label style="position: unset!important;">
              <input
                type="checkbox"
                id="user_update_associate_agreedToEmailUpdates"
                name="user_update[associate][agreedToEmailUpdates]"
                class="filled-in"
                v-model="formData.associate.agreedToEmailUpdates"
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
                v-model="formData.associate.agreedToTextMessageUpdates"
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
                v-model="formData.associate.agreedToTermsOfService"
              >
              <span>I agree to the terms of service</span>
            </label>
            <Error v-if="this.formErrors && this.formErrors.agreedToTermsOfService"
              v-bind:message="this.formErrors.agreedToTermsOfService"></Error>
          </div>
          <div class="profile-buttonWrap">
            <div id="userUpdateButtonWrapper">
                <button
                  type="button"
                  id="user_update_associate_submit"
                  name="user_update[associate][submit]"
                  class="waves-effect waves-light btn"
                  style="background-color:#3ab54a"
                  :disabled="isLoadingForm || isEmptyRequiredFields()"
                  @click="updateProfile()"
                >Save </button>
                <div class="progress" v-if="isLoadingForm">
                    <div class="indeterminate"></div>
                </div>
              </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import {
    mapActions, mapState, mapMutations, mapGetters,
} from 'vuex';
import { ContentLoader } from 'vue-content-loader';
import Countries from '../../../components/FormFields/Countries.vue';
import Telephones from '../../../components/FormFields/Telephones.vue';
import Error from '../../../components/Messages/Error.vue';
import Success from '../../../components/Messages/Success.vue';
import BuildFormData from '../../../services/BuildFormData';
import DragAndDrop from '../../../components/DragNDrop/DragNDrop.vue';

export default {
    name: 'Profile',
    components: {
        Countries,
        Telephones,
        Error,
        DragAndDrop,
        Success,
        ContentLoader,
    },
    props: [],
    data() {
        return {
            isLoading: false,
            isLoadingForm: false,
            formData: {
                email: '',
                oldPassword: '',
                newPassword: {
                    first: '',
                    second: '',
                },
                associate: {
                    fullName: '',
                    address: '',
                    address2: '',
                    city: '',
                    postcode: '',
                    country: '',
                    mobilePhone: {
                        country: '',
                        number: '',
                    },
                    homePhone: '',
                    agreedToEmailUpdates: false,
                    agreedToTextMessageUpdates: false,
                    agreedToTermsOfService: false,
                },
            },
        };
    },
    methods: {
        async updateProfile() {
            const buildFormDataObj = new BuildFormData();
            this.formData.associate.profilePicture = this.profilePicture;
            const formData = buildFormDataObj.jsonToFormData(this.formData);
            this.isLoadingForm = true;
            const res = await this.submitForm(formData);
            if (res) {
                this.setAssociate(res);
            }
            this.isLoadingForm = false;

            window.scrollTo(0, 0);
        },
        readUrl(e) {
            const files = e.target.files || e.dataTransfer.files;
            this.handeFilesFunc(files);
        },
        isEmptyRequiredFields() {
            return this.formData.email === '' || this.formData.associate.fullName === '' || this.formData.oldPassword === ''
             || this.formData.associate.address === '' || this.formData.associate.city === '' || this.formData.associate.postcode === ''
             || this.formData.associate.mobilePhone.country === '' || this.formData.associate.mobilePhone.number === ''
             || this.formData.associate.agreedToTermsOfService === false;
        },
        ...mapActions('Profile', [
            'home',
            'submitForm',
        ]),
        ...mapMutations('Security', [
            'setAssociate',
        ]),
        ...mapMutations('Profile', [
            'profileUpdate',
            'setProfilePicture',
        ]),
    },
    mounted() {
        this.formData.email = this.associate.email;
        this.formData.associate.fullName = this.associate.fullName;
        this.formData.associate.address = this.associate.address;
        this.formData.associate.address2 = this.associate.address2;
        this.formData.associate.city = this.associate.city;
        this.formData.associate.postcode = this.associate.postcode;
        this.formData.associate.country = this.associate.country;
        this.formData.associate.homePhone = this.associate.homePhone;
        this.formData.associate.agreedToEmailUpdates = this.associate.agreedToEmailUpdates;
        this.formData.associate.agreedToTextMessageUpdates = this.associate
            .agreedToTextMessageUpdates;
        this.formData.associate.agreedToTermsOfService = this.associate.agreedToTermsOfService;
    },
    computed: {
        ...mapState('Profile', [
            'formErrors',
            'formUpdated',
            'handeFilesFunc',
            'profilePicture',
        ]),
        ...mapState('Security', [
            'associate',
        ]),
        ...mapState('Sidebar', [
            'configuration',
        ]),
        ...mapGetters('Sidebar', [
            'checkEndPrelaunch',
            'getLandingContent',
        ]),
    },
    async created() {
        this.profileUpdate(false);
        this.isLoading = true;
        const res = await this.home();
        this.formData.associate.mobilePhone = res.mobilePhone;
        this.isLoading = false;
    },
};
</script>

<style lang="scss" scoped>
@import "./css/Profile.scss";
</style>
