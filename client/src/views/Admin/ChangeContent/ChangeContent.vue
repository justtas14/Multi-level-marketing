<template>
  <div class="admin-contentContainer">
    <div class="card">
      <div class="card-content">
        <span class="card-title">Change Content</span>
        <Success v-if="formSuccess" v-bind:message="'Saved!'"></Success>
        <form
          name="change_content"
          method="post"
          enctype="multipart/form-data"
        >
          <main id="main">
            <div
              class="fileContainer"
              style="margin-bottom: 2em;"
            >
              <span class="fileTitle">Main logo</span>
              <div
                id="mainLogoFromGallery"
                class="input-field"
              >
                <div id="mainLogoImagePreview"><img
                v-if="this.output.imagefileSrc"
                :src="this.output.imagefileSrc"></div>
                <a
                  id="mainLogoFilesPreview"
                  class="waves-effect waves-light btn"
                  @click="showModalGallery('images')"
                >Choose file</a>
                <span
                  id="mainLogoFileName"
                  class="addMargin"
                >{{ this.output.imageFileName }}</span>
              </div>
            </div>
            <div
              class="fileContainer"
              style="margin-bottom: 2em;"
            >
              <span class="fileTitle">Terms Of Services</span>
              <div
                id="termsOfServicesFromGallery"
                class="input-field"
              >
              <div id="termsOfServicesImagePreview"><img
                v-if="this.output.termsOfServicefileSrc"
                :src="this.output.termsOfServicefileSrc"></div>
                <a
                  id="termsOfServicesFilesPreview"
                  class="waves-effect waves-light btn"
                  @click="showModalGallery('files')"
                >Choose file</a>
                <span
                  id="termsOfServicesFileName"
                  class="addMargin"
                >{{ this.output.termsOfServiceFileName }}</span>
              </div>
            </div>
            <div class="input-field">
              <label for="change_content_tosDisclaimer">Terms of Services disclaimer content</label>
              <content-loader v-if="isLoading" :height='75' >
                        <rect :width='400' :height='75' />
                </content-loader>
              <textarea
                v-else
                id="change_content_tosDisclaimer"
                name="change_content[tosDisclaimer]"
                v-model="formData.tosDisclaimer"
              ></textarea>
            </div>
            <div class="input-field submitButtonWraper">
              <button
                @click="submitForm"
                :disabled="isLoading || isLoadingForm"
                type="button"
                id="change_content_Submit"
                name="change_content[Submit]"
                class="waves-effect waves-light btn"
              >Update</button>
              <div v-if="isLoadingForm" class="progress">
                  <div class="indeterminate"></div>
              </div>
            </div>
          </main>
          <a v-if="checkConfigurationTermsOfService" style="cursor: pointer"
          @click="downloadTermsOfServices">Download Terms of service</a>
        </form>
      </div>
      <ModalGalleryWrapper v-bind:style="{display: modalDisplay}"
         @closeModal="modalDisplay='none'"></ModalGalleryWrapper>
    </div>
  </div>
</template>

<script>
import {
    mapActions, mapState, mapMutations, mapGetters,
} from 'vuex';
import { ContentLoader } from 'vue-content-loader';
import Success from '../../../components/Messages/Success.vue';
import ModalGalleryWrapper from '../../../components/Gallery/ModalGalleryWrapper.vue';
import modalGalleryConst from '../../../components/Gallery/constants/modalGalleryConst';
import Parameters from '../../../../parameters';

export default {
    name: 'ChangeContent',
    components: {
        Success,
        ContentLoader,
        ModalGalleryWrapper,
    },
    props: [],
    data() {
        return {
            modalDisplay: 'none',
            isLoading: false,
            isLoadingForm: false,
        };
    },
    methods: {
        async submitForm() {
            this.isLoadingForm = true;
            const formDataObj = new FormData();
            formDataObj.append('hiddenMainLogoFile', this.formData.hiddenMainLogoFile);
            formDataObj.append('hiddenTermsOfServiceFile', this.formData.hiddenTermsOfServiceFile);
            formDataObj.append('tosDisclaimer', this.formData.tosDisclaimer);
            await this.home(formDataObj);
            this.isLoadingForm = false;
            this.setFormSuccess(true);
            await this.configurationApi();
        },

        async showModalGallery(type) {
            this.setSpinnerState(true);
            this.modalDisplay = 'block';
            this.changeFilesPerPage(21);
            this.changeCategory(type);
            for (let i = 0; i < modalGalleryConst.mqls.length; i += 1) {
                modalGalleryConst.mqls[i].addListener(this.mediaQuerryResponse);
            }

            const w = window.innerWidth;

            if (w > 1350) {
                await this.callDataAxios(21);
            } else if (w > 550 && w <= 1350) {
                await this.callDataAxios(15);
            } else {
                await this.callDataAxios(14);
            }
            this.setSpinnerState(false);
        },
        mediaQuerryResponse() {
            if (modalGalleryConst.mqls[0].matches) {
                this.callDataAxios(14);
            } else if (modalGalleryConst.mqls[1].matches) {
                this.callDataAxios(15);
            } else {
                this.callDataAxios(21);
            }
        },

        downloadTermsOfServices() {
            window.location.href = `${Parameters.API_HOST_URL}${this.configuration.termsOfServices.filePath}`;
        },
        ...mapActions('Gallery', [
            'callDataAxios',
        ]),

        ...mapActions('ChangeContent', [
            'home',
        ]),

        ...mapActions('Sidebar', [
            'configurationApi',
        ]),

        ...mapMutations('ChangeContent', [
            'changeImage',
            'changeTermsOfService',
            'setFormSuccess',
        ]),
        ...mapMutations('Gallery', [
            'changeCategory',
            'changeFilesPerPage',
            'setSpinnerState',
            'changeClickFile',
        ]),
    },
    mounted() {
        this.changeClickFile((fileId, fileName, fileSrc, filePath) => {
            this.modalDisplay = 'none';
            const fileObj = {
                fileId,
                fileName,
                fileSrc,
                filePath,
            };
            if (this.category === 'files') {
                this.changeTermsOfService(fileObj);
            } else if (this.category === 'images') {
                this.changeImage(fileObj);
            }
        });
    },
    computed: {

        ...mapState('Sidebar', [
            'configuration',
        ]),
        ...mapGetters('Sidebar', [
            'checkConfigurationTermsOfService',
        ]),
        ...mapState('ChangeContent', [
            'formData',
            'output',
            'formSuccess',
        ]),

        ...mapState('Gallery', [
            'category',
        ]),

    },
    async created() {
        this.setFormSuccess(false);
        this.isLoading = true;
        await this.home();
        this.isLoading = false;
    },
};
</script>

<style lang="scss" scoped>
@import "./css/ChangeContent.scss";
</style>
