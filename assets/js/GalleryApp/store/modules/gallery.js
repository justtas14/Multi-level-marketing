import axios from 'axios';
import constants from '../../constants/constants'

const state = {
    modalState: false,
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
    confirm: {
        display: 'none',
        message: '',
    },
};

const getters = {

};

const actions = {
    async callDataAxios({state, dispatch, commit}, filesPerPage = null) {
        if (filesPerPage) {
            state.filesPerPage = filesPerPage;
        }
        let res = await axios.get('/admin/jsonGallery', {
            params: {
                page: state.paginationInfo.currentPage,
                imageLimit: state.filesPerPage,
                category: state.category
            }
        });
        let data = {
            files: res.data.files,
            pagination: res.data.pagination,
            imageExtensions: res.data.imageExtensions
        };
        commit('loadInfo', data);
    },
    readUrl: function ({state, dispatch, commit}, e) {
        let input = e.target;
        if (input.files && input.files[0]) {
            let reader = new FileReader();

            reader.onload = (e) => {
                const file = input.files[0];
                commit('changeYesFn', () => {
                    dispatch('saveToServer', file);
                    input.value = '';
                    state.confirm.display = "none";
                });
                input.value = '';
                commit('showConfirm', "Add " + file.name + ' file?');
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
            axios.post('/uploadGalleryFile', fd, {
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
        commit('deleteFile', params.fileId);
        axios.post('/admin/removeFile', {
            params: {
                galleryId: params.fileId,
                fileId: params.galleryFileId
            }
        }).then(res => {
            commit('subtractPage');
            dispatch('callDataAxios');
            commit('showNotification','File ' + params.fileName + ' deleted!');
        }).catch(function (err) {
            console.log(err);
        });
        commit('hideConfirmation');
    },
};

const mutations = {
    changeModalState: (state, flag) => {
        state.modalState = flag;
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
    hideConfirmation: (state) => {
        state.confirm.display = 'none';
    },
    showConfirm: (state, msg) => {
        state.confirm.message = msg;
        state.confirm.display = 'block';
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