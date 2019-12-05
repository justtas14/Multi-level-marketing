import axios from 'axios';
import router from '../../../router';
import store from '../..';

export default class SecurityApiCalls {
    unAuthenticate() {
        store.commit('logout');
        router.push('/login');
    }

    setCookie(token) {
        const scope = this;
        try {
            return axios.post('http://prelaunchbuilder.local/api/setCookie', {
                token,
            }, {
                headers: { Authorization: `Bearer ${token}` },
                withCredentials: true,
            });
        } catch (e) {
            scope.unAuthenticate();
        }
        return false;
    }

    unsetCookie(token) {
        const scope = this;
        try {
            return axios.post('http://prelaunchbuilder.local/api/unsetCookie', {
                token,
            }, {
                headers: { Authorization: `Bearer ${token}` },
            });
        } catch (e) {
            scope.unAuthenticate();
        }
        return false;
    }

    authenticateMe(token) {
        const scope = this;
        try {
            return axios.post('/api/associate/me', {
                token,
            }, { headers: { Authorization: `Bearer ${token}` } });
        } catch (e) {
            scope.unAuthenticate();
        }
        return false;
    }

    authenticateGetApi(url, token) {
        const scope = this;
        try {
            return axios.get(url, {
                headers: { Authorization: `Bearer ${token}` },
            });
        } catch (e) {
            scope.unAuthenticate();
        }
        return false;
    }

    authenticateInvitationPostApi(url, token, data) {
        const scope = this;
        try {
            return axios.post(url, {
                email: data.invitationEmail,
                fullName: data.fullName,
                invitationId: data.invitationId,
                page: data.page,
                verifyResponseKey: data.verifyResponseKey,
            }, {
                headers: { Authorization: `Bearer ${token}` },
            });
        } catch (e) {
            scope.unAuthenticate();
        }
        return false;
    }

    profileUpdate(url, token, formData = null) {
        const scope = this;
        try {
            return axios.post(url, formData, {
                headers: {
                    Authorization: `Bearer ${token}`,
                    'Content-Type': 'multipart/form-data',
                },
            });
        } catch (e) {
            scope.unAuthenticate();
        }
        return false;
    }

    downloadCSVApi(token) {
        const scope = this;
        try {
            return axios.get('/api/admin/csv', {
                responseType: 'blob',
                headers: {
                    Authorization: `Bearer ${token}`,
                    'Content-Type': 'application/force-download',
                    'Content-Disposition': 'attachment; filename=associates.csv',
                },
            });
        } catch (e) {
            scope.unAuthenticate();
        }
        return false;
    }

    galleryGetApi(url, token, parameters) {
        const scope = this;
        try {
            return axios.get(url, {
                params: {
                    page: parameters.page,
                    imageLimit: parameters.imageLimit,
                    category: parameters.category,
                },
                headers: { Authorization: `Bearer ${token}` },
            });
        } catch (e) {
            scope.unAuthenticate();
        }
        return false;
    }

    galleryDeleteApi(url, token, parameters) {
        const scope = this;
        try {
            return axios.delete(url, {
                headers: { Authorization: `Bearer ${token}` },
                data: {
                    parameters,
                },
            });
        } catch (e) {
            scope.unAuthenticate();
        }
        return false;
    }

    getLogs(url, parameters, token) {
        const scope = this;
        try {
            return axios.get(url, {
                params: {
                    page: parameters,
                },
                headers: { Authorization: `Bearer ${token}` },
            });
        } catch (e) {
            scope.unAuthenticate();
        }
        return false;
    }

    associateInfo(url, formData, token) {
        const scope = this;
        try {
            return axios.post(url, formData, {
                headers: {
                    Authorization: `Bearer ${token}`,
                    'Content-Type': 'multipart/form-data',
                },
            });
        } catch (e) {
            scope.unAuthenticate();
        }
        return false;
    }

    endPrelaunch(url, formData, token) {
        const scope = this;
        try {
            return axios.post(url, formData, {
                headers: {
                    Authorization: `Bearer ${token}`,
                    'Content-Type': 'multipart/form-data',
                },
            });
        } catch (e) {
            scope.unAuthenticate();
        }
        return false;
    }

    changeContent(url, formData, token) {
        const scope = this;
        try {
            return axios.post(url, formData, {
                headers: {
                    Authorization: `Bearer ${token}`,
                    'Content-Type': 'multipart/form-data',
                },
            });
        } catch (e) {
            scope.unAuthenticate();
        }
        return false;
    }

    emailTemplates(url, formData, token) {
        const scope = this;
        try {
            return axios.post(url, formData, {
                headers: {
                    Authorization: `Bearer ${token}`,
                    'Content-Type': 'multipart/form-data',
                },
            });
        } catch (e) {
            scope.unAuthenticate();
        }
        return false;
    }
}
