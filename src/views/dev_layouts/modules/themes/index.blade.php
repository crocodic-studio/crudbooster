@extends("crudbooster::dev_layouts.layout")
@section("content")

    <div class="row">
        @foreach($result as $item)
            <div class="col-sm-4">
                <div style="text-align: center">
                    <img class="img-thumbnail" style="width: 100%; height: 145px" src="{{ asset($item['thumbnail']) }}" alt="thumbnail">
                </div>
                <br>
                <p><strong>{{ $item['name'] }}</strong></p>
                <p><small>Author <a href="{{ $item['author_homepage'] }}" target="_blank">{{ $item['author'] }}</a></small></p>
                <p style="height: 60px;">
                    {{ str_limit($item['description'],125) }}
                </p>
                @if(getThemePath() == $item['theme_path'])
                    <a class="btn btn-block btn-default" title="Theme is active" href="javascript:;"><i class="fa fa-check-square-o"></i> Current Theme</a>
                    <a href="javascript:;" onclick="$('#modal-configuration').modal('show');" class="btn btn-block btn-primary" title="Click here to configuration"><i class="fa fa-cog"></i> Theme Configuration</a>
                @else
                    <a class="btn btn-block btn-success" onclick="showConfirmation('Theme activation','Are you sure want to active this theme?',function() { location.href='{{ route("DeveloperThemesControllerGetActiveTheme",['theme_path'=>base64_encode($item['theme_path'])]) }}' })" title="Click here to activate theme" href="javascript:;"><i class="fa fa-picture-o"></i> Activate Theme</a>
                @endif
            </div>
        @endforeach
    </div>


    <div class="modal" id="modal-configuration">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Theme Configuration</h4>
                </div>
                <form method="post" enctype="multipart/form-data" action="{{ route("DeveloperThemesControllerPostSaveConfig") }}">
                    {!! csrf_field() !!}
                    <div class="modal-body">
                        @include(getThemePath("theme_configuration"))
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

@endsection