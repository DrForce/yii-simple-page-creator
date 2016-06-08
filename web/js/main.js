/*
 * pages_url содержит ссылку на адрес для пагинации
 * blocks_count - кол-во уже имеющихся динамических блоков
 * action_name - название контроллера-обработчика
 */
var pages_url = "http://m99945g5.bget.ru/";
var blocks_count = $(".content-element").size();
var action_name = $("#action-name").val();

/*
 * подключение плаигнов и прочих картинок
 */
$(document).ready(function() {
    $('#tags').tagsInput();
    $('.datepicker').datepicker({format:'dd.mm.yyyy'}).on('changeDate', function(){
        $('.datepicker').change();
    });;
    $("#publish").bootstrapSwitch({
        onText: 'public',
        offText: 'unpublic'
    });
    var thumbnail = $("#thumbnail").attr('data-value');
    if(thumbnail != null && thumbnail != ''){
        $("#thumbnail").fileinput({
            initialPreview: [
                "<img style='height:160px' src='/"+thumbnail+"'>"
            ]
        });
    }
    $('#thumbnail').on('fileclear', function(event) {
        $.get('/site/ajax-delete-session', { target: "thumbnail"});
    });
    var image = $("#image").attr('data-value');
    if(image != null && image != ''){
        $("#image").fileinput({
            initialPreview: [
                "<img style='height:160px' src='/"+image+"'>"
            ]
        });
    }
    $('#image').on('fileclear', function(event) {
        $.get('/site/ajax-delete-session', { target: "image"});
    });
    var thumbnail_fb = $("#thumbnail_fb").attr('data-value');
    if(thumbnail_fb != null && thumbnail_fb != ''){
        $("#thumbnail_fb").fileinput({
            initialPreview: [
                "<img style='height:160px' src='/"+thumbnail_fb+"'>"
            ]
        });
    }
    $('#thumbnail_fb').on('fileclear', function(event) {
        $.get('/site/ajax-delete-session', { target: "thumbnail_fb"});
    });
    $('.slideshow-input').each(function(){
        var images = $(this).attr('data-value');
        var path_array = images.split(',');
        var slideshow = [];
        for(var i = 0; i < path_array.length; i++){
            slideshow.push("<img style='height:160px' src='/"+path_array[i]+"'>");
        }
        console.log(images);
        $(this).fileinput({
            initialPreview: slideshow
        });
    });
});
$('.add-content').click(function(){
    var content = '';
    blocks_count++;
    if($('.content-tile').val() == 'Page Title'){
        content = '<div class="col-sm-12 content-element">' +
            '<input type="hidden" name="'+action_name+'[blocks]['+blocks_count+'][block_type]" value="page_title"/>' +
            '<p class="content-head">Page Title<span class="glyphicon glyphicon-trash delete-block"></span></p>' +
            '<div class="form-group">' +
            '<input class="form-control" type="text" name="'+action_name+'[blocks]['+blocks_count+'][name]" placeholder="Name"/>' +
            '</div>' +
            '<div class="form-group">' +
            '<input class="form-control" type="text" name="'+action_name+'[blocks]['+blocks_count+'][title]" placeholder="Title"/>' +
            '</div>' +
            '</div>';
    }else if($('.content-tile').val() == 'Product details'){
        content = '<div class="col-sm-12 content-element">' +
            '<input type="hidden" name="'+action_name+'[blocks]['+blocks_count+'][block_type]" value="product_details"/>' +
            '<p class="content-head">Product details<span class="glyphicon glyphicon-trash delete-block"></span></p>' +
            '<div class="form-group">' +
            '<input class="form-control" type="text" name="'+action_name+'[blocks]['+blocks_count+'][title]" placeholder="Title"/>' +
            '</div>' +
            '<div class="form-group">' +
            '<textarea class="form-control" rows="5" name="'+action_name+'[blocks]['+blocks_count+'][description]" placeholder="Description"></textarea>' +
            '</div>' +
            '<div class="form-group">' +
            '<input class="form-control" type="text" name="'+action_name+'[blocks]['+blocks_count+'][url]" placeholder="Web address"/>' +
            '</div>' +
            '<div class="form-group col-sm-6" style="padding-left: 0;">' +
            '<input class="form-control" type="text" name="'+action_name+'[blocks]['+blocks_count+'][price]" placeholder="Price"/>' +
            '</div>' +
            '<div class="form-group col-sm-6" style="padding-right: 0;">' +
            '<input class="form-control" type="text" name="'+action_name+'[blocks]['+blocks_count+'][currency]" placeholder="Currency"/>' +
            '</div>' +
            '</div>';
    }else if($('.content-tile').val() == 'Slideshow'){
        content = '<div class="col-sm-12 content-element">' +
            '<input type="hidden" class="slideshow-input" name="'+action_name+'[blocks]['+blocks_count+'][block_type]" value="slideshow"/>' +
            '<p class="content-head">Slideshow<span class="glyphicon glyphicon-trash delete-block"></span></p>' +
            '<div class="form-group">' +
            '<input class="form-control" id="slideshow-'+blocks_count+'" multiple data-show-upload="false" type="file" name="slideshow['+blocks_count+'][]" placeholder="Name"/>' +
            '</div>' +
            '</div>';
    }
    if(content != ''){
        $('.page-content').append(content);
        if($('.content-tile').val() == 'Slideshow'){
            $("#slideshow-"+blocks_count).fileinput();
        }
    }
    $('.content-tile :first-child').attr('selected','selected');
});
/*
 * вызываем ajax-функцию для установки фильтра(ов) и уходим на ссылку для просмотра страниц
 * так поступил потому что если фильтр будет установлен например на 2-ой странице - мы должны вернуться к первой при установке нового фильтра
 */
$('.filter').change(function(){
    $.get('/site/ajax-set-filter', { name: $(this).attr('data-name'), value: $(this).val() });
    window.location.href = pages_url;
});
$(".searchclear").click(function () {
    $(this).prev().val('').change();
    $(this).hide();
});
$(".page-content").on("click",".delete-block",function (){
   $(this).parents('.content-element').remove();
});