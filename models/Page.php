<?php
namespace app\models;

use Yii;
use yii\web\IdentityInterface;
use yii\behaviors\TimestampBehavior;

class Page extends \yii\db\ActiveRecord
{
    public static function tableName(){
        return 'pages';
    }

    public function rules()
    {
        return [
            [['name','title'], 'filter', 'filter' => 'trim'],
            [['name','title'], 'required']
        ];
    }

    /* Поведения */
    public function behaviors(){
        return [
            TimestampBehavior::className()
        ];
    }

    /* Поиск по ID */
    public static function findById($id){
        return static::findOne([
            'id' => $id
        ]);
    }

    /* Возваращает текущий ID */
    public function getId()
    {
        return $this->id;
    }

    /*
     * возвращаем список id тегов по содержимому
     */
    public function selectTagsByName($tags){
        return (new \yii\db\Query())
            ->select(['t.id'])
            ->from('tags t')
            ->where(['t.name' => $tags])
            ->all();
    }

    /*
     * сохраняем теги, игнорируя дубликаты
     */
    public function insertTags($tags_array){
        $connection = new \yii\db\Query();
        $db = Yii::$app->db;
        $sql = $db->queryBuilder->batchInsert('tags', ['name'], $tags_array);
        $sql = str_replace('INSERT INTO', 'INSERT IGNORE', $sql);
        $db->createCommand($sql)->execute();
    }

    /*
     * прикрепляем теги к странице
     */
    public function insertPageTags($page_tags){
        return Yii::$app->db->createCommand()->batchInsert('pages_tags', ['page_id', 'tag_id'], $page_tags)->execute();
    }

    /*
     * получение строки с тегами (в формате тег1,тег2...) для конкретной страницы
     */
    public function getTagsString($page_id){
        $tags_array = (new \yii\db\Query())
            ->select(['t.name'])
            ->from('pages p,')
            ->leftJoin('pages_tags pt', 'p.id = pt.page_id')
            ->leftJoin('tags t', 'pt.tag_id = t.id')
            ->where(['p.id' => $page_id])
            ->all();
        $tags_string = '';
        foreach($tags_array as $item){
            $tags_string .= $item['name'].',';
        }
        return substr($tags_string,0,-1);
    }

    /*
     * очищаем сессию картинок
     */
    public function clearImageSession(){
        Yii::$app->session->remove('thumbnail');
        Yii::$app->session->remove('image');
        Yii::$app->session->remove('thumbnail_fb');
        Yii::$app->session->remove('slideshow');
    }

    /*
     * сохраняем картинки в сессию, при переходе на страницу редактирования
     */
    public function setImageSession($page){
        if($page['thumbnail'] != ''){
            Yii::$app->session->set('thumbnail',$page['thumbnail']);
        }
        if($page['image'] != '') {
            Yii::$app->session->set('image', $page['image']);
        }
        if($page['thumbnail_fb'] != '') {
            Yii::$app->session->set('thumbnail_fb', $page['thumbnail_fb']);
        }
    }

    /*
     * сохраняем картинки во временную директорию
     * нужно будет подлючить cron-задачу на очищение папки с временными файлами(например раз в день)
     */
    public function saveTempImages($files){
        if(!empty($files['thumbnail']) && $files['thumbnail']['error'] == 0){
            $new_path = 'temp/'.md5(time()+rand(1,100)).substr($files['thumbnail']['name'],strpos($files['thumbnail']['name'],'.'));
            move_uploaded_file($files['thumbnail']['tmp_name'],$new_path);
            Yii::$app->session->set('thumbnail',$new_path);
        }
        if(!empty($files['image']) && $files['image']['error'] == 0){
            $new_path = 'temp/'.md5(time()+rand(1,100)).substr($files['image']['name'],strpos($files['image']['name'],'.'));
            move_uploaded_file($files['image']['tmp_name'],$new_path);
            Yii::$app->session->set('image',$new_path);
        }
        if(!empty($files['thumbnail_fb']) && $files['thumbnail_fb']['error'] == 0){
            $new_path = 'temp/'.md5(time()+rand(1,100)).substr($files['thumbnail_fb']['name'],strpos($files['thumbnail_fb']['name'],'.'));
            move_uploaded_file($files['thumbnail_fb']['tmp_name'],$new_path);
            Yii::$app->session->set('thumbnail_fb',$new_path);
        }
        if(!empty($files['slideshow']) && !empty($files['slideshow']['name'])){
            if(Yii::$app->session->has('slideshow') && Yii::$app->session->get('slideshow') != ''){
                $image_sess = Yii::$app->session->get('slideshow');
            }else{
                $image_sess = [];
            }
            foreach($files['slideshow']['tmp_name'] as $key => $block){
                foreach($block as $image_key => $image){
                    $new_path = 'temp/'.md5(time()+rand(1,100)).substr($files['slideshow']['name'][$key][$image_key],strpos($files['slideshow']['name'][$key][$image_key],'.'));
                    move_uploaded_file($files['slideshow']['tmp_name'][$key][$image_key],$new_path);
                    if(!isset($image_sess[$key]) || empty($image_sess[$key])){
                        $image_sess[$key] = [];
                    }
                    array_push($image_sess[$key],$new_path);
                }
            }
            Yii::$app->session->set('slideshow',$image_sess);
        }
    }

    /*
     * сохраняем картинки в постоянную папку
     */
    public function saveImage($path){
        if($path != ''){
            $new_path = str_replace('temp/','uploads/',$path);
            copy($path,$new_path);
            unlink($path);
            return $new_path;
        }else{
            return '';
        }
    }

    /*
     * получаем список всех категорий
     */
    public function getCategoriesList(){
        return (new \yii\db\Query())
            ->select(['c.name', 'c.id'])
            ->from('categories c,')
            ->orderBy(['c.id' => SORT_DESC])
            ->all();
    }

    /*
     * удаляем теги у страницы
     */
    public function deletePageTags($page_id){
        Yii::$app->db->createCommand()->delete('pages_tags', 'page_id = '.$page_id)->execute();
        return true;
    }

    /*
     * получаение объекта-списка страниц с применением фильтров
     */
    public function getPagesObject(){
        $query =  (new \yii\db\Query())
            ->select(['p.id', 'p.name', 'c.name category', 'p.published_at', 'u.name author'])
            ->from('pages p,')
            ->leftJoin('categories c', 'p.category_id = c.id')
            ->leftJoin('users u', 'p.author_id = u.id');
        $where = 0;
        if(Yii::$app->session->has('name') && Yii::$app->session->get('name') != ''){
            if($where == 0){
                $query->where(['p.name' => Yii::$app->session->get('name')]);
                $where = 1;
            }else{
                $query->andWhere(['p.name' => Yii::$app->session->get('name')]);
            }
            $where['p.name'] = Yii::$app->session->get('name');
        }
        if(Yii::$app->session->has('category') && Yii::$app->session->get('category') != ''){
            if($where == 0){
                $query->where(['p.category_id' => Yii::$app->session->get('category')]);
                $where = 1;
            }else{
                $query->andWhere(['p.category_id' => Yii::$app->session->get('category')]);
            }
        }
        if(Yii::$app->session->has('published') && Yii::$app->session->get('published') != ''){
            if(Yii::$app->session->get('published') == 2){
                if($where == 0){
                    $query->where(['!=', 'p.published_at', 0]);
                    $where = 1;
                }else{
                    $query->andWhere(['!=', 'p.published_at', 0]);
                }
            }else{
                if($where == 0){
                    $query->where(['p.published_at' => 0]);
                    $where = 1;
                }else{
                    $query->andWhere(['p.published_at' => 0]);
                }
            }
        }
        if(Yii::$app->session->has('publishdate') && Yii::$app->session->get('publishdate') != ''){
            if($where == 0){
                $query->where(['between','p.published_at',strtotime(Yii::$app->session->get('publishdate')),strtotime(Yii::$app->session->get('publishdate'))+86399]);
                $where = 1;
            }else{
                $query->andWhere(['between','p.published_at',strtotime(Yii::$app->session->get('publishdate')),strtotime(Yii::$app->session->get('publishdate'))+86399]);
            }
        }
        if(Yii::$app->session->has('author') && Yii::$app->session->get('author') != ''){
            if($where == 0){
                $query->where(['like', 'u.name', Yii::$app->session->get('author')]);
                $where = 1;
            }else{
                $query->andWhere(['like', 'u.name', Yii::$app->session->get('author')]);
            }
        }
        $query->orderBy(['p.id' => SORT_DESC]);
        return $query;
    }

    /*
     * получаем список блоков для страницы
     */
    public function getListPagesBlocks($page_id){
        return (new \yii\db\Query())
            ->select(['pb.id' ,'pb.block_id', 'pb.block_type', 'pb.order'])
            ->from('pages_blocks pb,')
            ->where(['pb.page_id' => $page_id])
            ->all();
    }

    /*
     * удаление блоков у страницы
     */
    public function deleteBlocks($list_ids, $delete_list){
        Yii::$app->db->createCommand()->delete('pages_blocks', ['id' => $list_ids])->execute();
        if(!empty($delete_list['page_title'])){
            Yii::$app->db->createCommand()->delete('page_title', ['id' => $delete_list['page_title']])->execute();
        }
        if(!empty($delete_list['product_details'])){
            Yii::$app->db->createCommand()->delete('product_details', ['id' => $delete_list['product_details']])->execute();
        }
        if(!empty($delete_list['slideshow'])){
            $image_list =(new \yii\db\Query())
                ->select(['si.path'])
                ->from('slideshow_image si,')
                ->where(['si.block_id' => $delete_list['slideshow']])
                ->all();
            foreach ($image_list as $item) {
                unlink($item['path']);
            }
            Yii::$app->db->createCommand()->delete('slideshow_image', ['block_id' => $delete_list['slideshow']])->execute();
        }
        return true;
    }

    /*
     * получение массива содержимого блоков
     */
    public function getPageBlocks($page_id){
        $page_blocks = $this->getListPagesBlocks($page_id);
        if(!empty($page_blocks)){
            $blocks_array = [];
            foreach($page_blocks as $block){
                if($block['block_type'] == 'page_title'){
                    $page_title = (new \yii\db\Query())
                        ->select(['pt.name', 'pt.title'])
                        ->from('page_title pt,')
                        ->where(['pt.id' => $block['block_id']])
                        ->one();
                    array_push($blocks_array,['block_type' => 'page_title', 'name' => $page_title['name'], 'title' => $page_title['title'], 'order' => $block['order']]);
                }elseif($block['block_type'] == 'product_details'){
                    $product_details = (new \yii\db\Query())
                        ->select(['pd.title', 'pd.description', 'pd.url', 'pd.price', 'pd.currency'])
                        ->from('product_details pd,')
                        ->where(['pd.id' => $block['block_id']])
                        ->one();
                    array_push($blocks_array,['block_type' => 'product_details', 'title' => $product_details['title'], 'description' => $product_details['description'], 'url' => $product_details['url'], 'price' => $product_details['price'], 'currency' => $product_details['currency'], 'order' => $block['order']]);
                }elseif($block['block_type'] == 'slideshow'){
                    array_push($blocks_array,['block_type' => 'slideshow', 'order' => $block['order']]);
                    $slideshow = (new \yii\db\Query())
                        ->select(['s.path'])
                        ->from('slideshow_image s,')
                        ->where(['s.block_id' => $block['id']])
                        ->all();
                    if(Yii::$app->session->has('slideshow') && Yii::$app->session->get('slideshow')) {
                        $slideshow_array = Yii::$app->session->get('slideshow');
                    }else{
                        $slideshow_array = [];
                    }
                    $slideshow_array[$block['order']] = [];
                    foreach($slideshow as $item){
                        array_push($slideshow_array[$block['order']],$item['path']);
                    }
                    Yii::$app->session->set('slideshow',$slideshow_array);
                }
            }
            $result = [];
            $i = 0;
            foreach($blocks_array as $item){
                $i++;
                foreach($blocks_array as $block){
                    if($block['order'] == $i){
                        array_push($result,$block);
                        break;
                    }
                }
            }
            return $result;
        }else{
            return null;
        }
    }

    /*
     * прикрепление блоков к странице
     */
    public function insertPagesBlocks($pages_blocks){
        return Yii::$app->db->createCommand()->batchInsert('pages_blocks', ['page_id', 'block_id', 'block_type', 'order'], $pages_blocks)->execute();
    }

    /*
     * Сохранение блока 'Page title'
     */
    public function insertPageTitle($block){
        $connection = Yii::$app->db;
        $connection->createCommand()->insert('page_title', [
            'name' => $block['name'],
            'title' => $block['title'],
        ])->execute();
        return $connection->getLastInsertID();
    }

    /*
     * Сохранение блока 'Product details'
     */
    public function insertProductDetails($block){
        $connection = Yii::$app->db;
        $connection->createCommand()->insert('product_details', [
            'title' => $block['title'],
            'description' => $block['description'],
            'url' => $block['url'],
            'price' => $block['price'],
            'currency' => $block['currency']
        ])->execute();
        return $connection->getLastInsertID();
    }

    /*
     * Сохранение блока 'Slideshow'
     */
    public function insertSlideshow($block, $key){
        $connection = Yii::$app->db;
        $connection->createCommand()->insert('pages_blocks', [
            'page_id' => $block['page_id'],
            'block_id' => 0,
            'block_type' => 'slideshow',
            'order' => $block['order']
        ])->execute();
        $block_id = $connection->getLastInsertID();
        $images = [];
        $images_sess = Yii::$app->session->get('slideshow');
        foreach($images_sess[$key] as $item){
            array_push($images, [$block_id, $item]);
        }
        $connection->createCommand()->batchInsert('slideshow_image', ['block_id', 'path'], $images)->execute();
        return true;
    }
}