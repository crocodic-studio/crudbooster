<div class="box-header with-border">
    <h3 class="box-title">Importing</h3>
    <div class="box-tools">
    </div>
</div>

<div class="box-body">
    <p style='font-weight: bold' id='status-import'><i class='fa fa-spin fa-spinner'></i> Please wait importing...</p>
    <div class="progress">
        <div id='progress-import' class="progress-bar progress-bar-primary progress-bar-striped"
             role="progressbar" aria-valuenow="40"
             aria-valuemin="0" aria-valuemax="100" style="width: 0%">
            <span class="sr-only">40% Complete (success)</span>
        </div>
    </div>

    @push('bottom')
        <script type="text/javascript">
            $(function () {
                var total = {{ intval(Session::get('total_data_import')) }};

                var int_prog = setInterval(function () {

                    $.post("{{ CRUDBooster::mainpath('do-import-chunk?file='.Request::get('file')) }}", {resume: 1}, function (resp) {
                        console.log(resp.progress);
                        $('#progress-import').css('width', resp.progress + '%');
                        $('#status-import').html("<i class='fa fa-spin fa-spinner'></i> Please wait importing... (" + resp.progress + "%)");
                        $('#progress-import').attr('aria-valuenow', resp.progress);
                        if (resp.progress >= 100) {
                            $('#status-import').addClass('text-success').html("<i class='fa fa-check-square-o'></i> Import Data Completed !");
                            clearInterval(int_prog);
                        }
                    })


                }, 2500);

                $.post("{{ CRUDBooster::mainpath('do-import-chunk').'?file='.Request::get('file') }}", function (resp) {
                    if (resp.status == true) {
                        $('#progress-import').css('width', '100%');
                        $('#progress-import').attr('aria-valuenow', 100);
                        $('#status-import').addClass('text-success').html("<i class='fa fa-check-square-o'></i> Import Data Completed !");
                        clearInterval(int_prog);
                        $('#upload-footer').show();
                        console.log('Import Success');
                    }
                })

            })

        </script>
    @endpush
</div>


<div class="box-footer" id='upload-footer' style="display:none">
    <div class='pull-right'>
        <a href='{{ CRUDBooster::mainpath("import-data") }}' class='btn btn-default'>
            {!! CB::icon('upload') !!} Upload Other File</a>
        <a href='{{CRUDBooster::mainpath()}}' class='btn btn-success'>Finish</a>
    </div>
</div>