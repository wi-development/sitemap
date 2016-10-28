@extends('dashboard::layouts.master')


@section('content')
        <!--Page Title-->
        <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
        <div id="page-title">
            <h1 class="page-header text-overflow">{{$tableConfig['header']}}</h1>
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
            <li><a href="{{route('admin::dashboard')}}">Dashboard</a></li>
            <li><a href="{{route('admin::sitemap.index.all')}}">pagina overzicht</a></li>
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
                        <span id="createPage">
                        @if ($allowed_child_templates->count() > 1)
                            <div class="btn-group">
                                <a class="btn btn-warning btn-labeled fa fa-cog"
                                   data-toggle="dropdown" aria-expanded="false"
                                   href="#">
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
                                               href="{{route('admin::sitemap.create').'?template_id='.$allowed_child_templates->first()->id.'&parent_id='.$sitemap->id.''}}">
                                                <i class="fa fa-plus fa-1"></i>
                                                {{$template->name}} toevoegen aan '{{$sitemap->translation->name}}'
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @elseif ($allowed_child_templates->count() == 1)
                                <a class="btn btn-warning btn-labeled fa fa-cog btn-defxault"
                                   href="{{route('admin::sitemap.create').'?template_id='.$allowed_child_templates->first()->id.'&parent_id='.$sitemap->id.''}}">
                                Pagina toevoegen aan '{{$sitemap->translation->name}}'
                            </a>
                        @endif
                        </span>
                    </div>
                    <h3 class="panel-title">
                        Overzicht van:
                        <small>{!! $breadcrumbAsHTML !!}</small>
                    </h3>

                </div>

                <!-- Laravel/DataTables Table - Filtering -->
                <!--===================================================-->

                <div class="panel-body">
                    <table class="table table-bordered table-hover toggle-circle table-striped-uit sortable-uit showExtraData" id="users-table" cellspacing="0" width="100%">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>id</th>
                            <th>Titel</th>
                            <th>Status</th>
                            <th>User</th>
                            <th>Gemaakt op</th>
                            <th>Gewijzigd op</th>
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
    <script type="text/javascript" src="/themes/nifty-2.4.1/vendor/bootbox/bootbox.min.js"></script>
    <!--<script src="/js/dashboard.js"></script>-->
    <script src="/js/wi-data.js"></script>
    <script>
        var tableConfig = {
            urlIndex: '{{ route('admin::sitemap.index.all')}}',
            urlDataRoot: '{{ route('admin::sitemap.index.all.data') }}',
            urlData: '{{ route('admin::sitemap.index.all.data',['sitemap_parent_id'=>$sitemap->id]) }}',

            urlSort: '{{ route('admin::sitemap.sort')}}',
            urlWiDeleteSitemap: '{{ route('admin::sitemap.delete')}}',
            csrf_token: '{{ csrf_token() }}',

            customSearchButtonValue:'status',
            customSearchColumn:'sitemaps.status',
            customSearchColumnValues:{!! $tableConfig['customSearchColumnValues'] !!},
            allowSortable:'{{$tableConfig['allowSortable']}}',
            orderColumnInit:0,
            orderColumnInitType:'asc',
            columns: [
                {data: 'path', name: 'path',visible:true, searchable: false,class:'dragpointer',width:'1%'},
                {data: 'id', name: 'sitemaps.id',visible:false},
                {data: 'testname', name: 'st.name',searchable: true,widthx:'40%' },
                {data: 'status', name: 'sitemaps.status',searchable: true,width:'80px' },

                //{data: 'locaties', name: 'sctest_st.name'},
                {data: 'usersname', name: 'u.name',searchable: true,width:'100px' },
                //{data: 'published_at', name: 'sitemaptranslations.published_at'},
                {data: 'created_at', name: 'sitemaps.created_at',searchable: true,width:'140px' },
                {data: 'updated_at', name: 'sitemaps.updated_at',searchable: true,width:'140px' },
                {data: 'action', name: 'action', orderable: false, searchable: false,visible:false}
            ],
            "language": {
                "lengthMenu": "Toon _MENU_  items per pagina",
                "zeroRecords": "Geen items gevonden",
                //"info": "Toon pagina _PAGE_ van _PAGES_ _TOTAL_",
                "info" :  "Toon _START_ t/m _END_ van _TOTAL_ items",
                "infoEmpty": "Geen pagina's beschikbaar",
                "infoFiltered": "(filtered from _MAX_ total records)",
                "paginate": {
                    "first":      "Eerste",
                    "last":       "Laatste",
                    "next":       "Volgende",
                    "previous":   "Vorige"
                },
                //"processing":     "Processing...",
                "search":         "Zoeken:"
            },
            "pageLength":10,
            "bulkActions":false
        };
        //console.info({!! $tableConfig['customSearchColumnValues'] !!});
        setTable(tableConfig);
    </script>
@endsection


@section('aside')
    @include('dashboard::partials.aside')
@endsection