import axios from "axios";

export default {
    login(login, password) {
        return axios.post("/api/token", {
            username: login,
                password: password
        });
    }
    }