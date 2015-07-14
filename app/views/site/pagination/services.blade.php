<?php

$lastPage = $paginator->getLastPage();
$currentPage = $paginator->getCurrentPage();

?>

@if($lastPage > 1)
    <div class="timeline-nav">
        <div class="timeline-dot"></div>
        <ul>
            @if($currentPage != $lastPage)
                <li class="left-nav"><a href="{{ $paginator->getUrl($currentPage + 1) }}"><i class="icon-left-open"></i></a></li>
            @endif
            
            @if($currentPage != 1)
                <li class="right-nav"><a href="{{ $paginator->getUrl($currentPage - 1) }}"><i class="icon-right-open"></i></a></li>
            @endif
        </ul>
    </div>
@endif
