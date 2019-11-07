import axios from 'axios';
import SecurityAPI from '../api/SecurityApi/apiCalls';

const state = {
    configuration: null,
    currentPath: '',
};

const getters = {
    checkConfigurationMainLogo: () => {
        if (state.configuration) {
            return !!state.configuration.mainLogoPath;
        }
        return false;
    },

    checkConfigurationTermsOfService: () => {
        if (state.configuration) {
            return !!state.configuration.termsOfServices;
        }
        return false;
    },
};

const actions = {
    async configurationApi({ commit }) {
        const response = await axios.get('/api/configuration');
        commit('setConfiguration', response.data.configuration);
    },
    async downloadCSV({ rootState }) {
        const response = await SecurityAPI.downloadCSVApi(rootState.Security.token);
        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', 'associates.csv'); // or any other extension
        document.body.appendChild(link);
        link.click();
        link.remove();
    },
};

const mutations = {
    setConfiguration: (configuration) => {
        state.configuration = configuration;
    },
    setCurrentPath: (currentPath) => {
        state.currentPath = currentPath;
    },
};

export default {
    state,
    getters,
    actions,
    mutations,
};
