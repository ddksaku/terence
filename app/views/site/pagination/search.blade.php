@if(!$paginator->isEmpty())
    <?php

    $lastPage = $paginator->getLastPage();
    $currentPage = $paginator->getCurrentPage();

    ?>

    @if($lastPage > 1)
        <div class="pagination-2">
            <ul>
                @if($currentPage != 1)
                    <li class="first-page"><a href="{{ $paginator->getUrl($currentPage - 1) }}"><i class="icon-left-open"></i></a></li>
                @endif
                
                @for($i = 1; $i <= $lastPage; $i++)
                    <li>
                        <a href="{{ $paginator->getUrl($i) }}" class="@if($i == $currentPage) active @endif">
                            {{ $i }}
                        </a>
                    </li>
                @endfor

                @if($currentPage != $lastPage)
                    <li class="last-page"><a href="{{ $paginator->getUrl($currentPage + 1) }}"><i class="icon-right-open"></i></a></li>
                @endif
            </ul>
        </div>
    @endif
@endif