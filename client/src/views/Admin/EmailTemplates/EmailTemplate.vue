<template>
  <div class="admin-contentContainer">
    <div class="card">
      <div class="card-content quillEditorCard">
        <content-loader
          v-if="isLoading"
          :height='25'
        >
          <rect
            :width='175'
            :height='25'
          />
        </content-loader>
        <span
          v-else
          class="card-title"
        >{{ this.title }}</span>
        <Success v-if="formSuccess" v-bind:message="'Saved!'"></Success>
        <Error v-if="formError" v-bind:message="formError"></Error>
        <main id="main">
          <form
            name="email_template"
            method="post"
          >
            <label
              for="email_template_emailSubject"
              class="required active"
            >Email subject</label>

            <textarea
              id="email_template_emailSubject"
              name="email_template[emailSubject]"
              required="required"
              v-model="formData.emailSubject"
            ></textarea>

            <label
              for="email_template_emailBody"
              class="active"
            >Email body</label>
            <div class="emailContainer">
              <div id="editor">
                  <content-loader v-if="isLoading" :height='300' :width="500" >
                        <rect :width='500' :height='300' />
                </content-loader>
                  <QuillEditor
                    v-else
                    v-bind:configurationContent="formData.configurationContent"
                    v-model="updateConfigurationContent"
                >
                </QuillEditor>
              </div>
              <div id="emailBodyParams">
                <div id="emailBodyParamsTitle">Available parameters</div>
                <content-loader
                v-if="isLoading"
                :height='100'
                >
                <rect
                    :width='100'
                    :height='100'
                />
                </content-loader>
                <div v-for="(item, index) in this.availableParameters"
                 :key="index" class="availableParametersContainer">
                    <span id="emailBodyParam">{{ item }}</span>
                    <span id ="emailBodyParamExplain"> - {{ index }}</span>
                </div>
              </div>
            </div>
            <br>
            <div id="buttons_container">
              <div id="button_submit">
                <div><button
                    @click="submitForm"
                    type="button"
                    id="email_template_Submit"
                    name="email_template[Submit]"
                    class="waves-effect waves-light btn"
                    :disabled="isLoadingForm || isLoading"
                  >Change Template</button>
                   <div v-if="isLoadingForm" class="progress">
                    <div class="indeterminate"></div>
                    </div>
                  </div>
              </div>
              <div id="button_preview">
                <a
                  class="waves-effect waves-light btn"
                  @click="modalDisplay = 'block';"
                  :disabled="isLoading"
                >Template preview</a>
              </div>
            </div>
          </form>
        </main>
      <Modal
         v-bind:style="{display: modalDisplay}"
         @closeModal="closeModal"
      >
       <template v-slot:header>
          {{ title }} preview
        </template>
        <template v-slot:body>
           <div class="subject card">{{ previewSubject }}</div>
          <div class="ql-editor editorPreview card" v-html="previewEditor"></div>
        </template>
      </Modal>
      </div>
    </div>
  </div>
</template>

<script>
import {
    mapActions, mapState, mapMutations,
} from 'vuex';
import { ContentLoader } from 'vue-content-loader';
import QuillEditor from '../../../components/Gallery/QuillEditor.vue';
import Modal from '../../../components/Modal/Modal.vue';
import DeltaToHtml from '../../../components/Gallery/Services/deltaToHtml';
import Error from '../../../components/Messages/Error.vue';
import Success from '../../../components/Messages/Success.vue';

export default {
    name: 'EmailTemplate',
    components: {
        ContentLoader,
        QuillEditor,
        Modal,
        Error,
        Success,
    },
    props: [],
    data() {
        return {
            isLoading: false,
            isLoadingForm: false,
            updateConfigurationContent: '',
            formData: {
                emailSubject: '',
                configurationContent: '',
            },
            modalDisplay: 'none',
        };
    },
    methods: {
        async submitForm() {
            const formData = new FormData();
            formData.append('emailSubject', this.formData.emailSubject);
            formData.append('emailBody', this.updateConfigurationContent);
            this.isLoadingForm = true;
            await this.home(formData);
            this.isLoadingForm = false;
            if (!this.formError) {
                this.setFormSuccess(true);
            }
        },

        closeModal() {
            this.modalDisplay = 'none';
        },

        replaceTemplate(emailPreview) {
            let emailPreviewString = emailPreview;
            const reformedAvailableParameters = JSON.parse(
                JSON.stringify(this.availableParameters),
            );
            Object.keys(reformedAvailableParameters).forEach((key) => {
                if (!reformedAvailableParameters[key].includes('link')) {
                    emailPreviewString = emailPreviewString.replace(
                        reformedAvailableParameters[key], key.toUpperCase(),
                    );
                }
            });
            return emailPreviewString;
        },

        ...mapActions('EmailTemplates', [
            'home',
        ]),

        ...mapMutations('EmailTemplates', [
            'setUrlType',
            'setFormSuccess',
        ]),
    },
    computed: {
        previewEditor() {
            const deltaToHtmlObj = new DeltaToHtml(this.updateConfigurationContent);
            const innerHtmlEditor = deltaToHtmlObj.deltaToHtml();
            return this.replaceTemplate(innerHtmlEditor);
        },

        previewSubject() {
            const emailSubjectPreview = this.formData.emailSubject;
            return this.replaceTemplate(emailSubjectPreview);
        },

        ...mapState('EmailTemplates', [
            'urlType',
            'availableParameters',
            'emailTemplate',
            'title',
            'formError',
            'formSuccess',
        ]),
    },
    async created() {
        this.setUrlType(this.$route.params.type);
        this.isLoading = true;
        await this.home();
        this.isLoading = false;
        this.formData.emailSubject = this.emailTemplate.emailSubject;
        this.formData.configurationContent = this.emailTemplate.emailBody;
        document.title = this.title;
    },
};
</script>

<style lang="scss" scoped>
@import "./css/EmailTemplate.scss";
</style>
