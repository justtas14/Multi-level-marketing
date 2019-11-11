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
    async associateHomeApi({ commit, rootState }, dependencies) {
        commit('setIsLoading');
        const SecurityApiObj = new SecurityAPI(dependencies.logout, dependencies.router);
        const response = await SecurityApiObj.authenticateGetApi('/api/associate', rootState.Security.token);
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
    initialState,
    getters,
    actions,
    mutations,
};
