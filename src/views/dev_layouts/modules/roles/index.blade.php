@extends("crudbooster::dev_layouts.layout")
@section("content")


    @push("head")
        <style>
            .grid-wrapper {

            }
            .grid-wrapper .grid-item {
                border-radius: 5px;
                border: 1px solid #cccccc;
                text-align: center;
                margin-top: 30px;
            }
            .grid-wrapper .grid-item:hover {
                box-shadow: 0px 0px 5px #cccccc;
            }
            .grid-wrapper .grid-item-dotted {
                border: 2px dashed #cccccc;
            }
            .grid-wrapper .grid-item a {
                display: block;
                padding: 20px;
                color: #555555;
                font-size: 18px;
            }
            .grid-wrapper .grid-item-dotted a {
                color: #9a9a9a !important;
            }
            .grid-wrapper .grid-item a i {
                font-size: 35px;

            }
        </style>
    @endpush
    <div class="callout callout-info" style="margin-bottom: 0px">
        <strong>Tips</strong> You can find the role data by using <code>cb()->getRoleByName("Admin")</code> helper.
    </div>

    <div class="row grid-wrapper">
        @foreach($result as $row)
            <div class="col-sm-3">
                <div class="grid-item gray-gradient">
                    <a title="{{ __("cb::cb.click_to_edit") }}" href="{{ cb()->getDeveloperUrl("roles/edit/".$row->id) }}">
                        <i class="fa fa-user"></i> <br> {{ $row->name }}
                    </a>
                </div>
            </div>
        @endforeach
        <div class="col-sm-3">
            <div class="grid-item grid-item-dotted">
                <a title="{{ __("cb::cb.click_to_add") }}" href="{{ cb()->getDeveloperUrl("roles/add") }}">
                    <i class="fa fa-plus"></i> <br> Add Role
                </a>
            </div>
        </div>
    </div>


@endsection