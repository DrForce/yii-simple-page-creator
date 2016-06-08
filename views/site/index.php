<?php
use yii\helpers\Url;
use yii\widgets\LinkPager;
/* @var $pages */
/* @var $list */
/* @var $categories */
?>
<div class="left_side col-sm-5">
    <div class="head">
        <a href="<?= Url::toRoute('/site/new-page') ?>" class="btn btn-primary new_page">new page</a>

        <hr />
        <h3>Pages</h3>
    </div>
    <div class="content">
        <p>Showing <b><?php echo ($pages->getPage()*20+1)."-".($pages->getPage()*20+count($list))?></b> of <b><?php echo $pages->totalCount ?></b> items.</p>
        <table class="table">
            <thead>
            <tr role="row">
                <th></th>
                <th>Name</th>
                <th>Type</th>
                <th>Published</th>
                <th>Publish at</th>
                <th>Author</th>
            </tr>
            </thead>
            <tbody>
            <tr class="filter">
                <td></td>
                <td width="25%">
                    <div class="btn-group">
                        <input class="form-control filter" type="text" data-name="name" value="<?php if(Yii::$app->session->has('name')){echo Yii::$app->session->get('name');} ?>"/>
                        <span class="searchclear glyphicon glyphicon-remove"></span>
                    </div>
                </td>
                <td width="20%">
                    <select class="form-control filter" data-name="category">
                        <option></option>
                        <?php foreach($categories as $item): ?>
                            <option value="<?php echo $item['id'] ?>" <?php if(Yii::$app->session->has('category') && Yii::$app->session->get('category') == $item['id']){echo 'selected';} ?>><?php echo $item['name'] ?></option>
                        <?php endforeach ?>
                    </select>
                </td>
                <td width="10%">
                    <select class="form-control filter" data-name="published">
                        <option></option>
                        <option value="2" <?php if(Yii::$app->session->has('published') && Yii::$app->session->get('published') == 2){echo 'selected';} ?>>Yes</option>
                        <option value="1" <?php if(Yii::$app->session->has('published') && Yii::$app->session->get('published') == 1){echo 'selected';} ?>>No</option>
                    </select>
                </td>
                <td>
                    <div class="btn-group">
                        <input class="form-control datepicker filter" type="text" data-name="publishdate" placeholder="<?php echo date('d.m.Y'); ?>" value="<?php if(Yii::$app->session->has('publishdate') && Yii::$app->session->get('publishdate') != ''){echo Yii::$app->session->get('publishdate');} ?>"/>
                        <span class="searchclear glyphicon glyphicon-remove"></span>
                    </div>
                </td>
                <td>
                    <div class="btn-group">
                        <input class="form-control filter" type="text" data-name="author"  value="<?php if(Yii::$app->session->has('author') && Yii::$app->session->get('author') != ''){echo Yii::$app->session->get('author');} ?>"/>
                        <span class="searchclear glyphicon glyphicon-remove"></span>
                    </div>
                </td>
            </tr>
            <?php foreach($list as $page): ?>
                <tr>
                    <td><a href="<?= Url::toRoute(['/site/edit-page/','id' => $page['id']]) ?>"" class="btn btn-primary"><span class="glyphicon glyphicon-pencil"></span></a></td>
                    <td><?php echo $page['name']; ?></td>
                    <td><?php echo $page['category']; ?></td>
                    <td><?php if($page['published_at'] != 0): ?>
                            <span class="label label-success">Yes</span>
                        <?php else: ?>
                            <span class="label label-primary">No</span>
                        <?php endif ?>
                    </td>
                    <td><?php if($page['published_at'] != 0){echo date('d.m.Y H:i',$page['published_at']);}else{echo '-';} ?></td>
                    <td><?php echo $page['author'] ?></td>
                </tr>
            <?php endforeach ?>
            </tbody>
        </table>
        <?php
            echo LinkPager::widget([
                'pagination' => $pages,
            ])
        ?>
    </div>
</div>