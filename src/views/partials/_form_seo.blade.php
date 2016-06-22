    <div class="panel">

        <div class="panel-heading">
            <div class="panel-control">
                Goed voor Google
            </div>

            <h3 class="panel-title"><small>SEO</small></h3>
        </div>

        <div class="panel-body">

            <div class="form-tab tab-base-nestd">


                <!-- Nav tabs || $key == $translation->locale->identifier -->
                <ul class="nav nav-tabs-off nav-justified-off nav-tabs-nested" role="tablist">
                    @foreach($enabledLocales as $locale)
                        <?php

                        $key = $locale->languageCode;
                        $tClass = "";
                        if ($key==(session()->has('active_language_tab') ? session()->get('active_language_tab') : 'nl')){$tClass = " active";}
                        if (empty($sitemap->translations[''.$locale->languageCode.'']->id)){$tClass .= " new-locale";}
                        if (array_key_exists($key,$errors->getMessages())){$tClass .= " has-error";}
                        ?>
                        <li role="presentation" class="{{$tClass}}"><a href="#seo_{{$key}}" aria-controls="{{$key}}" data-tab-type="seo" role="tab" data-toggle="tab">
                                {{$locale->name}}
                            </a></li>
                    @endforeach

                </ul>
                <!-- Tab panes -->
                <div class="tab-content">

                    <?php
                    //foreach($sitemap->translations as $key => $translation){
                    foreach($enabledLocales as $locale){
                    $key = $locale->languageCode;
                    $language_id = $key;//for error list //or $key
                    $tClass = "";if ($key==(session()->has('active_language_tab') ? session()->get('active_language_tab') : 'nl')){$tClass = " active";}?>

                    <div role="tabpanel" class="tab-pane{{$tClass}} " id="seo_{{$key}}">
                        <div class="panel-body" style="padding:0px 20px">

                            @include('errors.sitemaptranslation')

                                    <!--- Title Field --->
                            <div class="form-group">
                                {!! Form::label('translations['.$key.'][title]', 'Title:',['class'=>'control-label']) !!}
                                {!! Form::text('translations['.$key.'][title]', null, ['class' => 'form-control']) !!}
                            </div>

                            <!--- Description Field --->
                            <div class="form-group">
                                {!! Form::label('translations['.$key.'][description]', 'Description:',['class'=>'control-label']) !!}
                                {!! Form::textarea('translations['.$key.'][description]',(((isset($sitemap->translations[$key]->description))) ? htmlspecialchars($sitemap->translations[$key]->description) : null), ['class' => 'form-control default','rows' => '5']) !!}
                            </div>

                            <!--- Keyword Field --->
                            <div class="form-group">
                                {!! Form::label('translations['.$key.'][keywords]', 'Keywords:',['class'=>'control-label']) !!}
                                {!! Form::text('translations['.$key.'][keywords]', null, ['class' => 'form-control']) !!}
                            </div>

                        </div>
                    </div>
                    <?php
                    }//endforeach ?>









                </div>
            </div>







        </div>



    </div>


