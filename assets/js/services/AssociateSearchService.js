import axios from 'axios';

const searchUrl = '/admin/api/associates';


export async function findAll(){
    return axios.get(searchUrl)
        .then(response => {
            return {
                associates: response.data.associates,
                pages: response.data.pagination.maxPages,
                currentPage: response.data.pagination.currentPage,
            }
        });
}

export function findBy(params){
    return axios.get(searchUrl, { params })
        .then(response => {

            return {
                associates: response.data.associates,
                pages: response.data.pagination.maxPages,
                currentPage: response.data.pagination.currentPage,
            }
        });
}
