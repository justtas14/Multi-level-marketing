import axios from 'axios';

const state = {
    configuration: null,
    adminRoutes: {
        Route1:
        {
            path: '/admin',
            label: 'Home',
            icon: "home"
        },
        Route2:
        {
            path: '/admin/emailtemplateslist',
            label: 'Email Templates',
            icon: 'email'
        },
        Route3:
        {
            path: '/admin/endprelaunch',
            label: 'End Prelaunch',
            icon: 'stop'
        },
        Route4:
        {
            path: '/admin/changecontent',
            label: 'Change Content',
            icon: 'edit'
        },
        Route5:
        {
            path: '/admin/users',
            label: 'User Search',
            icon: 'search',
            subRoute: ['user_search_details']
        },
        Route6:
        {
            path: '/admin/gallery',
            label: 'Gallery',
            icon: 'collections',
        },
        Route7:
        {
            path: '/admin/logs',
            label: 'System logs',
            icon: 'library_books',
        },
        Route8:
        {
            path: '/admin/csv',
            label: 'Associate csv dump',
            icon: 'supervised_user_circle',
        },
    },
    associateRoutes: {
        Route1:
        {
            path: '/associate',
            label: 'Home',
            icon: "home"
        },
        Route2:
        {
            path: '/admin/viewer',
            label: 'Team Viewer',
            icon: 'people'
        },
        Route3:
        {
            path: '/associate/invite',
            label: 'Invite Associates',
            icon: 'insert_invitation'
        },
        Route4:
        {
            path: '/admin/profile',
            label: 'Edit Profile',
            icon: 'edit'
        },
    }
};

const getters = {
    checkConfigurationMainLogo: (state) => {
        if (state.configuration) {
            return !!state.configuration.mainLogoPath;
        }
        return false;
    },

    checkConfigurationTermsOfService: (state) => {
        if (state.configuration) {
            return !!state.configuration.termsOfServices;
        }
        return false;
    },
    getAdminRoutes: (state) => {
        return state.adminRoutes;
    },
    getAssociateRoutes: (state) => {
        return state.associateRoutes;
    },
};

const actions = {
    async configurationApi({commit, state}) {
        let response = await axios.get("/api/configuration");
        commit('setConfiguration', response.data.configuration);
    }
};

const mutations = {
    setConfiguration: (state, configuration) => {
        state.configuration = configuration;
    },
};

export default {
    state,
    getters,
    actions,
    mutations
};