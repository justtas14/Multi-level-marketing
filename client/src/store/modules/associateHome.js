import SecurityAPI from '../api/SecurityApi/apiCalls';

const initialState = {
    isLoading: false,
    associatesInLevels: null,
    levels: null,
    maxLevel: null,
    directAssociates: null,
    parent: null,
};
const getters = {
    getAssociateInLevels: state => JSON.parse(JSON.stringify(state.associatesInLevels)),
    getDirectAssociates: state => state.directAssociates,
    getLevels: state => state.levels,
    getMaxLevel: state => state.maxLevel,
};

const actions = {
    async associateHomeApi({ commit, rootState }) {
        commit('setIsLoading');
        const securityApiObj = new SecurityAPI();
        const response = await securityApiObj.authenticateGetApi('/api/associate', rootState.Security.token);
        commit('setAssociateHomePageData', response.data);
    },
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
    },
};

export default {
    namespaced: true,
    state: initialState,
    getters,
    actions,
    mutations,
};
