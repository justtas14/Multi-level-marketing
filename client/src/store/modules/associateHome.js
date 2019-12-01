import SecurityAPI from '../api/SecurityApi/apiCalls';

const initialState = {
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
        const securityApiObj = new SecurityAPI();
        const response = await securityApiObj.authenticateGetApi('/api/associate', rootState.Security.token);
        commit('setAssociateHomePageData', response.data);
    },
};

const mutations = {
    setAssociateHomePageData: (state, data) => {
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
