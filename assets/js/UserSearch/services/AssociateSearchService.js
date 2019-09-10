import axios from 'axios';

const searchUrl = '/admin/api/associates';

export async function findAll()
{
    return axios.get(searchUrl)
        .then(response => {
            return {
                associates: response.data.associates,
                numberOfPages: response.data.pagination.maxPages,
                currentPage: response.data.pagination.currentPage,
            }
        });
}

export async function findBy(params)
{
    return axios.get(searchUrl, { params })
        .then(response => {
            return {
                associates: response.data.associates,
                numberOfPages: response.data.pagination.maxPages,
                currentPage: response.data.pagination.currentPage,
            }
        });
}
