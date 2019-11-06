import SecurityAPI from "../api/SecurityApi/security";

const state = {
    isLoading: false,
    associatesInLevels: null,
    levels: null,
    maxLevel: null,
    directAssociates: null,
    parent: null,
};
const getters = {
    getAssociateInLevels: (state) => {
        return JSON.parse(JSON.stringify(state.associatesInLevels));
    },
    getDirectAssociates: (state) => {
        return state.directAssociates;
    },
    getLevels: (state) => {
        return state.levels;
    },
    getMaxLevel: (state) => {
        return state.maxLevel;
    },
};

const actions = {
    async associateHomeApi({commit, state, rootState}) {
        commit('setIsLoading');
        let response = await SecurityAPI.authenticateGetApi('/api/associate', rootState.Security.token);
        commit('setAssociateHomePageData', response.data);
    }
};

const mutations = {
    setIsLoading: (state) => {
        state.isLoading = true;
    },
    setAssociateHomePageData: (state, data) => {
        state.isLoading = false;
        state.associatesInLevels = data.associatesInLevels;
        state.levels = data.levels;
        state.maxLevel = data.maxLevel;
        state.parent = data.parent;
        state.directAssociates = data.directAssociates;
    }
};

export default {
    state,
    getters,
    actions,
    mutations
};
