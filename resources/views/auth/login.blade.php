@extends('layouts.master-without-nav')
@section('title')
Bienvenido
@endsection
@section('css')
    <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
    <style>
        /* Identidad del sistema (auditoría UX): teal, esquinas rectas, sin lila */
        body, .auth-page-wrapper{background:#f7f9f9 !important;font-family:'Open Sans','Segoe UI',system-ui,sans-serif;}
        .auth-page-wrapper .card{border-radius:0 !important;border:1px solid #d3dade !important;box-shadow:none !important;}
        .auth-page-wrapper .form-control, .auth-page-wrapper .input-group-text{border-radius:2px !important;border-color:#d3dade !important;background:#fff !important;}
        .auth-page-wrapper .input-group-text{border-left:3px solid #0f4c5c !important;color:#0f4c5c;}
        .auth-page-wrapper .form-control:focus{border-color:#0f4c5c !important;box-shadow:0 0 0 1px #0f4c5c !important;}
        .auth-page-wrapper .form-check-input:checked{background-color:#0f4c5c !important;border-color:#0f4c5c !important;}
        .auth-page-wrapper .btn-primary{background:#0f4c5c !important;border:1px solid #0f4c5c !important;border-radius:2px !important;}
        .auth-page-wrapper .btn-primary:hover{background:#0c3b47 !important;}
        .auth-page-wrapper .auth-card{background:#0f4c5c !important;border-radius:0 !important;}
        .auth-page-wrapper .text-muted{color:#5c6a6e !important;}
        .marca-login{display:flex;align-items:center;justify-content:center;gap:10px;margin-bottom:18px;}
        .marca-login span{font-size:14px;font-weight:700;color:#1b2427;line-height:1.25;text-align:left;}
    </style>
@endsection

@include('index.scripts.messages-scripts')

@section('content')

<section class="auth-page-wrapper py-5 position-relative bg-light d-flex align-items-center justify-content-center min-vh-100">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-11">
                <div class="card mb-0">
                    <div class="card-body">
                        <div class="row g-0 align-items-center">
                            <div class="col-xxl-6 mx-auto">
                                <div class="card mb-0 border-0 shadow-none mb-0">
                                    <div class="card-body p-sm-5 m-lg-4">
                                        <div class="text-center mt-5">
                                            <div class="marca-login"><svg width="34" height="34" viewBox="0 0 28 28" fill="none"><path d="M14 3L26 9.5V18.5L14 25L2 18.5V9.5L14 3Z" stroke="#0f4c5c" stroke-width="1.6" stroke-linejoin="round"/><path d="M14 3V14M14 14L26 9.5M14 14L2 9.5M14 14V25" stroke="#0f4c5c" stroke-width="1.6" stroke-linejoin="round"/></svg><span>Sistema Académico<br>Extensión Universitaria</span></div>
                                            <h5 class="fs-3xl">Bienvenido!</h5>
                                            <p class="text-muted">Inicia tu sesión para continuar a {{config('app.name')}}</p>
                                        </div>
                                        <div class="p-2 mt-5">
                                            <form action="{{ route('login')}}" method="post">
                                                @csrf
                                                <div class="mb-3">
                                                    <div class="input-group">
                                                        <span class="input-group-text" id="basic-addon"><i class="ri-user-3-line"></i></span>
                                                        <input type="text" class="form-control @error('email') is-invalid @enderror" id="username"  name="email" value="{{ old('email') }}" placeholder="Ingrese su correo electrónico">
                                                        @error('email')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <div class="position-relative auth-pass-inputgroup overflow-hidden">
                                                        <div class="input-group">
                                                            <span class="input-group-text" id="basic-addon1"><i class="ri-lock-2-line"></i></span>
                                                            <input type="password" class="form-control pe-5 password-input @error('password') is-invalid @enderror" placeholder="Ingrese su contraseña" id="password-input" name="password">
                                                            @error('password')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                            @enderror
                                                        </div>
                                                        <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon" type="button" id="password-addon"><i class="ri-eye-fill align-middle"></i></button>
                                                    </div>
                                                </div>
                                                <div class="float-end">
                                                    {{-- <a href="auth-pass-reset" class="text-muted">Olvidaste tu contraseña?</a> --}}
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" value="" id="auth-remember-check">
                                                    <label class="form-check-label" for="auth-remember-check">Recordar</label>
                                                </div>
                                                <div class="mt-4">
                                                    <button class="btn btn-primary w-100" type="submit">Iniciar sesión</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div><!-- end card body -->
                                </div><!-- end card -->
                            </div>
                            <!--end col-->
                            <div class="col-xxl-5">
                                <div class="card auth-card h-100 border-0 shadow-none d-none d-sm-block mb-0">
                                    <div class="card-body py-5 d-flex justify-content-between flex-column">
                                        <div class="text-center">
                                            <h5 class="text-white">Que gusto verte de nuevo!</h5>
                                            <p class="text-white opacity-75">Inserte sus credenciales para trabajar con nosotros.</p>
                                        </div>
                                        <div class="auth-effect-main my-5 position-relative rounded-circle d-flex align-items-center justify-content-center mx-auto">
                                            <div class="auth-user-list list-unstyled">
                                                <img src="{{asset('storage/auth/signin.png')}}" alt="" class="img-fluid">
                                            </div>
                                        </div>
                                        <div class="text-center">
                                            <p class="text-white opacity-75 mb-0 mt-3">
                                                &copy;
                                                <script>
                                                    document.write(new Date().getFullYear())

                                                </script> Creado con <i class="ti ti-heart-filled text-danger"></i> by BSoft
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--end col-->
                        </div>
                        <!--end row-->
                    </div>
                </div>
            </div>
            <!--end col-->
        </div>
        <!--end row-->
    </div>
    <!--end container-->
</section>
@endsection
@section('script')

<script src="{{ URL::asset('build/js/pages/password-addon.init.js') }}"></script>
<script src="{{ URL::asset('build/libs/swiper/swiper-bundle.min.js') }}"></script>
<script src="{{ URL::asset('build/js/pages/swiper.init.js') }}"></script>
<script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>
@include('index.scripts.index-scripts')

@endsection
