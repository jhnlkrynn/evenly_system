{{-- Validation errors in the design's error box. --}}
@if ($errors->any())
    <div class="auth-message error" role="alert">
        @foreach ($errors->all() as $message)
            <div>{{ $message }}</div>
        @endforeach
    </div>
@endif
