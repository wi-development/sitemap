
    <div class="panel">

        <div class="panel-heading">
            <div class="panel-control">
                <i class="fa fa-cog fa-lg fa-fw"></i> {{$template->name}}
            </div>

            <h3 class="panel-title"><small>Banners wijzigen{{--$frmHeader--}}</small></h3>
        </div>
    </div>

    @if ((isset($template)))
        <div id="accordion" class="panel-group accordion">
            @foreach($template->components as $key => $component)

                <div class="panel">

                    <!--Accordion title-->
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a href="#collapse{{$key}}"
                               data-user-settings='{"sitemap_form_reference_tab":"{{$key}}"}'
                               data-toggle="collapse" data-parent="#accordion" class="collapsed" aria-expanded="false">
                                <strong>{{$component->name}}</strong> <span class="pull-right">(component {{$component->id}})</span>
                            </a>
                        </h4>
                    </div>
                    <?php
                    $tClass = ("panel-collapse collapse");
                    $tAria = ("false");
                    if ($key==(settings()->has('sitemap_form_reference_tab') ? settings()->get('sitemap_form_reference_tab') : '0')){
                        $tClass = ("panel-collapse collapse in");
                        $tAria = ("true");
                    }
                    ?>
                    <!--Accordion content-->
                    <div id="collapse{{$key}}" class="{{$tClass}}" aria-expanded="{{$tAria}}">
                        <div class="panel-body">

                            <div class="table-responsive">
                                <table class="table table-stripedx table-hover sortable ??" style="border-bottom:2px solid rgba(0,0,0,0.14)">
                                    <thead>
                                    <tr>

                                        <th>Naam</th>
                                        <th>Volgorde</th>
                                        <th>Laatst gewijzigd</th>
                                        <th>Online</th>
                                        <th>Status</th>
                                        <th>Type</th>
                                        <th class="text-centerx">Actie</th>
                                    </tr>
                                    </thead>
                                    <tbody class="sortable-pane">

                                    {{--$component->referencetypes->first()->name--}}
                                    @foreach($component->references as $key => $reference)







                                        <tr>

                                            <td>
                                                {{ ($reference->translations->first()->name) }}
                                            </td>
                                            <td>
                                                @if ($reference->selected)
                                                    {{ ($reference->order_by_number) }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td><span class="text-muted"><i class="fa fa-clock-o"></i> {{$reference->updated_at->toFormattedDateString()}}</span></td>
                                            <td>
                                                {{  Form::checkbox('reference['.$reference->id.']['.$component->id.']', $reference->id, $reference->selected) }}
                                            </td>
                                            <td>
                                                @if ($reference->selected)
                                                    <div class="labelx badge label-table label-success">Online</div>
                                                @else
                                                    <div class="labelx badge label-table label-warning">Offline</div>
                                                @endif

                                            </td>
                                            <td>

                                                @if (($component->referencetypes->where('id',$reference->referencetype_id)->count()) > 0)
                                                    {{($component->referencetypes->where('id',$reference->referencetype_id)->first()->name)}}
                                                @else
                                                    depricated by system
                                                @endif

                                            </td>
                                            <td>
                                                @if (isset($sitemap))
                                                    <a href="{{route('admin::reference.edit.fromsitemap', ['id' => $reference->id,'sitemap_id' => $sitemap->id])}}" class="btn-link">Edit</a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @if (isset($sitemap))
                                @foreach ($component->referencetypes as $key1 => $referencetype)
                                    @if ($referencetype->category != 'pre-defined')
                                        <a class="btn-link" href="{{route('admin::reference.create.fromsitemap', ['sitemap_id' => $sitemap->id,'component_id' => $component->id,'referencetype_id' => $referencetype->id])}}">'{{$referencetype->name}}' toevoegen</a><br>
                                    @endif
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        geen banners
    @endif
