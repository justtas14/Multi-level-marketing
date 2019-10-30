import axios from 'axios';

const state = {
    configuration: null
};

const getters = {
    checkConfigurationMainLogo: (state) => {
        if (state.configuration) {
            return !!state.configuration.mainLogoPath;
        }
        return false;
    },
};

const actions = {
    async configurationApi({commit, state}) {
        let response = await axios.get("/api/configuration");
        commit('setConfiguration', response.data.configuration);
    }
};

const mutations = {
    setConfiguration: (state, configuration) => {
        state.configuration = configuration;
    }
};

export default {
    state,
    getters,
    actions,
    mutations
};