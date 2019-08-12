@extends("crudbooster::dev_layouts.layout")
@section("content")


    <div class="mt-40">
        <div style="text-align: center">
            <img src="{{ cbAsset("images/logo_cb_blue.png") }}" alt="CRUDBooster">
            <div class="mt-20">
                <p>
                    <a target="_blank" href="http://crudbooster.com">Documentation</a> &nbsp;&nbsp;&bull;&nbsp;&nbsp;
                    <a target="_blank" href="https://github.com/crocodic-studio/crudbooster">GitHub</a> &nbsp;&nbsp;&bull;&nbsp;&nbsp;
                    <a href="javascript:;" onclick='$("#modal-welcome").modal("show")'>Greeting</a>
                </p>
            </div>

        </div>
    </div>

    <div class="modal" id="modal-welcome">
        <div class="modal-dialog modal-lg" style="width: 1000px">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" align="center"><i class="fa fa-star"></i> Welcome to CRUDBooster <i class="fa fa-star"></i></h4>
                </div>
                <div class="modal-body">
                    <div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
                        <!-- Indicators -->
                        <ol class="carousel-indicators">
                            <li data-target="#carousel-example-generic" data-slide-to="0" class="active"></li>
                            {{--<li data-target="#carousel-example-generic" data-slide-to="1"></li>--}}

                        </ol>

                        <!-- Wrapper for slides -->
                        <div class="carousel-inner" role="listbox">
                            <div class="item active">
                                <div align="center">
                                    <img width="850px" height="390px" src="{{ cbAsset("images/crudbooster_slider.png") }}" alt="">
                                    <br><br>
                                    <p>
                                        Thank you for choosing <strong>CRUDBooster</strong> :). Don't forget to share CRUDBooster on your Social Media and also tell to your friend.
                                    </p>
                                </div>
                            </div>

                            <div class="item">
                                <div align="center">
                                    <img width="850px" height="390px" src="{{ cbAsset("images/step1.png") }}" style="max-width: 100%" alt="">

                                <br><br>
                                <p>
                                    <strong>First</strong>, you have to create a module
                                </p>
                                </div>
                            </div>

                            <div class="item">
                                <div align="center">
                                    <img width="850px" height="390px" src="{{ cbAsset("images/step2.png") }}" style="max-width: 100%" alt="">

                                <br><br>
                                <p>
                                    <strong>Second</strong>, create a new role. Check all privileges available like Browse, Create, Read, Update, Delete. Or as you wish.
                                </p>
                                </div>
                            </div>

                            <div class="item">
                                <div align="center">
                                    <img width="850px" height="390px" src="{{ cbAsset("images/step3.png") }}" style="max-width: 100%" alt="">

                                <br><br>
                                <p>
                                    <strong>Third</strong>, create a new user for Admin. Fill up the form and choose the role that you have made
                                </p>
                                </div>
                            </div>

                            <div class="item">
                                <div align="center">
                                    <img width="850px" height="390px" src="{{ cbAsset("images/step4.png") }}" style="max-width: 100%" alt="">

                                <br><br>
                                <p>
                                    Lets, <strong>check it out</strong> your first module, just click the shortcut button on the top right
                                </p>
                                </div>
                            </div>
                        </div>

                        <!-- Controls -->
                        <a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">
                            <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                            <span class="sr-only">Previous</span>
                        </a>
                        <a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
                            <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                            <span class="sr-only">Next</span>
                        </a>
                    </div><!--end carousel-->
                </div>
                <div class="modal-footer">
                    <div align="right">
                        <input type="checkbox" onclick="dontShowAnymore(this)"> Don't show this popup on next startup
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->


    @push('bottom')
        <script>
            function dontShowAnymore(t) {
                var status = false;
                if( $(t).prop("checked") ) {
                    status = true;
                }
                $.post("{{ route("DeveloperDashboardControllerPostSkipTutorial") }}",{_token:"{{csrf_token()}}",status: status})
            }

            $(function() {
                @if(\Illuminate\Support\Facades\Cache::get("skip_developer_tutorial") != true)
                    $("#modal-welcome").modal("show")
                @endif
            })
        </script>
    @endpush


@endsection