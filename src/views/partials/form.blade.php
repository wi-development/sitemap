

{!! Form::hidden('active_language_tab', (session()->has('active_language_tab') ? session()->get('active_language_tab') : 'nl'), ['class' => 'form-control','id' => 'active_language_tab']) !!}

<!--end temp--->



<div class="col-lg-8">
    <div class="panel-heading hidden">
        <h3 class="panel-title frm-header">{{$frmHeader}}</h3>
    </div>
    <div class="tab-base tab-stacked-left">
        <!--Nav tabs-->
		{{--dc(settings()->viewConfig('sitemap_form_left_tab','content',['active','','default']))--}}
        <ul class="nav nav-tabs">
            <li class="{{settings()->viewConfig('sitemap_form_left_tab','content',['active','','default'])}}">
                <a href="#demo-stk-lft-tab-1" data-user-settings='{"sitemap_form_left_tab":"content"}' data-toggle="tab" aria-expanded="{{settings()->viewConfig('sitemap_form_left_tab','content',['true','false','default'])}}">content</a>
            </li>
            <li class="{{settings()->viewConfig('sitemap_form_left_tab','banners',['active',''])}}">
                <a href="#demo-stk-lft-tab-2" data-user-settings='{"sitemap_form_left_tab":"banners"}' data-toggle="tab" aria-expanded="{{settings()->viewConfig('sitemap_form_left_tab','banners',['true','false'])}}">banners</a>
            </li>
            <li class="{{settings()->viewConfig('sitemap_form_left_tab','setting',['active',''])}}">
                <a href="#demo-stk-lft-tab-3" data-user-settings='{"sitemap_form_left_tab":"setting"}' data-toggle="tab" aria-expanded="{{settings()->viewConfig('sitemap_form_left_tab','setting',['true','false'])}}">Setting</a>
            </li>
            <li class="{{settings()->viewConfig('sitemap_form_left_tab','seo',['active',''])}}">
                <a href="#demo-stk-lft-tab-4" data-user-settings='{"sitemap_form_left_tab":"seo"}' data-toggle="tab" aria-expanded="{{settings()->viewConfig('sitemap_form_left_tab','seo',['true','false'])}}">SEO</a>
            </li>
        </ul>

        <!--Tabs Content-->
        <div class="tab-content">
            <div class="panel noshadow">
                <div class="panel-heading hiddenx">
                    @if (isset($sitemap->prevNextSitemap))
                        <div class="panel-control">
                            @if ($sitemap->prevNextSitemap['prev_order_by_number'] != false)
                                <a href="{{route('admin::sitemap.edit',['id'=>$sitemap->prevNextSitemap['prev_order_by_number']->id])}}" class="label btn-primary"><i class="fa fa-arrow-left" aria-hidden="true"></i> prev </a>
                            @else
                                &nbsp;<a class="label btn-default" style="color:silver;cursor:default"><i class="fa fa-arrow-left" aria-hidden="true"></i> prev </a>
                            @endif
                            @if ($sitemap->prevNextSitemap['next_order_by_number'] != false)
                                &nbsp;<a href="{{route('admin::sitemap.edit',['id'=>$sitemap->prevNextSitemap['next_order_by_number']->id])}}" class="label btn-primary">next <i class="fa fa-arrow-right" aria-hidden="true"></i> </a>
                            @else
                                &nbsp;<a class="label btn-default" style="color:silver;cursor:default">next <i class="fa fa-arrow-right" aria-hidden="true"></i> </a>
                            @endif
                        </div>
                    @endif

                    <h3 class="panel-title">{{$frmHeader}}</h3>
                </div>
            </div>


            @include('flash::message')
            <!--start content -->

            <div class="tab-pane {{settings()->viewConfig('sitemap_form_left_tab','content',['active in','','default'])}} fade" id="demo-stk-lft-tab-1">

                <div class="panel">
                    <div class="panel-heading hidden">
@if (isset($sitemap->prevNextSitemap))
                        <div class="panel-control">
                            @if ($sitemap->prevNextSitemap['prev_order_by_number'] != false)
                                <a href="{{route('admin::sitemap.edit',['id'=>$sitemap->prevNextSitemap['prev_order_by_number']->id])}}" class="label btn-primary"><i class="fa fa-arrow-left" aria-hidden="true"></i> prev </a>
                            @else
                                &nbsp;<a class="label btn-default" style="color:silver;cursor:default"><i class="fa fa-arrow-left" aria-hidden="true"></i> prev </a>
                            @endif
                            @if ($sitemap->prevNextSitemap['next_order_by_number'] != false)
                                 &nbsp;<a href="{{route('admin::sitemap.edit',['id'=>$sitemap->prevNextSitemap['next_order_by_number']->id])}}" class="label btn-primary">next <i class="fa fa-arrow-right" aria-hidden="true"></i> </a>
                            @else
                                &nbsp;<a class="label btn-default" style="color:silver;cursor:default">next <i class="fa fa-arrow-right" aria-hidden="true"></i> </a>
                            @endif
                        </div>
@endif

                        <h3 class="panel-title">{{$frmHeader}}</h3>
                    </div>

                    <div class="panel-body">
                        {{--@include('flash::message')--}}
                        @if ((($category_list_config->count() > 0)))
                            <div class="col-md-12 pad-no">
                                {{ Form::label('translations[sitemapCategory]['.$category_list_config['sitemap_category_key'].'][]', 'locatie category:') }}
                                <div class="checkbox">
                                    {{ Form::checkBoxList('category_list',$category_list_config['category_list'],null,[
                                    'name'=>'sitemapCategory['.$category_list_config['sitemap_category_key'].'][]',
                                    'class'=>'form-controlx','autocomplete'=>'off'],$category_list_config['frm_type'],$category_list_config['sitemap_category_key'])}}
                                </div>
                                <hr class="hr-sm">
                            </div>
                        @endif
                        <div class="form-tab tab-base-nestd" ng-app="ng.wi.cms">
							<!-- Nav tabs || $key == $translation->locale->identifier -->
                            <ul class="nav nav-tabs-off nav-justified-off nav-tabs-nested" role="tablist">
                                @foreach($enabledLocales as $locale)
                                    <?php

                                    $key = $locale->languageCode;
                                    $tClass = "";
									//session()->set('active_language_tab','nl');
									//dc(session()->has('active_language_tab'));
                                    //if ($key==(session()->has('active_language_tab') ? session()->get('active_language_tab') : 'nl')){$tClass = " active";}
									if ($key==(settings()->has('active_language_tab') ? settings()->get('active_language_tab') : 'nl')){$tClass = " active";}
									//dc($sitemap->translations[''.$locale->languageCode.'']);
									if (empty($sitemap->translations[''.$locale->languageCode.'']->id)){$tClass .= " new-locale";}
                                    if (array_key_exists($key,$errors->getMessages())){$tClass .= " has-error";}
                                    ?>
                                    <li role="presentation"
										class="{{$tClass}}">
											<a href="#main_{{$key}}"
											   aria-controls="{{$key}}"
											   data-user-settings='{"active_language_tab":"{{$key}}"}'
											   data-tab-type="main"
											   role="tab" data-toggle="tab">
                                            {{$locale->name}}
                                        </a></li>
                                @endforeach

                            </ul>
                            <!-- Tab panes -->
                            <div ng-controller="ModalDemoCtrl" class="tab-content container">



                                {{--<div class="col-12-lg">@include('admin.sitemap._partials.testmediang')</div>--}}



                                <?php
                                //foreach($sitemap->translations as $key => $translation){
                                foreach($enabledLocales as $locale){
                                $key = $locale->languageCode;
                                $language_id = $key;//for error list //or $key
                                //$tClass = "";if ($key==(session()->has('active_language_tab') ? session()->get('active_language_tab') : 'nl')){$tClass = " active";}

								$tClass = "";if ($key==(settings()->has('active_language_tab') ? settings()->get('active_language_tab') : 'nl')){$tClass = " active";}


								?>

                                <div role="tabpanel" class="tab-pane{{$tClass}} ng-cloak-uit" id="main_{{$key}}">

                                    <div class="panel-body" style="padding:0px 20px">

                                        @include('errors.sitemaptranslation')

                                                <!--- Name Field --->
                                        <div class="form-group">
                                            {!! Form::label('translations['.$key.'][name]', 'Titel:',['class'=>'control-label']) !!}
                                            {!! Form::text('translations['.$key.'][name]', null, ['class' => 'form-control']) !!}
                                        </div>

                                        @if (isset($sitemap->id) && ($sitemap->id != 1))
                                                <!--- Slug Field --->
                                        <div class="form-group hidden">
                                            {!! Form::label('translations['.$key.'][slug]', 'slug:',['class'=>'control-label']) !!}
                                            {!! Form::text('translations['.$key.'][slug]', null, ['class' => 'form-control']) !!}
                                        </div>
                                        @endif

                                                <!--- Content Field --->
                                        <div class="form-group">
                                            {!! Form::label('translations['.$key.'][content]', 'Introtekst:',['class'=>'control-label']) !!}
                                            {!! Form::textarea('translations['.$key.'][content]',(((isset($sitemap->translations[$key]->content))) ? htmlspecialchars($sitemap->translations[$key]->content) : null), ['class' => 'form-control editor','rows' => '5']) !!}
                                        </div>





                                        {{--$post_type--}}
                                        {{--@include('admin.sitemap.'.$template->db_table_name.'')--}}

                                        @include('sitemap::'.$template->db_template_name.'')

                                    </div>






                                </div>
                                <?php
                                }//endforeach ?>









                            </div>
                        </div>
                    </div>



                </div>

            </div>

            <!--end content -->

            <!--start reference -->
            <div class="tab-pane fade banner-pane-x {{settings()->viewConfig('sitemap_form_left_tab','seo',['active in',''])}}" id="demo-stk-lft-tab-4">

                @include('sitemap::partials._form_seo')
            </div>
            <!--end reference -->


            <!--start reference -->
            <div class="tab-pane fade banner-pane {{settings()->viewConfig('sitemap_form_left_tab','banners',['active in',''])}}" id="demo-stk-lft-tab-2">
                @include('sitemap::partials._form_references')
            </div>
            <!--end reference -->

            <div class="tab-pane fade {{settings()->viewConfig('sitemap_form_left_tab','setting',['active in',''])}}" id="demo-stk-lft-tab-3">


                <div class="tab-base-nestd tabxs-left">

                    <!--Nav Tabs-->
                    <ul class="nav nav-tabs-nested">
                        <li class="active">
                            <a href="#demo-lft-tab-1" data-toggle="tab">Home</a>
                        </li>
                        <li>
                            <a href="#demo-lft-tab-2" data-toggle="tab">Profile</a>
                        </li>
                        <li>
                            <a href="#demo-lft-tab-3" data-toggle="tab">Setting</a>
                        </li>
                    </ul>

                    <!--Tabs Content-->
                    <div class="tab-content">
                        <div class="tab-pane fade active in" id="demo-lft-tab-1">
                            <h4 class="text-thin">First Tab Content</h4>
                            <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit.</p>
                        </div>
                        <div class="tab-pane fade" id="demo-lft-tab-2">
                            <h4 class="text-thin">Second Tab Content</h4>
                            <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit.</p>
                        </div>
                        <div class="tab-pane fade" id="demo-lft-tab-3">
                            <h4 class="text-thin">Third Tab Content</h4>
                            <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit.</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>



<div class="col-lg-4">






    <div class="panel">


        <div class="panel">
            <div class="modal-headexr panel-heading">
                <div class="panel-control">
                    <i class="fa fa-thumbs-o-up fa-lg fa-fw"></i>



                    <?php

                    if (!(isset($sitemap->status))){
                        $statusValue = 'new';
                        $statusButtonValue = 'hidden';
                    }
                    else{
                    $statusValue = $sitemap->status;
                    $statusButtonValue = 'bg-gray-dark';
                    if ($sitemap->status == 'pending_review'){
                    $statusButtonValue = 'bg-warning';
                    $statusValue = 'wacht op goedkeuring';
                    }
                    if ($sitemap->status == 'online'){
                    $statusButtonValue = 'bg-success';
                    }

                    }
                    ?>




                    <span class="badge {{$statusButtonValue}}">{{$statusValue}}</span>
                    <span class="label label-purple">Administrator</span>
                </div>
                <h3 class="panel-title">Publiceren</h3>


            </div>

            <div class="panel-body" style="padding-top: 0px; padding-bottom: 0px;">
                <div class="panel-body">
                    {!! Form::hidden('post_type', $post_type) !!}
                    <div class="form-group hidden">
                        {!! Form::label('system_name', 'system_name:') !!}
                        {!! Form::hidden('system_name', null, ['class' => 'form-control']) !!}
                    </div>

                    <div class="form-group hidden">
                        {!! Form::label('online', 'online:') !!}



                        {!! Form::checkbox('online', 1, ['class' => 'form-control','id' => 'online']) !!}
                    </div>

                    <div class="form-group" style="height:30px;margin-bottom:15px;">
                        {!! Form::label('status', 'status:',['class'=>'col-md-4 control-label']) !!}
                                <!-- Bootstrap Select with Option Groups -->
                        <!--===================================================-->
                        {!! Form::select('status', $status_list,null, ['class' => 'selectpicker col-md-8']) !!}

                    </div>


                    <?php
                    if ((isset($sitemap->status))){?>

                    <div class="form-group" style="height:30px;margin-bottom:15px;">
                        <label for="" class="col-md-4 control-label">Aangemaakt op:</label>
                        <div class="col-md-8">
                            <input class="form-control" placeholder="{{$sitemap->created_at->formatLocalized('%A %d %B %Y')}}" disabled="" type="text">
                            <small class="help-block">{{$sitemap->created_at->diffForHumans()}} door {{$sitemap->user->name}}</small>
                        </div>
                    </div>
                    <div class="form-group" style="height:30px;margin-bottom:15px;">
                        <label for="" class="col-md-4 control-label">Aangepast op: </label>
                        <div class="col-md-8">
                            <input class="form-control" id="sitemap_updated_at" placeholder="{{$sitemap->updated_at->formatLocalized('%A %d %B %Y')}}" disabled="" type="text">
                            <small id="sitemap_updated_at_info" class="help-block">{{$sitemap->updated_at->diffForHumans()}} door {{$sitemap->user->name}}</small>
                        </div>
                    </div>

                    <?php
                    }?>






                </div>
            </div>

            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-default">Reset</button>


                <button class="btn btn btn-primary btn-default pull-right submit-sitemap" type="submit">
                    <i class="fa fa-spinner fa-spin" style="display: none;"></i> {{$submitButtonText}}</button>
            </div>
        </div>






    </div>




    <!--===================================================-->
    <!-- END DISABLED FORM ELEMENTS -->


    <!--Collapsed Panel by default-->
    <!--===================================================-->
    <div class="panel">

        <!--Panel heading-->
        <div class="panel-heading" style="bxxorder-color:silver">
            <div class="panel-control">
                <div  class="btn btn-default" data-target="#demo-panel-collapse-default" data-toggle="collapse"><i class="fa fa-chevron-down"></i></div>
                <i class="fa fa-thumbs-o-up fa-lg fa-fw"></i>
                <span class="badge badge-pink">7</span>
                <span class="label label-purple">Administrator</span>

            </div>
            <h3 class="panel-title" role="button" data-toggle="collapse" data-target="#demo-panel-collapse-default" aria-expanded="false" aria-controls="demo-panel-collapse-default">
                Opties
            </h3>
        </div>

        <!--Panel body-->
        <div id="demo-panel-collapse-default" class="collapse panel-body" style="padding-top: 0px; padding-bottom: 0px;">
            <div class="panel-body">
                <div class="form-group">
                    {!! Form::label('parent_id', 'sitemap parent:') !!}
                    {!! Form::select('parent_id', $sitemap_list,Request::get('parent_id'), ['class' => 'form-control']) !!}
                </div>


                <div class="form-group">
                    {{ Form::label('template_id', 'template:') }}
                    {!! Form::select('template_id', $template_list,Request::get('template_id'), ['class' => 'form-control']) !!}
                </div>


                <!--- Depth Field --->
                <div class="col-md-12 pad-no">
                    {!! Form::label('depth', 'depth:') !!}
                    {!! Form::text('depth', null, ['class' => 'form-control']) !!}
                    <hr class="hr-sm">

                </div>

                <!--- Order_by Field --->
                <div class="col-md-12 pad-no">
                    {!! Form::label('order_by_number', 'order by number:') !!}
                    {!! Form::text('order_by_number', null, ['class' => 'form-control']) !!}
                    <hr class="hr-sm">
                </div>

                <!--- Form Field --->
                <div class="col-md-12 pad-no">
                    {!! Form::label('form', 'form:') !!}
                    {!! Form::text('form', null, ['class' => 'form-control']) !!}
                    <hr class="hr-sm">
                </div>


            </div>
        </div>
    </div>
    <!--===================================================-->
    <!--End Collapsed Panel by default-->



    <div class="panel panel-success hidden">

        <!--Panel heading-->
        <div class="panel-heading">
            <div class="panel-control">
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#demo-tabs2-box-1" data-toggle="tab">
                            <i class="fa fa-magic fa-lg"></i>
                        </a>
                    </li>
                    <li>
                        <a href="#demo-tabs2-box-2" data-toggle="tab">
                            <i class="fa fa-user fa-lg"></i>
                        </a>
                    </li>
                </ul>
            </div>
            <h3 class="panel-title">A-B test (alles via tabs?)</h3>
        </div>

        <!--Panel Body-->
        <div class="panel-body">
            <div class="tab-content">
                <div class="tab-pane fade in active" id="demo-tabs2-box-1">
                    <h4 class="text-thin"><i class="fa fa-magic fa-fw"></i> Magic Icon</h4>
                    <p>Nulla vel metus scelerisque ante sollicitudin commodo.</p>
                </div>
                <div class="tab-pane fade" id="demo-tabs2-box-2">

                    <div class="widget-body text-center">
                        <div class="panel-body">


                            <?php
                            if ((isset($sitemap->status))){?>
                            <h4 class="mar-no">{{$sitemap->user->name}}</h4>
                            <p class="text-muted mar-btm">Administrator</p>
                            Aangepast op:<br>
                            {{$sitemap->updated_at->diffForHumans()}}
                            {{$sitemap->updated_at->formatLocalized('%A %d %B %Y')}}
                            <?php
                            }?>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>


</div>



@section('css.head')
    @include('core::partials.head_tinymce')

    <script>
        var apiRoute = '{{route('admin::api.media.modal')}}';
        var formMediaTemplateUrl = '/js/wi/angular/form_media_field.html';
        var modalCreateMediaTemplateUrl = '/js/wi/angular/dropzone1.php';


        ///admin/media/modal_select_media
        //var modalSelectMediaUrl = '/admin/media/modal_select_media';
        var modalSelectMediaUrl = '{{route('admin::api.modal.select.media')}}';

        var modalCreateMediaUrl = '{{route('admin::api.modal.create.media')}}';
        var mediaUploadUrl = '{{route('admin::media.upload')}}';

        var formMediaFieldEditMediaUrl = '{{route('admin::media.index')}}';



        //alert(modalSelectMediaUrl);

        //templateUrl: '/admin/media/modal_select_media',
        //alert(apiRoute);
    </script>
    <script src="{{config('wi.dashboard.theme_path')}}/js/jquery_ui_1_11_4/jquery-ui.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.4.7/angular.min.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.4.7/angular-animate.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.4.7/angular-sanitize.js"></script>
    <script src="//angular-ui.github.io/bootstrap/ui-bootstrap-tpls-0.14.3.js"></script>

    <script src="/js/wi/angular/angular-dragdrop.js"></script>
    <script src="/js/wi/angular/myAngular.js"></script>
    <script src="/js/wi-form.js"></script>
@endsection

@section('scripts.footer')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.2.0/dropzone.js"></script>
    <script>


        $(function () {
            $window = $(window), $("body.home").length && (tinymce.init({
                selector: "textarea",
                width: 752,
                height: 261,
                resize: !1,
                plugins: ["advlist", "autolink", "lists", "link", "image", "charmap", "print", "preview", "anchor", "searchreplace", "visualblocks", "code", "fullscreen", "insertdatetime", "media", "table", "contextmenu", "paste", "imagetools"],
                toolbar: "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist | link image"
            }), tinymce.init({
                selector: "textarea",
                width: "100%",
                resize: !1,
                plugins: ["autoresize", "advlist", "autolink", "lists", "link", "image", "charmap", "print", "preview", "anchor", "searchreplace", "visualblocks", "code", "fullscreen", "insertdatetime", "media", "table", "contextmenu", "paste", "imagetools"],
                autoresize_max_height: 161,
                menubar: !1,
                elementpath: !1,
                statusbar: !1,
                toolbar: "bold italic | alignleft aligncenter alignright alignjustify",
                content_style: "body { padding-bottom: 0 !important; }"
            })), $("body.pricing").length && (tinymce.PluginManager.load("moxiemanager", "/pro-demo/moxiemanager/plugin.min.js"), tinymce.PluginManager.load("powerpaste", "/pro-demo/powerpaste/plugin.min.js"), tinymce.PluginManager.load("tinymcespellchecker", "/pro-demo/tinymcespellchecker/plugin.min.js"), tinymce.PluginManager.load("a11ychecker", "/pro-demo/a11ychecker/plugin.min.js"), tinymce.PluginManager.load("mentions", "/pro-demo/mentions/plugin.min.js"), tinymce.init({
                selector: "textarea",
                plugins: ["advlist autolink lists link image charmap print preview anchor", "searchreplace visualblocks code fullscreen", "insertdatetime media table contextmenu moxiemanager imagetools powerpaste tinymcespellchecker a11ychecker mentions"],
                mentions_fetch: function (e, n) {
                    var t = ["Andrew Roberts", "Amy Chen", "Tim Thatcher", "Jeff Olson", "John Hummelstad", "David Spreng", "Gary Kovacs", "Misha Logvinov", "Michael Fromin", "Lisa Newsome", "Ketaki Joshi", "Jennifer Knowlton", "Wynne Vick", "Robert Collings", "Jessica Lee", "Colin Westacott", "Ken Hodges", "Ivan White", "Richard Garcia", "Shirin Abbaszadeh", "Joakim Lindkvist", "Johan Sörlin", "Damien Fitzpatrick", "Brett Henderson", "David Wood", "Andrew Herron", "Jack Mason", "Dylan Just", "Morgan Smith", "Malcolm Sharman", "Mark Terry", "Mike Chau", "Maurizio Napoleoni", "Mark Ludlow", "Andreas Huemer", "Joshua Haines", "George Wilson", "Luke Butt", "David Sakko", "Jeremy Carver", "Dayne Lean", "James Johnson", "Ben Kolera", "Sneha Choudhary", "Anna Harrison", "Bill Roberts", "Therese Lavelle", "Irene Goot", "Mai Tran", "John Doe", "Jane Doe"];
                    t = $.map(t, function (e) {
                        var n = e.replace(/ /g, "").toLowerCase();
                        return {id: n, name: n, fullName: e}
                    }), t = $.grep(t, function (n) {
                        return 0 === n.name.indexOf(e.term)
                    }), n(t)
                },
                toolbar: "insertfile a11ycheck undo redo | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist | link image",
                autosave_ask_before_unload: !1,
                content_style: "h1 {font-size: 32px; color: #0089ee}",
                width: 752,
                height: 261,
                resize: !1,
                powerpaste_allow_local_images: !0,
                spellchecker_rpc_url: "https://spelling.tinymce.com/ephox-spelling",
                spellchecker_api_key: "h22wb7h8xi78b4fyo46hhx5k7fbh46vt5f6yqmvd492iy00c"
            })), $(".not-found").length && ga("send", {
                hitType: "event",
                eventCategory: "Website",
                eventAction: "404",
                eventLabel: document.location.href
            }), $("body.custom-builds").length && ($(".checkbox").checkbox(), $(".switch")["switch"](), $(".select-none").on("click", function () {
                $(this).siblings(".checkbox").removeClass("selected")
            }), $(".select-all").on("click", function () {
                $(this).siblings(".checkbox").addClass("selected")
            }), $(".custom-builds-submit-row button").on("click", function () {
                var e = {core: "core_standalone"};
                $(".custom-builds .checkbox").each(function () {
                    var n = $(this);
                    n.attr("data-name") && n.hasClass("selected") && (e[n.attr("data-name")] = !0)
                });
                var n = $(".custom-builds-form").empty();
                Object.keys(e).forEach(function (t) {
                    $('<input type="hidden" value="' + e[t] + '" name="' + t + '">').appendTo(n)
                }), n.submit()
            })), $("body.language-packages").length && ($(".checkbox").checkbox(), $("tr").on("click", function (e) {
                "TD" === $(e.target).prop("tagName") && $(this).find(".checkbox").toggleClass("selected")
            }), $(".select-none").on("click", function () {
                $(".checkbox").removeClass("selected")
            }), $(".select-all").on("click", function () {
                $(".checkbox").addClass("selected")
            }), $(".language-packages-submit-row button:last-child").on("click", function () {
                var e = $(".language-packages-form").empty();
                $(".checkbox").each(function () {
                    var n = $(this);
                    n.hasClass("selected") && $('<input type="hidden" value="' + n.attr("data-value") + '" name="' + n.attr("data-name") + '">').appendTo(e)
                }), e.submit()
            })), $(".download-track").on("click", function () {
                ga("send", {
                    hitType: "event",
                    eventCategory: "Website",
                    eventAction: "download",
                    eventLabel: $(this).attr("data-version")
                })
            }), $(".click-track").on("click", function () {
                ga("send", {hitType: "event", eventCategory: "Website", eventAction: "click", eventLabel: $(this).text()})
            })
        });



    </script>
@endsection




@section('scripts.footer0uit')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.2.0/dropzone.js"></script>
        <!--Summernote [ OPTIONAL ]
<script src="/nifty/plugins/summernote/summernote.js"></script>-->

<script>

    $(document).ready(function() {
        $('.summernote').summernote({

            //ui:[
            //   ['toolbar','<div class="note-toolbar panel-heading test"/>']
            //],
            //airMode: true,
            toolbar: [
                ['style', ['style']],
                //['font', ['bold', 'italic', 'underline', 'superscript', 'subscript', 'strikethrough', 'clear']],
                ['font', ['bold', 'italic', 'underline']],
                //['fontname', ['fontname']],
                // ['fontsize', ['fontsize']], // Still buggy
                //['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                //['height', ['height']],
                //['table', ['table']],
                //['insert', ['link', 'picture', 'video', 'hr']],

                ['insert', ['link']],
                ['view', ['fullscreen', 'codeview']],
                ['help', ['help']]
            ],
            icons: {
                'align': 'fa fa-align',
                'alignCenter': 'fa fa-align-center',
                'alignJustify': 'fa fa-align-justify',
                'alignLeft': 'fa fa-align-left',
                'alignRight': 'fa fa-align-right',
                'indent': 'fa fa-align-indent',
                'outdent': 'fa fa-align-outdent',
                'arrowsAlt': 'fa fa-arrows-alt',
                'bold': 'fa fa-bold',
                'caret': 'caret',
                'circle': 'fa fa-circle',
                'close': 'fa fa-close',
                'code': 'fa fa-code',
                'eraser': 'fa fa-eraser',
                'font': 'fa fa-font',
                'frame': 'fa fa-frame',
                'italic': 'fa fa-italic',
                'link': 'fa fa-link',
                'unlink': 'fa fa-chain-broken',
                'magic': 'fa fa-magic',
                'menuCheck': 'fa fa-check',
                'minus': 'fa fa-minus',
                'orderedlist': 'fa fa-list-ol',
                'pencil': 'fa fa-pencil',
                'picture': 'fa fa-picture-o',
                'question': 'fa fa-question',
                'redo': 'fa fa-redo',
                'square': 'fa fa-square',
                'strikethrough': 'fa fa-strikethrough',
                'subscript': 'fa fa-subscript',
                'superscript': 'fa fa-superscript',
                'table': 'fa fa-table',
                'textHeight': 'fa fa-text-height',
                'trash': 'fa fa-trash',
                'underline': 'fa fa-underline',
                'undo': 'fa fa-undo',
                'unorderedlist': 'fa fa-list-ul',
                'video': 'fa fa-youtube-play'
            },
            callbacks: {
                onPaste: function (e) {
                    //alert('test');
                    var bufferText = ((e.originalEvent || e).clipboardData || window.clipboardData).getData('Text');
                    //console.info(bufferText);
                    e.preventDefault();

                    // Firefox fix
                    setTimeout(function () {
                        document.execCommand('insertText', false, bufferText);
                    }, 10);
                }
            },
            styleWithSpan: false
        });
    });


    // SUMMERNOTE
    // =================================================================
    // Require Summernote
    // http://hackerwins.github.io/summernote/
    // =================================================================


    //$(document).ready(function() {
    //$('.summernote').summernote({});

    //$('.note-codable').eq(0).attr('name','test');
    /*
    $('.summernote').summernote({
        callbacks: {
            onPaste: function (e) {
                //alert('test');
                var bufferText = ((e.originalEvent || e).clipboardData || window.clipboardData).getData('Text');
console.info(bufferText);
                e.preventDefault();

                // Firefox fix
                setTimeout(function () {
                    document.execCommand('insertText', false, bufferText);
                }, 10);
            }
        },
        styleWithSpan: false
    });
*/

    //});
</script>
@endsection





























