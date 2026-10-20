<div class="mb-3">
    <label for="image" class="form-label fw-semibold">Ảnh đại diện danh mục</label>
    <div class="category-image-field" data-category-image-field>
        <div class="category-image-preview"><img src="{{ isset($category) ? $category->image_url : asset('assets/images/category-placeholder.svg') }}" data-fallback="{{ isset($category) ? $category->fallback_image_url : asset('assets/images/category-placeholder.svg') }}" alt="Ảnh xem trước của danh mục" width="180" height="180"></div>
        <div class="category-image-controls">
            <input type="file" name="image" id="image" class="form-control" accept="image/jpeg,image/png,image/webp" aria-describedby="category-image-help category-image-error">
            <div class="form-text" id="category-image-help">Chọn ảnh JPG, PNG hoặc WEBP, tối đa 2MB. Ảnh sẽ được hiển thị trọn vẹn trong khung vuông.</div>
            <p class="category-image-filename" data-image-filename>{{ isset($category) ? 'Ảnh hiện tại của danh mục' : 'Chưa chọn ảnh' }}</p>
            <button type="button" class="btn btn-sm btn-outline-secondary category-image-reset" data-image-reset hidden>Hủy chọn ảnh mới</button>
            <div class="category-image-error" id="category-image-error" data-image-error role="status" aria-live="polite"></div>
        </div>
    </div>
</div>
