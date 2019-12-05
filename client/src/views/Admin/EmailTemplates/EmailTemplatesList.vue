<template>
  <div class="admin-contentContainer">
    <div class="card">
      <div class="card-content quillEditorCard">
        <span class="card-title">Email Templates</span>
        <main id="main">
          <table>
            <thead>
              <tr>
                <th>Template</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>Invitation email template</td>
                <td><a
                    @click="goToEmailTemplate('invitation')"
                    class="btn"
                  >EDIT</a></td>
              </tr>
              <tr>
                <td>Reset password email template</td>
                <td><a
                    @click="goToEmailTemplate('password')"
                    class="btn"
                  >EDIT</a></td>
              </tr>
              <tr>
                <td>Welcome email template</td>
                <td><a
                    @click="goToEmailTemplate('welcome')"
                    class="btn"
                  >EDIT</a></td>
              </tr>
            </tbody>
          </table>
        </main>
      </div>
    </div>
  </div>
</template>

<script>

export default {
    name: 'EmailTemplatesList',
    components: {
    },
    props: [],
    data() {
        return {
        };
    },
    methods: {
        goToEmailTemplate(type) {
            this.$router.push(`/admin/emailtemplate/${type}`);
        },

    },
    mounted() {

    },
    computed: {

    },
    created() {
    },
};
</script>

<style lang="scss" scoped>
@import "./css/EmailTemplatesList.scss";
</style>
