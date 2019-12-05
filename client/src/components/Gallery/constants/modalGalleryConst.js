export default {
    galleryClasses: {
        galleryId: 'modalGallery',
        closeButtonClasses: {
            closeIcon: 'modalCloseIcon',
        },
    },
    fileCutLength: 10,
    downloadable: false,
    uploadable: true,
    mqls: [
        window.matchMedia('(max-width: 550px)'),
        window.matchMedia('(max-width: 1350px)'),
    ],
};
