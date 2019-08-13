@extends("crudbooster::dev_layouts.layout")
@section("content")

    <div align="right">
        <a href="{{ route("DeveloperPluginStoreControllerGetIndex") }}?refresh=1" class="btn btn-success"><i class="fa fa-refresh"></i> Update List</a>
    </div>
    <div class="box box-default mt-10">
        <div class="box-body">
            <table class="table table-bordered datatable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Version</th>
                        <th>Author</th>
                        <th>Price</th>
                        <th>Install</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($result as $row)
                        <tr>
                            <td>
                                @if($row['icon'])
                                    <i class="{{ $row['icon'] }}"></i>
                                @endif
                                {{ $row['name'] }}</td>
                            <td>{{ $row['description'] }}</td>
                            <td>{{ $row['version'] }}
                                @if(isset($row['changelog']))
                                    <a href="javascript:;" title="Changelog {{$row['version']}}:&#013;{{ $row['changelog'] }}"><i class="fa fa-question-circle"></i></a>
                                @endif
                            </td>
                            <td>
                                @if(isset($row['author_homepage']))
                                    <a target="_blank" href="{{ $row['author_homepage'] }}">{{ $row['author'] }}</a>
                                @else
                                    {{ $row['author'] }}
                                @endif
                            </td>
                            <td>
                                @if($row['price']==0)
                                    <span class="label label-success">FREE</span>
                                @else
                                    <span class="label label-warning">PAID</span>
                                @endif
                            </td>
                            <td>
                                @if(\crocodicstudio\crudbooster\helpers\Plugin::has($row['key']))
                                    @if(\crocodicstudio\crudbooster\helpers\Plugin::isNeedUpgrade($row['key'], $row['version']))
                                        <a href="javascrip:;" onclick="installPlugin('{{ route("DeveloperPluginStoreControllerGetInstall",["key"=>$row['key']]) }}','Upgrade {{$row['name']}} plugin to {{ $row['version'] }} version')" class="btn btn-xs btn-info"><i class="fa fa-download"></i> Upgrade</a>
                                    @else
                                        <a href="javascript:;" class="btn disabled btn-xs btn-default"><i class="fa fa-check"></i> Installed</a>
                                    @endif

                                        <a href="javascript:;" onclick="uninstallPlugin('{{ route("DeveloperPluginStoreControllerGetUninstall",["key"=>$row['key']]) }}','Uninstall plugin {{$row['name']}}')" class="btn btn-xs btn-danger"><i class="fa fa-trash"></i></a>
                                @else
                                    @if($row['price']==0)
                                    <a href="javascript:;" onclick="installPlugin('{{ route("DeveloperPluginStoreControllerGetInstall",["key"=>$row['key']]) }}','Install {{$row['name']}} plugin v{{ $row['version'] }}')" class="btn btn-xs btn-success">Install</a>
                                    @else
                                        <a href="javascript:;" onclick="buyPlugin(this)" data-key="{{ $row['key'] }}" data-name="{{ $row['name'] }}" data-price="{{ $row['price'] }}" data-version="{{ $row['version'] }}" data-description="{{ $row['description'] }}" data-author="{{ $row['author'] }}" class="btn btn-xs btn-success">Install</a>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>


    <div id="app-vue">

        <div class="modal fade" id="modal-buy">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">Buy Confirmation</h4>
                    </div>
                    <div class="modal-body">
                        <table class="table table-striped table-bordered">
                            <tbody>
                                <tr>
                                    <th width="20%">Plugin Name</th><td>: <span id="plugin-name"></span></td>
                                </tr>
                                <tr>
                                    <th>Description</th><td>: <span id="plugin-description"></span></td>
                                </tr>
                                <tr>
                                    <th>Version</th><td>: <span id="plugin-version"></span></td>
                                </tr>
                                <tr>
                                    <th>Author</th><td>: <span id="plugin-author"></span></td>
                                </tr>
                                <tr>
                                    <th>Price</th><td>: <span id="plugin-price" class="text-danger"></span></td>
                                </tr>
                            </tbody>
                        </table>

                        <div class="buy-plugin-paypal-form" v-if="form_paypal_payment" align="center" v-html="form_paypal_payment"></div>
                        <br>
                        <p class="text-muted" v-if="form_paypal_payment" align="center">
                            Buy plugin with PayPal, Credit Card supported
                        </p>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->


        <div class="modal fade" id="modal-login">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">Login CRUDBooster Account</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="">Email</label>
                            <input type="email" autocomplete="off" v-model="account_email" required class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="">Password</label>
                            <input type="password" autocomplete="off" v-model="account_password" required class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" @click="loginAccount" class="btn btn-primary">Login</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->

    </div>

    @push("bottom")
        <script src="{{ cbAsset("js/vue.min.js") }}"></script>
        <script src="{{ cbAsset("js/axios.min.js") }}"></script>
        <script>
            var currentPluginKey = null

            function installPlugin(url, message) {
                showConfirmation("{{ cbLang("are_you_sure") }}", message, ()=>{
                    showLoading()
                    $.get(url,resp=>{
                        if(resp.status) {
                            swal("Success", resp.message, "success")
                        } else {
                            swal("Oops", resp.message, "warning")
                        }
                        hideLoading()
                        location.href = "{{ request()->url() }}"
                    })
                })
            }

            function uninstallPlugin(url, message) {
                showConfirmation("{{ cbLang("are_you_sure") }}", message, ()=>{
                    showLoading()
                    $.get(url,resp=>{
                        if(resp.status) {
                            swal("Success", resp.message, "success")
                        } else {
                            swal("Oops", resp.message, "warning")
                        }
                        hideLoading()
                        location.href = "{{ request()->url() }}"
                    })
                })
            }

            function buyPlugin(t) {
                currentPluginKey = $(t).data('key')
                var name = $(t).data('name')
                var version = $(t).data('version')
                var description = $(t).data('description')
                var author = $(t).data('author')
                var price = $(t).data('price')

                $("#plugin-name").text(name)
                $("#plugin-version").text(version)
                $("#plugin-description").text(description)
                $("#plugin-author").text(author)
                $("#plugin-price").text("USD "+price)
                $("#modal-buy").modal("show")

                app.submitBuyPlugin()
            }

            const app = new Vue({
                el: '#app-vue',
                data: {
                    account_email: "",
                    account_password: "",
                    token: "{{ session("account_token") }}",
                    after_login : "",
                    form_paypal_payment: ""
                },
                mounted() {

                },
                methods: {
                    submitBuyPlugin: function() {
                        this.after_login = "buy-plugin"
                        if(this.token == "") {
                            this.showModalLogin()
                        } else {
                            showLoading()
                            axios.post("{{ route("DeveloperPluginStoreControllerPostRequestBuyPlugin") }}",{
                                _token: "{{ csrf_token() }}",
                                token: this.token,
                                key: currentPluginKey
                            })
                                .finally(()=>{
                                    hideLoading()
                                })
                                .then(response=>{
                                    let data = response.data
                                    if(data && data.status == true) {
                                        this.form_paypal_payment = data.form
                                    } else {
                                        swal("Oops","Something went wrong, please try again later","warning")
                                    }
                                })
                                .catch(err=>{
                                    swal("Oops","Something went wrong, please try again later","warning")
                                })
                        }
                    },
                    showModalLogin: function() {
                        $("#modal-login").modal("show")
                    },
                    loginAccount: function() {
                        if(this.account_email == "") {
                            swal("Oops","Please enter your account email","warning")
                            return false;
                        }

                        if(this.account_password == "") {
                            swal("Oops","Please enter your account password","warning")
                        }

                        showLoading()
                        axios.post("{{ route("DeveloperPluginStoreControllerPostLoginAccount") }}",{
                            _token: "{{ csrf_token() }}",
                            email: this.account_email,
                            password: this.account_password
                        })
                            .finally(()=>{
                                hideLoading()
                            })
                            .then(response=>{
                                if(response.data) {
                                    let data = response.data
                                    if(data.status) {
                                        this.token = data.token
                                        $("#modal-login").modal("hide")

                                        if(this.after_login == "buy-plugin") {
                                            this.submitBuyPlugin()
                                        }
                                    }else {
                                        swal("Oops","Your username and or password is wrong!","warning")
                                    }
                                }
                            })
                            .catch(err=>{
                                swal("Oops","{{ cbLang("something_went_wrong") }}","warning")
                            })
                    }
                }
            });
        </script>
    @endpush

@endsection