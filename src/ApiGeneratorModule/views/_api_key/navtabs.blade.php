<ul class="nav nav-tabs">
    <li>
        <a href="{{ CRUDBooster::mainpath() }}">{!! CB::icon('file') !!}List API</a>
    </li>

    <li>
        <a href="{{ CRUDBooster::mainpath('generator') }}">{!! CB::icon('cog') !!}API Generator</a>
    </li>

    <li class="active">
        <a href="{{ CRUDBooster::mainpath('screet-key') }}">{!! CB::icon('key') !!}API Secret Key</a>
    </li>

    <li>
        <a href="{{ url('api/doc')}}" target="_blank">{!! CB::icon('book') !!}API Documentation</a>
    </li>
</ul>