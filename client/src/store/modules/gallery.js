import axios from 'axios';
import constants from '../../constants/constants';

const initialState = {
    modalState: false,
    dataLoaded: false,
    files: [],
    paginationInfo: {
        currentPage: 1,
    },
    filesPerPage: 20,
    imageExtensions: [],
    category: 'all',
    yesClickFn() {},
    notification: {
        display: 'none',
        message: '',
    },
    confirm: {
        display: 'none',
        message: '',
    },
    spinner: false,
};

const getters = {

};

const actions = {
    async callDataAxios({ state, commit }, filesPerPage = null) {
        if (filesPerPage) {
            state.filesPerPage = filesPerPage;
        }
        const res = await axios.get(constants.api.jsonData, {
            params: {
                page: state.paginationInfo.currentPage,
                imageLimit: state.filesPerPage,
                category: state.category,
            },
        });
        const data = {
            files: res.data.files,
            pagination: res.data.pagination,
            imageExtensions: res.data.imageExtensions,
            imageTypes: res.data.imageTypes,
        };
        commit('setSpinnerState', false);
        commit('loadInfo', data);
    },
    readURL({ dispatch, commit }, e) {
        const input = e.target;
        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = () => {
                const file = input.files[0];
                commit('changeYesFn', () => {
                    commit('changeConfirmation', { display: 'none' });
                    dispatch('saveToServer', file);
                    input.value = '';
                });
                input.value = '';
                commit('changeConfirmation', { display: 'block', message: `Add ${file.name} file?` });
            };
            reader.readAsDataURL(input.files[0]);
        }
    },
    handleFiles({ dispatch }, files) {
        ([...files]).forEach((file) => {
            dispatch('saveToServer', file);
        });
    },
    saveToServer({ state, dispatch, commit }, file) {
        if (file.size > constants.maxAllowedSize) {
            commit('showNotification', `File ${file.name} is too large`);
        } else if (file.name.length > constants.maxAllowedLength) {
            commit('showNotification', 'File name is too long');
        } else {
            const fd = new FormData();
            fd.append('galleryFile', file);
            axios.post(constants.api.uploadFile, fd, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                },
            }).then((res) => {
                switch (state.category) {
                case 'images':
                    if (state.imageTypes.includes(res.data.file.mimeType)) {
                        commit('insertImage', res.data.file);
                    }
                    break;
                case 'files':
                    if (!state.imageTypes.includes(res.data.file.mimeType)) {
                        commit('insertImage', res.data.file);
                    }
                    break;
                default:
                    commit('insertImage', res.data.file);
                }
                commit('showNotification', `File ${file.name} added!`);
                dispatch('callDataAxios');
            }).catch((err) => {
                console.log(err);
            });
        }
    },
    async deleteRequestFunction({ dispatch, commit }, params) {
        const response = await axios.post(constants.api.removeFile, {
            params: {
                galleryId: params.fileId,
                fileId: params.galleryFileId,
            },
        });
        if (response.data.fileInUse) {
            commit('showNotification', `File ${params.fileName} is already in use!`);
            return false;
        }
        commit('deleteFile', params.fileId);
        commit('subtractPage');
        dispatch('callDataAxios');
        commit('showNotification', `File ${params.fileName} deleted!`);
        return true;
    },
};

const mutations = {
    changeModalState: (state, flag) => {
        state.modalState = flag;
    },
    changeDataLoadState: (state, flag) => {
        state.dataLoaded = flag;
    },
    changeEditorState: (state, flag) => {
        state.editor = flag;
    },
    changeFilesPerPage: (state, filesPerPage) => {
        state.filesPerPage = filesPerPage;
    },
    changeCategory: (state, category) => {
        state.category = category;
    },
    insertImage: (state, newFile) => {
        if (state.files.length > 7) {
            state.files.pop();
        }
        state.files.unshift(newFile);
    },
    deleteFile: (state, id) => {
        state.files = state.files.filter(file => file.id !== id);
    },
    subtractPage: (state) => {
        if (state.files.length === 0 && state.paginationInfo.currentPage !== 1) {
            state.paginationInfo.currentPage -= 1;
        }
    },
    changePage: (state, { page, action }) => {
        if (action == null) {
            state.paginationInfo.currentPage = page;
        } else if (action === 'add') {
            state.paginationInfo.currentPage += 1;
        } else if (action === 'subtract') {
            state.paginationInfo.currentPage -= 1;
        }
    },
    changeYesFn: (state, fn) => {
        state.yesClickFn = fn;
    },
    closeNotification: (state) => {
        state.notification.display = 'none';
    },
    showNotification: (state, msg) => {
        state.notification.message = msg;
        state.notification.display = 'block';
    },
    changeConfirmation: (state, obj) => {
        if (obj.message) {
            state.confirm.message = obj.message;
        }
        if (obj.display) {
            state.confirm.display = obj.display;
        }
    },
    setSpinnerState: (state, flag) => {
        state.spinner = flag;
    },
    loadInfo: (state, { ...data }) => {
        state.files = data.files;
        state.paginationInfo = data.pagination;
        state.imageExtensions = data.imageExtensions;
        state.imageTypes = data.imageTypes;
        state.spinner = false;
    },
};

export default {
    initialState,
    getters,
    actions,
    mutations,
};
