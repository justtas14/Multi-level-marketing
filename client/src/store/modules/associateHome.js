import SecurityAPI from '../api/SecurityApi/apiCalls';

const state = {
    isLoading: false,
    associatesInLevels: null,
    levels: null,
    maxLevel: null,
    directAssociates: null,
    parent: null,
};
const getters = {
    getAssociateInLevels: () => JSON.parse(JSON.stringify(state.associatesInLevels)),
    getDirectAssociates: () => state.directAssociates,
    getLevels: () => state.levels,
    getMaxLevel: () => state.maxLevel,
};

const actions = {
    async associateHomeApi({ commit, rootState }) {
        commit('setIsLoading');
        const response = await SecurityAPI.authenticateGetApi('/api/associate', rootState.Security.token);
        commit('setAssociateHomePageData', response.data);
    },
};

const mutations = {
    setIsLoading: () => {
        state.isLoading = true;
    },
    setAssociateHomePageData: (data) => {
        state.isLoading = false;
        state.associatesInLevels = data.associatesInLevels;
        state.levels = data.levels;
        state.maxLevel = data.maxLevel;
        state.parent = data.parent;
        state.directAssociates = data.directAssociates;
    },
};

export default {
    state,
    getters,
    actions,
    mutations,
};
