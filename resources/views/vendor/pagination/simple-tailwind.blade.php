@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" style="display: flex; justify-content: space-between; align-items: center; gap: 1rem;">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span style="display: inline-flex; align-items: center; padding: 0.5rem 1rem; font-size: 0.875rem; font-weight: 500; color: #9CA3AF; background-color: #F9FAFB; border: 1px solid #D1D5DB; border-radius: 0.375rem; cursor: default;">
                &laquo; Précédent
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" style="display: inline-flex; align-items: center; padding: 0.5rem 1rem; font-size: 0.875rem; font-weight: 500; color: #6B7280; background-color: #FFFFFF; border: 1px solid #D1D5DB; border-radius: 0.375rem; text-decoration: none;">
                &laquo; Précédent
            </a>
        @endif

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" style="display: inline-flex; align-items: center; padding: 0.5rem 1rem; font-size: 0.875rem; font-weight: 500; color: #6B7280; background-color: #FFFFFF; border: 1px solid #D1D5DB; border-radius: 0.375rem; text-decoration: none;">
                Suivant &raquo;
            </a>
        @else
            <span style="display: inline-flex; align-items: center; padding: 0.5rem 1rem; font-size: 0.875rem; font-weight: 500; color: #9CA3AF; background-color: #F9FAFB; border: 1px solid #D1D5DB; border-radius: 0.375rem; cursor: default;">
                Suivant &raquo;
            </span>
        @endif
    </nav>
@endif
