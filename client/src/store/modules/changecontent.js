import ApiCalls from '../api/SecurityApi/apiCalls';
import FileSrc from '../../components/Gallery/Services/fileSrc';

const initialState = {
    output: {
        imagefileSrc: '',
        imageFileName: '',
        termsOfServicefileSrc: '',
        termsOfServiceFileName: '',
    },
    formData: {
        hiddenMainLogoFile: null,
        hiddenTermsOfServiceFile: null,
        tosDisclaimer: '',
    },
    contentChanged: false,
    termsOfServices: null,
    mainLogo: null,
    formSuccess: false,

};

const getters = {

};

const actions = {
    async home({ commit, rootState }, formData = null) {
        const apiCallObj = new ApiCalls();
        const response = await apiCallObj.changeContent(
            '/api/admin/changecontent',
            formData,
            rootState.Security.token,
        );
        commit('setGeneralInfo', response.data);
        const { mainLogo } = response.data.configuration;
        const { termsOfServices } = response.data.configuration;
        const fileObj = { };
        if (mainLogo) {
            const fileSrc = new FileSrc(mainLogo.fileName, mainLogo.filePath);
            fileObj.fileSrc = fileSrc.determineSrc();
            fileObj.fileName = mainLogo.fileName;
            fileObj.fileId = mainLogo.id;
            commit('changeImage', fileObj);
        }
        if (termsOfServices) {
            const fileSrc = new FileSrc(termsOfServices.fileName, termsOfServices.filePath);
            fileObj.fileSrc = fileSrc.determineSrc();
            fileObj.fileName = termsOfServices.fileName;
            fileObj.fileId = termsOfServices.id;
            commit('changeTermsOfService', fileObj);
        }
    },
};

const mutations = {
    setGeneralInfo(state, data) {
        state.contentChanged = data.contentChanged;
        state.formData.tosDisclaimer = data.configuration.tosDisclaimer;
        state.mainLogo = data.configuration.mainLogo;
        state.termsOfServices = data.configuration.termsOfServices;
    },

    setFormSuccess(state, flag) {
        state.formSuccess = flag;
    },

    changeImage(state, obj) {
        state.output.imagefileSrc = obj.fileSrc;
        state.output.imageFileName = obj.fileName;
        state.formData.hiddenMainLogoFile = obj.fileId;
    },

    changeTermsOfService(state, obj) {
        state.output.termsOfServicefileSrc = obj.fileSrc;
        state.output.termsOfServiceFileName = obj.fileName;
        state.formData.hiddenTermsOfServiceFile = obj.fileId;
    },
};

export default {
    namespaced: true,
    state: initialState,
    getters,
    actions,
    mutations,
};
