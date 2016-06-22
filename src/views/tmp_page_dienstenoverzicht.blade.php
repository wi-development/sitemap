<div class="form-group">
    {!! Form::label('translations['.$key.'][defaultpage][content]', 'Tekst (onder iconen, indien aanwezig)') !!}
    {!! Form::textarea('translations['.$key.'][defaultpage][content]',(((isset($sitemap->translations[$key]->defaultpage->content))) ? htmlspecialchars($sitemap->translations[$key]->defaultpage->content) : null), ['class' => 'form-control defaultx','rows' => '5'
    ,'style' => 'display: nonex;'
    ]) !!}
</div>
<?php
/*
<!--- Slug Field --->
<div class="form-group">
    <br>
    Optioneel (wordt nu niet gebruikt) :
</div>
<div class="form-group">
    {!! Form::label('translations['.$key.'][defaultpage][subtitle]', 'Subtitel:') !!}
    {!! Form::text('translations['.$key.'][defaultpage][subtitle]', null, ['class' => 'form-control']) !!}
</div>

*/
?>

{!! Form::hidden('translations['.$key.'][defaultpage][subtitle]', null, ['class' => 'form-control']) !!}


