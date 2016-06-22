@extends('dashboard::layouts.master')


@section('content')

    <!--Page Title-->
    <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
    <div id="page-title">
        <h1 class="page-header text-overflow">Locatie: {{$sitemap->translation->name}}</h1>

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
        <li><a href="{{ route('admin::sitemap.locaties_sub.index',['sitemap_parent_id'=>$sitemap->id]) }}">Pagina overzicht van '{{$sitemap->translation->name}}'</a></li>
    </ol>
    <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
    <!--End breadcrumb-->




    <!--Page content-->
    <!--===================================================-->
    <div id="page-content">
        @include('flash::message')
        <div class="panel">

            <div class="panel-heading">
                <div class="panel-control wi-control">

<!--
                    <button class="btn btn-default btn-hover-info add-tooltip" data-placement="top" data-toggle="tooltip" data-original-title="Tooltip on top">Tooltip on top</button>

                    <div class="btn-group">
                        <button class="btn btn-default">Actionxxx</button>
                        <button data-toggle="dropdown" class="dropdown-toggle btn btn-info">
                            <i class="fa fa-gear fa-lg"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <li><a href="#">Action</a></li>
                            <li><a href="#">Another action</a></li>
                            <li><a href="#">Something else here</a></li>
                            <li class="divider"></li>
                            <li><a href="#">Separated link</a></li>
                        </ul>
                    </div>

                    <div class="btn-group">
                        <button class="btn btn-primary dropdown-toggle" data-toggle="dropdown" type="button" aria-expanded="false">
                            Dropdown <i class="dropdown-caret fa fa-caret-down"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <li class="dropdown-header">Dropdown header</li>
                            <li><a href="#">Action <i class="dropdown-caret fa fa-caret-down"></i></a>

                            </li>
                            <li><a href="#">Another action</a>
                            </li>
                            <li><a href="#">Something else here</a>
                            </li>
                            <li class="divider"></li>
                            <li class="dropdown-header">Dropdown header</li>
                            <li><a href="#">Separated link</a>
                            </li>
                        </ul>
                    </div>

                    <div class="btn-group">
                        <button class="btn btn-default">Action</button>
                        <button class="btn btn-default dropdown-toggle dropdown-toggle-icon" data-toggle="dropdown" type="button" aria-expanded="false">
                            <i class="dropdown-caret fa fa-caret-down"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a href="#">Action</a>
                            </li>
                            <li><a href="#">Another action</a>
                            </li>
                            <li><a href="#">Something else here</a>
                            </li>
                            <li class="divider"></li>
                            <li><a href="#">Separated link</a>
                            </li>
                        </ul>
                    </div>
-->




                        @if ($allowed_child_templates->count() > 1)
                            <div class="btn-group">
                                <a class="btn btn-warning btn-labeled fa fa-cog"
                                   data-toggle="dropdown" aria-expanded="false"
                                   href="{{url(('/admin/sitemap/create?template_id=11&parent_id='.$sitemap->id.''))}}">
                                    Pagina toevoegen aan '{{$sitemap->translation->name}}'
                                </a>
                                <button class="btn btn-warning dropdown-toggle dropdown-toggle-icon" data-toggle="dropdown" type="button" aria-expanded="false">
                                    <i class="dropdown-caret fa fa-caret-down"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                    <li class="dropdown-header">Kies een pagina type</li>
                                    @foreach($allowed_child_templates as $key => $template)
                                        <li>
                                            <a class=""
                                               href="{{url(('/admin/sitemap/create?template_id='.$template->id.'&parent_id='.$sitemap->id.''))}}">
                                                <i class="fa fa-plus fa-1"></i>
                                                {{$template->name}} toevoegen aan '{{$sitemap->translation->name}}'
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @else
                        <a class="btn btn-warning btn-labeled fa fa-cog btn-defxault" href="{{url(('/admin/sitemap/create?template_id=11&parent_id='.$sitemap->id.''))}}">
                            Pagina toevoegen aan '{{$sitemap->translation->name}}'
                        </a>
                        @endif

                </div>

                <h3 class="panel-title">
                    Alle subpagina's van ''{{$sitemap->translation->name}}''
                </h3>

            </div>

            <!-- Laravel/DataTables Table - Filtering -->
            <!--===================================================-->

            <div class="panel-body">


                <table class="table table-bordered table-hover sortable-uit toggle-circle" id="users-table" cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>id</th>
                        <th>Name</th>
                        <th>Status</th>
                        <th>User</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tfoot>
                    <tr>
                        <th>#</th>
                        <th>id</th>
                        <th>Name</th>
                        <th>Status</th>
                        <th>User</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                        <th>Action</th>
                    </tr>
                    </tfoot>
                </table>


            </div>
            <!--===================================================-->
            <!-- End Laravel/DataTables - Filtering -->

        </div>









    </div>
    <!--===================================================-->
    <!--End page content-->

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
            urlIndex: '',//niet gebruikt geen subpage
            urlDataRoot: '',//niet gebruikt subpage
            urlData: '{{ route('admin::sitemap.locaties_sub.data',['sitemap_parent_id'=>$sitemap->id]) }}',

            //customSearchColumn:'status',
            //customSearchColumnValues:{!! $tableConfig['customSearchColumnValues'] !!},

            urlSort: '{{ route('admin::sitemap.sort')}}',
            urlWiDeleteSitemap: '{{ route('admin::sitemap.delete')}}',
            csrf_token: '{{ csrf_token() }}',


            customSearchButtonValue:'status',
            customSearchColumn:'sitemaps.status',
            customSearchColumnValues:{!! $tableConfig['customSearchColumnValues'] !!},


            allowSortable:true,
            orderColumnInit:0,
            orderColumnInitType:'asc',
            columns: [
                { data: 'order_by_number', name: 'sitemaps.order_by_number',class:'dragpointer',width:'1%' },
                { data: 'id', name: 'sitemaps.id',visible:false},
                { data: 'testname', name: 'sitemaptranslations.name',width:'40%' },
                { data: 'status', name: 'sitemaps.status' },

                //{ data: 'locaties', name: 'sctest_st.name' },


                { data: 'usersname', name: 'users.name' },
                //{ data: 'published_at', name: 'sitemaptranslations.published_at' },
                { data: 'created_at', name: 'sitemaps.created_at',width:'105px' },
                { data: 'updated_at', name: 'sitemaps.updated_at',width:'105px' },
                { data: 'action', name: 'action', orderable: false, searchable: false,width:'50px'}
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
                html : '<h4 class="alert-title">{{ Session::get('flash_notification.level') }}</h4><p class="alert-message">{{ Session::get('flash_notification.message') }}</p><div class="mar-top"><button type="button" class="btn btn-info" data-dismiss="noty">Close this notification</button></div>',
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


