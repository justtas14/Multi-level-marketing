import SecurityAPI from '../api/SecurityApi/apiCalls';

const state = {
    isLoading: false,
    associatesInLevels: null,
    levels: null,
    maxLevel: null,
};
const getters = {
    getAssociateInLevels: () => JSON.parse(JSON.stringify(state.associatesInLevels)),
    getLevels: () => state.levels,
    getMaxLevel: () => state.maxLevel,
};

const actions = {
    async adminHomeApi({ commit, rootState }) {
        commit('setIsLoading');
        const response = await SecurityAPI.authenticateGetApi('/api/admin', rootState.Security.token);
        commit('setAssociateLevelsData', response.data);
    },
};

const mutations = {
    setIsLoading: () => {
        state.isLoading = true;
    },
    setAssociateLevelsData: (data) => {
        state.isLoading = false;
        state.associatesInLevels = data.associatesInLevels;
        state.levels = data.levels;
        state.maxLevel = data.maxLevel;
    },
};

export default {
    state,
    getters,
    actions,
    mutations,
};
