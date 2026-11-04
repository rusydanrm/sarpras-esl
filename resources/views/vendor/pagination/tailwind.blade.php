@if ($paginator->hasPages() || $paginator->total() > 0)
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-between flex-wrap gap-4 select-none">
        {{-- Mobile View --}}
        <div class="flex items-center justify-between flex-1 sm:hidden gap-2">
            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-[#9EA9B9] bg-[#F4F6F5] border border-[#E9EFEF] rounded-xl cursor-not-allowed select-none">
                    <svg class="w-3.5 h-3.5 stroke-[2.2]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                    </svg>
                    <span>Prev</span>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                    class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-[#0A0E2E] bg-white border border-[#E9EFEF] rounded-xl hover:bg-[#F4F6F5] transition-colors cursor-pointer">
                    <svg class="w-3.5 h-3.5 stroke-[2.2]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                    </svg>
                    <span>Prev</span>
                </a>
            @endif

            <span class="text-xs text-[#6C7E75] font-medium">
                {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}
            </span>

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                    class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-[#0A0E2E] bg-white border border-[#E9EFEF] rounded-xl hover:bg-[#F4F6F5] transition-colors cursor-pointer">
                    <span>Next</span>
                    <svg class="w-3.5 h-3.5 stroke-[2.2]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                    </svg>
                </a>
            @else
                <span class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-[#9EA9B9] bg-[#F4F6F5] border border-[#E9EFEF] rounded-xl cursor-not-allowed select-none">
                    <span>Next</span>
                    <svg class="w-3.5 h-3.5 stroke-[2.2]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                    </svg>
                </span>
            @endif
        </div>

        {{-- Desktop View --}}
        <div class="hidden sm:flex sm:items-center sm:justify-between sm:flex-1 gap-4">
            <div>
                <p class="text-[13px] text-[#6C7E75] font-normal">
                    Menampilkan <span class="text-[#0A0E2E] font-medium">{{ $paginator->firstItem() ?? 0 }}</span> sampai <span class="text-[#0A0E2E] font-medium">{{ $paginator->lastItem() ?? 0 }}</span> dari <span class="text-[#0A0E2E] font-medium">{{ $paginator->total() }}</span> data
                </p>
            </div>

            @if ($paginator->hasPages())
                <div class="flex items-center gap-1.5">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}"
                            class="w-8 h-8 flex items-center justify-center text-[#9EA9B9]/50 cursor-not-allowed select-none">
                            <svg class="w-4 h-4 stroke-[2.2]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                            </svg>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                            class="w-8 h-8 flex items-center justify-center text-[#6C7E75] hover:text-[#0A0E2E] hover:bg-[#F4F6F5] rounded-xl transition-colors cursor-pointer"
                            aria-label="{{ __('pagination.previous') }}">
                            <svg class="w-4 h-4 stroke-[2.2]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                            </svg>
                        </a>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <span aria-disabled="true" class="w-8 h-8 flex items-center justify-center text-xs font-semibold text-[#9EA9B9] select-none">
                                {{ $element }}
                            </span>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page"
                                        class="w-8 h-8 flex items-center justify-center rounded-xl bg-[#0A0E2E] text-white text-xs font-bold shadow-xs select-none">
                                        {{ $page }}
                                    </span>
                                @else
                                    <a href="{{ $url }}"
                                        class="w-8 h-8 flex items-center justify-center rounded-xl text-xs font-semibold text-[#495465] hover:text-[#0A0E2E] hover:bg-[#F4F6F5] transition-colors cursor-pointer"
                                        aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                            class="w-8 h-8 flex items-center justify-center text-[#6C7E75] hover:text-[#0A0E2E] hover:bg-[#F4F6F5] rounded-xl transition-colors cursor-pointer"
                            aria-label="{{ __('pagination.next') }}">
                            <svg class="w-4 h-4 stroke-[2.2]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                            </svg>
                        </a>
                    @else
                        <span aria-disabled="true" aria-label="{{ __('pagination.next') }}"
                            class="w-8 h-8 flex items-center justify-center text-[#9EA9B9]/50 cursor-not-allowed select-none">
                            <svg class="w-4 h-4 stroke-[2.2]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                            </svg>
                        </span>
                    @endif
                </div>
            @endif
        </div>
    </nav>
@endif
