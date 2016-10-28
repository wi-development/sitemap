<?php

namespace WI\Sitemap;

use WI\Core\Entities\Reference\Reference;

use WI\Core\Entities\Template\Template;
use WI\Sitemap\Repositories\SitemapRepositoryInterface;

#use WI\Sitemap\SitemapTranslation;
use Auth;
use Carbon\Carbon;
use Form;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use DB;
use Flash;
use Debugbar;

use Illuminate\Pagination\LengthAwarePaginator;

#use App\Sitemap;

use App\Http\Requests\SitemapRequest;
use Datatables;
use Illuminate\Support\Str;
use Response;
use Route;
use Session;
use URL;

class SitemapController extends Controller
{

  private $sitemap;
  //private $locale;

  public function __construct(SitemapRepositoryInterface $sitemap)
  {
    $this->sitemap = $sitemap;


    //$this->sitemap->getEnabledLocales()

    //app('debugbar')->warning('Watch out..');
    //$this->sitemap->enabledLocales;
    //$debugbar = new \DebugBar();
//

  }



/*
 * INDEX CONTROLLERS
 */

  public function getIndexAll($sitemap_id = 0){
    //dc($sitemap_parent_id);
    $tableConfig = [];

    if (Session::has('flash_notification')){
      Session::flash(
          'flash_notification', [
          'message'=>Session::get('flash_notification.message'),
          'level'=>Session::get('flash_notification.level')
      ]);
    }



    $sitemap = Sitemap::with('translation','template')->where('id',$sitemap_id)->first();

    //homepage
    if ((is_null($sitemap))){
      //dc('TEST');
      //empty object
      $sitemap = collect();
      $sitemap->id = null;
      $sitemap->parent_id = null;
      $sitemap->translation = collect();
      $sitemap->translation->name = 'Root';
      //$sitemap->translation->name = 'alle locaties menu index';
      $allowed_child_templates = collect();

      $tableConfig['allowSortable'] = false;
      $tableConfig['header'] = 'Alle pagina\'s';

      //tableConfig.header
    }
    else{
      if ($sitemap->parent_id == 0){
        $sitemap->translation->name = 'Homepage';
      }
      //dc($sitemap->template->id);
      $tableConfig['allowSortable'] = true;
      $tableConfig['header'] = 'Alle pagina\'s van \''.$sitemap->translation->name.'\'';
      $allowed_child_templates = Template::where('parent_id',$sitemap->template->id)->get();
    }
    $tableConfig['customSearchColumnValues'] = "['online','pending_review','concept']";




    $breadcrumb = [];
    if (($sitemap->count()>0) && ($sitemap->id != 1)) {
      $breadcrumb = $this->sitemap->getBreadcrumbByIdAndDepth($sitemap->id,$sitemap->depth);
    }

    Session::put(
        'previous_route', [
        'name'=>null,
        'url'=>(route(Route::currentRouteName(),['sitemap_parent_id'=>$sitemap_id])),
      //'url'=>(route('admin::locaties_sub.index',['sitemap_parent_id'=>$sitemap->id])),
        'anchorText'=> $tableConfig['header']
    ]);
    //Session::save();

    $retval = "";
    if ($allowed_child_templates->count() > 1) {
      $retval = "<div class=\"btn-group\">
                            <a class=\"btn btn-warning btn-labeled fa fa-cog\"
                               data-toggle = \"dropdown\" aria-expanded = \"false\"
                               href = \"none\" >Pagina toevoegen aan '".$sitemap->translation->name."'
                            </a>
                            <button class=\"btn btn-warning dropdown-toggle dropdown-toggle-icon\" data-toggle = \"dropdown\" type = \"button\" aria-expanded = \"false\">
                                <i class=\"dropdown-caret fa fa-caret-down\" ></i >
                            </button >
                            <ul class=\"dropdown-menu dropdown-menu-right\" role = \"menu\" >
                                <li class=\"dropdown-header\" > Kies een pagina type </li >";
      foreach ($allowed_child_templates as $key => $template) {
        $retval .= "<li>
                            <a class=\"\"
                               href=\"".route('admin::sitemap.create')."?template_id=".$template->id."&parent_id=".$sitemap->id."\"
                               >
                                <i class=\"fa fa-plus fa-1\" ></i >
                                ".$template->name." toevoegen aan '".$sitemap->translation->name."'
                            </a >
                        </li>";
      }
      $retval .= "    </ul>
                       </div>";
    }
    elseif ($allowed_child_templates->count() == 1){
      $retval = "<a class=\"btn btn-warning btn-labeled fa fa-cog btn-defxault\" href=\"".route('admin::sitemap.create')."?template_id=".$allowed_child_templates->first()->id."&parent_id=".$sitemap->id."\"
      >Pagina toevoegen aan '".$sitemap->translation->name."'</a>";
    }
    $allowed_child_templates_as_html = $retval;


    //dc(route('admin::sitemap.create').'');
    //dc($allowed_child_templates_as_html);

//allowSortable

    //dc($allowed_child_templates->toArray());
    /*return Response::json([
		'sitemap'=>$sitemap->toArray(),
		'allowed_child_templates'=>$allowed_child_templates->toArray(),
		'breadcrumbAsHTML'=>$breadcrumbAsHTML]);
*/
    //return "view";



    //dc($tableConfig);
    if(request()->ajax()){
      $breadcrumbAsHTML = $this->sitemap->getBreadcrumbForMenuIndexTableAjax($breadcrumb,'alle pagina\'s');
      //return response()->json(['name' => 'Abigail', 'state' => 'CA']);
      return Response::json([
          'sitemap'=>$sitemap->toArray(),
          'allowed_child_templates'=>$allowed_child_templates->toArray(),
          'allowed_child_templates_as_html'=>$allowed_child_templates_as_html,
        //'session_test'=> Session::all(),
          'breadcrumbAsHTML'=>$breadcrumbAsHTML,
          'tableConfig'=>$tableConfig
      ]);
    }
    else{
      $breadcrumbAsHTML = $this->sitemap->getBreadcrumbForMenuIndexTableAjax($breadcrumb,'alle pagina\'s');
      //$breadcrumbAsHTML = $this->sitemap->getBreadcrumbForMenuIndexTable($breadcrumb);
    }

    //$test=(route('admin::sitemap.index.menu.data'));
    //return $test;

    return view('sitemap::indexAll',compact('sitemap','allowed_child_templates','breadcrumbAsHTML','tableConfig'));
    //dc($sitemap->template->id);
    $template = "TEMPLASTE";

    //Gz18jBTf9sSi8epjsVzZy1UlbR2RPJpBx6IxNTEc

  }

  /**
   * Process datatables ajax request.
   * Used by 'admin.sitemap.menuIndex' via 'this.getMenuIndex()'
   * @return \Illuminate\Http\JsonResponse
   */
  public function indexAllData(Request $request,$sitemap_parent_id = 0)
  {
    /*
	 * get sitemap with urlpath etc
	 * */

    /*sitemap_category*/
    $sitemaps = Sitemap::leftJoin('sitemap_category as sc','sc.sitemap_id', '=', 'sitemaps.id')
        ->leftJoin('sitemaps as sctest', 'sc.sitemap_category_id','=','sctest.id')
        ->leftJoin('sitemaptranslations as sctest_st','sctest.id','=','sctest_st.sitemap_id')

        /*sitemap_relations*/
        ->join('sitemaptranslations as st','st.sitemap_id','= ','sitemaps.id')
        ->join('templates as t','t.id','=','sitemaps.template_id')
        ->join('users as u','u.id','=','sitemaps.updated_by_user_id')

        /*sitemap_tree*/
        ->leftJoin('sitemaps as down1','down1.id','=','sitemaps.parent_id')
        ->leftJoin('sitemaptranslations as st1','st1.sitemap_id','=','down1.id')

        ->leftJoin('sitemaps as down2','down2.id','=','down1.parent_id')
        ->leftJoin('sitemaptranslations as st2','st2.sitemap_id','=','down2.id')

        ->leftJoin('sitemaps as down3','down3.id','=','down2.parent_id')
        ->leftJoin('sitemaptranslations as st3','st3.sitemap_id','=','down3.id');

    /**Locale*/
    $sitemaps->where('st.locale_id',1)
        ->whereRaw('(st1.locale_id = 1 OR st1.locale_id IS NULL)')
        ->whereRaw('(st2.locale_id = 1 OR st2.locale_id IS NULL)')
        ->whereRaw('(st3.locale_id = 1 OR st3.locale_id IS NULL)')
        ->whereRaw('(sctest_st.locale_id = 1  OR sctest_st.locale_id IS null)');
    #AND t.type != 'post'
    $sitemaps->groupBy('sitemaps.id');

    if ($sitemap_parent_id != 0){
      $sitemaps->where('sitemaps.parent_id',$sitemap_parent_id);
    }

    /*order by*/
    //$sitemaps->orderBy('path');

    //uit
    if ($path = $request->get('path')) {
      //app('debugbar')->warning($locaties);
      //$sitemaps->orderBy('ABS(path)'); // additional users.name search
    }


    $sitemaps->select([
        'sitemaps.id',
        DB::raw('CONCAT(\'{"id":\',sitemaps.id,\',\',\'"order_by_number":\',sitemaps.order_by_number,\'}\') as reorderData_id_AND_order_by_number'),
        'sitemaps.created_at',
        'sitemaps.updated_at',
        'sitemaps.status',
        'sitemaps.order_by_number',
        't.name as templateName',
        'u.name as usersname',
        'sitemaps.depth',
        DB::raw('GROUP_CONCAT(DISTINCT(`st`.`name`)) as `testname` ,
                    GROUP_CONCAT(`sctest_st`.`name`) as `tName` ,
                    GROUP_CONCAT(`sctest_st`.`slug`) as `tSlug`'),
      //'st.name as testname',
      //'sctest_st.name as tName',
      //'sctest_st.slug as tSlug',
      //1.2.4.5 for display, hmm..
        DB::raw('CONCAT(\'0\',
                IF (down3.order_by_number IS NULL,\'\',CONCAT(\'.\',down3.order_by_number)),
                IF (down2.order_by_number IS NULL,\'\',CONCAT(\'.\',down2.order_by_number)),
                IF (down1.order_by_number IS NULL,\'\',CONCAT(\'.\',down1.order_by_number)),
                IF (sitemaps.order_by_number IS NULL,\'\',CONCAT(\'.\',sitemaps.order_by_number))
                ) as path'
        ),
      //1235 for sorting
        DB::raw("CONCAT('0',
                IF (down3.order_by_number IS NOT NULL AND down3.order_by_number < 10 ,CONCAT('.',down3.order_by_number),''),
                IF (down3.order_by_number IS NOT NULL AND down3.order_by_number > 9 ,CONCAT('.9.',down3.order_by_number),''),

                IF (down2.order_by_number IS NOT NULL AND down2.order_by_number < 10 ,CONCAT('.',down2.order_by_number),''),
                IF (down2.order_by_number IS NOT NULL AND down2.order_by_number > 9 ,CONCAT('.9.',down2.order_by_number),''),

                IF (down1.order_by_number IS NOT NULL AND down1.order_by_number < 10 ,CONCAT('.',down1.order_by_number),''),
                IF (down1.order_by_number IS NOT NULL AND down1.order_by_number > 9 ,CONCAT('.9.',down1.order_by_number),''),

                IF (sitemaps.order_by_number IS NOT NULL AND sitemaps.order_by_number < 10 ,CONCAT('.',sitemaps.order_by_number),''),
                IF (sitemaps.order_by_number IS NOT NULL AND sitemaps.order_by_number > 9 ,CONCAT('.9.',sitemaps.order_by_number),'')
                ) as path1
            "),
      //slug1/slug2/category!!/slug3
        DB::raw('CONCAT(
                IF (st3.slug IS NULL OR st3.slug = \'\' ,\'\',CONCAT(\'/\',st3.slug,\'\')),
                IF (st2.slug IS NULL OR st2.slug = \'\',\'\',CONCAT(\'/\',st2.slug,\'\')),
                IF (st1.slug IS NULL OR st1.slug = \'\',\'\',CONCAT(\'/\',st1.slug,\'\')),
                IF (t.name = \'Nieuwsbericht\',
	                IF (sitemaps.depth = 2 AND sctest_st.slug IS NOT NULL,CONCAT(\'/\',sctest_st.slug),\'\')
,               \'\'),
                IF (st.slug IS NULL OR st.slug = \'\',\'/\',CONCAT(\'/\',st.slug,\'\'))
                ) as urlPath'
        )
    ]);

    //dc($sitemaps->get());
    //return "view";
    //dc($sitemaps->get());
    //dc($sitemaps->pluck('testname')->all());
    //return "view";



    $datatable =  Datatables::of($sitemaps);
    $datatable->setRowId('sortable_'.'{{$id}}');
    //$datatable->orderColumn('path');
    // $datatable->orderColumn('path', 'email $1');


    //kanweg
    $datatable->addColumn('action', function ($sitemap) {

      $r = "<a class=\"btn btn-success btn-labeled-x\" href=\"".route('admin::sitemap.edit',['id'=>$sitemap->id])."\" >
                    <i class=\"fa fa-pencil fa-1x\"></i> editxxx</a> <br>";


      $r .= "<a class=\"btn btn-primary btn-labeled-x setTable\" onclick=\"wiLoad(".$sitemap->id.")\">
                    <i class=\"fa fa-level-down fa-1x\"></i>  sub pagins's</a> <br>";

      $r .= "<a class=\"btn btn-warning btn-labeled-x setTable\" onclick=\"wiDuplicate(".$sitemap->id.")\">
                    <i class=\"fa fa-copy fa-1x\"></i> copy</a> <br>";

      $r .= "<a class=\"btn btn-danger btn-labeled-x setTable\" onclick=\"wiDelete(".$sitemap->id.")\">
                    <i class=\"fa fa-trash fa-1x\"></i> delete</a> <br>";

      $r .= "<a class=\"btn btn-default btn-md btn-labeled-x\" href=\"".$sitemap->urlPath."\" target=\"_blank\">
                    <i class=\"fa fa-search fa-1x\"></i> preview</a>";

      return $r;
    });



    /*$datatable->editColumn('path', function ($test) {
		return $test->path;
	});
*/
    $datatable->editColumn('testname', function ($test) {



      $r = "<div class=\"extraData\" style='display:none;'>";

      //$r .= "<span class='pulxl-right'>".$test->urlPath."</span><br>";
      $r .= "<a class=\"btn btn-success btn-labeled-x\" href=\"".route('admin::sitemap.edit',['id'=>$test->id])."\" >
                    <i class=\"fa fa-pencil fa-1x\"></i> edit</a> ";


      $r .= "<a class=\"btn btn-primary btn-labeled-x setTable\" onclick=\"wiLoad(".$test->id.")\">
                    <i class=\"fa fa-level-down fa-1x\"></i>  sub pagins's</a> ";

      $r .= "<a class=\"btn btn-warning btn-labeled-x setTable\" onclick=\"wiDuplicate(".$test->id.")\">
                    <i class=\"fa fa-copy fa-1x\"></i> copy</a> ";

      $r .= "<a class=\"btn btn-danger btn-labeled-x setTable\" onclick=\"wiDeleteSitemap(".$test->id.")\">
                    <i class=\"fa fa-trash fa-1x\"></i> deleteX</a> ";

      $r .= "<a class=\"btn btn-default btn-md btn-labeled-x\" href=\"".$test->urlPath."\" target=\"_blank\">
                    <i class=\"fa fa-search fa-1x\"></i> preview</a> ";

      //$r .= "<a class=\"btn btn-default btn-md btn-labeled-x\" href=\"".$test->urlPath."\" target=\"_blank\"
      //        data-placement=\"right\" data-toggle=\"tooltip\" data-original-title=\"".$test->urlPath."\">
      //        <i class=\"fa fa-info fa-1x\"></i> info</a> ";

      $r .= "</div>";


      /*
				  $urlString = str_replace('/',' » ',$test->urlPath);
				  $urlString = htmlentities($urlString, ENT_QUOTES);

				  $pos = strrpos($urlString, "»");
				  if ($pos === false) { // note: three equal signs
					  // not found...
					  $pos1 = 'asdf';
				  }
				  else{
					  $pos1 = ($pos+1);
					  //$pos++;
					  $urlString = substr_replace($urlString, '<strong>', ($pos1), 0);

				  }
				  //unset($pos);
	  */


      $urlStringStart = str_replace('/',' » ',(str_limit($test->urlPath, strrpos($test->urlPath, "/"),' » ')));

      return "".$urlStringStart."<strong>".$test->testname."</strong><br><br>".$r." ";
    });

    $datatable->editColumn('status', function ($test) use ($sitemap_parent_id) {

      $statusValue = $test->status;
      if ($test->status == 'pending_review'){
        $statusValue = 'pending';
      }
      return "<span class=\"labelx badge label-table label-".$test->status."\">".$statusValue."</span>";
    });


    $datatable->editColumn('created_at', function ($test) {

      //$retval .= $test->created_at ? with(new Carbon($test->created_at))->format('l jS \\of F Y h:i:s A') : '';
      //Carbon::setLocale('fr');
      //$retval .= $test->created_at->formatLocalized('%l %jS \\of %F %Y h:i:s %A')."<br>";


      $retval = $test->created_at ? with(new Carbon($test->created_at))->diffForHumans() : '';
      $retval .= "<div class=\"extraData\" style='display:none;'>";
      $retval .= "<br><date><i class=\"fa fa-clock-o\" aria-hidden=\"true\"></i> ";
      $retval .= $test->created_at->formatLocalized('%a %d %B');
      $retval .= $test->created_at->format(', h:i');
      $retval .= "</date>";
      $retval .= "</div>";
      return $retval;
    });

    $datatable->editColumn('updated_at', function ($test) {
      $retval = $test->updated_at ? with(new Carbon($test->updated_at))->diffForHumans() : '';
      $retval .= "<div class=\"extraData\" style='display:none;'>";
      $retval .= "<br><date><i class=\"fa fa-clock-o\" aria-hidden=\"true\"></i> ";
      $retval .= $test->updated_at->formatLocalized('%a %d %B');
      $retval .= $test->updated_at->format(', h:i');
      $retval .= "</date>";
      $retval .= "</div>";
      return $retval;
    });

    if ($status = $request->get('status')) {
      app('debugbar')->warning($status);
      //console.info('zoeke status');
      //$datatable->where('sitemaps.status', 'like', "%{$status}%"); // additional users.name search
    }


    if ($status = $request->get('pathxx')) {
      //app('debugbar')->warning($locaties);
      //$datatable->orderBy('ABS(path)'); // additional users.name search
    }


    $datatable->orderColumn('path', 'path1 $1');
    //$datatable->orderColumn();


    return $datatable->make(true);
  }

  /**
   * Process datatables re-order ajax request.
   * Used by 'Datatables'
   * @return \Illuminate\Http\JsonResponse
   */
  public function sort(Request $request){
    $sortArray = $request->get('sortable_');
    if (is_null($sortArray)){
      $data = ['status' => 'warning', 'statusText' => 'Geen update', 'responseText' => 'Er was geen wijziging!? (sortArray is null)'];
      return response()->json($data,400);
    }
    try{
      $cnt = 0;
      foreach ($sortArray as $key => $sitemapId){
        $sitemap = sitemap::find($sitemapId);
        $sitemap->timestamps = false;
        $sitemap->order_by_number = ++$cnt;
        $sitemap->save();
        //- See more at: http://findnerd.com/list/view/Update-without-touching-timestamps-Laravel/10269/#sthash.8m6PWJpT.dpuf

        //Sitemap::where('id',$sitemapId)->update(['order_by_number'=>++$cnt]);
      }
      $data = ['status' => 'success', 'statusText' => 'Update is gelukt', 'responseText' => 'De volgorde is aangepast'];
      return response()->json($data,200);
    }catch (\Exception $e){
      $data = ['status' => 'danger', 'statusText' => 'Update is mislukt!',
          'responseText' => '' . $e->getMessage() . '<br>line: ('.$e->getLine().')<bR>file: ('.$e->getFile().')'];
      return response()->json($data,400);
    }
    return "sort";
  }




/*
 * EDIT/UPDATE CONTROLLERS
 */

  /**
   * Show the form for editing the specified resource.
   *
   * @param Request $request
   * @return \Illuminate\Http\Response
   * @internal param int $id
   */
  public function edit(Request $request)
  {
    //dc('PACKAGE');
  	//dc(session('previous_route'));

    //dc(session->flash('previous_route_name'));
    //todo post_type nodig, JA DUS VOOR POST REQUEST
    $id = $request->route()->parameter('id') ? $request->route()->parameter('id') : null;
    //$post_type = $request->route()->parameter('post_type') ? $request->route()->parameter('post_type') : null;
    //if (is_null($post_type)){
    //    $template = Sitemap::with('template')->findOrFail($id)->template;
    //    $post_type = Sitemap::with('template')->findOrFail($id)->template->db_table_name;
    //}


    $template = Sitemap::with('template')->findOrFail($id)->template;

    //todo
    $allowedParentSitemaps = $this->sitemap->getAllowedParentSitemapsByTemplate($template);
    $sitemap_list = $this->sitemap->getParentSitemapList($allowedParentSitemaps);

    $enabledLocales = $this->sitemap->getEnabledLocales();



    $sitemap = $this->sitemap->getSelectedSitemap($id,$template);
    //dc($sitemap->categories);
    $sitemap = $this->sitemap->setSelectedReferencesBySitemap($sitemap);

    $this->sitemap->groupMediaCollectionByFieldName($sitemap->translations);


    //kan weg geen templates wijzigen
    $template_list = Template::lists('name','id');

    $template = $sitemap->template;
    //dc($template->db_template_name);
    //dc($template->sitemap_category_sitemap_parent_id);


    $category_list_config = collect([]);
    //dc($category_list_config);
    if ($sitemap->parent_id != 0){
      $parent_template = Sitemap::with('template')->findOrFail($sitemap->parent_id)->template;
      if ($parent_template->sitemap_category_sitemap_parent_id != ""){
        $category_list_config = collect(['sitemap_category_key' => 'locatie_als_nieuwsgroep',
            'frm_type' => 'radio',
            'category_list' => Sitemap::select(DB::raw('sitemaps.id,sitemaptranslations.name'))
                ->join('sitemaptranslations', 'sitemaps.id', '=', 'sitemaptranslations.sitemap_id')
                ->join('locales', 'locales.id', '=', 'sitemaptranslations.locale_id')
                ->where('locales.languageCode',app()->getLocale())
                //->where('sitemaptranslations.locale_id',1)
                ->where('parent_id',$parent_template->sitemap_category_sitemap_parent_id)
                ->orderBy('order_by_number', 'asc')
                //-
                ->lists('name','id')
        ]);
      }
    }


    if ($sitemap->template->name == "Diensten pagina") {
      //if ($parent_template->sitemap_category_sitemap_parent_id != ""){
      $category_list_config = collect(['sitemap_category_key' => 'locatie_als_dienstgroep',
          'frm_type' => 'checkbox',
          'category_list' => Sitemap::select(DB::raw('sitemaps.id,sitemaptranslations.name'))
              ->join('sitemaptranslations', 'sitemaps.id', '=', 'sitemaptranslations.sitemap_id')
              ->join('locales', 'locales.id', '=', 'sitemaptranslations.locale_id')
              ->where('locales.languageCode', app()->getLocale())
              //->where('sitemaptranslations.locale_id',1)
              ->where('parent_id', 37)
              ->orderBy('order_by_number', 'asc')
              //-
              ->lists('name', 'id')
      ]);
      //}
    }

    //dc($category_list_config);

    //dc($category_list->count());
    $status_list = collect(['online'=>'Online','pending_review'=>'Wacht op review','concept'=>'Concept']);

    //dc($sitemap);
    $post_type = $template->slug;//voor PostRequest




    /*
	 * PREV NEXT
	 * */

    $sql = "select id,parent_id,depth,order_by_number,status,
                IF ((order_by_number < :order_by_number1), 'prev_order_by_number','next_order_by_number') as test
                from sitemaps
                where
                (
                    (order_by_number = IFNULL((select min(order_by_number) as next_order_by_number from sitemaps where parent_id = :parent_id2 and order_by_number > :order_by_number3),0)
                        and parent_id = :parent_id4)
                        or
                        (order_by_number = IFNULL((select max(order_by_number) as prev_order_by_number from sitemaps where parent_id = :parent_id5 and order_by_number < :order_by_number6),0)
                        and parent_id = :parent_id7)
                )
                order by order_by_number;";


    //dc($sitemap->id);
    //dc($sitemap->parent_id);
    //dc($sitemap->order_by_number);

    $results = DB::select(DB::raw($sql)
        ,array(
            'order_by_number1' => $sitemap->order_by_number,

            'parent_id2' => $sitemap->parent_id,
            'order_by_number3' => $sitemap->order_by_number,
            'parent_id4' => $sitemap->parent_id,

            'parent_id5' => $sitemap->parent_id,
            'order_by_number6' => $sitemap->order_by_number,
            'parent_id7' => $sitemap->parent_id
        )
    );


    //$results = (($results));
    $prevNextSitemap = [];
    $prevNextSitemap['prev_order_by_number'] = array_where($results, function ($key, $value) {
      if ($value->test == 'prev_order_by_number'){
        return (($value));
      }
    });

    $prevNextSitemap['next_order_by_number'] = (array_where($results, function ($key, $value) {
      if ($value->test == 'next_order_by_number'){
        return (($value));
      }
    }));

    $prevNextSitemap['prev_order_by_number'] = head($prevNextSitemap['prev_order_by_number']);
    $prevNextSitemap['next_order_by_number'] = head($prevNextSitemap['next_order_by_number']);


    $sitemap->prevNextSitemap = $prevNextSitemap;
    /*
	 * END PREV NEXT
	 * */

    //dc(($prevNextSitemap));





    $sitemap->updated_at_as_formatLocalized = $sitemap->updated_at->formatLocalized('%A %d %B %Y');
    $sitemap->updated_at_info = $sitemap->updated_at->diffForHumans().' door '.$sitemap->user->name;
    //update via ajax
    if (request()->ajax()) {
      $data = ['status' => 'success', 'statusText' => 'Update gelukt!', 'responseEnabledLocales'=>$enabledLocales->pluck('languageCode'),'responseSitemap' => $sitemap, 'responseText' => '\''.$sitemap->translations[''.(session()->has('active_language_tab') ? session()->get('active_language_tab') : 'nl').'']->name.'\' is gewijzigd'];
      return response()->json($data, 200);
    }

    return view('sitemap::edit',compact('sitemap','post_type','template','enabledLocales','template_list','sitemap_list','status_list','category_list_config'));
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(SitemapRequest $request, $id)
  {

    //get sitemap order_by_number



    if ($request->get('order_by_number') == '0'){
      $siblings_of_updated_sitemap = Sitemap::where('parent_id',($request->get('parent_id')))->get();
      //dc($siblings_of_updated_sitemap);
      $sitemap_order_by_number = ($siblings_of_updated_sitemap->max('order_by_number')+1); //null+1 = 1
      //dc($sitemap_order_by_number);
      $request->merge(array('order_by_number' => $sitemap_order_by_number));
    }

    //get sitemap depth
    if (($request->get('depth') == '')){
      $sitemap_depth = (Sitemap::where('id',$request->get('parent_id'))->first()->depth+1);
      $request->merge(array('depth' => $sitemap_depth));
    }
    //dc($sitemap_order_by_number);
    //dc($sitemap_depth);
    //return "view";




    //return "view";


    $request->merge(array('updated_by_user_id' => auth()->user()->id));

    DB::transaction(function () use ($request,$id) {
      try
      {
        $enabledLocales = $this->sitemap->getEnabledLocales();

        $template = Sitemap::with('template')->findOrFail($id)->template;



        //todo to much sql
        $sitemap = $this->sitemap->getSelectedSitemap($id,$template);
        $sitemap->update($request->all());


        //sitemapCategory[locatie][]


        //dc($sitemap->categories);


        if ($request->has('sitemapCategory')){
          $syncArray = [];
          //dc('test');
          foreach ($request['sitemapCategory'] as $formName => $formValue) {
            //dc($formName);
            //dc($formValue);
            $cnt = 1;
            $sitemap->categories()->where('category',$formName)->delete();
            //$flight->delete();

            //$sitemap->categories()->destroy(1);
            foreach ($formValue as $key2 => $sitemap_category_id) {
              //echo $mediatranslation_id;
              $syncArray = [
                  'sitemap_id' => $id,
                  'sitemap_category_id' => (int)$sitemap_category_id,
                  'category' => $formName,
                  'order_by_number' => $cnt++
              ];
              $sitemap->categories()->insert($syncArray);
            }

          }
        }




        //$sitemap->touch();
        //$sitemap->timestamp('updated_at')->useCurrent();


        foreach($enabledLocales as $key => $enabledLocale){
          $localeRequest = array_add($request->translations[$enabledLocale->languageCode]
              ,'locale_id', //no foreign key in view
              $enabledLocale->id);


          //translation
          //make slug from name
          if ($sitemap->id != 1){
            $localeRequest['slug'] = str_slug($request->translations[$enabledLocale->languageCode]['name']);
          }
          else{
            $localeRequest['slug'] = "";
          }

          if (isset($localeRequest['content'])){
            $localeRequest['content'] = clean($request->translations[$enabledLocale->languageCode]['content']);
          }


          $sitemap->translations[$enabledLocale->languageCode]->update($localeRequest);


          //translation->template
          $localeRequest[$template->slug]['content'] = clean($localeRequest[$template->slug]['content']);
          //dc($template->slug);

          $sitemap->translations[$enabledLocale->languageCode]->{$template->slug}->update($localeRequest[$template->slug]);

          //mediatranslation
          $this->sitemap->syncThisTranslationMediaTranslation($sitemap->translations[$enabledLocale->languageCode]->id,$request->translations[$enabledLocale->languageCode],$sitemap->translations[$enabledLocale->languageCode]);
        }

        //update reference_sitemap
        if ($request->has('reference')){
          $syncArray = [];
          $cnt=1;
          foreach($request->input('reference') as $key => $component_reference) {
            //dc($id);
            foreach($component_reference as $component_id => $reference_id) {
              //dc($component_id." = ".$reference_id);
              $testArray[] = [
                  'sitemap_id' => $id,
                  'reference_id' => $reference_id,
                  'component_id' => $component_id,
                  'order_by_number' => $cnt++
              ];

            }
          }
          $sitemap->references()->sync([]);//pivot 3 key
          $sitemap->references()->attach($testArray);
        }
        else{
          $sitemap->references()->sync([]);
        }
        Flash::success('Update is gelukt!');
      }
      catch (\Exception $e)
      {

        if (request()->ajax()){
          throw new \Exception('Updaten is niet gelukt.<br>SitemapController->update()
                        <br>'.$e->getMessage().' ');

        }
        Flash::error('Update is mislukt! '.$e->getMessage().'');
        dc($e);
        dd($e->getMessage());
        return redirect()->back();
      }
    });

    return redirect()->back();
  }




/*
 * CREATE/STORE CONTROLLERS
 */

  public function selectSitemapTypeBeforeCreate(Request $request)
  {

    //return "view";
    $choosen_template_id = $request->input('template_id');
    if ($choosen_template_id != null){
      return $this->create($request);
    }

    $template_list = Template::where('type', 'page')
        ->orWhere('type', 'post-container')
        ->orWhere('type', 'post')
        ->select('name','id')
        ->orderBy('order_by_number', 'asc')
        //->take(10)
        //->get()->all();
        ->lists('name','id');

    /* $template_list_goed = Template::where('type', 'page')
		 ->orWhere('type', 'post-container')
		 ->select('name','db_table_name')
		 ->orderBy('order_by_number', 'asc')
		 //->take(10)
		 //->get()->all();
		 ->lists('name','db_table_name');
*/


    $sitemap_list = [];
    return view('sitemap::select',compact('template_list','sitemap_list'));
    //return view('admin.sitemap.select',compact('template_list','sitemap_list'));
  }

  //used by create
  private function getTemplatesWithRelations(){
    return Template::with(['components.references.translations' => function ($q) {  // 1 query for photos with...
      $q->whereHas('locale', function ($q) { // ...1 subquery to filter the photos by related tags' name
        $q->where('languageCode', '' . app()->getLocale() . '');
        //$q->where('status', 'enabled');
      });
    }]);
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create(Request $request)
  {
    //get choosen template
    try{
      //from select
      if (!(is_null($request->input('template_id')))) {
        $choosen_template_id = $request->input('template_id');

        //todo
        $choosen_parent_sitemap_id = $request->input('parent_id');
        //dc($choosen_parent_sitemap_id);

        $choosenTemplate = $this->getTemplatesWithRelations()->findOrFail($choosen_template_id);
      }
      //from url
      else{
        $choosen_template_slug = $request->route()->parameter('template_slug') ? $request->route()->parameter('template_slug') : null;
        $choosenTemplate = $this->getTemplatesWithRelations()->where('slug',$choosen_template_slug)->firstOrFail();
        //todo
        $choosen_parent_sitemap_id = 1000;
      }
    }catch (\Exception $e){
      Flash::error('Error: '.$e->getMessage().' <br><br>template bestaat niet!');
      return redirect()->route('admin::sitemap.create');
    }






    //todo: get only allowed templates, these are all templates
    if ($choosenTemplate->type == 'post'){
      $template_list = Template::where('type', 'post')
          //->orWhere('type', 'xpost-container')
          //->orWhere('type', 'post')
          ->select('name', 'id')
          ->orderBy('order_by_number', 'desc')
          ->lists('name', 'id');
    }
    else{
      $template_list = Template::where('type', 'page')
          ->orWhere('type', 'post-container')
          ->select('name', 'id')
          ->orderBy('order_by_number', 'desc')
          ->lists('name', 'id');
    }


    $category_list_config = collect([]);
    //dc($category_list_config);
    //if ($sitemap->parent_id != 0){
    $choosen_parent_template = Template::findOrFail($choosenTemplate->parent_id);
    if ($choosen_parent_template->sitemap_category_sitemap_parent_id != ""){
      $category_list_config = collect(['sitemap_category_key' => 'locatie_als_nieuwsgroep',
          'frm_type' => 'radio',
          'category_list' => Sitemap::select(DB::raw('sitemaps.id,sitemaptranslations.name'))
              ->join('sitemaptranslations', 'sitemaps.id', '=', 'sitemaptranslations.sitemap_id')
              ->join('locales', 'locales.id', '=', 'sitemaptranslations.locale_id')
              ->where('locales.languageCode',app()->getLocale())
              //->where('sitemaptranslations.locale_id',1)
              ->where('parent_id',$choosen_parent_template->sitemap_category_sitemap_parent_id)
              ->orderBy('order_by_number', 'asc')
              //-
              ->lists('name','id')
      ]);
    }
    //}


    if ($choosenTemplate->name == "Diensten pagina") {
      //if ($parent_template->sitemap_category_sitemap_parent_id != ""){
      $category_list_config = collect(['sitemap_category_key' => 'locatie_als_dienstgroep',
          'frm_type' => 'checkbox',
          'category_list' => Sitemap::select(DB::raw('sitemaps.id,sitemaptranslations.name'))
              ->join('sitemaptranslations', 'sitemaps.id', '=', 'sitemaptranslations.sitemap_id')
              ->join('locales', 'locales.id', '=', 'sitemaptranslations.locale_id')
              ->where('locales.languageCode', app()->getLocale())
              //->where('sitemaptranslations.locale_id',1)
              ->where('parent_id', 37)
              ->orderBy('order_by_number', 'asc')
              //-
              ->lists('name', 'id')
      ]);
      //}
    }












    /*
			$parent_template = Sitemap::with('template')->findOrFail($sitemap->parent_id)->template;
			$category_list = Sitemap::select(DB::raw('sitemaps.id,sitemaptranslations.name'))
				->join('sitemaptranslations', 'sitemaps.id', '=', 'sitemaptranslations.sitemap_id')
				->join('locales', 'locales.id', '=', 'sitemaptranslations.locale_id')
				->where('locales.languageCode',app()->getLocale())
				//->where('sitemaptranslations.locale_id',1)
				->where('parent_id',$parent_template->sitemap_category_sitemap_parent_id)
				->orderBy('order_by_number', 'asc')
				//-
				->lists('name','id');
	*/


    $allowedParentSitemaps = $this->sitemap->getAllowedParentSitemapsByTemplate($choosenTemplate);
    //dc($allowedParentSitemaps);
    if ($allowedParentSitemaps != 'root'){
      //geen parent_sitemap_id en als er maar 1 sitemap als parent toegestaan: (bijv. parent = homepage)
      if ((($choosen_parent_sitemap_id==null) && ((count($allowedParentSitemaps)) == 1))){
        $choosen_parent_sitemap_id = $allowedParentSitemaps->first()->id;
      }


      if ($request->has('parent_id')){
        $choosen_parent_sitemap = $allowedParentSitemaps->find(4);
      }
      else{
        //todo er zijn meerdere allowedParentSitemaps, weet nog niet welke, aangeven bij selectBeforecreate view?
        $choosen_parent_sitemap_id = $allowedParentSitemaps->first()->id;

      }

      $choosen_parent_sitemap = $allowedParentSitemaps->find($choosen_parent_sitemap_id);

    }

    $parent_translation = ($choosen_parent_sitemap->translations->first());


    $sitemap_list = $this->sitemap->getParentSitemapList($allowedParentSitemaps);
    //dc($choosen_parent_sitemap->translation);
    //homepage has no parent
    if ($choosenTemplate->parent_id != 0){
      //dc($choosen_parent_sitemap->translations->first()->name);
      //dc($choosenTemplate->name);
      $choosenTemplate = $this->sitemap->setSelectedReferencesByTemplateAndParentSitemap($choosen_parent_sitemap, $choosenTemplate);
    }

    //dc($choosenTemplate);
    //return "view";

    $enabledLocales = $this->sitemap->getEnabledLocales();
    $template = $choosenTemplate;
    //dc($template);
    $post_type = $template->slug;//voor SitemapRequest
    $status_list = collect(['online'=>'Online','pending_review'=>'Wacht op review','concept'=>'Concept']);
    return view('sitemap::create', compact('parent_translation','post_type', 'template', 'enabledLocales', 'template_list', 'sitemap_list','status_list','category_list_config'));
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(SitemapRequest $request)
  {


    //get sitemap order_by_number
    $siblings_of_created_sitemap = Sitemap::where('parent_id',($request->get('parent_id')))->get();
    $sitemap_order_by_number = ($siblings_of_created_sitemap->max('order_by_number')+1); //null+1 = 1
    //dc($sitemap_order_by_number);

    //get sitemap depth
    $sitemap_depth = (Sitemap::where('id',$request->get('parent_id'))->first()->depth+1);
    //dc($sitemap_depth);
    //return "view";

    $request->merge(array(
        'created_by_user_id' => auth()->user()->id,
        'updated_by_user_id' => auth()->user()->id,
        'depth' => $sitemap_depth,
        'order_by_number' => $sitemap_order_by_number,
    ));





    DB::transaction(function () use ($request) {
      try
      {


        $enabledLocales = $this->sitemap->getEnabledLocales();
        $created_sitemap = Sitemap::create($request->all());
        $template = $created_sitemap->template;
        $created_sitemap = $this->sitemap->getSelectedSitemap($created_sitemap->id,$template);


        if ($request->has('sitemapCategory')){
          $syncArray = [];
          //dc('test');
          foreach ($request['sitemapCategory'] as $formName => $formValue) {
            //dc($formName);
            //dc($formValue);
            $cnt = 1;
            $created_sitemap->categories()->where('category',$formName)->delete();
            //$flight->delete();

            //$sitemap->categories()->destroy(1);
            foreach ($formValue as $key2 => $sitemap_category_id) {
              //echo $mediatranslation_id;
              $syncArray = [
                  'sitemap_id' => $created_sitemap->id,
                  'sitemap_category_id' => (int)$sitemap_category_id,
                  'category' => $formName,
                  'order_by_number' => $cnt++
              ];
              $created_sitemap->categories()->insert($syncArray);
            }

          }
        }



        foreach($enabledLocales as $key => $enabledLocale){
          //dc($request->translations);
          $localeRequest = array_add($request->translations[$enabledLocale->languageCode]
              ,'locale_id', //no foreign key in view
              $enabledLocale->id);

          //translation
          $localeRequest['slug'] = str_slug($request->translations[$enabledLocale->languageCode]['name']);

          //$localeRequest['slug'] = str_slug($request->translations[$enabledLocale->languageCode]['name']);


          $localeRequest['content'] = clean($request->translations[$enabledLocale->languageCode]['content']);

          $created_translation = $created_sitemap->translations()->create($localeRequest);

          //translation->template
          $localeRequest[$template->slug]['content'] = clean($localeRequest[$template->slug]['content']);

          $created_translation->{$template->slug}()->create($localeRequest[$template->slug]);

          //mediatranslations
          $this->sitemap->syncThisTranslationMediaTranslation($created_translation->id,$request->translations[$enabledLocale->languageCode],$created_sitemap->translations[$enabledLocale->languageCode]);
        }
        //update reference_sitemap
        if ($request->has('reference')){
          $syncArray = [];
          $cnt=1;
          foreach($request->get('reference') as $key => $component_reference) {
            //dc($id);
            foreach($component_reference as $component_id => $reference_id) {
              //dc($component_id." = ".$reference_id);
              $testArray[] = [
                  'sitemap_id' => $created_sitemap->id,
                  'reference_id' => $reference_id,
                  'component_id' => $component_id,
                  'order_by_number' => $cnt++
              ];
            }
          }
          $created_sitemap->references()->sync([]);//pivot 3 key
          $created_sitemap->references()->attach($testArray);
        }
        else{
          //$created_sitemap->references()->sync([]);
        }
        Flash::success('Je pagina is aangemaakt!');
      }
      catch (\Exception $e)
      {
        Flash::error('Your Sitemap translation has NOT been created!');
        dc($e);
        dd($e->getMessage());
        //send mail with subject "db import failed" and body of $e->getMessage()
      }
    });
    //dd('stop');
    //Flash::success('Your Sitemap translation has been created!');
    return redirect()->back();
  }



/*
 * DUPLICATE
 */
  public function duplicate($sitemap_id)
  {


    $action = DB::transaction(function () use ($sitemap_id) {
      try {


        /*start duplicate*/
        $orginalSitemap = Sitemap::findOrFail($sitemap_id);

        $template = Template::findOrFail($orginalSitemap->template_id);

        //dc($template->slug);
        //return "view";

        $clone = $orginalSitemap->replicate();
        $clone->push(); //push

        $orginalSitemap->load('translations');
        $orginalSitemap->load('translations.defaultpage');// ?? kan weg, verkeerde translation template (tmp_page_location bijv)

        $orginalSitemap->load('references');


        //$clone->setRelation('translations',$orginalSitemap->translations);
        foreach ($orginalSitemap->translations as $translation) {
          //dc(str_slug($translation->name));
          $newName = $translation->name.' copy '.$clone->id;
          $newSlug = str_slug($newName);
          $translation->setAttribute('sitemap_id',$clone->id);
          $translation->setAttribute('name',$newName);
          $translation->setAttribute('slug',$newSlug);

          $created_translation = $clone->translations()->create($translation->getAttributes());


          $translation->{$template->slug}->setAttribute('id',$created_translation->id);
          $created_translation->{$template->slug}()->create($translation->{$template->slug}->getAttributes());
        }


        //dc($orginalSitemap->references->first()->pivot);
        foreach ($orginalSitemap->references as $reference) {
          $testArray[] = [
              'sitemap_id' => $clone->id,
              'reference_id' => $reference->pivot->reference_id,
              'component_id' =>  $reference->pivot->component_id,
              'order_by_number' => $reference->pivot->order_by_number];
          //dc($testArray);
          //dc($reference->getAttributes());

          $clone->references()->sync([]);//pivot 3 key
          $clone->references()->attach($testArray);
        }
        /*end duplicate*/

        if (request()->ajax()) {
          $data = ['status' => 'succes', 'statusText' => 'Ok', 'responseText' => 'Dupliceren is gelukt'];
          return response()->json($data, 200);
        }
        Flash::success('Your Sitemap translation has been duplicated!');
      } catch (\Exception $e) {
        if (request()->ajax()){
          $data = ['status' => 'succes', 'statusText' => 'Fail', 'responseText' => '' . $e->getMessage() . ''];
          return response()->json($data, 400);
        }
        //dc($e);
        Flash::error('Dupliceren is mislukt!<br>' . $e->getMessage() . '');
      }
    });

    if(request()->ajax()){
      return $action;
    }
    //return "view";
    return redirect()->back();
  }


/*
 * DESTROY
 */

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    $action = DB::transaction(function () use ($id) {
      try {
        Sitemap::destroy($id);
        if (request()->ajax()) {
          $data = ['status' => 'succes', 'statusText' => 'Ok', 'responseText' => 'Delete is gelukt'];
          return response()->json($data, 200);
        }
        Flash::success('Delete is geluktJAJA! ' . $id . '');
      } catch (\Exception $e) {
        if (request()->ajax()){
          $data = ['status' => 'succes', 'statusText' => 'Fail', 'responseText' => '' . $e->getMessage() . ''];
          return response()->json($data, 400);
        }
        Flash::error('Delete is mislukt!<br>' . $e->getMessage() . ' ' . $id . '');
      }
    });

    if(request()->ajax()){
      return $action;
    }
    return redirect()->back();
  }




  /**
   * Displays datatables front end view
   *
   * @return \Illuminate\View\View
   */
  public function getNewsIndex()
  {

    //request()->session()->forget('previous_route_name');
    request()->session()->put(
        'previous_route', [
        'name'=>Route::currentRouteName(),
        'anchorText'=> 'nieuwsoverzicht',
    ]);

    $sitemap = [];
    $columnNames = null;
    $pagination = false;
    $post_type = null;
    $locaties = Sitemap::with(['translation'])
        ->whereHas('template', function ($q1) { // ...1 subquery to filter the photos by related tags' name
          $q1->where('name', 'Locatie pagina');
        })->get();
    //$locatiesNames = ($locaties->pluck('translation.name')->toJson());
    $locatiesNames = ($locaties->pluck('translation.name'));
    $locatiesNames[] = 'geen';
    //dc(($locatiesNames));



    //return "view";
    return view('sitemap::indexNews',compact('sitemap','columnNames','pagination','post_type','locatiesNames'));
    return view('datatables.index');
  }


  /**
   * Process datatables ajax request.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function newsIndexData(Request $request)
  {

    $tests = Sitemap::join('sitemaptranslations', 'sitemaps.id', '=', 'sitemaptranslations.sitemap_id')
        ->join('users', 'sitemaps.created_by_user_id', '=', 'users.id')
        ->join('templates', 'sitemaps.template_id', '=', 'templates.id')
        ->leftJoin('sitemap_category as sc','sitemaps.id', '=', 'sc.sitemap_id')
        ->leftJoin('sitemaps as sctest', 'sc.sitemap_category_id', '=', 'sctest.id')
        ->leftJoin('sitemaptranslations as sctest_st','sctest.id', '=', 'sctest_st.sitemap_id')
        ->where('sitemaptranslations.locale_id',1)
        ->whereRaw('(sctest_st.locale_id = 1 OR ISNULL(sc.sitemap_id))')
        //->where(function ($query) {
        //    $query->where('sctest_st.locale_id' ,'=', '1');
        //})
        ->where('templates.name','Nieuwsbericht')
        // ->where('sitemaps.id',120)

        ->select(['sitemaps.id',
            'sitemaps.status',
            'sitemaps.created_at',
            'sitemaps.updated_at',
            DB::raw('MIN(users.name) AS usersname'),
            DB::raw('MIN(sitemaptranslations.name) AS testname'),
            DB::raw('MIN(sitemaptranslations.published_at) AS published_at'),
          //DB::raw("IF(GROUP_CONCAT(sctest_st.name) IS NULL, 'geen', GROUP_CONCAT(`sctest_st`.`name` ORDER BY `sctest_st`.`name` ASC)) as `locaties`")
            DB::raw("IF(GROUP_CONCAT(sctest_st.name) IS NULL, 'geen', GROUP_CONCAT(sctest_st.name)) as locaties")
        ])
        ->groupBy('sitemaps.id');

    $datatable =  Datatables::of($tests);

    //app('debugbar')->warning($datatable);
    $datatable->addColumn('action', function ($test) {
      return '<a href="'.route('admin::sitemap.edit',['id'=>$test->id]).'" class="btn btn-xs-uit btn-primary"><i class="glxyphicon glyphicon-xedit"></i> Edit</a>';
    });
    //->editColumn('created_at', function ($test) {
    //    return $test->created_at ? with(new Carbon($test->created_at))->format('m/d/Y') : '';
    //})
    $datatable->editColumn('published_at', function ($test) {
      return $test->published_at ? with(new Carbon($test->published_at))->diffForHumans() : '';
    });

    $datatable->editColumn('status', function ($test){

      $statusValue = $test->status;
      if ($test->status == 'pending_review'){
        $statusValue = 'pending';
      }
      return "<span class=\"labelx badge label-table label-".$test->status."\">".$statusValue."</span>";
    });





    //app('debugbar')->warning($tRequest['columns'][3]['data']);
    //app('debugbar')->warning($tRequest['columns'][3]['search']['value']);
    //$request['columns'][3]['search']['value'] = "RET";
    $tRequest = $request->all();
    //app('debugbar')->warning($tRequest['columns'][3]['search']['isNull']);
    if (($tRequest['columns'][3]['data'] == 'locaties') && (isset($tRequest['columns'][3]['search']['isNull']))) {
      app('debugbar')->warning($tRequest['columns'][3]);
      $datatable->whereNull('sctest_st.name');
      //$datatable->whereNull('sctest_st.name');
    }

    return $datatable->make(true);
  }



  /*
   * LOCATIES
   * */

  public function getIndexLocaties()
  {

    //request()->session()->forget('previous_route_name');
    request()->session()->put(
        'previous_route', [
        'name'=>Route::currentRouteName(),
        'anchorText'=> 'locatie overzicht',
    ]);

    $sitemap = [];
    $columnNames = null;
    $pagination = false;
    $post_type = null;
    $sitemaps = Sitemap::join('sitemaptranslations', 'sitemaps.id', '=', 'sitemaptranslations.sitemap_id')
        ->join('tmp_page_location', 'sitemaptranslations.id', '=', 'tmp_page_location.sitemaptranslation_id')
        ->join('templates', 'sitemaps.template_id', '=', 'templates.id')
        ->where('sitemaptranslations.locale_id',1)
        ->where('templates.name','Locatie pagina')
        ->get();
    $cities = (($sitemaps->pluck('city')));
    $cities = $cities->unique();
    $cities = $cities->values();

    return view('sitemap::indexLocaties',compact('sitemap','columnNames','pagination','post_type','cities'));
    return view('datatables.index');
  }


  /**
   * Process datatables ajax request.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function indexLocatiesData(Request $request)
  {

    $sitemaps = Sitemap::join('sitemaptranslations', 'sitemaps.id', '=', 'sitemaptranslations.sitemap_id')
        ->join('tmp_page_location', 'sitemaptranslations.id', '=', 'tmp_page_location.sitemaptranslation_id')
        ->join('users', 'sitemaps.created_by_user_id', '=', 'users.id')
        ->join('templates', 'sitemaps.template_id', '=', 'templates.id')
        ->leftJoin('sitemap_category as sc','sitemaps.id', '=', 'sc.sitemap_id')
        ->leftJoin('sitemaps as sctest', 'sc.sitemap_category_id', '=', 'sctest.id')
        ->leftJoin('sitemaptranslations as sctest_st','sctest.id', '=', 'sctest_st.sitemap_id')
        ->where('sitemaptranslations.locale_id',1)
        ->whereRaw('(sctest_st.locale_id = 1 OR ISNULL(sc.sitemap_id))')
        //->where(function ($query) {
        //    $query->where('sctest_st.locale_id' ,'=', '1');
        //})
        ->where('templates.name','Locatie pagina')
        // ->where('sitemaps.id',120)

        ->select(['sitemaps.id',
            'sitemaps.status',
            'sitemaps.order_by_number',
            'sitemaps.created_at',
            'sitemaps.updated_at',
            DB::raw('CONCAT(\'{"id":\',sitemaps.id,\',\',\'"order_by_number":\',sitemaps.order_by_number,\'}\') as reorderData_id_AND_order_by_number'),
            DB::raw('MIN(users.name) AS usersname'),
            DB::raw('MIN(sitemaptranslations.name) AS testname'),
            DB::raw('MIN(sitemaptranslations.published_at) AS published_at'),
          //DB::raw("IF(GROUP_CONCAT(sctest_st.name) IS NULL, 'geen', GROUP_CONCAT(`sctest_st`.`name` ORDER BY `sctest_st`.`name` ASC)) as `locaties`")
            DB::raw("IF(GROUP_CONCAT(sctest_st.name) IS NULL, 'geen', GROUP_CONCAT(`sctest_st`.`name`)) as `locaties`"),
            'tmp_page_location.city as city'
        ])
        ->groupBy('sitemaps.id');





    $datatable =  Datatables::of($sitemaps);
    $datatable->setRowId('sortable_'.'{{$id}}');
    $datatable->addColumn('action', function ($sitemap) {


//edit button



      $r = "<a class=\"btn btn-success btn-labeled-x\" href=\"".route('admin::sitemap.edit',['id'=>$sitemap->id])."\" >
                    <i class=\"fa fa-pencil fa-1x\"></i> edit </a> ";






      //get locatie sub pagina's
      //$r .= '<a href="'.route('admin::locaties_sub.index',['sitemap_parent_id'=>$sitemap->id]).'" class="btn btn-xs-uit btn-pink"><i class="glxyphicon glyphicon-xedit"></i>Subpagina\'s</a><br>';

      $r .= "<a class=\"btn btn-primary btn-labeled-x setTable\" href=\"".route('admin::sitemap.locaties_sub.index',['sitemap_parent_id'=>$sitemap->id])."\">
                    <i class=\"fa fa-level-down fa-1x\"></i>  sub pagins's</a> ";
      /*
	  $r .= "<a href='".route('admin::sitemap.index.all',['sitemap_parent_id'=>$sitemap->id])."'>test</a>";
	  //duplicate button
	  $r .=  Form::open(['method'=>'POST', 'action'=>array('Admin\SitemapController@duplicate',$sitemap->id)]);
	  $r .=  Form::submit('Duplicate', ['class' => 'btn btn-mint btn-xsx hiddexn']);
	  $r .=  Form::close();

//delete button
	  $r .=  Form::open(['method'=>'DELETE', 'action'=>array('Admin\SitemapController@destroy',$sitemap->id)]);
	  $r .= Form::submit('Delete', ['class' => 'btn btn-danger btn-xsx']);
	  $r .= Form::close();
	  */



      return $r;



    });


    $datatable->editColumn('status', function ($test) {

      $statusValue = $test->status;
      if ($test->status == 'pending_review'){
        $statusValue = 'pending';
      }
      return "<span class=\"labelx badge label-table label-".$test->status."\">".$statusValue."</span>";
    });


    //kan weg
    /*if ($city = $request->get('city')) {
		app('debugbar')->warning($city);
		$datatable->where('city', 'like', "%{$city}%"); // additional users.name search
	}*/
    return $datatable->make(true);
  }



  public function getIndexLocatiesSub($sitemap_parent_id = null)
  {

    //dc(Carbon::now());
    //dc(Carbon::now()->addYear(30));
    $sitemap = Sitemap::with('translation','template')->find($sitemap_parent_id);

    if ((is_null($sitemap))){
      //empty object
      $sitemap = collect();
      $sitemap->id = null;
      $sitemap->translation = collect();
      $sitemap->translation->name = 'alle locaties';
    }

    request()->session()->put(
        'previous_route', [
        'name'=>null,
      //'url'=>(route(Route::currentRouteName(),['sitemap_parent_id'=>$sitemap->id])),
        'url'=>(route('admin::sitemap.locaties_sub.index',['sitemap_parent_id'=>$sitemap->id])),

        'anchorText'=> 'sub pagina\'s van locatie \''.$sitemap->translation->name.'\''
    ]);
    //dc($sitemap->template->id);
    $template = "TEMPLASTE";
    $tableConfig['customSearchColumnValues'] = "['online','pending_review','concept']";

    $allowed_child_templates = Template::where('parent_id',$sitemap->template->id)->get();
    //dc($allowed_child_templates);
    return view('sitemap::indexLocatiesSub',compact('sitemap','allowed_child_templates','tableConfig'));
  }


  /**
   * Process datatables ajax request.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function indexLocatiesSubData(Request $request,$sitemap_parent_id = null)
  {

    //dc($sitemap_parent_id);



    //echo($sitemap_parent_id);
    //return 'test '.$sitemap_parent_id.' x';
    $sitemaps = Sitemap::join('sitemaptranslations', 'sitemaps.id', '=', 'sitemaptranslations.sitemap_id')
        //->join('tmp_page_location', 'sitemaptranslations.id', '=', 'tmp_page_location.sitemaptranslation_id')
        ->join('users', 'sitemaps.created_by_user_id', '=', 'users.id')
        ->join('templates', 'sitemaps.template_id', '=', 'templates.id')
        ->leftJoin('sitemap_category as sc','sitemaps.id', '=', 'sc.sitemap_id')
        ->leftJoin('sitemaps as sctest', 'sc.sitemap_category_id', '=', 'sctest.id')
        ->leftJoin('sitemaptranslations as sctest_st','sctest.id', '=', 'sctest_st.sitemap_id')
        ->where('sitemaptranslations.locale_id',1)
        ->whereRaw('(sctest_st.locale_id = 1 OR ISNULL(sc.sitemap_id))');
    //->where(function ($query) {
    //    $query->where('sctest_st.locale_id' ,'=', '1');
    //})


    if (!(is_null($sitemap_parent_id))){
      $sitemaps->where('sitemaps.parent_id',$sitemap_parent_id);
    }
    //$sitemaps->where('templates.name','Locatie sub pagina');
    //$sitemaps->orWhere('templates.name','Locatie sub news pagina')
    // ->where('sitemaps.id',120)

    $sitemaps->select(['sitemaps.id',
        'sitemaps.status',
        'sitemaps.order_by_number',
        'sitemaps.created_at',
        'sitemaps.updated_at',
        DB::raw('CONCAT(\'{"id":\',sitemaps.id,\',\',\'"order_by_number":\',sitemaps.order_by_number,\'}\') as reorderData_id_AND_order_by_number'),
        DB::raw('MIN(users.name) AS usersname'),
        DB::raw('MIN(sitemaptranslations.name) AS testname'),
        DB::raw('MIN(sitemaptranslations.published_at) AS published_at'),
      //DB::raw("IF(GROUP_CONCAT(sctest_st.name) IS NULL, 'geen', GROUP_CONCAT(`sctest_st`.`name` ORDER BY `sctest_st`.`name` ASC)) as `locaties`")
        DB::raw("IF(GROUP_CONCAT(sctest_st.name) IS NULL, 'geen', GROUP_CONCAT(`sctest_st`.`name`)) as `locaties`")
      //,'tmp_page_location.city'
    ])
        ->groupBy('sitemaps.id');

    $datatable =  Datatables::of($sitemaps);
    $datatable->setRowId('sortable_'.'{{$id}}');
    $datatable->addColumn('action', function ($sitemap) {

      //edit button
      $r = '<a href="'.route('admin::sitemap.edit',['id'=>$sitemap->id]).'" class="btn btn-xsx btn-primary"><i class="glxyphicon glyphicon-xedit"></i> Edit</a>';


      //duplicate button
      /*$r .=  Form::open(['method'=>'POST', 'action'=>array('Admin\SitemapController@duplicate',$sitemap->id)]);
	  $r .=  Form::submit('Duplicate', ['class' => 'btn btn-mint btn-xsx hiddexn']);
	  $r .=  Form::close();

	  //delete button
	  $r .=  Form::open(['method'=>'DELETE', 'action'=>array('Admin\SitemapController@destroy',$sitemap->id)]);
	  $r .= Form::submit('Delete', ['class' => 'btn btn-danger btn-xsx']);
	  $r .= Form::close();
*/

      return $r;




    });

    $datatable->editColumn('status', function ($test) {

      $statusValue = $test->status;
      if ($test->status == 'pending_review'){
        $statusValue = 'pending';
      }
      return "<span class=\"labelx badge label-table label-".$test->status."\">".$statusValue."</span>";
    });

    if ($status = $request->get('status')) {
      //app('debugbar')->warning($status);
      $datatable->where('sitemaps.status', 'like', "%{$status}%"); // additional users.name search
    }
    return $datatable->make(true);
  }




  /* NEW */







  public function uitstoreSession(Request $request){
    list($keys, $values) = array_divide($request->all());
    $request->session()->put($keys[0], $values[0]);
    return response()->json($request->session()->all(),200);
  }




  /**
   * @param null $post_type
   * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
   */
  public function index($post_type=null)
  {

    request()->session()->put(
        'previous_route', [
        'name'=>Route::currentRouteName(),
        'anchorText'=> 'pagina overzicht (index)',
    ]);

    if($post_type)
    {
      $sitemap = $this->sitemap->getAllByActiveLocaleByType($post_type);
    }
    else{
      $sitemap = $this->sitemap->getAllByActiveLocale();
    }
    //dc($sitemap);
    $pagination = ($sitemap instanceof LengthAwarePaginator);
    $columnNames = null;

    dc($sitemap);
    return "view";
    //return "view";
    return view('admin.sitemap.index',compact('sitemap','columnNames','pagination','post_type'));

  }
  public function xx(){
    return "xx";
  }


  /**
   * Displays datatables front end view
   *
   * @return \Illuminate\View\View
   */
  public function getEventsIndex()
  {
    //request()->session()->forget('previous_route_name');
    request()->session()->put(
        'previous_route', [
        'name'=>Route::currentRouteName(),
        'anchorText'=> 'evenementenoverzicht',
    ]);

    $sitemap = [];
    $columnNames = null;
    $pagination = false;
    $post_type = null;
    $statusNames = "['online','pending_review','concept']";

    return view('admin.sitemap.indexEvents',compact('sitemap','columnNames','pagination','post_type','statusNames'));
    return view('datatables.index');
  }

  /**
   * Process datatables ajax request.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function eventsIndexData(Request $request)
  {

    $tests = Sitemap::join('sitemaptranslations', 'sitemaps.id', '=', 'sitemaptranslations.sitemap_id')
        ->join('tmp_page_event', 'sitemaptranslations.id', '=', 'tmp_page_event.sitemaptranslation_id')
        ->join('users', 'sitemaps.created_by_user_id', '=', 'users.id')

        ->join('templates', 'sitemaps.template_id', '=', 'templates.id')
        ->where('sitemaptranslations.locale_id',1)
        ->where('templates.name','Evenement')
        // ->where('sitemaps.id',120)

        ->select(['sitemaps.id',
            'sitemaps.status',
            'sitemaps.created_at',
            'sitemaps.updated_at',

            'users.name AS usersname',
            'sitemaptranslations.name as testname',
            'sitemaptranslations.published_at as published_at',

            'tmp_page_event.name as eventname',
            'tmp_page_event.when as wanneer'
        ])
        ->groupBy('sitemaps.id');

    //dd($tests->get());

    $datatable =  Datatables::of($tests);

    $datatable->addColumn('check', '<input type="checkbox" name="selected_dt_row[]" value="{{ $id }}">{{ $id }}');
    //app('debugbar')->warning($datatable);
    $datatable->addColumn('action', function ($test) {
      return '<a href="'.route('admin::sitemap.edit',['id'=>$test->id]).'" class="btn btn-xs-uit btn-primary"><i class="glxyphicon glyphicon-xedit"></i> Edit</a>';
    });
    //->editColumn('created_at', function ($test) {
    //    return $test->created_at ? with(new Carbon($test->created_at))->format('m/d/Y') : '';
    //})
    $datatable->editColumn('published_at', function ($test) {
      return $test->published_at ? with(new Carbon($test->published_at))->diffForHumans() : '';
    });

    $datatable->editColumn('wanneer', function ($test) {



      return $test->wanneer ? with(new Carbon($test->wanneer))->formatLocalized('%A %d %B %Y') : '';
      return $test->wanneer ? with(new Carbon($test->wanneer))->diffForHumans() : '';
    });

    $datatable->editColumn('status', function ($test){

      $statusValue = $test->status;
      if ($test->status == 'pending_review'){
        $statusValue = 'pending';
      }
      return "<span class=\"labelx badge label-table label-".$test->status."\">".$statusValue."</span>";
    });





    //app('debugbar')->warning($tRequest['columns'][3]['data']);
    //app('debugbar')->warning($tRequest['columns'][3]['search']['value']);
    //$request['columns'][3]['search']['value'] = "RET";
    $tRequest = $request->all();
    //app('debugbar')->warning($tRequest['columns'][3]['search']['isNull']);
    //if (($tRequest['columns'][3]['data'] == 'locaties') && (isset($tRequest['columns'][3]['search']['isNull']))) {
    //    app('debugbar')->warning($tRequest['columns'][3]);
    //    $datatable->whereNull('sctest_st.name');
    //$datatable->whereNull('sctest_st.name');
    //}

    return $datatable->make(true);
  }
















  /**
   * @param null $post_type
   * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
   */
  public function indexPosts($templateName=null)
  {

    if($templateName)
    {
      $sitemap = $this->sitemap->getAllSitemapsTranslationAndRelationsByTemplateName($templateName);
    }

    $pagination = ($sitemap instanceof LengthAwarePaginator);

    return view('admin.sitemap.index',compact('sitemap','pagination','post_type'));

  }






  /**
   * @param null $post_type
   * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
   */
  //todo ?? kan weg
  public function indexDataTable($parent_id=0)
  {



    $sitemap = $this->sitemap->getSitemapsTranslationAndRelationsByParentId($parent_id);//homepage

    //TODO

    $childDepth = ($sitemap->first()->depth+1);
    $allParentsWithDepth = DB::table('sitemaps')
        ->select('parent_id')->distinct()
        ->where('depth',$childDepth)
        ->get();

    $allParentsWithDepthArray = [];
    foreach ($allParentsWithDepth as $key => $parent){
      //$allParentsWithDepthArray[] = $parent;
      array_push($allParentsWithDepthArray,$parent->parent_id);

    }
    //dc($allParentsWithDepthArray); //todo filter all $sitemap_item->id (SELECT * FROM sitemaps AS t1  WHERE parent_id = 1)

    //dc($test);
    foreach ($sitemap as $key => $sitemap_item){
      //dc($sitemap_item->translation->name.' / '.$sitemap_item->depth.' / '.$sitemap_item->parent_id.' / '.$sitemap_item->id);

      $sitemap_item->hasChild = in_array($sitemap_item->id,$allParentsWithDepthArray);
      //dc(in_array($sitemap_item->id,$allParentsWithDepthArray));


      $childDepth = ($sitemap_item->depth+1);

    }

    //todo end
    $breadcrumb = [];
    //dc($sitemap->first()->depth);
    if ($parent_id != 0){
      $breadcrumb = $this->sitemap->getBreadcrumbByIdAndDepth($parent_id,$sitemap->first()->depth);
    }

    $pagination = ($sitemap instanceof LengthAwarePaginator);
    $columnNames = null;
    return view('admin.sitemap.indexDataTable',compact('sitemap','breadcrumb','pagination','post_type'));

  }





  public function indexHome(Request $request)
  {


    //$references = Sitemap::with([
    //    'references.componentsTest',
    //    'references.translation'])->where('id',36);

    //dc($references->get());
    //return "view";
    $this->sitemap->initWebPage();

    $navigation = $this->sitemap->getWebpageNavigation();
    $sitemap = $this->sitemap->getWebpageContent();

    //dc($navigation[1]);

    $breadcrumb = $this->sitemap->getBreadcrumbArray();
    $breadcrumbHTML = $this->sitemap->getBreadcrumbHTML();

    //dc($breadcrumbHTML);
    $sitemapList = $this->sitemap->getWebPageSitemapList();
    $pagination = ($sitemapList instanceof LengthAwarePaginator);

    //dc($sitemapList);
    if (!(is_null($sitemapList))){
      foreach ($sitemapList as $key => $sitemapListItem){
        //if ($key == 0){
        $this->sitemap->groupMediaTranslationCollectionByFieldName($sitemapListItem->translation);
        //}

      }
    }
    $this->sitemap->groupMediaTranslationCollectionByFieldName($sitemap->translation);




    //dc($sitemap);
    //$template = Sitemap::with('template')->findOrFail($id)->template;



    //dc($sitemap->references);

    //dc($sitemapList);
    //dc($sitemapList[0]->translation->media['intro']);
    //foreach ($sitemapList[0]->translation->media['intro'] as $key => $image){
    //    dc($key);
    //}
    //$this->sitemap->groupMediaCollectionByFieldName($sitemap->translations);
    //dc($test);
    //dc($navigation[0]);
    foreach ($navigation[0] as $key => $nav){
      //    dc($nav->translation->name.' - '.$nav->selected.' - '.$nav->hasChildren);
    }
    return view('index',compact('sitemap','navigation','breadcrumb','breadcrumbHTML','sitemapList','pagination'));



    //dc($breadcrumb[0]->template->name);
    $test = '';
    foreach ($breadcrumb as $key => $breadcrumbItem){
      //    $test .= ($breadcrumbItem->translation->name.' - ');
    }
    dc($breadcrumb);
    dc($sitemap->translation->name);
    /*
	foreach($navigation  as $key => $nav){
		foreach($nav  as $key1 => $nav1){
			dc($nav1->translation->name.' - '.$nav1->selected.' - '.$nav1->no_sitemap_parents);
			//dc($nav1->selected);
			//dc($nav1->no_sitemap_parents);
		}
		dc('end');
	}
	*/
    return "view";
    //dc($test);
    //dc($breadcrumb);
    //dc($navigation);
    //dc($sitemapList);

    /*
	 * GOED
	 $
	*/

    //$navigation[0][0]->setAttribute('selected',true);
    //dc($navigation[0][1]->getAttributes());
    //dc($navigation[0][1]);
    //dc($navigation[0][1]->selected);
    /*
			foreach ($navigation[2] as $key => $nav){
				dc($nav->translation->name);
				dc($nav->selected);
				dc($nav->no_sitemap_parents);

			}
	foreach ($navigation as $key => $nav){
		dc($nav);
	}
	dc($navigation);
	*/


    //$test = $this->sitemap->getOnlineSitemapsTranslationByParentIdAndSitemapCategory(40);
    //dc($test[0]->categoriesTest);
    //foreach ($test[0]->categoriesTest as $cat){
    //    dc($cat->translation->name);
    //}


    //dc($breadcrumb[0]->translation->slug);



    return view('index',compact('sitemap','navigation','breadcrumb','sitemapList'));




    return "view";

    //dc($request->path());
    //dc($request);
    //dc($request->path());
    if ($request->path() != "/"){
      //dc($requestUriArray);
      $sitemap = $this->sitemap->getSitemapByUrl();
      dc($sitemap->id);
      $navigation = $this->sitemap->getNavigation();
      $breadcrumb = $this->sitemap->getBreadcrumb();
      //return "view";
      //dc($sitemap);
      dc($breadcrumb);
      dc('test');
    }
    else{
      $sitemapId =1;
      $sitemap = $this->sitemap->getSitemapForIndex($sitemapId);

    }
    return "view";
    //return view('index-amp',compact('sitemap','testComposer'));
    return view('index',compact('sitemap','navigation','breadcrumb'));





    //todo post_type nodig, JA DUS VOOR POST REQUEST
    $id = $request->route()->parameter('id') ? $request->route()->parameter('id') : null;
    //$post_type = $request->route()->parameter('post_type') ? $request->route()->parameter('post_type') : null;
    //if (is_null($post_type)){
    //    $template = Sitemap::with('template')->findOrFail($id)->template;
    //    $post_type = Sitemap::with('template')->findOrFail($id)->template->db_table_name;
    //}

    $id = 1;
    $template = Sitemap::with('template')->findOrFail($id)->template;


    //todo
    $allowedParentSitemaps = $this->sitemap->getAllowedParentSitemapsByTemplate($template);
    $sitemap_list = $this->sitemap->getParentSitemapList($allowedParentSitemaps);





    $enabledLocales = $this->sitemap->getEnabledLocales();

    $sitemap = $this->sitemap->getSelectedSitemap($id,$template);
    $sitemap = $this->sitemap->setSelectedReferencesBySitemap($sitemap);

    $this->sitemap->groupMediaCollectionByFieldName($sitemap->translations);


    //kan weg geen templates wijzigen
    $template_list = Template::lists('name','id');
    $template = $sitemap->template;
    //dc($template);

    $status_list = collect(['online'=>'Online','pending_review'=>'Wacht op review','concept'=>'Concept']);

    //dc($sitemap);
    $post_type = $template->slug;//voor PostRequest


    return view('index',compact('sitemap','post_type','template','enabledLocales','template_list','sitemap_list','status_list'));
    //return view('admin.sitemap.edit',compact('sitemap','post_type','template','enabledLocales','template_list','sitemap_list','status_list'));


  }










  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    //
  }



}
