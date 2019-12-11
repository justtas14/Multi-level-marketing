import axios from 'axios';
import SecurityAPI from '../api/SecurityApi/apiCalls';

const initialState = {
    configuration: null,
    currentPath: '',
    hamburgerClicked: null,
};

const getters = {
    checkConfigurationMainLogo: (state) => {
        if (state.configuration) {
            return !!state.configuration.mainLogo.filePath;
        }
        return false;
    },
    checkEndPrelaunch: state => state.configuration.hasPrelaunchEnded,

    checkConfigurationTermsOfService: (state) => {
        if (state.configuration) {
            return !!state.configuration.termsOfServices.filePath;
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
        const securityApiObj = new SecurityAPI();
        const response = await securityApiObj.downloadCSVApi(rootState.Security.token);
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
    setConfiguration: (state, configuration) => {
        state.configuration = configuration;
    },
    setCurrentPath: (state, currentPath) => {
        state.currentPath = currentPath;
    },
    setHamburgerClicked: (state, flag) => {
        state.hamburgerClicked = flag;
    },
};

export default {
    namespaced: true,
    state: initialState,
    getters,
    actions,
    mutations,
};
