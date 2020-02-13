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
    container: {
        beginX: null,
        middleX: null,
        width: null,
    },
    minValueX: 9999,
    address: [],
    currentFocusNumber: null,
};

const getters = {
};

const actions = {
    async getAllAssociates({ dispatch, commit, rootState }, addresses) {
        const apiCallsObj = new ApiCalls();
        const id = addresses.length > 0 ? addresses[addresses.length - 1] : null;
        const res = await apiCallsObj.getAssociates(
            '/api/associate/downline',
            id,
            rootState.Security.token,
        );
        // console.log(addresses);
        if (res.data.length === 0) {
            return;
        }
        if (addresses.length === 0) {
            commit('setRootNode', res.data);
            dispatch('getAllAssociates', [res.data.id]);
        } else {
            commit('setNodeChildren', { address: addresses, children: res.data });
            res.data.forEach((element) => {
                dispatch('getAllAssociates', [...addresses, element.id.toString()]);
            });
        }
    },
    async getAssociates({ rootState }, id) {
        const apiCallsObj = new ApiCalls();
        const res = await apiCallsObj.getAssociates(
            '/api/associate/downline',
            id,
            rootState.Security.token,
        );
        return res.data;
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
    setContainerParams: (state, { beginX, middleX, width }) => {
        state.container.beginX = beginX;
        state.container.middleX = middleX;
        state.container.width = width;
    },
    setMinValueX: (state, value) => {
        state.minValueX = value;
    },
    setCurrentFocusNumber: (state, focusNumber) => {
        state.currentFocusNumber = focusNumber;
    },
};

export default {
    namespaced: true,
    state: initialState,
    getters,
    actions,
    mutations,
};
