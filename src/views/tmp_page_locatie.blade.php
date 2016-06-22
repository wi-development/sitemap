
<!--- Subtitel Field --->
<div class="form-group hidden">
    {!! Form::label('translations['.$key.'][locationpage][subtitle]', 'Subtitel:') !!}
    {!! Form::text('translations['.$key.'][locationpage][subtitle]', null, ['class' => 'form-control']) !!}
</div>

<div class="form-group">
    {!! Form::label('translations['.$key.'][locationpage][content]', 'Tekst links') !!}

    {!! Form::textarea('translations['.$key.'][locationpage][content]',(((isset($sitemap->translations[$key]->locationpage->content))) ? htmlspecialchars($sitemap->translations[$key]->locationpage->content) : null), ['class' => 'form-control summernote','rows' => '15']) !!}
</div>

<div class="form-group">
    {!! Form::label('translations['.$key.'][locationpage][content_1]', 'Tekst rechts') !!}

    {!! Form::textarea('translations['.$key.'][locationpage][content_1]',(((isset($sitemap->translations[$key]->locationpage->content_1))) ? htmlspecialchars($sitemap->translations[$key]->locationpage->content_1) : null), ['class' => 'form-control summernote','rows' => '15']) !!}
</div>


<hr>

<div form-media-field
     label-value="GEBOUW"
     button-select-value="Selecteer afbeelding"
     button-upload-value="Upload afbeelding"
     field-id="translations[{{$key}}][media][gebouw]"
     locale="{{$key}}"
     field-name="gebouw"
     related-media="{{isset($sitemap->translations[$key]->media['gebouw']) ? $sitemap->translations[$key]->media['gebouw'] : ''}}"
     dropzone-message="Drop file here INPUT TYPE MEDIA 1 translations[{{$key}}][gebouw]"
     xdropzone-mimetypes=".pdf"
     dropzone-max-file-size="50"
     media-type="image"
     class="form-group"
     style="height: 179px;margin-bottom: 15px;xdisplay:none;"
>
</div>


<div form-media-field
     label-value="rondleiding"
     button-select-value="Selecteer afbeelding"
     button-upload-value="Upload afbeelding"
     field-id="translations[{{$key}}][media][rondleiding]"
     locale="{{$key}}"
     field-name="rondleiding"
     related-media="{{isset($sitemap->translations[$key]->media['rondleiding']) ? $sitemap->translations[$key]->media['rondleiding'] : ''}}"
     dropzone-message="Drop file here INPUT TYPE MEDIA 1 translations[{{$key}}][rondleiding]"
     xdropzone-mimetypes=".pdf"
     dropzone-max-file-size="50"
     media-type="image"
     class="form-group"
     style="height: 179px;margin-bottom: 15px;xdisplay:none;"
>
</div>
<!--- Name Field --->
<div class="form-group">
    {!! Form::label('translations['.$key.'][locationpage][name]', 'Naam:') !!}
    {!! Form::text('translations['.$key.'][locationpage][name]', null, ['class' => 'form-control']) !!}
</div>

<!--- address Field --->
<div class="form-group">
    {!! Form::label('translations['.$key.'][locationpage][address]', 'Adres:') !!}
    {!! Form::text('translations['.$key.'][locationpage][address]', null, ['class' => 'form-control']) !!}
</div>

<!--- postal_code Field --->
<div class="form-group">
    {!! Form::label('translations['.$key.'][locationpage][postal_code]', 'Postcode:') !!}
    {!! Form::text('translations['.$key.'][locationpage][postal_code]', null, ['class' => 'form-control']) !!}
</div>

<!--- state_region Field --->
<div class="form-group">
    {!! Form::label('translations['.$key.'][locationpage][state_region]', 'Provincie:') !!}
    {!! Form::text('translations['.$key.'][locationpage][state_region]', null, ['class' => 'form-control']) !!}
</div>

<!--- city Field --->
<div class="form-group">
    {!! Form::label('translations['.$key.'][locationpage][city]', 'Woonplaats:') !!}
    {!! Form::text('translations['.$key.'][locationpage][city]', null, ['class' => 'form-control']) !!}
</div>

<!--- country Field --->
<div class="form-group">
    {!! Form::label('translations['.$key.'][locationpage][country]', 'Land:') !!}
    {!! Form::text('translations['.$key.'][locationpage][country]', null, ['class' => 'form-control']) !!}
</div>


<!--- phone Field --->
<div class="form-group">
    {!! Form::label('translations['.$key.'][locationpage][phone]', 'Tel:') !!}
    {!! Form::text('translations['.$key.'][locationpage][phone]', null, ['class' => 'form-control']) !!}
</div>

<!--- phone Field --->
<div class="form-group">
    {!! Form::label('translations['.$key.'][locationpage][email]', 'Email:') !!}
    {!! Form::text('translations['.$key.'][locationpage][email]', null, ['class' => 'form-control']) !!}
</div>
<hr>

