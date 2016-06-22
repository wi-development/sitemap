<!--- Slug Field --->
<div class="form-group hidden">
    {!! Form::label('translations['.$key.'][defaultpage][subtitle]', 'Subtitel:') !!}
    {!! Form::text('translations['.$key.'][defaultpage][subtitle]', null, ['class' => 'form-control']) !!}
</div>

<div class="form-group hidden">
    {!! Form::label('translations['.$key.'][defaultpage][content]', 'Tekst') !!}

    {!! Form::textarea('translations['.$key.'][defaultpage][content]',(((isset($sitemap->translations[$key]->defaultpage->content))) ? htmlspecialchars($sitemap->translations[$key]->defaultpage->content) : null), ['class' => 'form-control summernote-uit','rows' => '5']) !!}
</div>
