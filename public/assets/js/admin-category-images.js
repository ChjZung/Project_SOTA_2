document.querySelectorAll('[data-category-image-field]').forEach(field => {
    const input = field.querySelector('input[type=file]');
    const image = field.querySelector('img');
    const filename = field.querySelector('[data-image-filename]');
    const error = field.querySelector('[data-image-error]');
    const reset = field.querySelector('[data-image-reset]');
    const original = image.src;
    const originalName = filename.textContent;
    let previewUrl;

    const restore = () => {
        if (previewUrl) URL.revokeObjectURL(previewUrl);
        previewUrl = null;
        input.value = '';
        image.src = original;
        filename.textContent = originalName;
        reset.hidden = true;
    };

    input.addEventListener('change', () => {
        error.textContent = '';
        const file = input.files[0];
        if (!file) { restore(); return; }
        if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) {
            restore();
            error.textContent = 'Vui lòng chọn ảnh JPG, PNG hoặc WEBP.';
            return;
        }
        if (file.size > 2 * 1024 * 1024) {
            restore();
            error.textContent = 'Ảnh không được vượt quá 2MB.';
            return;
        }
        if (previewUrl) URL.revokeObjectURL(previewUrl);
        previewUrl = URL.createObjectURL(file);
        image.src = previewUrl;
        filename.textContent = file.name;
        reset.hidden = false;
    });

    image.addEventListener('error', () => {
        if (previewUrl) {
            restore();
            error.textContent = 'Không thể đọc ảnh này. Vui lòng chọn ảnh khác.';
        } else if (image.src !== image.dataset.fallback) {
            image.src = image.dataset.fallback;
        }
    });
    if (image.complete && !image.naturalWidth && image.src !== image.dataset.fallback) {
        image.src = image.dataset.fallback;
    }
    reset.addEventListener('click', () => { restore(); error.textContent = ''; });
    input.form?.addEventListener('reset', () => { restore(); error.textContent = ''; });
});
