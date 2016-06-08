<?php
use yii\helpers\Url;
/* @var $action  */
/* @var $categories  */
/* @var errors */
/**
 * Подготавливаем данные к выводу, т.к. есть небольшие различия в функциях Add и Edit
 * В кратце: $action содержит название функции-обработчика Add/Edit
 * для вывода картинок проверяем их наличие в сессии (пути к ним хранятся там)
 * для вывода динамических блоков пробегаем по ним в цикле
 */
if(empty($blocks) && !empty($page['blocks'])){
    $blocks = $page['blocks'];
    unset($page['blocks']);
}
if(empty($tags) && !empty($page['tags'])){
    $tags = $page['tags'];
    unset($page['tags']);
}
$slideshow = Yii::$app->session->get('slideshow');
?>
<div class="left_side col-sm-3">
    <input type="hidden" id="action-name" value="<?php echo $action ?>"" />
    <form method="post" id="new-page-form" action="<?php if(Yii::$app->controller->action->id == 'new-page'){echo Url::toRoute('/site/new-page');}else{echo Url::toRoute(['/site/edit-page','id' => $page['id']]);} ?>" enctype="multipart/form-data">
        <div class="head">
            <input type="checkbox" id="publish" name="<?php echo $action.'[published]' ?>" <?php if((isset($page['published_at']) && $page['published_at'] != 0) || (isset($page['published']) && $page['published'] == 'on')){echo 'checked';} ?>/>
            <?php if(Yii::$app->controller->action->id == 'edit-page'): ?>
                <a class="btn btn-primary" style="float:right; margin-right:5px; border-radius: 4px 4px 0 0" href="<?php echo Url::toRoute(['/site/delete-page', 'id' => $page['id']]); ?>"  onclick="return confirm('Are you sure to delete this page?');">Delete</a>
            <?php endif ?>
            <hr />
            <h3><?php if(Yii::$app->controller->action->id == 'new-page'){echo 'Create Page';}else{echo 'Update Page Details';} ?></h3>
        </div>
        <div class="main-info">
            <div class="form-group <?php if(!empty($errors['name'])){echo 'has-error';} ?>">
                <input class="form-control" name="<?php echo $action.'[name]' ?>" type="text" placeholder="Name" value="<?php if(isset($page['name']) && !empty($page['name'])){echo $page['name'];} ?>"/>
                <?php if(!empty($errors['name'])): ?>
                    <label class="control-label"><?php echo $errors['name'][0] ?></label>
                <?php endif ?>
            </div>
            <div class="form-group <?php if(!empty($errors['title'])){echo 'has-error';} ?>">
                <input class="form-control" name="<?php echo $action.'[title]' ?>" type="text" placeholder="Title" value="<?php if(isset($page['title']) && !empty($page['title'])){echo $page['title'];} ?>"/>
                <?php if(!empty($errors['title'])): ?>
                    <label class="control-label"><?php echo $errors['title'][0] ?></label>
                <?php endif ?>
            </div>

            <div class="form-group">
                <select class="form-control" name="<?php echo $action.'[category_id]' ?>">
                    <?php foreach($categories as $item): ?>
                        <option value="<?php echo $item['id'] ?>" <?php if($page['category_id'] == $item['id']){echo 'selected';} ?>><?php echo $item['name'] ?></option>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="form-group">
                <p>Thumbnail</p>
                <input id="thumbnail" type="file" data-show-upload="false" name="thumbnail" class="file<?php if(Yii::$app->session->has('thumbnail')){echo '-loading';} ?>" data-value="<?php echo Yii::$app->session->get('thumbnail') ?>">
            </div>
            <div class="form-group">
                <p>Image</p>
                <input id="image" type="file" data-show-upload="false" name="image" class="file<?php if(Yii::$app->session->has('image')){echo '-loading';} ?>" data-value="<?php echo Yii::$app->session->get('image') ?>">
            </div>
            <div class="form-group">
                <p>Thumbnail for Facebook</p>
                <input id="thumbnail_fb" type="file" data-show-upload="false" name="thumbnail_fb" class="file<?php if(Yii::$app->session->has('thumbnail_fb')){echo '-loading';} ?>" data-value="<?php echo Yii::$app->session->get('thumbnail_fb') ?>">
            </div>
            <div class="form-group">
                <p>Add tags</p>
                <input name="<?php echo $action.'[tags]' ?>" class="form-control" id="tags" value="<?php if(isset($tags) && !empty($tags)){echo $tags;} ?>" />
            </div>
            <div class="page-content">
                <h3>Content</h3>
                <div class="form-group" style="margin-bottom: 55px">
                    <div class="col-sm-8" style="padding: 0;">
                        <select class="form-control content-tile">
                            <option>-- select a tile --</option>
                            <option>Page Title</option>
                            <option>Product details</option>
                            <option>Slideshow</option>
                        </select>
                    </div>
                    <button type="button" class="btn btn-primary add-content" style="padding: 6px 30px; float: right;">Add</button>
                </div>
                <?php if(!empty($blocks)): ?>
                    <?php $i = 0 ?>
                    <?php foreach($blocks as $item): ?>
                        <?php $i++ ?>
                        <?php if($item['block_type'] == 'page_title'): ?>
                            <div class="col-sm-12 content-element">
                                <input type="hidden" name="<?php echo $action.'[blocks]['.$i.'][block_type]' ?>" value="page_title"">
                                <p class="content-head">Page Title<span class="glyphicon glyphicon-trash delete-block"></span></p>
                                <div class="form-group">
                                    <input class="form-control" type="text" name="<?php echo $action.'[blocks]['.$i.'][name]' ?>" placeholder="Name" value="<?php echo $item['name'] ?>"/>
                                </div>
                                <div class="form-group">
                                    <input class="form-control" type="text" name="<?php echo $action.'[blocks]['.$i.'][title]' ?>" placeholder="Title" value="<?php echo $item['title'] ?>"/>
                                </div>
                            </div>
                        <?php elseif($item['block_type'] == 'product_details'): ?>
                            <div class="col-sm-12 content-element">
                                <input type="hidden" name="<?php echo $action.'[blocks]['.$i.'][block_type]' ?>" value="product_details">
                                <p class="content-head">Product details<span class="glyphicon glyphicon-trash delete-block"></span></p>
                                <div class="form-group">
                                    <input class="form-control" type="text" name="<?php echo $action.'[blocks]['.$i.'][title]' ?>" placeholder="Title"  value="<?php echo $item['title'] ?>">
                                </div>
                                <div class="form-group">
                                    <textarea class="form-control" rows="5" name="<?php echo $action.'[blocks]['.$i.'][description]' ?>" placeholder="Description"><?php echo $item['description'] ?></textarea>
                                </div>
                                <div class="form-group">
                                    <input class="form-control" type="text" name="<?php echo $action.'[blocks]['.$i.'][url]' ?>" placeholder="Web address"  value="<?php echo $item['url'] ?>">
                                </div>
                                <div class="form-group col-sm-6" style="padding-left: 0;">
                                    <input class="form-control" type="text" name="<?php echo $action.'[blocks]['.$i.'][price]' ?>" placeholder="Price"  value="<?php echo $item['price'] ?>">
                                </div>
                                <div class="form-group col-sm-6" style="padding-right: 0;">
                                    <input class="form-control" type="text" name="<?php echo $action.'[blocks]['.$i.'][currency]' ?>" placeholder="Currency"  value="<?php echo $item['currency'] ?>">
                                </div>
                            </div>
                        <?php elseif(($item['block_type'] == 'slideshow')): ?>
                            <div class="col-sm-12 content-element">
                                <input type="hidden" name="<?php echo $action.'[blocks]['.$i.'][block_type]' ?>" value="slideshow"">
                                <p class="content-head">Slideshow<span class="glyphicon glyphicon-trash delete-block"></span></p>
                                <div class="form-group">
                                    <input class="form-control slideshow-input" multiple data-show-upload="false" type="file" name="<?php echo $action.'[slideshow]['.$i.'][]' ?>" data-value="<?php foreach($slideshow[$i] as $key => $item){if($key != 0){echo ',';} echo $item;} ?>"/>
                                </div>
                            </div>
                        <?php endif ?>
                    <?php endforeach ?>
                <?php endif; ?>
            </div>
            <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Save" style="float:right;"/>
            </div>
        </div>
    </form>
</div>
