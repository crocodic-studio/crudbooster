@extends("crudbooster::admin_template")
@section("content")
    @if(g('path'))
        @php
            $path = g('path');
            $path = base64_decode($path);
            $path = explode('/',$path);
            array_pop($path);
            $prev = implode('/',$path);
            if($prev == 'uploads') {
                $link = route('AdminFileManagerControllerGetIndex');
            }else{
                $link = route('AdminFileManagerControllerGetIndex').'?path='.base64_encode($prev);
            }
        @endphp

    @endif
    <p>
        @if(g('path')!='uploads')
            <a href="{{$link}}" class="btn btn-primary {{$link==''?'disabled':''}}"><i class="fa fa-arrow-left"></i>
                Previous</a>
        @endif
        <a class="btn btn-primary" href="javascript:void(0)" onclick="showModalCreateDirectory()"><i
                    class="fa fa-plus"></i> Create a Directory</a>
        <a class="btn btn-primary" href="javascript:void(0)" onclick="showModalUpload()"><i class="fa fa-upload"></i>
            Upload a file</a>
    </p>
    <div class="box box-default">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-cubes"></i> File Manager</h3>
        </div>
        <div class="box-body table-responsive no-padding">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>File Name</th>
                    <th>Type</th>
                    <th>File Size</th>
                    <th>File Time</th>
                    <th width="160px">Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach($directories as $r)
                    @php
                        $link = route('AdminFileManagerControllerGetIndex').'?path='.base64_encode($r);
                    @endphp
                    <tr>

                        <td><a href="{{$link}}">{{$r}}</a></td>
                        <td>Directory</td>
                        <td>-</td>
                        <td>{{ date('Y-m-d H:i:s',Storage::lastModified($r)) }}</td>
                        <td>
                            <a class="btn btn-danger btn-sm"
                               href="{{route('AdminFileManagerControllerGetDeleteDirectory')}}/{{base64_encode($r)}}">Delete</a>
                        </td>
                    </tr>
                @endforeach

                @foreach($files as $r)
                    <?php
                    $ext = pathinfo($r, PATHINFO_EXTENSION);
                    $link = url($r);
                    ?>
                    <tr>

                        @if(in_array(strtolower($ext),['jpg','png','gif','jpeg','bmp']))
                            <td>
                                <a target="_blank" href="{{$link}}" target="_blank"><img class="thumbnail"
                                                                                         src="{{$link}}" width="100px"
                                                                                         height="100px"></a>
                            </td>
                        @else
                            <td><a target="_blank" href="{{$link}}">{{$r}}</a></td>
                        @endif

                        <td>File</td>
                        <td>{{ round(Storage::size($r)/1024,2) }} KB</td>
                        <td>{{ date('Y-m-d H:i:s',Storage::lastModified($r)) }}</td>
                        <td>
                            <a class="btn btn-danger btn-sm"
                               href="{{route('AdminFileManagerControllerGetDeleteFile')}}/{{base64_encode($r)}}">Delete</a>
                        </td>
                    </tr>
                @endforeach


                </tbody>
            </table>
        </div>
    </div>

    @push('bottom')
        <script type="text/javascript">
            function showModalCreateDirectory() {
                $('#modal-create-directory').modal('show');
            }
        </script>


        @include('CbFileManager::partials.create_folder_modal')



        <script type="text/javascript">
            function showModalUpload() {
                $('#modal-upload-file').modal('show');
            }
        </script>

        @include('CbFileManager::partials.file_upload_modal')

    @endpush
@endsection