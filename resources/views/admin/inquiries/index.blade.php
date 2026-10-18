@extends('admin.layouts.app')

@section('title', 'Quản Lý Hộp Thư Liên Hệ')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-1">Hộp Thư Liên Hệ</h4>
        <p class="text-muted small mb-0">Tiếp nhận và phản hồi thông điệp, ý kiến đóng góp từ khách hàng</p>
    </div>
</div>

<!-- Thống kê trạng thái nhanh -->
<div class="row g-3 mb-4">
    <div class="col-12 col-md-4">
        <a href="{{ route('admin.inquiries.index', ['status' => 'new']) }}" class="text-decoration-none">
            <div class="admin-card mb-0 d-flex align-items-center justify-content-between p-3 {{ request('status') === 'new' ? 'border-danger bg-danger-subtle' : '' }}">
                <div>
                    <span class="text-muted small d-block">Yêu Cầu Mới Chưa Xử Lý</span>
                    <h4 class="fw-bold mb-0 text-danger">{{ $statusCounts['new'] }}</h4>
                </div>
                <div class="fs-2 text-danger"><i class="bi bi-envelope-exclamation-fill"></i></div>
            </div>
        </a>
    </div>
    <div class="col-12 col-md-4">
        <a href="{{ route('admin.inquiries.index', ['status' => 'contacted']) }}" class="text-decoration-none">
            <div class="admin-card mb-0 d-flex align-items-center justify-content-between p-3 {{ request('status') === 'contacted' ? 'border-warning bg-warning-subtle' : '' }}">
                <div>
                    <span class="text-muted small d-block">Đang Liên Hệ Trao Đổi</span>
                    <h4 class="fw-bold mb-0 text-warning">{{ $statusCounts['contacted'] }}</h4>
                </div>
                <div class="fs-2 text-warning"><i class="bi bi-telephone-outbound-fill"></i></div>
            </div>
        </a>
    </div>
    <div class="col-12 col-md-4">
        <a href="{{ route('admin.inquiries.index', ['status' => 'resolved']) }}" class="text-decoration-none">
            <div class="admin-card mb-0 d-flex align-items-center justify-content-between p-3 {{ request('status') === 'resolved' ? 'border-success bg-success-subtle' : '' }}">
                <div>
                    <span class="text-muted small d-block">Đã Giải Quyết Xong</span>
                    <h4 class="fw-bold mb-0 text-success">{{ $statusCounts['resolved'] }}</h4>
                </div>
                <div class="fs-2 text-success"><i class="bi bi-check-circle-fill"></i></div>
            </div>
        </a>
    </div>
</div>

<div class="admin-card">
    <!-- Filter and Search -->
    <form action="{{ route('admin.inquiries.index') }}" method="GET" class="row g-2 mb-3">
        <div class="col-md-6">
            <input type="text" name="search" class="form-control" placeholder="Tìm theo người gửi, email, số điện thoại, tiêu đề..." value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">-- Tất cả trạng thái --</option>
                <option value="new" {{ request('status') === 'new' ? 'selected' : '' }}>Mới nhận</option>
                <option value="contacted" {{ request('status') === 'contacted' ? 'selected' : '' }}>Đang liên hệ</option>
                <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Đã giải quyết</option>
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-outline-success w-100">
                <i class="bi bi-search me-1"></i> Tìm Kiếm
            </button>
            @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('admin.inquiries.index') }}" class="btn btn-outline-secondary">Xóa</a>
            @endif
        </div>
    </form>

    <!-- Table -->
    <div class="table-responsive">
        <table class="table align-middle table-hover">
            <thead>
                <tr>
                    <th style="width: 40px;">#</th>
                    <th>Người Gửi</th>
                    <th>Chủ Đề & Nội Dung Tóm Tắt</th>
                    <th>Liên Hệ</th>
                    <th class="text-center">Trạng Thái</th>
                    <th>Thời Gian</th>
                    <th class="text-end" style="width: 130px;">Thao Tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($inquiries as $inquiry)
                    <tr class="{{ $inquiry->status === 'new' ? 'table-warning fw-semibold' : '' }}">
                        <td>
                            @if($inquiry->status === 'new')
                                <i class="bi bi-envelope-fill text-danger" title="Mới"></i>
                            @else
                                <i class="bi bi-envelope-open text-muted"></i>
                            @endif
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $inquiry->name }}</div>
                            <small class="text-muted">{{ $inquiry->email }}</small>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $inquiry->subject }}</div>
                            <small class="text-muted text-truncate d-inline-block" style="max-width: 320px;">
                                {{ Str::limit($inquiry->message, 80) }}
                            </small>
                        </td>
                        <td>
                            <div><i class="bi bi-telephone small text-muted me-1"></i> {{ $inquiry->phone ?? 'N/A' }}</div>
                            @if($inquiry->company)
                                <small class="text-muted"><i class="bi bi-building small me-1"></i> {{ $inquiry->company }}</small>
                            @endif
                        </td>
                        <td class="text-center">
                            {!! $inquiry->status_badge !!}
                        </td>
                        <td>
                            <span class="text-muted small">{{ $inquiry->created_at->format('d/m/Y H:i') }}</span>
                        </td>
                        <td class="text-end">
                            <div class="btn-group">
                                <a href="{{ route('admin.inquiries.show', $inquiry) }}" class="btn btn-sm btn-outline-primary" title="Xem chi tiết & phản hồi">
                                    <i class="bi bi-chat-left-dots"></i>
                                </a>
                                <form action="{{ route('admin.inquiries.destroy', $inquiry) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa tin nhắn này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            Hộp thư trống. Không có yêu cầu liên hệ nào.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($inquiries->hasPages())
        <div class="d-flex justify-content-center mt-3">
            {{ $inquiries->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection
