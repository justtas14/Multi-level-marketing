import axios from 'axios';

const searchUrl = '/api/admin/associates';

export async function findAll(token) {
    return axios.get(searchUrl, { headers: { Authorization: `Bearer ${token}` } })
        .then(response => ({
            associates: response.data.associates,
            numberOfPages: response.data.pagination.maxPages,
            currentPage: response.data.pagination.currentPage,
        }));
}

export async function findBy(params, token) {
    return axios.get(searchUrl, {
        params,
        headers: { Authorization: `Bearer ${token}` },
    }).then(response => ({
        associates: response.data.associates,
        numberOfPages: response.data.pagination.maxPages,
        currentPage: response.data.pagination.currentPage,
    }));
}
