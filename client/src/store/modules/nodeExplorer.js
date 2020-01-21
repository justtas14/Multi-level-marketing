import Vue from 'vue';
import ApiCalls from '../api/SecurityApi/apiCalls';
import getChildrenFromAddress from '../../components/NodeExplorer/services/nodeExplorerMethods';

const initialState = {
    rootAssociate: {
        id: '',
        title: '',
        filePath: '',
        parentId: '',
        numberOfChildren: '',
        children: {

        },
    },
    address: [],
    containerWidth: null,
    containerX: null,
};

const getters = {
};

const actions = {
    async getAssociates({ dispatch, commit, rootState }, addresses) {
        const apiCallsObj = new ApiCalls();
        const id = addresses.length > 0 ? addresses[addresses.length - 1] : null;
        const res = await apiCallsObj.getAssociates(
            '/api/associate/downline',
            id,
            rootState.Security.token,
        );
        if (res.data.length === 0) {
            return;
        }
        if (addresses.length === 0) {
            commit('setRootNode', res.data);
            dispatch('getAssociates', [res.data.id]);
        } else {
            commit('setNodeChildren', { address: addresses, children: res.data });
            res.data.forEach((element) => {
                dispatch('getAssociates', [...addresses, element.id]);
            });
        }
    },
};

const mutations = {
    setNodeChildren: (state, { address, children }) => {
        const node = getChildrenFromAddress(address, state.rootAssociate);
        const nodeChildren = children.reduce(
            (childrenObj, child) => (
                { ...childrenObj, [child.id]: { ...child, children: {} } }
            ), {},
        );
        Vue.set(node, 'children', nodeChildren);
    },
    setRootNode: (state, value) => {
        state.rootAssociate.id = value.id;
        state.rootAssociate.filePath = value.filePath;
        state.rootAssociate.numberOfChildren = value.numberOfChildren;
        state.rootAssociate.parentId = value.parentId;
        state.rootAssociate.title = value.title;
        state.rootAssociate.children = {};
    },
    setAddress: (state, addressArr) => {
        state.address = addressArr;
    },
    pushAddress: (state, value) => {
        state.address.push(value);
    },
    popAddress: (state) => {
        state.address.pop();
    },
    setContainerWidth: (state, containerWidth) => {
        state.containerWidth = containerWidth;
    },
    setContainerX: (state, containerX) => {
        state.containerX = containerX;
    },
};

export default {
    namespaced: true,
    state: initialState,
    getters,
    actions,
    mutations,
};
