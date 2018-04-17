<ul class="nav nav-tabs">
    <li>
        <a href="{{ CRUDBooster::mainpath() }}">{!! cbIcon('file') !!}List API</a>
    </li>

    <li>
        <a href="{{ CRUDBooster::mainpath('generator') }}">{!! cbIcon('cog') !!}API Generator</a>
    </li>

    <li class="active">
        <a href="{{ CRUDBooster::mainpath('secret-key') }}">{!! cbIcon('key') !!}API Secret Key</a>
    </li>

    <li>
        <a href="{{ url('api/doc')}}" target="_blank">{!! cbIcon('book') !!}API Documentation</a>
    </li>
</ul>