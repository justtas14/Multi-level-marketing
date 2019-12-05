export default {
    maxAllowedLength: 190,
    maxAllowedSize: 1000000,
    api: {
        jsonData: '/api/admin/jsonGallery',
        uploadFile: '/api/admin/uploadGalleryFile',
        removeFile: '/api/admin/removeFile',
    },
    imageExtensions: ['jpg', 'jpeg', 'bmp', 'gif', 'png', 'webp', 'ico'],
};
