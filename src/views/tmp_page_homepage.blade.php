<?php
/*
<!--- Intro Field --->
<div class="form-group">
    {!! Form::label('translations['.$key.'][homepage][content]', '[homepage][content] '.$key.'') !!}
    {!! Form::textarea('translations['.$key.'][homepage][content]', null, ['class' => 'form-control', 'rows' => '5']) !!}
</div>
*/
?>
{!! Form::hidden('translations['.$key.'][homepage][content]', '') !!}