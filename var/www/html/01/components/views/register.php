<?php
use yii\helpers\Url;
?>
<!--Форма Регистрации-->
<?php $errors = Yii::$app->session->getFlash('register_error') ?>

<div class="wrapper modal_reg" <?php if(!empty($errors)){echo 'style="display:block; opacity: 1"';} ?>>
    <div class="reg_form">
        <div class="reg_form_head">РЕГИСТРАЦИЯ11</div>
        <form method="post" id="register-form" class="valid_form" action="<?= Url::toRoute('/site/register') ?>">
            <div class="input_item">
                <div class="input_title">Ваше имя</div>
                <input type="text" class="form_input" placeholder="" name="RegisterForm[username]" data-validetta="required,minLength[2],maxLength[64]">
                <?php if(isset($errors['username'])): ?>
                    <span class="validetta-bubble validetta-bubble--right" style="top: 23px; left: 439px;"><?php echo $errors['username'][0] ?><br></span>
                <?php endif ?>
            </div>
            <div class="input_item">
                <div class="input_title">E-mail</div>
                <input type="text" class="form_input" placeholder="" name="RegisterForm[email]" data-validetta="required,email">
                <?php if(isset($errors['email'])): ?>
                    <span class="validetta-bubble validetta-bubble--right" style="top: 23px; left: 439px;"><?php echo $errors['email'][0] ?><br></span>
                <?php endif ?>
            </div>
            <div class="input_item">
                <div class="input_title">Пароль</div>
                <input type="password" class="form_input" placeholder="" name="RegisterForm[password]" data-validetta="required,minLength[6],maxLength[128]">
                <?php if(isset($errors['password'])): ?>
                    <span class="validetta-bubble validetta-bubble--right" style="top: 23px; left: 439px;"><?php echo $errors['password'][0] ?><br></span>
                <?php endif ?>
            </div>
            <button class="enter_button enter_button_redirect"></button>
            <button type="submit" class="reg_button"></button>
            <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />
        </form>
        <a href="#" class="close_btn"></a>
    </div>
</div>

<div class="wrapper" <?php if(Yii::$app->session->hasFlash('register_good')){echo 'style="display:block; opacity: 1"';} ?>>
    <div class="forgot_pass2_form">
        <div class="forgot_pass2_form_head">ЗАВЕРШЕНИЕ РЕГИСТРАЦИИ</div>
        <div class="pass_text">Проверьте электронную почту. На указанный вами<br>адрес отправлено сообщение.<br>Письмо содержит ссылку для активации<br>вашего аккаунта.</div>
        <a href="#" class="close_btn"></a>
    </div>
</div>

<div class="wrapper" <?php if(Yii::$app->session->hasFlash('activate_account_error')){echo 'style="display:block; opacity: 1"';} ?>>
    <div class="forgot_pass2_form">
        <div class="forgot_pass2_form_head">ЗАВЕРШЕНИЕ РЕГИСТРАЦИИ</div>
        <div class="pass_text">Ошибка при активании аккаунта.</div>
            <a href="#" class="close_btn"></a>
    </div>
</div>

<div class="wrapper" <?php if(Yii::$app->session->hasFlash('activate_account_good')){echo 'style="display:block; opacity: 1"';} ?>>
    <div class="forgot_pass2_form">
        <div class="forgot_pass2_form_head">ЗАВЕРШЕНИЕ РЕГИСТРАЦИИ</div>
        <div class="pass_text">Аккаунт успешно активирован.<br>Теперь вы можете авторизвоваться,<br>используя ваш Email и пароль.</div>
            <a href="#" class="close_btn"></a>
    </div>
</div>
