function readURL(input)
{
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            const file = input.files[0];

            if (/^image\//.test(file.type)) {
                saveToServer(file, file.name);
            } else {
                console.warn('You could only upload images.');
            }
        };
        reader.readAsDataURL(input.files[0]);
    } else {
        removeUpload();
    }
}

function saveToServer(file, name)
{
    const fd = new FormData();
    fd.append('image', file);
    const xhr = new XMLHttpRequest();
    xhr.open('POST', '/uploadFile', true);
    xhr.onload = () => {
        if (xhr.status === 200 && xhr.readyState === 4) {
            const url = JSON.parse(xhr.responseText);
            insertImage(url, name);
        }
    };
    xhr.send(fd);
}

function insertImage(url, name)
{
    $('.”gallery”').append(`<figure class="gallery__item gallery__item--1"><img src="${url}" class="gallery__img" alt="${name}"> </figure>`);
}

function removeUpload()
{
    $('.file-upload-input').replaceWith($('.file-upload-input').clone());
    $('.file-upload-content').hide();
}

$('.image-upload-wrap').bind('dragover', function () {
    $('.image-upload-wrap').addClass('image-dropping');
});
$('.image-upload-wrap').bind('dragleave', function () {
    $('.image-upload-wrap').removeClass('image-dropping');
});
