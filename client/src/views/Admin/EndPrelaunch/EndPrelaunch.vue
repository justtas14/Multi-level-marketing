<template>
  <div
    class="admin-contentContainer"
    style="overflow:hidden"
  >
    <div class="card">
      <div class="card-content quillEditorCard">
        <span class="card-title">End Prelaunch</span>
        <Success v-if="formSuccess" v-bind:message="'Form Updated'"></Success>
        <Error v-if="errorMessage" v-bind:message="errorMessage"></Error>
        <main id="main">
          <form
            name="end_prelaunch"
            method="post"
          >
            <div class="input-field">
              <label style="position: unset!important;">
                <input
                  type="checkbox"
                  id="end_prelaunch_prelaunchEnded"
                  name="end_prelaunch[prelaunchEnded]"
                  class="filled-in"
                  v-model="formData.prelaunchEnded"
                >
                <span>End Prelaunch</span>
              </label>
            </div>
            <div class="input-field">
              <label for="end_prelaunch_landingContent">Landing content</label>
              <input
                type="hidden"
                id="end_prelaunch_landingContent"
                name="end_prelaunch[landingContent]"
              >
                <content-loader v-if="isLoading" :height='225' >
                        <rect :width='400' :height='225' />
                </content-loader>
                <QuillEditor
                    v-else
                    v-bind:configurationContent="formData.configurationContent"
                    v-model="updateConfigurationContent"
                >
                </QuillEditor>
            </div>
            <div id="buttonWraper">
            <button
                type="button"
                id="end_prelaunch_Submit"
                name="end_prelaunch[Submit]"
                class="waves-effect waves-light btn"
                :disabled="isLoadingForm || isLoading"
                @click="submitForm"
              >Save
              </button>
                <div v-if="isLoadingForm" class="progress">
                    <div class="indeterminate"></div>
                </div>
            </div>
          </form>
        </main>
      </div>
    </div>
  </div>
</template>

<script>
import {
    mapActions, mapState, mapMutations,
} from 'vuex';
import { ContentLoader } from 'vue-content-loader';
import Success from '../../../components/Messages/Success.vue';
import Error from '../../../components/Messages/Error.vue';
import QuillEditor from '../../../components/Gallery/QuillEditor.vue';

export default {
    name: 'EndPrelaunch',
    components: {
        Success,
        QuillEditor,
        ContentLoader,
        Error,
    },
    props: [],
    data() {
        return {
            isLoading: false,
            isLoadingForm: false,
            updateConfigurationContent: '',
            formData: {
                prelaunchEnded: false,
                configurationContent: '',
            },
        };
    },
    methods: {
        async submitForm() {
            const formData = new FormData();
            formData.append('prelaunchEnded', this.formData.prelaunchEnded);
            formData.append('landingContent', this.updateConfigurationContent);
            this.isLoadingForm = true;
            await this.home(formData);
            this.isLoadingForm = false;
            window.scroll(0, 0);
            await this.configurationApi();
        },

        ...mapActions('Sidebar', [
            'configurationApi',
        ]),
        ...mapMutations('EndPrelaunch', [
            'setFormSuccess',
        ]),
        ...mapActions('EndPrelaunch', [
            'home',
        ]),
    },
    mounted() {

    },
    computed: {

        ...mapState('EndPrelaunch', [
            'formSuccess',
            'errorMessage',
        ]),
    },
    async created() {
        this.setFormSuccess(false);
        this.isLoading = true;
        const res = await this.home();
        this.isLoading = false;
        this.formData.prelaunchEnded = res.hasPrelaunchEnded;
        this.formData.configurationContent = res.configurationContent;
    },
};
</script>

<style lang="scss" scoped>
@import "./css/EndPrelaunch.scss";
</style>
