{{-- hien thong bao dang ky thanh cong --}}
@if (Session::has('success'))
    <div class="alert alert-success">
        {{ Session::get('success') }}
    </div>
 @endif
    {{-- hien thong bao dang nhap that bai --}}
    @if (Session::has('error'))
    <div class="alert alert-danger">
        {{ Session::get('error') }}
    </div>
@endif