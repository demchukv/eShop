@php
    $bread_crumb['page_main_bread_crumb'] = labels('front_messages.blogs', 'Blogs');
@endphp
<div id="page-content">
    <x-utility.breadcrumbs.breadcrumbTwo :$bread_crumb />
    <div class="container-fluid">
        <div class="toolbar toolbar-wrapper blog-toolbar">
            <div class="row align-items-center">
                <div
                    class="col-12 col-sm-6 col-md-6 col-lg-6 text-left filters-toolbar-item d-flex justify-content-center justify-content-sm-start">
                    <div class="search-form mb-3 mb-sm-0">
                        <input wire:model.live.debounce.250ms="search" class="search-input" type="text"
                            placeholder="Blog search..." value="{{ $search }}">
                        <button wire:ignore class="search-btn"><ion-icon name="search-outline"
                                class="icon fs-5"></ion-icon></button>
                    </div>
                </div>
                <div
                    class="col-12 col-sm-6 col-md-6 col-lg-6 text-right filters-toolbar-item d-flex justify-content-between justify-content-sm-end">
                    <div class="filters-item d-flex align-items-center">
                        <label for="ShowBy" class="mb-0 me-2">{{ labels('front_messages.show', 'Show') }}:</label>
                        <select name="ShowBy" id="perPage" class="filters-toolbar-sort">
                            <option value="9" {{ $perPage == '9' ? 'selected' : '' }}>9</option>
                            <option value="18" {{ $perPage == '18' ? 'selected' : '' }}>18</option>
                            <option value="27" {{ $perPage == '27' ? 'selected' : '' }}>27</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        @if ($blogs_count >= 1)
            <!--Blog Grid-->
            <div class="blog-grid-view">
                <div class="row col-row row-cols-lg-3 row-cols-sm-2 row-cols-1">
                    @foreach ($blogs['listing'] as $blog)
                        <div class="blog-item col-item">
                            <div class="blog-article zoomscal-hov">
                                <div class="blog-img">
                                    @php
                                        $image = dynamic_image($blog->image, 600);
                                    @endphp
                                    <a wire:navigate
                                        class="featured-image rounded-0 zoom-scal d-flex justify-content-center align-items-center"
                                        href="{{ customUrl('blogs/' . $blog->slug) }}"><img
                                            class="rounded-0 blur-up lazyload" data-src="{{ $image }}"
                                            src="{{ $image }}" alt="{{ $blog->title }}" /></a>
                                </div>
                                <div class="blog-content">
                                    <h2 class="h3"><a wire:navigate
                                            href="{{ customUrl('blogs/' . $blog->slug) }}">{{ $blog->title }}</a></h2>
                                    <ul class="publish-detail d-flex-wrap">
                                        <li><i class="icon anm anm-clock-r"></i> <time
                                                datetime="{{ $blog->created_at }}">{{ $blog->created_at }}</time></li>
                                    </ul>
                                    <div class="content">{!! Str::limit($blog->description, 100) !!}</div>
                                    <a wire:navigate href="{{ customUrl('blogs/' . $blog->slug) }}"
                                        class="btn btn-brd btn-sm">{{ labels('front_messages.read_more', 'Read more') }}</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-2">{!! $blogs['links'] !!}</div>
            </div>
        @else
            @php
                $title = labels('front_messages.no_blog_found', 'No Blog Found!');
            @endphp
            <x-utility.others.not-found :$title />
        @endif
    </div>
</div>
