<?php

namespace app\controllers;

use app\models\AddPageForm;
use app\models\EditPageForm;
use app\models\Page;
use Yii;
use yii\db\ActiveRecord;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\data\Pagination;

class SiteController extends Controller
{

    public function actionIndex()
    {
        $model = new Page();
        /*
         * делаем выборку-объект для пагинации и список категорий.
         */
        $query = $model->getPagesObject();
        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSize' => 20, 'forcePageParam' => false]);
        $models = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->all();
        return $this->render('index', ['list' => $models, 'pages' => $pages, 'categories' => $model->getCategoriesList()]);
    }

    public function actionError()
    {
        return $this->render('error');
    }

    public function actionNewPage(){
        $page = new Page();
        if(!isset($_POST) || empty($_POST)){
            $page->clearImageSession();
        }
        $model = new AddPageForm();
        /*
         * сохраняем картинки в сессию, если они есть
         */
        if(!empty($_FILES)){
            $page->saveTempImages($_FILES);
        }
        if($model->load(Yii::$app->request->post()) && $model->validate()){
            $model->AddPage();
            return $this->goHome();
        }else{
            return $this->render('new-page', ['action' => 'AddPageForm', 'errors' => $model->errors, 'page' => Yii::$app->request->post('AddPageForm'), 'categories' => $page->getCategoriesList()]);
        }
    }

    public function actionEditPage($id = null){
        if($id == null){
            $this->goHome();
        }
        $page = new Page();
        $model = new EditPageForm();
        if(!empty($_FILES)){
            $page->saveTempImages($_FILES);
        }
        if($model->load(Yii::$app->request->post()) && $model->validate()){
            $model->EditPage($id);
            return $this->goHome();
        }else{
            $post = Yii::$app->request->post();
            if(!empty($post)){
                return $this->render('new-page', ['action' => 'EditPageForm', 'errors' => $model->errors, 'page' => Yii::$app->request->post('EditPageForm'), 'categories' => $page->getCategoriesList()]);
            }else{
                $page = new Page();
                $page = $page->findById($id);
                $page->setImageSession($page);
                $blocks = $page->getPageBlocks($id);
                return $this->render('new-page',['action' => 'EditPageForm', 'errors' => $model->errors, 'page' => $page, 'blocks' => $blocks, 'tags' => $page->getTagsString($id), 'categories' => $page->getCategoriesList()]);
            }
        }
    }

    public function actionDeletePage($id){
        $page = new Page();
        $page = $page->findById($id);
        $page->deletePageTags($page->id);
        if($page->thumbnail != ''){
            unlink($page->thumbnail);
        }
        if($page->image != ''){
            unlink($page->image);
        }
        if($page->thumbnail_fb != ''){
            unlink($page->thumbnail_fb);
        }
        $pages_blocks = $page->getListPagesBlocks($page->id);
        $delete_list = ['page_title' => []];
        $delete_list = ['product_details' => []];
        $delete_list = ['slideshow' => []];
        $pages_blocks_ids = [];
        foreach($pages_blocks as $item){
            array_push($pages_blocks_ids, $item['id']);
            if($item['block_type'] == 'slideshow'){
                array_push($delete_list[$item['block_type']], $item['id']);
            }else{
                array_push($delete_list[$item['block_type']], $item['block_id']);
            }
        }
        $page->deleteBlocks($pages_blocks_ids, $delete_list);
        $page->deletePageTags($page->id);
        $page->delete();
        $this->goHome();
    }

    /*
     * ajax-функции очистки картинки из сессии, при удалении ее со страницы и очистка фильтров
     */
    public function actionAjaxDeleteSession(){
        Yii::$app->session->set($_GET['target'],'');
        return true;
    }

    public function actionAjaxSetFilter(){
        Yii::$app->session->set($_GET['name'], $_GET['value']);
        return true;
    }
}
