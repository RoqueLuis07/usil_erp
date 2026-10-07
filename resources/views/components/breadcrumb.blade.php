<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">{{ $title }}</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    @if(isset($li_1))
                        <li class="breadcrumb-item"><a href="{{ trim((string) ($href ?? "")) ?: url()->previous() }}">{{ $li_1 }}</a></li>
                    @endif
                    @if(isset($title) && strpos($title, 'BIENVENIDO') === false)
                        <li class="breadcrumb-item active">{{ $title }}</li>
                    @endif
                </ol>
            </div>

        </div>
    </div>
</div>
<!-- end page title -->
