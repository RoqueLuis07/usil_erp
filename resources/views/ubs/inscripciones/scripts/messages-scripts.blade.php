{{-- Success-Error Messages --}}
@if (session('success-message'))
    <input type="text" id="success-message" value="{{ session('success-message') }}">
@endif
@if (session('error-message'))
    <input type="text" id="error-message" value="{{ session('error-message') }}">
@endif
@if (session('error'))
    <input type="text" id="error" value="{{ session('error') }}">
@endif
{{-- /Success-Error Messages --}}
