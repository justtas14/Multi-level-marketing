import axios from 'axios';
import SecurityAPI from "../api/SecurityApi/security";

const state = {
    isLoading: false,
    associatesInLevels: null,
    levels: null,
    maxLevel: null,
};
const getters = {
    getAssociateInLevels: (state) => {
        return JSON.parse(JSON.stringify(state.associatesInLevels));
    },
    getLevels: (state) => {
        return state.levels;
    },
    getMaxLevel: (state) => {
        return state.maxLevel;
    }
};

const actions = {
    async adminHomeApi({commit, state, rootState}) {
        commit('setIsLoading');
        let response = await SecurityAPI.authenticateGetApi('/api/admin', rootState.Security.token);
        commit('setAssociateLevelsData', response.data);
    }
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
    }
};

export default {
    state,
    getters,
    actions,
    mutations
};