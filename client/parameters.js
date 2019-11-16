/* global globalEnvs */
export default {
    API_HOST_URL: (globalEnvs && globalEnvs.VUE_APP_HOST_URL) || '/',
};
