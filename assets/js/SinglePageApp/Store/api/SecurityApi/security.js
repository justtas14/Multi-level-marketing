import axios from "axios";
import router from "../../../Router/index";
import store from '../../../Store';

export default {
    login(login, password) {
        return axios.post("/api/token", {
            email: login,
                password: password
        });
    },
    unAuthenticate() {
        store.commit('Security/logout');
        router.push('/login');
    },
    authenticateMe(token) {
        try {
            return axios.post('/api/associate/me', {
                token: token
            }, {headers: {"Authorization" : `Bearer ${token}`} });
        } catch (e) {
            this.unAuthenticate();
        }
    },
    authenticateGetApi(url, token) {
        try {
            return axios.get(url, {
                headers: {"Authorization" : `Bearer ${token}`}
            });
        } catch (e) {
            this.unAuthenticate();
        }
    },
    downloadCSVApi(token) {
        try {
            return axios.get('api/admin/csv', {
                responseType: 'blob',
                headers: {
                    "Authorization" : `Bearer ${token}`,
                    'Content-Type': 'application/force-download',
                    'Content-Disposition': 'attachment; filename=associates.csv'
                }
            });
        } catch (e) {
            this.unAuthenticate();
        }
    },
    }