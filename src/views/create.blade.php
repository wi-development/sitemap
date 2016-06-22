@extends('dashboard::layouts.master')





@section('content')

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
            <li><a href="{{route('admin::dashboard')}}">dashboard</a></li>
            @if (null !== session('previous_route.name'))
                <li><a href="{{route(session('previous_route.name'))}}">{{session('previous_route.anchorText')}}</a></li>
            @endif

            @if (null !== session('previous_route.url'))
                <li><a href="{{(session('previous_route.url'))}}">{{session('previous_route.anchorText')}}</a></li>
            @endif
            <li class="active">pagina aanmaken</li>
        </ol>
        <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
        <!--End breadcrumb-->




        <!--Page content-->
        <!--===================================================-->
        <div id="page-content">
            <div class="row">
                <?php
                $frmHeader = "Nieuwe pagina toevoegen aan '".$parent_translation->name."'";
/*
if ((null !== (request()->get('parent_id')))){
        if (request()->get('parent_id') == 12){
            $frmHeader .= " toevoegen aan 'Zorg thuis'";
        }

    if (request()->get('parent_id') == 2){
        $frmHeader .= " toevoegen aan 'Wonen met zorg'";
    }

    if (request()->get('parent_id') == 18){
        $frmHeader .= " toevoegen aan 'Services'";
    }


}
*/
                ?>





                {{ Form::open(['route'=>array('admin::sitemap.store'), 'class'=>'form-horizontal form-padding']) }}

                @include('errors.list')

                @include('sitemap::partials.form', ['submitButtonText' => 'Publiceren','frmHeader'=>''.$frmHeader.''])


                {{ Form::close() }}


            </div>
        </div>
        <!--===================================================-->
        <!--End page content-->


@endsection
