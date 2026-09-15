@if ($paginator->hasPages())
    <nav class="d-flex justify-items-center justify-content-between">
        {{-- Móvil --}}
        <div class="d-flex justify-content-between flex-fill d-sm-none">
            <ul class="pagination">
                {{-- Anterior --}}
                @if ($paginator->onFirstPage())
                    <li class="page-item disabled"><span class="page-link">@lang('pagination.previous')</span></li>
                @else
                    <li class="page-item"><a class="page-link" href="{{ $paginator->previousPageUrl() }}">@lang('pagination.previous')</a></li>
                @endif

                {{-- Siguiente --}}
                @if ($paginator->hasMorePages())
                    <li class="page-item"><a class="page-link" href="{{ $paginator->nextPageUrl() }}">@lang('pagination.next')</a></li>
                @else
                    <li class="page-item disabled"><span class="page-link">@lang('pagination.next')</span></li>
                @endif
            </ul>
        </div>

        {{-- Escritorio --}}
        <div class="d-none flex-sm-fill d-sm-flex align-items-sm-center justify-content-sm-between">
            <div>
                <p class="small text-muted">
                    @lang('pagination.showing')
                    <span class="fw-semibold">{{ $paginator->firstItem() }}</span>
                    @lang('pagination.to')
                    <span class="fw-semibold">{{ $paginator->lastItem() }}</span>
                    @lang('pagination.of')
                    <span class="fw-semibold">{{ $paginator->total() }}</span>
                    @lang('pagination.results')
                </p>
            </div>

			@php
				$current = $paginator->currentPage();
				$last = $paginator->lastPage();
				$start = max(1, $current - 1);
				$end = min($last, $start + 2);

				if ($end - $start < 2) {
					$start = max(1, $end - 2);
				}
			@endphp

            <div>
                <ul class="pagination">
					{{-- Anterior --}}
					@if ($paginator->onFirstPage())
						<li class="page-item mx-1">
							<span class="page-link rounded">
								<
							</span>
						</li>
					@else
						<li class="page-item mx-1">
							<a class="page-link rounded" href="{{ $paginator->previousPageUrl() }}" rel="prev">
								<
							</a>
						</li>
					@endif

					{{-- Páginas --}}
					@for ($page = $start; $page <= $end; $page++)
						@if ($page == $current)
							<li class="page-item active mx-1" aria-current="page">
								<span class="page-link rounded">{{ $page }}</span>
							</li>
						@else
							<li class="page-item mx-1">
								<a class="page-link rounded" href="{{ $paginator->url($page) }}">{{ $page }}</a>
							</li>
						@endif
					@endfor

					{{-- Puntos suspensivos --}}
					@if ($end < $last)
						<li class="page-item disabled mx-1">
							<span class="page-link rounded" aria-disabled="true">…</span>
						</li>
					@endif

					{{-- Siguiente --}}
					@if ($paginator->hasMorePages())
						<li class="page-item mx-1">
							<a class="page-link rounded" href="{{ $paginator->nextPageUrl() }}" rel="next">
								>
							</a>
						</li>
					@else
						<li class="page-item mx-1">
							<span class="page-link rounded">
								>
							</span>
						</li>
					@endif
				</ul>
            </div>
        </div>
    </nav>
@endif
