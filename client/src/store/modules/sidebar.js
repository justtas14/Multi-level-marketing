import axios from 'axios';
import SecurityAPI from "../api/SecurityApi/security";

const state = {
    configuration: null,
    currentPath: ''
};

const getters = {
    checkConfigurationMainLogo: (state) => {
        if (state.configuration) {
            return !!state.configuration.mainLogoPath;
        }
        return false;
    },

    checkConfigurationTermsOfService: (state) => {
        if (state.configuration) {
            return !!state.configuration.termsOfServices;
        }
        return false;
    },
};

const actions = {
    async configurationApi({commit, state}) {
        let response = await axios.get("/api/configuration");
        commit('setConfiguration', response.data.configuration);
    },
    async downloadCSV({commit, state, rootState}) {
        let response = await SecurityAPI.downloadCSVApi(rootState.Security.token);
        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', 'associates.csv'); //or any other extension
        document.body.appendChild(link);
        link.click();
        link.remove();
    }
};

const mutations = {
    setConfiguration: (state, configuration) => {
        state.configuration = configuration;
    },
    setCurrentPath: (state, currentPath) => {
        state.currentPath = currentPath;
    }
};

export default {
    state,
    getters,
    actions,
    mutations
};