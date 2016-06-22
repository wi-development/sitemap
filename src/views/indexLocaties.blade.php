@extends('dashboard::layouts.master')


@section('content')

    <!--Page Title-->
    <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
    <div id="page-title">
        <h1 class="page-header text-overflow">Locaties</h1>

        <!--Searchbox-->
        <div class="searchbox">
            <div class="input-group custom-search-form">
                <input type="text" class="form-control" placeholder="Search..">
                        <span class="input-group-btn">
                            <button class="text-muted" type="button"><i class="fa fa-search"></i></button>
                        </span>
            </div>
        </div>
    </div>
    <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
    <!--End page title-->

    <!--Breadcrumb-->
    <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
    <ol class="breadcrumb">
        <li><a href="/admin">dashboard</a></li>
        <li><a href="{{ route('admin::sitemap.locaties.index') }}">Locatie overzicht</a></li>
    </ol>
    <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
    <!--End breadcrumb-->

    <!--Page content-->
    <!--===================================================-->
    <div id="page-content">
        @include('flash::message')
        <div class="panel">

            <div class="panel-heading">
                <div class="panel-control">
                    <div class="btn-group">

                        <button class="btn btn-default btn-hover-info add-tooltip" data-placement="top" data-toggle="tooltip" data-original-title="Tooltip on top">Tooltip on top</button>

                        <button data-target="#demo-chat-body" data-toggle="collapse" type="button" class="btn btn-default hidden"><i class="fa fa-chevron-down"></i></button>
                        <button data-toggle="dropdown" class="btn btn-default" type="button"><i class="fa fa-gear"></i></button>

                        <a class="btn btn-warning btn-labeled fa fa-save btn-lg" href="{{route('admin::sitemap.create')}}?template_id=10">Locatie toevoegen</a>

                        <ul class="dropdown-menu dropdown-menu-right">
                            <li><a href="#">Available</a></li>
                            <li><a href="#">Busy</a></li>
                            <li><a href="#">Away</a></li>
                            <li class="divider"></li>
                            <li><a data-target="#demo-chat-body" class="disabled-link" href="#" id="demo-connect-chat">Connect</a></li>
                            <li><a data-target="#demo-chat-body" href="#" id="demo-disconnect-chat">Disconect</a></li>
                        </ul>
                    </div>
                </div>
                <h3 class="panel-title">
                    Alle locaties
                </h3>

            </div>

            <!-- Laravel/DataTables Table - Filtering -->
            <!--===================================================-->

            <div class="panel-body">


                <table class="table table-bordered table-hover toggle-circle table-striped sortable-uit" id="users-table" cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>id</th>
                        <th>Titel</th>
                        <th>Status</th>
                        <th>Plaats</th>

                        <th>User</th>

                        <th>Aangemaakt op</th>
                        <th>Aangepast op</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tfoot>
                    <tr>
                        <th>#</th>
                        <th>id</th>
                        <th>Titel</th>
                        <th>Status</th>
                        <th>Plaats</th>

                        <th>User</th>

                        <th>Aangemaakt op</th>
                        <th>Aangepast op</th>
                        <th>Action</th>
                    </tr>
                    </tfoot>
                </table>


            </div>
            <!--===================================================-->
            <!-- End Laravel/DataTables - Filtering -->

        </div>









    <!--===================================================-->
    <!--End page content-->


    </div>

@endsection


@section('css.head')
    <link href="https://cdn.datatables.net/t/bs/dt-1.10.11,rr-1.1.1/datatables.min.css" rel="stylesheet">
@endsection


@section('scripts.footer')

            <!--<script src="https://cdn.datatables.net/1.10.11/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.11/js/dataTables.bootstrap.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/t/bs/dt-1.10.11,rr-1.1.1/datatables.min.js"></script>
    -->
    <script type="text/javascript" src="/js/wi/datatables/datatables-tonny.js"></script>
    <!--<script src="/js/dashboard.js"></script>-->
    <script src="/js/wi-data.js"></script>

    <script>
        var tableConfig = {
            urlIndex: '{{ route('admin::sitemap.locaties.index')}}',//niet gebruikt geen subpage
            urlDataRoot: '{{ route('admin::sitemap.locaties.index') }}',//niet gebruikt subpage
            urlData: '{{ route('admin::sitemap.locaties.index.data') }}',

            urlSort: '{{ route('admin::sitemap.sort')}}',
            urlWiDeleteSitemap: '{{ route('admin::sitemap.delete')}}',
            csrf_token: '{{ csrf_token() }}',

            customSearchButtonValue:'plaats',
            customSearchColumn:'city',
            customSearchColumnValues:{!! $cities !!},
            allowSortable:true,
            orderColumnInit:0,
            orderColumnInitType:'asc',
            columns: [
                { data: 'order_by_number', name: 'sitemaps.order_by_number',class:'dragpointer',width:'1%' },
                { data: 'id', name: 'sitemaps.id',visible:false},
                { data: 'testname', name: 'sitemaptranslations.name',width:'30%' },
                { data: 'status', name: 'sitemaps.status' },
                { data: 'city', name: 'city',searchable: true },

                //{ data: 'locaties', name: 'sctest_st.name' },


                { data: 'usersname', name: 'users.name' },
                //{ data: 'published_at', name: 'sitemaptranslations.published_at' },
                { data: 'created_at', name: 'sitemaps.created_at',width:'105px' },
                { data: 'updated_at', name: 'sitemaps.updated_at',width:'105px' },
                { data: 'action', name: 'action', orderable: false, searchable: false,width:'170px'}
            ]
        };
        setTable(tableConfig);
    </script>
    @if (Session::has('flash_notification.message'))
        <script src="/nifty/js/demo/ui-panels.js"></script>
        <script>

            $.niftyNoty({
                type: 'info',
                container : '#page-content',
                html : '<h4 class="alert-title">Oh snap! You got an error!</h4><p class="alert-message">Change this and that and try again. Duis mollis, est non commodo luctus.</p><div class="mar-top"><button type="button" class="btn btn-info" data-dismiss="noty">Close this notification</button></div>',
                closeBtn : false
            });

/*
            $.niftyNoty({
                type: 'purple',
                container : 'floating',
                title : 'Update gelukt!',
                message : 'De volgorde is aangepast.',
                closeBtn : false,
                timer : 2000
                //,
                //onShow:function(){
                //	alert("onShow Callback");
                //}
            });
            */
        </script>
    @endif


@endsection


