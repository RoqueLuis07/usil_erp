@foreach ($cuentas_contables as $cuenta_contable)
    <li class="list-group-item pt-0" style="margin-left: {{$cuenta_contable->nivel}}em !important">
        {{$cuenta_contable->cuenta}} - {{$cuenta_contable->nombre}}
        @if ($cuenta_contable->hijos->isNotEmpty())
            <ul class="list-group list-group-flush">
                @include('cuentas_contables.subcuentas', ['cuentas_contables' => $cuenta_contable->hijos])
            </ul>
        @endif
    </li>
@endforeach
