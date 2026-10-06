{{-- Success-Error Messages --}}
@if (session('success-message'))
    <input type="hidden" id="success-message" value="{{ session('success-message') }}">
@endif
@if (session('error-message'))
    <input type="hidden" id="error-message" value="{{ session('error-message') }}">
@endif
{{-- /Success-Error Messages --}}
