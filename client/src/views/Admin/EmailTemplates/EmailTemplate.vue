<template>
  <div class="admin-contentContainer">
    <div class="card">
      <div class="card-content quillEditorCard">
         <content-loader v-if="isLoading" :height='25' >
            <rect :width='175' :height='25' />
        </content-loader>
        <span v-else class="card-title">{{ this.title }}</span>


      </div>
    </div>
  </div>
</template>

<script>
import {
    mapActions, mapState, mapMutations,
} from 'vuex';
import { ContentLoader } from 'vue-content-loader';

export default {
    name: 'EmailTemplate',
    components: {
        ContentLoader,
    },
    props: [],
    data() {
        return {
            isLoading: false,
            isLoadingForm: false,
        };
    },
    methods: {

        ...mapActions('EmailTemplates', [
            'home',
        ]),

        ...mapMutations('EmailTemplates', [
            'setUrlType',
        ]),
    },
    mounted() {

    },
    computed: {

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
    },
};
</script>

<style lang="scss" scoped>
@import "./css/EmailTemplate.scss";
</style>
