@if($cmsBackgroundColor = getSetting("cms_background_color"))
    <style>.content-wrapper { background-color: {{ $cmsBackgroundColor }} }</style>
@endif

@if($fontSize = getSetting("cms_font_size"))
    <style>body { font-size: {{ $fontSize }}px }</style>
@endif

@if($color = getSetting("cms_body_color"))
    <style>
        body { color: {{ $color }} }
        .content-header>.breadcrumb>li>a { color: {{ $color }} }
        .breadcrumb>.active { color: {{ $color }} }
    </style>
@endif

@if($additionalCss = getSetting("cms_additional_css"))
    <style>{!! $additionalCss !!}</style>
@endif