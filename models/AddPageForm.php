<?php
namespace app\models;

use Yii;
use yii\base\Model;
use app\models\Page;

class AddPageForm extends  Model
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

    public function  AddPage(){
        $page = new Page();
        $page->name = htmlspecialchars($this->name);
        $page->title = htmlspecialchars($this->title);
        $page->category_id = $this->category_id;
        $page->author_id = 1;
        if($this->published){
            $page->published_at = time();
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
         * сохранение картинок с временной директории в постояннуую
         */
        $page['thumbnail'] = $page->saveImage(Yii::$app->session->get('thumbnail'));
        $page['image'] = $page->saveImage(Yii::$app->session->get('image'));
        $page['thumbnail_fb'] = $page->saveImage(Yii::$app->session->get('thumbnail_fb'));
        $page->save();
        /*
         * сохранение блоков, тегов и очистка сессии
         */
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
        $page->insertPageTags($batch_pages_tags);
        return true;
    }
}