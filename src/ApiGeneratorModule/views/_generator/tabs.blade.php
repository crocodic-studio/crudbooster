<ul class="nav nav-tabs">
    <li>
        <a href="{{ CRUDBooster::mainpath() }}">{!! CB::icon('file') !!}</i> List API</a>
    </li>

    <li class="active">
        <a href="{{ CRUDBooster::mainpath('generator') }}">{!! CB::icon('cog') !!}</i> API Generator</a>
    </li>

    <li>
        <a href="{{ CRUDBooster::mainpath('screet-key') }}">{!! CB::icon('key') !!}</i> API Secret Key</a>
    </li>

    <li>
        <a href="{{ url('api/doc')}}" target="_blank">{!! CB::icon('book') !!}</i> API Documentation</a>
    </li>
</ul>