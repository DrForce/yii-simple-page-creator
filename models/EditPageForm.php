<?php
namespace app\models;

use Yii;
use yii\base\Model;
use app\models\Page;

class EditPageForm extends  Model
{

    public $name;
    public $title;
    public $category_id;
    public $tags;
    public $published;
    public $blocks;

    public function rules()
    {
        return [
            [['name','title'], 'filter', 'filter' => 'trim'],
            [['name','title','tags','published'], 'string', 'max' => 255],
            [['name','title','category_id'], 'required'],
            ['blocks', 'default', 'value' => null]
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => 'Name',
            'title' => 'Title'
        ];
    }

    public function  EditPage($id){
        $page = new Page();
        $page = $page->findById($id);
        $page->name = htmlspecialchars($this->name);
        $page->title = htmlspecialchars($this->title);
        $page->category_id = $this->category_id;
        $page->author_id = 1;
        if($this->published){
            if($page->published_at == 0){
                $page->published_at = time();
            }
        }else{
            $page->published_at = 0;
        }
        $tags_array = explode(',',$this->tags);
        $batch_tags = [];
        foreach($tags_array as $item){
            array_push($batch_tags,['name' => $item]);
        }
        $page->insertTags($batch_tags);
        /*
         * удаляем картинки, блоки и теги у страницы
         * сохраняем новые
         */
        if(Yii::$app->session->get('thumbnail') == ''){
            unlink($page['thumbnail']);
            $page['thumbnail'] = '';
        }elseif(strpos(Yii::$app->session->get('thumbnail'),'uploads/') === false){
            $page['thumbnail'] = $page->saveImage(Yii::$app->session->get('thumbnail'));
        }
        if(Yii::$app->session->get('image') == ''){
            unlink($page['image']);
            $page['image'] = '';
        }elseif(strpos(Yii::$app->session->get('image'),'uploads/') === false){
            $page['image'] = $page->saveImage(Yii::$app->session->get('image'));
        }
        if(Yii::$app->session->get('thumbnail_fb') == ''){
            unlink($page['thumbnail_fb']);
            $page['thumbnail_fb'] = '';
        }elseif(strpos(Yii::$app->session->get('thumbnail_fb'),'uploads/') === false){
            $page['thumbnail_fb'] = $page->saveImage(Yii::$app->session->get('thumbnail_fb'));
        }
        $page->save();
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
        if(!empty($this->blocks)){
            $i = 0;
            $pages_blocks = [];
            foreach($this->blocks as $key => $block){
                $i++;
                if($block['block_type'] == 'page_title'){
                    array_push($pages_blocks, [$page->id, $page->insertPageTitle($block), 'page_title' , $i]);
                }elseif($block['block_type'] == 'product_details'){
                    array_push($pages_blocks, [$page->id, $page->insertProductDetails($block), 'product_details' , $i]);
                }elseif($block['block_type'] == 'slideshow'){
                    $page->insertSlideshow(['page_id' => $page->id, 'block_type' => 'slideshow' , 'order' => $i], $key);
                }
            }
            if(!empty($pages_blocks)){
                $page->insertPagesBlocks($pages_blocks);
            }
        }
        $page->clearImageSession();
        $ids = $page->selectTagsByName($tags_array);
        $batch_pages_tags = [];
        foreach($ids as $id){
            array_push($batch_pages_tags, [$page->id, $id['id']]);
        }
        $page->deletePageTags($page->id);
        $page->insertPageTags($batch_pages_tags);
        return true;
    }
}