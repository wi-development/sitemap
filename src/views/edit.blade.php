@extends('dashboard::layouts.master')
@section('content')
       <!--Page Title-->
        <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
        <div id="page-title">
            <h1 class="page-header text-overflow">Pagina wijzigen</h1>

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

            <li class="active">pagina wijzigen</li>
        </ol>
        <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
        <!--End breadcrumb-->




        <!--Page content-->
        <!--===================================================-->
        <div id="page-content">





            <div class="row">

                <!-- BASIC FORM ELEMENTS -->
                <?php
                $frmHeader = 'Wijzigen \''.$sitemap->translations->first()->name.'\'';
                ?>
                @include('errors.sitemap')

                {{ Form::model($sitemap,['method'=>'PATCH', 'route'=>array('admin::sitemap.update',$sitemap->id), 'class'=>'form-horizontal form-padding'
                ,'data-remote'=>'true']) }}

                {{--@include('errors.sitemap')--}}
                @include('sitemap::partials.form', ['submitButtonText' => 'Publiceren','frmHeader' => ''.$frmHeader.''])
                {{ Form::close() }}
                <!-- END BASIC FORM ELEMENTS -->


            </div>
        </div>
        <!--===================================================-->
        <!--End page content-->

@endsection




