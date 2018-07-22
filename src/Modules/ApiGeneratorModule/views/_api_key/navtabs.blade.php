<ul class="nav nav-tabs">
    <li>
        <a href="{{ route('AdminApiGeneratorControllerGetIndex') }}">{!! cbIcon('file') !!} List API</a>
    </li>

    <li>
        <a href="{{ route('AdminApiGeneratorControllerGetGenerator') }}">{!! cbIcon('cog') !!} API Generator</a>
    </li>

    <li class="active">
        <a href="{{ route('AdminApiKeyControllerGetIndex') }}">{!! cbIcon('key') !!} API Secret Key</a>
    </li>

    <li>
        <a href="{{ route('apiDocumentation') }}" target="_blank">{!! cbIcon('book') !!} API Documentation</a>
    </li>
</ul>