@extends('dashboard::layouts.master')

@section('css.head')
    <link href="https://cdn.datatables.net/t/bs/dt-1.10.11,rr-1.1.1/datatables.min.css" rel="stylesheet">
    @endsection

    @section('scripts.footer')


            <!-- ORG
    <script src="https://cdn.datatables.net/1.10.11/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.11/js/dataTables.bootstrap.min.js"></script>-->

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
            urlData: '{{ route('admin::sitemap.news.data') }}',
            customSearchButtonValue:'locatie',
            customSearchColumn:'sctest_st.name',
            customSearchColumnValues:{!! $locatiesNames !!},
            allowSortable:false,
            orderColumnInit:5,
            orderColumnInitType:'desc',
            columns: [
                { data: 'id', name: 'sitemaps.id' },
                { data: 'testname', name: 'sitemaptranslations.name' },
                { data: 'status', name: 'sitemaps.status' },
                { data: 'locaties', name: 'sctest_st.name' },
                { data: 'usersname', name: 'users.name' },
                { data: 'published_at', name: 'sitemaptranslations.published_at' },
                { data: 'created_at', name: 'sitemaps.created_at' },
                { data: 'updated_at', name: 'sitemaps.updated_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false}
            ]
        };
        setTable(tableConfig);
        //});
    </script>


    @endsection
@section('content')

        <!--Page Title-->
        <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
        <div id="page-title">
            <h1 class="page-header text-overflow">Alle Nieuwsberichten </h1>

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
            <li><a href="{{route('admin::sitemap.news.index')}}">nieuws overzicht</a></li>
        </ol>
        <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
        <!--End breadcrumb-->




        <!--Page content-->
        <!--===================================================-->
        <div id="page-content">

            <div class="panel">

                <div class="panel-heading">
                    <div class="panel-control">
                        <div class="btn-group">

                            <button data-target="#demo-chat-body" data-toggle="collapse" type="button" class="btn btn-default hidden"><i class="fa fa-chevron-down"></i></button>
                            <button data-toggle="dropdown" class="btn btn-default" type="button"><i class="fa fa-gear"></i></button>

                            <a class="btn btn-warning btn-labeled fa fa-save btn-lg" href="{{route('admin::sitemap.create')}}?template_id=4">Nieuwsbericht toevoegen</a>

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
                        Alle nieuwsberichten
                    </h3>

                </div>

                <!-- Laravel/DataTables Table - Filtering -->
                <!--===================================================-->

                <div class="panel-body">
                    <table class="table table-bordered table-hover toggle-circle" id="users-table" cellspacing="0" width="100%">
                        <thead>
                        <tr>
                            <th>id</th>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Locaties</th>
                            <th>User</th>
                            <th>Published At</th>
                            <th>Created At</th>
                            <th>Updated At</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>

                            <th>id</th>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Locaties</th>
                            <th>User</th>
                            <th>Published At</th>
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




