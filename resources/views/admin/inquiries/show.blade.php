@extends('admin.layouts.app')

@section('title', 'Yêu Cầu Liên Hệ Từ: ' . $inquiry->name)

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-3">
    <div>
        <div class="d-flex align-items-center gap-2">
            <h4 class="fw-bold mb-0">Yêu Cầu Liên Hệ #{{ $inquiry->id }}</h4>
            {!! $inquiry->status_badge !!}
        </div>
        <p class="text-muted small mb-0 mt-1">Gửi lúc {{ $inquiry->created_at->format('d/m/Y \l\ú\c H:i:s') }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="mailto:{{ $inquiry->email }}?subject=Phản hồi từ Avada Vegan Store: {{ $inquiry->subject }}" class="btn btn-outline-success">
            <i class="bi bi-envelope-at me-1"></i> Gửi Email Khách
        </a>
        <a href="{{ route('admin.inquiries.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Quay lại danh sách
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Cột Trái: Nội dung tin nhắn -->
    <div class="col-lg-8">
        <div class="admin-card mb-4">
            <h5 class="fw-bold mb-3 text-dark">
                <i class="bi bi-chat-quote-fill text-success me-2"></i>{{ $inquiry->subject }}
            </h5>

            <div class="p-3 bg-light rounded-3 border mb-4">
                <p class="mb-0 text-dark" style="white-space: pre-wrap; font-size: 0.95rem; line-height: 1.7;">{{ $inquiry->message }}</p>
            </div>

            <!-- Phản hồi của Quản trị viên -->
            <h6 class="fw-bold text-dark mb-2">
                <i class="bi bi-reply-fill text-primary me-1"></i> Ghi Chú Phản Hồi Của Admin
            </h6>
            @if($inquiry->admin_reply)
                <div class="p-3 bg-success-subtle border border-success-subtle rounded-3 text-dark mb-3" style="white-space: pre-wrap;">
                    {{ $inquiry->admin_reply }}
                </div>
            @else
                <p class="text-muted small fst-italic">Chưa có ghi chú phản hồi nào từ quản trị viên.</p>
            @endif
        </div>
    </div>

    <!-- Cột Phải: Thông tin người gửi & Cập nhật trạng thái -->
    <div class="col-lg-4">
        <!-- Thông tin người gửi -->
        <div class="admin-card mb-4">
            <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-person-lines-fill text-success"></i> Người Gửi
            </h5>

            <div class="small">
                <div class="py-2 border-bottom">
                    <span class="text-muted d-block">Họ và tên:</span>
                    <span class="fw-bold text-dark fs-6">{{ $inquiry->name }}</span>
                </div>
                <div class="py-2 border-bottom">
                    <span class="text-muted d-block">Địa chỉ Email:</span>
                    <a href="mailto:{{ $inquiry->email }}" class="fw-semibold text-success text-decoration-none">{{ $inquiry->email }}</a>
                </div>
                <div class="py-2 border-bottom">
                    <span class="text-muted d-block">Số điện thoại:</span>
                    <span class="fw-semibold text-dark">{{ $inquiry->phone ?? 'Không cung cấp' }}</span>
                </div>
                @if($inquiry->company)
                    <div class="py-2 border-bottom">
                        <span class="text-muted d-block">Đơn vị / Công ty:</span>
                        <span class="fw-semibold text-dark">{{ $inquiry->company }}</span>
                    </div>
                @endif
                <div class="py-2">
                    <span class="text-muted d-block">Gửi lúc:</span>
                    <span class="text-dark">{{ $inquiry->created_at->format('d/m/Y H:i:s') }}</span>
                </div>
            </div>
        </div>

        <!-- Form cập nhật trạng thái & Phản hồi -->
        <div class="admin-card mb-0">
            <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-gear-wide-connected text-success"></i> Xử Lý Yêu Cầu
            </h5>

            <form action="{{ route('admin.inquiries.update', $inquiry) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="status" class="form-label fw-semibold small">Trạng thái xử lý</label>
                    <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                        <option value="new" {{ $inquiry->status === 'new' ? 'selected' : '' }}>Mới nhận (New)</option>
                        <option value="contacted" {{ $inquiry->status === 'contacted' ? 'selected' : '' }}>Đang liên hệ khách (Contacted)</option>
                        <option value="resolved" {{ $inquiry->status === 'resolved' ? 'selected' : '' }}>Đã giải quyết xong (Resolved)</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="admin_reply" class="form-label fw-semibold small">Ghi chú / Nội dung phản hồi</label>
                    <textarea name="admin_reply" id="admin_reply" rows="4" class="form-control" placeholder="Ghi lại nội dung đã gọi điện hoặc giải quyết cho khách...">{{ old('admin_reply', $inquiry->admin_reply) }}</textarea>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-admin-primary">
                        <i class="bi bi-check-circle me-1"></i> Lưu Xử Lý
                    </button>
                </div>
            </form>

            <hr class="my-3">

            <form action="{{ route('admin.inquiries.destroy', $inquiry) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa thư liên hệ này?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                    <i class="bi bi-trash me-1"></i> Xóa Thư Này
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
