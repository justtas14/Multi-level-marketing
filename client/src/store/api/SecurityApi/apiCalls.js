import axios from 'axios';
import router from '../../../router';
import store from '../..';

export default class SecurityApiCalls {
    unAuthenticate() {
        store.commit('logout');
        router.push('/login');
    }

    setCookie(token) {
        try {
            return axios.post('http://prelaunchbuilder.local/api/setCookie', {
                token,
            }, {
                headers: { Authorization: `Bearer ${token}` },
                withCredentials: true,
            });
        } catch (e) {
            this.unAuthenticate();
        }
        return false;
    }

    unsetCookie(token) {
        try {
            return axios.post('http://prelaunchbuilder.local/api/unsetCookie', {
                token,
            }, {
                headers: { Authorization: `Bearer ${token}` },
            });
        } catch (e) {
            this.unAuthenticate();
        }
        return false;
    }

    authenticateMe(token) {
        try {
            return axios.post('/api/associate/me', {
                token,
            }, { headers: { Authorization: `Bearer ${token}` } });
        } catch (e) {
            this.unAuthenticate();
        }
        return false;
    }

    authenticateGetApi(url, token) {
        try {
            return axios.get(url, {
                headers: { Authorization: `Bearer ${token}` },
            });
        } catch (e) {
            this.unAuthenticate();
        }
        return false;
    }

    authenticateInvitationPostApi(url, token, data) {
        try {
            return axios.post(url, {
                email: data.invitationEmail,
                fullName: data.fullName,
                invitationId: data.invitationId,
                page: data.page,
            }, {
                headers: { Authorization: `Bearer ${token}` },
            });
        } catch (e) {
            this.unAuthenticate();
        }
        return false;
    }

    downloadCSVApi(token) {
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
            this.unAuthenticate();
        }
        return false;
    }

    galleryGetApi(url, token, parameters) {
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
            this.unAuthenticate();
        }
        return false;
    }

    galleryDeleteApi(url, token, parameters) {
        try {
            return axios.delete(url, {
                headers: { Authorization: `Bearer ${token}` },
                data: {
                    parameters,
                },
            });
        } catch (e) {
            this.unAuthenticate();
        }
        return false;
    }

    getLogs(url, parameters, token) {
        try {
            return axios.get(url, {
                params: {
                    page: parameters,
                },
                headers: { Authorization: `Bearer ${token}` },
            });
        } catch (e) {
            this.unAuthenticate();
        }
        return false;
    }
}
