<?php

namespace WI\Sitemap\Repositories;

#use WI\Locale\Locale;
use WI\Core\Entities\Reference\ReferenceTranslation;

#use App\Repositories\DbRepository;
use WI\Core\Repositories\DbRepository;

use WI\Sitemap\Sitemap;
use WI\Sitemap\SitemapTranslation;
use WI\Core\Entities\Template\Template;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Request;

#use SitemapRepositoryInterface;


/**
 * @property Sitemap model
 */
class DbSitemapRepository extends DbRepository implements SitemapRepositoryInterface
{


    /**
     * @var Sitemap
     */
    protected $model;
    protected $referenceTranslation;
    protected $translationType; //sitemap, post, reference || used for mediatranslation pivot table

    protected $WebPageNavigation;
    protected $WebPageContent;
    protected $WebPageSitemapList; //newslist etc
    protected $breadcrumbArray;
    protected $requestPathArray;


    protected $testSitemap;


    /**
     * DbSitemapRepository constructor.
     */
    public function __construct(Sitemap $sitemap, ReferenceTranslation $referenceTranslation)
    {

        parent::__construct();
        $this->model = $sitemap;
        $this->translationType = 'sitemap';//put in model?
        //$this->locale = $locale;
        //$this->enabledLocales = $this->locale->getEnabled();
        $this->referenceTranslation = $referenceTranslation;

        $this->navigation = [];
        $this->breadcrumb = [];
        $this->requestPathArray = [];

    }
    public function tests(){
        return "sadf";
    }

    /**
     * Used for the editing form for the specified resource.
     * And for update() and store() (relations)
     *
     * - resetKeyTranslationCollectionByLocaleIdentifier
     *
     * @param $id sitemap id
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     *         Sitemap Collection with relations
     */
    public function getSelectedSitemap($id,$template)
    {
        $input = "";
        $sitemap = Sitemap::with(
        //'categories',
        'translations.locale'
        ,'translations.'.$template->slug.''
        ,'translations.mediatranslations'
        //,'translations.mediatranslations.images'//!!
        //,'translations.mediatranslations.files'
        )->with((
        array('translations' => function ($query) use ($input) {
            $query->whereHas('locale', function ($q) { // ...1 subquery to filter the active locales
                $q->where('status', '<>', 'disabled');
            });
        }, 'template.components.references.translations.locale',
            'template.components.referencetypes',
            //'translations.mediatranslations',//!!
            'references')))
            ->with(
                array('template.components.references.translations' => function ($q1) {  // 1 query for template of the sitemap with nested collections
                    $q1->whereHas('locale', function ($q2) { // ...1 subquery to filter locale by active language
                        $q2->where('languageCode', '' . app()->getLocale() . '');
                    });
                })
            )
            ->findOrFail($id);
        //dd($sitemap);
        //return $sitemap;

        //DbRepository

        $this->resetKeyTranslationCollectionByLocaleIdentifier($sitemap);
        //dc($sitemap);
        //$this->groupMediaCollectionByFormName($sitemap->translations);//werkt niet voor update?
        return $sitemap;
    }



    public function getAllowedParentSitemapsByTemplate($template){


        //homepage has no parent
        $choosenTemplate = $template;
        //dc($choosenTemplate->id);
        $allowedParentSitemaps = [];
        if ($choosenTemplate->parent_id == 0) {
            return 'root';
        }
        //dc($choosenTemplate->parent_id);
        $allowedParentTemplate = Template::where('id', $choosenTemplate->parent_id)->first();
        //dc($allowedParentTemplate->id);
        //$allowedParentTemplate = Template::all();


        $allowedParentSitemaps = Sitemap::with(['translations' => function ($q) {  // 1 query for photos with...
            $q->whereHas('locale', function ($q) { // ...1 subquery to filter the photos by related tags' name
                $q->where('languageCode', '' . app()->getLocale() . '');
                //$q->where('status', 'enabled');
            });
        }
            //,'template'
            , 'references'
        ])
            //temp uit
            ->where('template_id', $allowedParentTemplate->id)
            ->get();
        //dc($allowedParentSitemaps);
        return $allowedParentSitemaps;
    }

    public function getParentSitemapList($allowedParentSitemaps){
        $allowedParentSitemapList = collect();
        if ($allowedParentSitemaps == 'root'){
            return $allowedParentSitemapList->put(0, 'root');
        }
        foreach ($allowedParentSitemaps as $key => $sitemap) {
            $allowedParentSitemapList->put($sitemap->id, $sitemap->translations->first()->name);
        }
        return $allowedParentSitemapList;
    }



    /**
     * Used for the Index page, get translation for active (user) locale
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllByActiveLocale()
    {
        $sitemap = Sitemap::with(['translation', 'template','user'])
            ->paginate(500);//->get()
        return $sitemap;
    }


    //todo select by template db_table_name column
    public function getAllSitemapsTranslationAndRelationsByTemplateName($templateName)
    {
        $sitemap = Sitemap::with(['translation','template','user']
        )->whereHas('template', function ($q)  use ($templateName) { // ...1 subquery to filter the photos by related tags' name
            $q->where('name', $templateName);
            //$q->where('status', 'enabled');
        })->paginate(500);//->get()
        //dc(get_class($sitemap));
        //dc($sitemap);
        return $sitemap;
    }



    /* SELECTED REFERENCES */
    public function setSelectedReferencesBySitemap($sitemap){
        //alle gekoppelde references (pivot table -> reference_sitemap)
        $sitemap_references_original = [];
        foreach ($sitemap->references as $key => $reference){
            $sitemap_references_original[$reference->id.'-'.$reference->pivot->component_id] = ($reference->getOriginal());
        }
        //dc($sitemap_references_original);
        $sitemap->template->components = $this->_setSelectedReferences($sitemap->template->components,$sitemap_references_original);
        return $sitemap;
    }

    public function setSelectedReferencesByTemplateAndParentSitemap($parentSitemap,$choosenTemplate)
    {
        //dc($parentSitemap);
        $sitemap_references_original = [];
        foreach ($parentSitemap->references as $key => $reference){
            $sitemap_references_original[$reference->id.'-'.$reference->pivot->component_id] = ($reference->getOriginal());
        }
        //return $sitemap_references_original;
        $choosenTemplate->components = $this->_setSelectedReferences($choosenTemplate->components,$sitemap_references_original);
        //dc($choosenTemplate->components);
        return $choosenTemplate;
    }

    private function _setSelectedReferences($components,$sitemap_references_original){
        //to sort references, selected references first and then by order_nr
        $makeComparer = function($criteria) {
            $comparer = function ($first, $second) use ($criteria) {
                foreach ($criteria as $key => $orderType) {
                    // normalize sort direction
                    $orderType = strtolower($orderType);
                    if ($first[$key] < $second[$key]) {
                        return $orderType === "asc" ? -1 : 1;
                    } else if ($first[$key] > $second[$key]) {
                        return $orderType === "asc" ? 1 : -1;
                    }
                }
                // all elements were equal
                return 0;
            };
            return $comparer;
        };
        $criteria = ["selected" => "desc", "order_by_number" => "asc"];
        $comparer = $makeComparer($criteria);
        foreach ($components as $key => $component){
            //alle references van component
            foreach ($component->references as $key1 => $reference){
                //is deze reference gekoppeld aan sitemap
                $isReferenceSelected = (isset($sitemap_references_original[$reference->id.'-'.$component->id]));
                $reference->selected = $isReferenceSelected;
                if ($isReferenceSelected){
                    $order_by_number = $sitemap_references_original[$reference->id.'-'.$component->id]['pivot_order_by_number'];
                    $reference->order_by_number_help = 'take from reference_sitemap '.$order_by_number.'';
                    $reference->order_by_number = $order_by_number;
                }
                else{
                    $order_by_number = $reference->pivot->order_by_number;
                    $reference->order_by_number_help = 'take from component_reference '.$order_by_number.'';
                    $reference->order_by_number = $order_by_number;

                }
            }
            $sorted = $component->references->sort($comparer);
            $component->references = $sorted;
        }
        return $components;
    }
    /* END SELECTED REFERENCES */


    //for view composers

    private function setWebPageNavigation($navigation){
        $this->WebPageNavigation = $navigation;
    }
    public function getWebPageNavigation(){
        return $this->WebPageNavigation;
    }

    private function setWebPageContent($sitemap){
        $this->WebPageContent = $sitemap;
    }

    public function getWebPageContent(){
        return $this->WebPageContent;
    }

    private function setBreadcrumbArray($sitemaps){
        $this->breadcrumbArray = $sitemaps;
    }
    public function getBreadcrumbArray(){
        return $this->breadcrumbArray;
    }

    public function getBreadcrumbHTML(){
        $depth = count($this->breadcrumbArray);
        $url = '';
        $retval = "<ol class=\"breadcrumb\">";
        foreach ($this->breadcrumbArray as $key => $breadcrumb){
            //first
            if ($key == 0){
                $retval .= "<li><a href=\"/\">Home</a></li>";
            }
            //last
            elseif ($key == ($depth-1)){
                //$url .= "/".$breadcrumb->translation->slug;
                //$retval .= "<li><a href=\"".$url."\">".$breadcrumb->translation->name."LAST</a></li>";
                $retval .= "<li class=\"active\">".$breadcrumb->translation->name."</li>";
            }
            else{
                $url .= "/".$breadcrumb->translation->slug;
                $retval .= "<li><a href=\"".$url."\">".$breadcrumb->translation->name."</a></li>";
            }
        }
        $retval .= "</ol>";
        return $retval;
    }

    private function setWebPageSitemapList($sitemap){
        $this->WebPageSitemapList = $sitemap;
    }
    public function getWebPageSitemapList(){
        return $this->WebPageSitemapList;
    }

    private function getRequestPathArray(){
        return $this->requestPathArray;
    }
    private function setRequestPathArray(){
        if (request()->path() == "/"){
            //dc('test');
            $this->requestPathArray = [];
        }
        else{
            $this->requestPathArray = (explode("/", request()->path()));
        }

        array_unshift($this->requestPathArray, '');

        //$this->requestPathArray
        //setRequestPathArray
    }

    //siteGenerator
    public function initWebPage(){

        $this->setRequestPathArray();
        $requestPathArray = $this->getRequestPathArray();
        $requestPathDepth = count($this->getRequestPathArray());


        $testArray = [];
        $cnt = 0;

        $requestPathCollection = collect($requestPathArray);

        $parent_id = 0;

        $parent_id_array = [];
        $get_sitemap_category = false; //sitemap_category
        $get_previous_sitemap = false; //sitemap_category

        $previousParentId = 0;

        foreach ($requestPathCollection as $key => $requestPath){


            if ($get_previous_sitemap == true){
                $parent_id = $previousParentId;
                $get_previous_sitemap = false;
                $previousParentId = 0;
            }
            if ($get_sitemap_category == true){
                $get_sitemap_category = false;
                $get_previous_sitemap = true;
            }

            //get breadcrumb
//newslist/newsitem werkt niet, opzich goed
            /*if ($parent_id == 37){
                dc('tet'.$previousParentId);
                $parent_id = 36;
            }*/
            $breadcrumb = $this->getSitemapTranslationTemplateByIdSlug($parent_id,$key,$requestPath);
            $webPage_breadcrumb[$key] = $breadcrumb;

            $parent_id = $breadcrumb->id;
            //if slug is sitemap_category
            if (is_int($breadcrumb->template->sitemap_category_sitemap_parent_id)){
                $previousParentId = $parent_id;
                $get_sitemap_category = true;
                $parent_id = $breadcrumb->template->sitemap_category_sitemap_parent_id;
            }

            //get navigation
            $navigation = $this->getOnlineSitemapsTranslationByParentId($parent_id);
            $webPage_navigation[$key] = $navigation;

            //$parent_id = $parent_id;
            //get webPage_content
            if (($requestPath == ($requestPathCollection->last()))){
                //dc('webPage_content'.$parent_id.' - '.$breadcrumb->template->name.' // breadcrumb- '.$breadcrumb->id.' - prev '.$previousParentId.' ('.$get_sitemap_category.') ('.$get_previous_sitemap.')');
                if ($get_sitemap_category){
                    //dc("get_sitemap_category: ".$breadcrumb->id.' - '.$previousParentId);
                }

                ///nieuws-activiteiten/de-noorderkroon
                if ($get_previous_sitemap){
                                                                    //previous sitemap id
                    //dc("get_previous_sitemap: ".$breadcrumb->id.' - '.$webPage_breadcrumb[$key-1]->id.' - '.$previousParentId);
                    $this->setWebPageContent($this->getSitemapForWebPage($breadcrumb->id,$previousParentId));
                }
                else{
                    $this->setWebPageContent($this->getSitemapForWebPage($breadcrumb->id));
                }


                //$this->setWebPageContent($this->getSitemapForWebPage($breadcrumb->id));

                //last slug is sitemap_category
                //replace db_template_name with previous sitemap /newslis/cat

                $get_list_by_category_sitemap = false;
                if ($get_previous_sitemap){
                    //dc('1) '.$webPage_breadcrumb[$key-1]->template->db_template_name);
                    //dc('2) '.$this->WebPageContent->template->db_template_name);
                    //$this->WebPageContent->template->db_template_name = $webPage_breadcrumb[$key-1]->template->db_template_name;
                    $this->WebPageContent->template = $webPage_breadcrumb[$key-1]->template;
                    $get_list_by_category_sitemap = true;
                }

                //GET LIST
                //TODO nu alleen newslist orderby translation.published_at
                //post-container
                if (($this->WebPageContent->template->type == 'post-container') && (!($get_list_by_category_sitemap))){ //newslist
                    ///dc('getListByID: '.$breadcrumb->id);
                    //$test = $this->getOnlineSitemapsTranslationByParentIdAndPaginate($breadcrumb->id);
                    //$test = $this->getOnlineSitemapsTranslationMediaByParentIdAndPaginate($breadcrumb->id);
                    //get news status online and date between



                    if ($this->WebPageContent->template->db_template_name == 'tmp_postcontainer_eventlist'){
                        //dc('asdf'.$this->WebPageContent->template->db_template_name);
                        //$test = $this->getOnlineSitemapsTranslationMediaByParentIdAndPaginateOrderBy($breadcrumb->id,5,['asc','translation.events.when']);
                        $test = $this->getPublicEventList(10);

                    }
                    else{
                        $test = $this->getOnlineSitemapsTranslationMediaByParentIdAndPaginateOrderBy($breadcrumb->id,5);
                    }


                    $this->setWebPageSitemapList($test);
                    //dc('test 1');
                    //dc($this->WebPageContent->template->name);
                    //dc($test);
                }
                //post-container with sitemap_category
                elseif (($this->WebPageContent->template->type == 'post-container') && ($get_list_by_category_sitemap)){//newsList
                    //dc('getLISTBYCATID ('.$breadcrumb->id.')');

                    //ORG
                    //$test = $this->getOnlineSitemapsTranslationBySitemapCategoryIdAndPaginate($breadcrumb->id);


                    //dc($breadcrumb->id);
                    //news by category where status online and between dates and sitemap_category.category == 'nieuws'
                    $test = $this->getOnlineSitemapsTranslationBySitemapCategoryIdAndPaginateNew($breadcrumb->id,'locatie_als_nieuwsgroep');
                    //dc($this->WebPageContent);
                    //$navigation = $this->getOnlineSitemapsTranslationByParentId($parent_id);

                    //$test = [];
                    $this->setWebPageSitemapList($test);
                    //dc('test 2');
                    //dc($test);
                }
                //default //todo alleen doen bij sitemaplist
                else{
                    if ($this->WebPageContent->template->name == 'Locatie overzicht'){
                        $test = $this->getOnlineSitemapsTranslationMediaPagetemplateByParentIdAndTemplate($breadcrumb->id,$this->WebPageContent->template);
                        $this->setWebPageSitemapList($test);
                        //dc('test 3');
                    }


                }
            }
            //$c = $webPage_breadcrumb[$key];

            //$selectedSitemap[$cnt] = $webPage_navigation[$cnt]->where('id', $webPage_config->{'sitemap_id_'.($cnt+1).''})->first();
            //$selectedSitemap[$cnt]->setAttribute('selected',true);

            //->pluck('id')
            $selectedSitemap = null;
            if ($key > 0){
                $selectedSitemap = $webPage_navigation[$key-1]->where('id', $breadcrumb->id)->first();
                if ($selectedSitemap != null){
                    $selectedSitemap = $selectedSitemap->setAttribute('selected',true);
                }
            }

            if ($selectedSitemap != null){
                $selectedSitemap = $selectedSitemap->setAttribute('hasChildren',$webPage_navigation[$key]->count());
            }

            //$selectedSitemap[$cnt]->setAttribute('selected',true);

            //dc($breadcrumb->id.' - '.$key);
            //dc($selectedSitemap);

            //dc('---');
            /*
            $testArray['cnt'][$key] = $key;
            $testArray['parent_id'][$key] = $parent_id;
            $testArray['name'][$key] = $breadcrumb->translation->name;
            $cnt++;
            */
        }

        $this->setBreadcrumbArray($webPage_breadcrumb);
        $this->setWebPageNavigation($webPage_navigation);

        return true;
    }

    private function getSitemapTranslationTemplateByIdSlug($parent_id,$depth,$slug){

        $sitemap = Sitemap::with(['translation'

            /*=> function ($q) use ($parent_id,$slug){  // 1 query for photos with...
                //$q->where('parent_id',$parent_id);
                $q->where('slug',$slug);
                //$q->whereHas('locale', function ($q) { // ...1 subquery to filter the photos by related tags' name
                //    $q->where('languageCode', '' . app()->getLocale() . '');
                //$q->where('status', 'enabled');
                //});
            }*/

            //,'translation.mediatranslations'
            //,'childSitemaps'
            ,'template'

        ])->whereHas('translation', function ($q) use ($slug) { // ...1 subquery to filter the photos by related tags' name
            //    $q->where('languageCode', '' . app()->getLocale() . '');
            //$q->where('status', 'enabled');
            $q->where('slug',$slug);
        })
            //->where('parent_id',$parent_id)
            //->where('depth',$depth)
            ->where('parent_id',$parent_id)
            ->where('status','online')
            ->orderBy('order_by_number', 'asc')
            ->firstOrFail();
        return $sitemap;
    }
    private function getSitemapTranslationChildSitemapTemplateByIdSlug($parent_id,$depth,$slug){

        $sitemap = Sitemap::with(['translation'

            /*=> function ($q) use ($parent_id,$slug){  // 1 query for photos with...
                //$q->where('parent_id',$parent_id);
                $q->where('slug',$slug);
                //$q->whereHas('locale', function ($q) { // ...1 subquery to filter the photos by related tags' name
                //    $q->where('languageCode', '' . app()->getLocale() . '');
                //$q->where('status', 'enabled');
                //});
            }*/


            ,'childSitemaps'
            ,'template'

            ])->whereHas('translation', function ($q) use ($slug) { // ...1 subquery to filter the photos by related tags' name
            //    $q->where('languageCode', '' . app()->getLocale() . '');
            //$q->where('status', 'enabled');
                $q->where('slug',$slug);
            })
            //->where('parent_id',$parent_id)
            //->where('depth',$depth)
            ->where('parent_id',$parent_id)
            ->where('status','online')
            ->orderBy('order_by_number', 'asc')
            ->firstOrFail();
        return $sitemap;
    }

    public function getSitemapForWebPage($sitemapId,$previousParentId = null){
        $template = Sitemap::with('template')->findOrFail($sitemapId)->template;
        //dc($template);
        //$template = Sitemap::with('template')->findOrFail($id)->template;
        $sitemap = Sitemap::with(['translation'
                //,'template'
                ,'translation.'.$template->slug.''
                ,'translation.mediatranslations'
                ////,'translation.mediatranslations.images'
                ////,'translation.mediatranslations.files'
                //,'references.componentsTest'
                //,'references.referencetype'
                ////,'translations.'.$referenceType->slug.''
                //,'references.translation.mediatranslations'
                //,'references.translation.sitemaplistbyparentid'
                //,'references.translation.sitemaplistbyids'
                //,'references.translation.banner'
                ////,'references.referencetype'translations.
            ]
        )->where('id', $sitemapId)
         ->where('status','online')
         //->get();
         ->firstOrFail();



        if (is_null($previousParentId)){
            $sitemapIdForReferences = $sitemapId;
        }
        else{
            $sitemapIdForReferences = $previousParentId;
        }
        $sitemapReferences = Sitemap::with(['references.componentsTest'
            ,'references.referencetype'
            ////,'translations.'.$referenceType->slug.''
            ,'references.translation.mediatranslations'
            ,'references.translation.sitemaplistbyparentid'
            ,'references.translation.sitemaplistbyids'
            ,'references.translation.banner'])->where('id', $sitemapIdForReferences)->first();

        $sitemap->setRelation('references',$sitemapReferences->references);


        //} catch (\Exception $e) {
        //    abort('404');
        //abort('errors.401include');
        //}
        //return true;
        ///dc($sitemap->references->first()->translations);
        ///dc($sitemap->references->first()->translations->first());
        $referenceCollectionGroupByComponentName = [];
        //dc($sitemap->references);
        foreach ($sitemap->references as $key => $reference){
            $componentName = ($reference->componentsTest->where('id',$reference->pivot->component_id)->first()->name);

            /*$reference->translations->first()->setRelation(''.$reference->referencetype->slug.'',
                $reference->translations->first()->{$reference->referencetype->slug});
            */
            $referenceCollectionGroupByComponentName[$componentName][] = ($reference);
        }
        //dc($referenceCollectionGroupByComponentName);
        $sitemap->references = ($referenceCollectionGroupByComponentName);
        //$sitemap->template = $template;
        $sitemap->setRelation('template',$template);

        //return true;
        return $sitemap;
    }

    //end siteGenerator


    public function getOnlineSitemapsTranslationByParentId($parent_id){
        //dc($slug.','.$depth.','.$parent_id);
        $sitemap = Sitemap::with(['translation','template'])
            ->where('parent_id',$parent_id)
            ->where('status','online')
            ->orderBy('order_by_number', 'asc')
            ->get();
            //->paginate(5);
            //->firstOrFail();
        //dc($sitemap);

        return $sitemap;
    }
    public function getOnlineSitemapsTranslationMediaByParentId($parent_id){

        //dc($slug.','.$depth.','.$parent_id);
        $sitemap = Sitemap::with(['translation','translation.mediatranslations','template'])
            ->where('parent_id',$parent_id)
            ->where('status','online')
            ->orderBy('order_by_number', 'asc')
            ->get();
        //->paginate(5);
        //->firstOrFail();
        //dc($sitemap);

        return $sitemap;
    }
    public function getOnlineSitemapsTranslationMediaPagetemplateByParentIdAndTemplate($parent_id,$parentTemplate){
        //dc($parentTemplate);
        //$parent_template = Template::firstOrFail('parent_id');
        $template = Template::where('parent_id',$parentTemplate->id)->firstOrFail();
        //dc($template);
        //dc($slug.','.$depth.','.$parent_id);
        $sitemap = Sitemap::with(['translation'
            ,'translation.'.$template->slug.''
            ,'translation.mediatranslations'
            ,'template'])
            ->where('parent_id',$parent_id)
            ->where('status','online')
            ->orderBy('order_by_number', 'asc')
            ->get();
        //->paginate(5);
        //->firstOrFail();
        //dc($sitemap);

        return $sitemap;
    }
    public function getOnlineSitemapsTranslationByParentIdAndPaginate($parent_id){
        //dc($slug.','.$depth.','.$parent_id);
        $sitemap = Sitemap::with(['translation','template'])
            ->where('parent_id',$parent_id)
            ->where('status','online')
            ->orderBy('order_by_number', 'asc')
            //->get();
            ->paginate(5);
        //->firstOrFail();
        //dc($sitemap);

        return $sitemap;
    }

    public function getOnlineSitemapsTranslationMediaByParentIdAndPaginate($parent_id){
        //dc($slug.','.$depth.','.$parent_id);
        $sitemap = Sitemap::with(['translation','template'
            //,'translations.'.$template->slug.''
            ,'translation.mediatranslations'
            ,'categories.translation'
            //,'translation.mediatranslations.files'
            ])
            ->where('parent_id',$parent_id)
            ->where('status','online')
            ->orderBy('order_by_number', 'asc')
            //->get();
            ->paginate(5);
        //->firstOrFail();
        //dc($sitemap);

        return $sitemap;
    }

    public function getOnlineSitemapsTranslationMediaByParentIdAndPaginateOrderBy($parent_id, $perPage = 15,$sort=['desc','translation.published_at']){
        //dc($slug.','.$depth.','.$parent_id);
        $sitemap = Sitemap::with(['translation'


        => function ($q) {  // 1 query for photos with...
                $q->where('published_at','<',Carbon::now());
                $q->where('published_until','>',Carbon::now());
            }

            ,'template'
            //,'translations.'.$template->slug.''
            ,'translation.mediatranslations'
            ,'categories.translation'
            //,'translation.mediatranslations.files'
        ])
            ->where('parent_id',$parent_id)
            ->where('status','online')
            //->orderBy('order_by_number', 'asc')
            ->get();
            //->paginate(5);
        //->firstOrFail();
        //dc($sitemap);
        //dc($sitemap->all());

        //['asc','translation.events.when']
        if ($sort[0] == 'desc'){
            $sorted = $sitemap->sortByDesc($sort[1]);
        }
        else{
            $sorted = $sitemap->sortBy($sort[1]);
        }

        $sorted->values()->all();
        //dc($sorted->all());

        $sorted = $this->getPaginator($sorted->all(), $perPage);

        return $sorted;
    }



    //? get list by sitemap_category
    public function getOnlineSitemapsTranslationBySitemapCategoryId($sitemap_category_id){
        //dc($slug.','.$depth.','.$parent_id);
        $sitemap = Sitemap::with([
            //'translation',
            //'template',
            'categoriesTest.translation'

        => function ($q) {  // 1 query for photos with...
                //$q->where('sitemap_category_id',40);
                //$q->whereHas('locale', function ($q) { // ...1 subquery to filter the photos by related tags' name
                //    $q->where('languageCode', '' . app()->getLocale() . '');
                //$q->where('status', 'enabled');
                //});
            }


        ])
            ->where('id',$sitemap_category_id)
            ->where('status','online')
            ->orderBy('order_by_number', 'asc')
            ->get();
        //->paginate(5);
        //->firstOrFail();
        //dc($sitemap);

        return $sitemap;
    }


    //get list with between published_at and published_until and online
    public function getOnlineSitemapsTranslationBySitemapCategoryIdAndPaginateNew($sitemap_category_id,$sitemap_category_key,$paginate=10){
        //return [];
        //dc('test x');
        $sitemap = Sitemap::with(['categoriesTest'
         => function ($q) use ($sitemap_category_key){  // 1 query for photos with...
                $q->where('status','online');
                $q->where('category',''.$sitemap_category_key.'');
                #$q->whereHas('categoriesTest', function ($q) { // ...1 subquery to filter the photos by related tags' name
                    //$q->orderBy('sitemap_id','DESC');//won't work
                    #$q->where('published_at','<',Carbon::now());
                    #$q->where('published_until','>',Carbon::now());
                #    $q->where('category','locaties');
                #});
                $q->whereHas('translation', function ($q) { // ...1 subquery to filter the photos by related tags' name
                    //$q->orderBy('sitemap_id','DESC');//won't work
                    $q->where('published_at','<',Carbon::now());
                    $q->where('published_until','>',Carbon::now());
                });
        },'categoriesTest.translation.mediatranslations'
         ,'categoriesTest.categories.translation'
        ])->where('id',$sitemap_category_id)
            ->where('online',1)
            ->firstOrFail();
        //dc($sitemap);
        $sitemapCategories = $sitemap->categoriesTest;
        $sitemapCategories = $sitemapCategories->sortByDesc('translation.published_at');
        $sitemapCategories = $sitemapCategories->values()->all();

        $sitemapCategories = $this->getPaginator($sitemapCategories, $paginate);
        //dc($sitemapCategories);
        return $sitemapCategories;

        foreach ($sitemapCategories as $key => $sitemap_item){
            //dc($sitemap_item);

            //dc($key.') '.$sitemap_item->id.' - '.$sitemap_item->translation->name.' - '.$sitemap_item->translation->published_at->diffForHUmans().'');
        }

        //dc($sitemapCategories);

    }






//getOnlineSitemapsTranslationBySitemapCategoryIdAndPaginate
    //? get list by sitemap_category
    public function getOnlineSitemapsTranslationBySitemapCategoryIdAndPaginate($sitemap_category_id,$paginate=10){
        //dc($slug.','.$depth.','.$parent_id);
        $sitemap = Sitemap::with([
            //'translation',
            //'template',
            'categories.translation'

            => function ($q) {  // 1 query for photos with...
                //$q->paginate(5);
                //$q->where('sitemap_category_id',40);
                //$q->whereHas('locale', function ($q) { // ...1 subquery to filter the photos by related tags' name
                //    $q->where('languageCode', '' . app()->getLocale() . '');
                //$q->where('status', 'enabled');
                //});
            }


        ])
            ->where('id',$sitemap_category_id)
            ->where('status','online')
            ->orderBy('order_by_number', 'asc')
            ->firstOrFail();
        //dc($sitemap);
        $sitemap = $sitemap->categoriesTest()->paginate($paginate);
        dc($sitemap);
            //->paginate(5);
        //->firstOrFail();
        //dc($sitemap);


/*
        $sitemap = Sitemap::with(['translation','template'])
            ->where('parent_id',$parent_id)
            ->where('id',$sitemap_category_id)
            ->where('status','online')
            ->orderBy('order_by_number', 'asc')
            //->get();
            ->paginate(5);
*/
        return $sitemap;
    }





    public function getSitemapListIncRelationsByDbTemplateName($db_template_name,$relationsArray = []){

        $template = Template::where('db_template_name',$db_template_name)->firstOrFail();
        //$relationArray = ['translation'];
        $sitemapList = Sitemap::with([
            'translation','translation.'.$template->slug.''
        ]);

        if (count($relationsArray)>0){
            $sitemapList->with($relationsArray);
        }

        $sitemapList->where('template_id',$template->id)
            ->where('status','online')
            ->orderBy('order_by_number', 'asc');

        $sitemapList = $sitemapList->get();
        return $sitemapList;
    }



    private function setCondition($builder,$relation,$column,$operator,$value){
        $builder->whereHas($relation,function($q) use ($column,$operator,$value){
            $q->where($column,$operator,$value);
        });
    }

    private function isPublished($builder){
        $builder->whereHas('translation',function($q){
            $q->where('published_at','<',Carbon::now());
            $q->where('published_until','>',Carbon::now());
        });
    }
    private function isOnline($builder){
        $builder->where('status','online');
    }

    private function isPublishedAndOnline($builder){
        $this->isPublished($builder);
        $this->isOnline($builder);
    }

    public function getPublicEventList($perPage=15){
        $relations = ['template','translation.mediatranslations','translation.event'];
        $sitemapList = $this->getOnlineSitemapListIncRelation($relations);

        //get the events
        $this->setCondition($sitemapList,'template','db_template_name','=','tmp_page_events');
        //which are online
        $this->isPublishedAndOnline($sitemapList);

        $sitemapList = $sitemapList->get();


        $sorted = $sitemapList->sortBy('translation.event.when');
        //dc($sitemapList->pluck('translation.event.when')->all());

 /*       $sorted = $sorted->sortBy(function($sitemapList)
        {
            return $sitemapList->translation;
        });
*/

        $sorted->values()->all();


        $sorted = $this->getPaginator($sorted->all(), $perPage);

        //group mediaCollection
        $this->groupThisTranslationMediaCollection($sitemapList);
        return $sorted;
    }






    public function getOnlineSitemapListIncRelation($relationsArray = [],$perPage = 15){
        //dc($slug.','.$depth.','.$parent_id);




        $sitemapList = Sitemap::with([
            'translation'
            //,'template'
            //,'translations.'.$template->slug.''
            //,'translation.mediatranslations'
            //,'categories.translation'
            //,'translation.mediatranslations.files'
            ]);
            if (count($relationsArray)>0){
                $sitemapList->with($relationsArray);
            }



        //$sitemapList->whereHas('template',function($q){
        //    $q->where('db_template_name','=','tmp_post_event');
        //});

            //->where('parent_id',$parent_id)
        //$sitemapList->where('status','online');
            //->orderBy('order_by_number', 'asc')

        //dc($sitemap);

        return $sitemapList;


        //->get();
        //->paginate(5);
        //->firstOrFail();
        //dc($sitemap);
        //dc($sitemap->all());

        $sorted = $sitemap->sortByDesc('translation.published_at');
        $sorted->values()->all();
        //dc($sorted->all());

        $sorted = $this->getPaginator($sorted->all(), $perPage);

        return $sorted;
    }






    //?
    public function getSitemapById($id,$depth,$parent_id){
        //dc($slug.','.$depth.','.$parent_id);
        $sitemap = Sitemap::whereHas('translations.locale',function($q) use ($id){
            $q->where('languageCode', '' . app()->getLocale() . '');
        })
            //->whereHas('translations',function($q) use ($slug){
            //    $q->where('slug', '=', $slug);
            //})
            ->where('id',$id)
            ->where('depth',$depth)
            ->where('parent_id',$parent_id)
            //->get();
            ->firstOrFail();
        return $sitemap;
    }
    //?
    public function getSitemapTranslationById($id){


        //dc($slug.','.$depth.','.$parent_id);
        $sitemap = Sitemap::with(['translation'])
            //temp uit
            // ->where('id', $sitemapId)
            //->whereHas('translations',function($q) use ($slug){
            //    $q->where('slug', '=', $slug);
            //})
            //->where('id',$id)
            //->where('depth',$depth)
            ->where('id',$id)
            ->firstOrFail();
        //->paginate(5);
        //->firstOrFail();
        return $sitemap;
    }


    //admin sitemap/indexNav
    public function getSitemapsTranslationAndRelationsByParentId($parent_id){
        //dc($slug.','.$depth.','.$parent_id);
        $sitemap = Sitemap::with(['translation','template','user','childSitemaps','childSitemaps.childSitemaps'])
            ->where('parent_id',$parent_id)
            ->orderBy('order_by_number', 'asc')
            ->get();
            //->paginate(5);
            //->firstOrFail();

        return $sitemap;
    }

    //admin sitemap/indexTable
    public function getBreadcrumbByIdAndDepth($id,$depth){

        //dc($depth);
        //$depth  = 2;
        $sqlSelect = [];
        $sqlJoin= [];
        for ($cnt = 1;$cnt<=$depth;$cnt++){
            array_push($sqlSelect,'t'.$cnt.'.id AS level'.$cnt.'');
            if ($cnt > 1){
                array_push($sqlJoin,'LEFT JOIN sitemaps AS t'.$cnt.' ON t'.$cnt.'.parent_id = t'.($cnt-1).'.id');
            }
        }
        $sqlSelect = (implode(',',$sqlSelect));
        $sqlJoin = (implode(' ',$sqlJoin));
        $sqlJoin .= ' WHERE t'.$depth.'.id = :somevariable';


        $sql = "SELECT ".$sqlSelect." FROM sitemaps AS t1 ".$sqlJoin."";
//dc($sql);
        $results = DB::select(DB::raw($sql),
            array(
                'somevariable' => $id,
            ));

        $results = (head($results));

        $sitemapIdsIn = array();
        for ($cnt = 1;$cnt<=$depth;$cnt++){
            array_push($sitemapIdsIn,$results->{'level'.$cnt.''});
        }

        $sitemap = Sitemap::with(['translation'])
            ->whereIn('id',$sitemapIdsIn)->get();
        //dc($sitemapIdsIn);




        $url = '';
        foreach ($sitemap as $key => $sitemap_item){
            $sitemap_item->url = $sitemap_item->translation->slug;
            if ($sitemap_item->depth > 0){
                $url .= '/'.$sitemap_item->translation->slug;
            }
            $sitemap_item->url = $url;
            //dc($sitemap_item->depth.' - '.$sitemap_item->url.' - '.$sitemap_item->translation->name);
            if ($sitemap_item->id == 1){
                //dc('asdfds');
                $sitemap_item->translation->name = 'home';
            }
        }
        return $sitemap;
    }


    public function getBreadcrumbForMenuIndexTable($sitemap){
        $depth = count($sitemap);
        $url = '';
        $retval = "<ol class=\"breadcrumb form-group\" style=\"display:inline\">";
        $retval .= "<li><a href=\"".route('admin::menu.index',['sitemap_parent_id'=>0])."\">Homepage</a></li>";
        foreach ($sitemap as $key => $breadcrumbItem){
            //last

            if ($key == ($depth-1)){
                //$url .= "/".$breadcrumb->translation->slug;
                //$retval .= "<li><a href=\"".$url."\">".$breadcrumb->translation->name."LAST</a></li>";
                $retval .= "<li class=\"active\">".$breadcrumbItem->translation->name."</li>";
            }
            else{
                //$url .= "/".$breadcrumbItem->translation->slug;
                $url = route('admin::menu.index',['sitemap_parent_id'=>$breadcrumbItem->id]);
                $retval .= "<li><a href=\"".$url."\">".$breadcrumbItem->translation->name." ($breadcrumbItem->id)</a></li>";
            }
        }
        $retval .= "</ol>";
        return $retval;
    }


    public function getBreadcrumbForMenuIndexTableAjax($sitemap,$start='start'){
        $depth = count($sitemap);
        $url = '';
        $start_disabled = "";
        $homepage_disabled = "";
        //if ($sitemap == 0){
        //    $start_disabled = "";
        //    $homepage_disabled = "disabled";
        //}

        $retval = "<ol class=\"breadcrumb form-group\" style=\"display:inline\">";
        $retval .= "<li><a onclick=\"wiLoad(0)\" class='".$start_disabled."'>".$start."</a></li>";
        $retval .= "<li><a onclick=\"wiLoad(1)\" class='".$homepage_disabled."'>Homepage</a></li>";
        foreach ($sitemap as $key => $breadcrumbItem){
            //last

            if ($key == ($depth-1)){
                //$url .= "/".$breadcrumb->translation->slug;
                //$retval .= "<li><a href=\"".$url."\">".$breadcrumb->translation->name."LAST</a></li>";
                $retval .= "<li class=\"active\">".$breadcrumbItem->translation->name."</li>";
            }
            else{
                //$url .= "/".$breadcrumbItem->translation->slug;
                //$url = route('admin::menu.index',['sitemap_parent_id'=>$breadcrumbItem->id]);


                $retval .= "<li><a onclick=\"wiLoad(".$breadcrumbItem->id.")\">".$breadcrumbItem->translation->name." ($breadcrumbItem->id) ajax</a></li>";
            }
        }
        $retval .= "</ol>";
        return $retval;
    }



    /*INDEX NEW*/

    public function getSitemapForIndexByTemplateName($templateName){

        $template = Template::where('name','Nieuwsbericht')->firstOrFail();

        $sitemap = Sitemap::with([
            'translation',
            'template',
            'user'
            //'categoriesTest.translation'
             //=> function ($q) {  // 1 query for photos with...
                //$q->paginate(5);
                //$q->where('sitemap_category_id',40);
                //$q->whereHas('locale', function ($q) { // ...1 subquery to filter the photos by related tags' name
                //    $q->where('languageCode', '' . app()->getLocale() . '');
                //$q->where('status', 'enabled');
                //});
            //}


        ])
            ->where('template_id',$template->id)
            //->where('status','online')
            ->orderBy('created_at', 'asc')
            ->get();
        return $sitemap;
    }






}