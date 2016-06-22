@extends('admin.layouts.app')


@section('content')



    <div id="content-container">


        <!--Page Title-->
        <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
        <div id="page-title">
            <h1 class="page-header text-overflow">Pagina toevoegen</h1>

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
            <li><a href="#">Dashboard</a></li>
            <li><a href="#">Pagina's</a></li>
            <li class="active">Selecteer een pagina</li>
        </ol>
        <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
        <!--End breadcrumb-->




        <!--Page content-->
        <!--===================================================-->
        <div id="page-content">







            <div class="panel-body demo-nifty-modal">

                <!--Static Examplel-->
                <div class="modal">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" data-dismiss="modal" class="close"><span>×</span><span class="sr-only">Close</span></button>
                                <h4 class="modal-title">Selecteer een pagina</h4>
                            </div>
                            {!! Form::open(['method'=>'GET','action'=>'Admin\SitemapController@selectSitemapTypeBeforeCreate']) !!}
                            <div class="modal-body">


                                <div class="form-group">
                                    {!! Form::label('template_id', 'template:') !!}
                                    {!! Form::select('template_id', $template_list,Request::get('template_id'), ['class' => 'form-control']) !!}
                                </div>

                                @if (Request::get('template_id') != null)
                                    <div class="form-group">
                                        {!! Form::label('parent_id', 'sitemap parent:') !!}
                                        {!! Form::select('parent_id', $sitemap_list,null, ['class' => 'form-control']) !!}
                                    </div>
                                @endif


                            </div>

                            <div class="modal-footer">
                                {!! Form::submit('Kies pagina', ['class' => 'btn btn-primary']) !!}
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>

            </div>



        </div>
        <!--===================================================-->
        <!--End page content-->


    </div>


@endsection


