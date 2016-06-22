<!--- Slug Field
<div class="form-group">
    {!! Form::label('translations['.$key.'][defaultpage][subtitle]', 'Subtitel:') !!}
    {!! Form::text('translations['.$key.'][defaultpage][subtitle]', null, ['class' => 'form-control']) !!}
</div>--->

<div class="form-group">
    {!! Form::label('translations['.$key.'][defaultpage][content]', 'Tekst') !!}

    {!! Form::textarea('translations['.$key.'][defaultpage][content]',(((isset($sitemap->translations[$key]->defaultpage->content))) ? htmlspecialchars($sitemap->translations[$key]->defaultpage->content) : null), ['class' => 'form-control summernote','rows' => '5']) !!}
</div>



<!--- Published_at Field --->
<div class="panel" style="border:1px solid silver">
    <div class="panel-body">
    <strong>voor later</strong>
<div class="form-group">
    {!! Form::label('translations['.$key.'][published_at]', 'published_at:') !!}
    {!! Form::text('translations['.$key.'][published_at]', (((isset($sitemap->translations[$key]->published_at))) ? $sitemap->translations[$key]->published_at : \Carbon\Carbon::now()), ['class' => 'form-control']) !!}
</div>
<!--- Published_until Field --->
<div class="form-group">
    {!! Form::label('translations['.$key.'][published_until]', 'published_until:') !!}
    {!! Form::text('translations['.$key.'][published_until]', (((isset($sitemap->translations[$key]->published_until))) ? $sitemap->translations[$key]->published_until : \Carbon\Carbon::now()->addYear(20)), ['class' => 'form-control']) !!}
</div>
    </div>
</div>

<div form-media-field
     label-value="OVERZICHT"
     button-select-value="Selecteer afbeelding"
     button-upload-value="Upload afbeelding"
     field-id="translations[{{$key}}][media][overzicht]"
     locale="{{$key}}"
     field-name="overzicht"
     related-media="{{isset($sitemap->translations[$key]->media['overzicht']) ? $sitemap->translations[$key]->media['overzicht'] : ''}}"
     dropzone-message="Drop file here INPUT TYPE MEDIA 1 translations[{{$key}}][overzicht]"
     xdropzone-mimetypes=".pdf"
     dropzone-max-file-size="50"
     media-type="image"
     class="form-group"
     style="height: 179px;margin-bottom: 15px;xdisplay:none;"
>
</div>


<div form-media-field
     label-value="CONTENT"
     button-select-value="Selecteer afbeelding"
     button-upload-value="Upload afbeelding"
     field-id="translations[{{$key}}][media][content]"
     locale="{{$key}}"
     field-name="content"
     related-media="{{isset($sitemap->translations[$key]->media['content']) ? $sitemap->translations[$key]->media['content'] : ''}}"
     dropzone-message="Drop file here INPUT TYPE MEDIA 1 translations[{{$key}}][content]"
     xdropzone-mimetypes=".pdf"
     dropzone-max-file-size="50"
     media-type="image"
     class="form-group"
     style="height: 179px;margin-bottom: 15px;xdisplay:none;"

>
</div>


<div form-media-field
     label-value="PDF"
     button-select-value="Selecteer een pdf"
     button-upload-value="Upload een pdf"
     field-id="translations[{{$key}}][media][news-pdf]"
     locale="{{$key}}"
     field-name="news-pdf"
     related-media="{{isset($sitemap->translations[$key]->media['news-pdf']) ? $sitemap->translations[$key]->media['news-pdf'] : ''}}"
     dropzone-message="Drop file here INPUT TYPE MEDIA 1 translations[{{$key}}][news-pdf]"
     dropzone-mimetypes=".pdf"
     dropzone-max-file-size="50"
     media-type="file"
     class="form-group"
     style="height: 179px;margin-bottom: 15px;xdisplay:none;"

>
</div>




