import axios from 'axios';
import constants from '../../constants/constants'

const state = {
    modalState: false,
    dataLoaded: false,
    files: [],
    paginationInfo: {
        'currentPage': 1
    },
    filesPerPage: 20,
    imageExtensions: [],
    category: 'all',
    yesClickFn: function () {},
    notification: {
        display: 'none',
        message: ''
    },
};

const getters = {

};

const actions = {
    async callDataAxios({state, dispatch, commit}, filesPerPage = null) {
        if (filesPerPage) {
            state.filesPerPage = filesPerPage;
        }
        const res = await axios.get(constants.api.jsonData, {
            params: {
                page: state.paginationInfo.currentPage,
                imageLimit: state.filesPerPage,
                category: state.category
            }
        });
        const data = {
            files: res.data.files,
            pagination: res.data.pagination,
            imageExtensions: res.data.imageExtensions
        };
        commit('loadInfo', data);
    },
    readURL: function ({state, dispatch, commit}, params) {
        const input = params.e.target;
        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = (e) => {
                const file = input.files[0];
                commit('changeYesFn', () => {
                    dispatch('saveToServer', file);
                    input.value = '';
                });
                input.value = '';
                params.confirmation('Add ' + file.name + ' file?');
            };
            reader.readAsDataURL(input.files[0]);
        }
    },
    handleFiles: function ({state, dispatch}, files) {
        ([...files]).forEach(function (file) {
            dispatch('saveToServer', file);
        });
    },
    saveToServer: function ({dispatch, state, commit}, file) {
        if (file.size > constants.maxAllowedSize) {
            commit('showNotification', 'File ' +file.name+ ' is too large');
        } else if (file.name.length > constants.maxAllowedLength) {
            commit('showNotification', 'File name is too long');
        } else {
            const fd = new FormData();
            fd.append('galleryFile', file);
            axios.post(constants.api.uploadFile, fd, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            }).then(res => {
                commit('insertImage', res.data.file);
                commit('showNotification', 'File '+file.name+' added!');
                dispatch('callDataAxios');
            }).catch(function (err) {
                console.log(err);
            });
        }
    },
    deleteRequestFunction: function ({dispatch, state, commit}, params) {
        axios.post(constants.api.removeFile, {
            params: {
                galleryId: params.fileId,
                fileId: params.galleryFileId
            }
        }).then(res => {
            if (res.data.fileInUse) {
                commit('showNotification','File ' + params.fileName + ' is already in use!');
            } else {
                commit('deleteFile', params.fileId);
                commit('subtractPage');
                dispatch('callDataAxios');
                commit('showNotification','File ' + params.fileName + ' deleted!');
            }
        }).catch(function (err) {
            console.log(err);
        });
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
        if (state.files.length === 0 && state.paginationInfo.currentPage != 1) {
            state.paginationInfo.currentPage--;
        }
    },
    changePage: (state, {page, action}) => {
        if (action == null) {
            state.paginationInfo.currentPage = page;
        } else if (action == 'add') {
            state.paginationInfo.currentPage++;
        } else if (action == 'subtract') {
            state.paginationInfo.currentPage--;
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
    loadInfo: (state, {...data}) => {
        state.files = data.files;
        state.paginationInfo = data.pagination;
        state.imageExtensions = data.imageExtensions;
    }
};

export default {
    state,
    getters,
    actions,
    mutations
};