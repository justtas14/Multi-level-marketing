import SecurityAPI from '../api/SecurityApi/apiCalls';

const initialState = {
    isLoading: false,
    associatesInLevels: null,
    levels: null,
    maxLevel: null,
};
const getters = {
    getAssociateInLevels: state => JSON.parse(JSON.stringify(state.associatesInLevels)),
    getLevels: state => state.levels,
    getMaxLevel: state => state.maxLevel,
};

const actions = {
    async adminHomeApi({ commit, rootState }, dependencies) {
        commit('setIsLoading');
        const SecurityApiObj = new SecurityAPI(dependencies.logout, dependencies.router);
        const response = await SecurityApiObj.authenticateGetApi('/api/admin', rootState.Security.token);
        commit('setAssociateLevelsData', response.data);
    },
};

const mutations = {
    setIsLoading: (state) => {
        state.isLoading = true;
    },
    setAssociateLevelsData: (state, data) => {
        state.isLoading = false;
        state.associatesInLevels = data.associatesInLevels;
        state.levels = data.levels;
        state.maxLevel = data.maxLevel;
    },
};

export default {
    initialState,
    getters,
    actions,
    mutations,
};
