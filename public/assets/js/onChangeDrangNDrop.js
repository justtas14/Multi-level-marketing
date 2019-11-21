function onChangeEvent()
{
    originalProfilePictureUpload.onchange = (e) => {
        if (submitUploadErrorBlock) {
            submitUploadErrorBlock.style.display = 'none';
        }
        const imageMimeTypes = ['image/png', 'image/jpeg', 'image/webp'];
        const input = e.target;
        let isImage = false;
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = (e) => {
                const file = input.files[0];
                if (imageMimeTypes.includes(file.type)) {
                    isImage = true;
                } else {
                    originalProfilePictureUpload.value = '';
                    profilePicturePreview.style.display = 'none';
                    dragAndDropError.style.display = 'block';
                    dragAndDropError.innerHTML = 'Only images are allowed';
                }
            };
            reader.onloadend = () => {
                if (isImage) {
                    const img = profilePicturePreview.querySelector('img');
                    img.src = reader.result;
                    profilePicturePreview.style.display = 'block';
                    dragAndDropElement.style.display = 'none';
                    dragAndDropError.style.display = 'none';
                }
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
}