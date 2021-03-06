$(function() {
    $('#load').css({opacity: 0.6});
    $("#load").ajaxStart(function() {
        $(this).show();
    });
    $("#load").ajaxStop(function() {
        $(this).hide();
    });
});
$(function() {
    $('.jImgFormat').find('img').each(function() {
        var $float = $(this).css('float');
        $(this).css('float', 'none');
        var margin = '';
        switch ($float) {
            case 'left':
                margin = 'margin:2px 10px 2px 0px';
                break;
            case 'right':
                margin = 'margin:2px 0px 2px 10px';
                break;
            default:
                break;
        }
        var url_amp = $(this).attr('src');
        url_amp = url_amp.replace(/-([0-9]*)\./, '.');
        var img = $(this).wrap('<a href="' + url_amp + '" rel="shadowbox" style="float:' + $float + ';' + margin + '" class="imgTexto"/>');
        img.after('<strong style="display:block">' + $(this).attr('alt') + '</strong>');
    });
});

function showPopup() {
    $('.popup').fadeIn('slow');
    var popupMarginLeft = $('.popup-img').width() / 2,
        popupMarginTop = $('.popup-img').height() / 2;

    // console.log(popupMarginLeft);
    // console.log($('.popup-img img').attr('height'));
    $('.popup-content').css({marginLeft: -popupMarginLeft + 'px'/*, marginTop: -popupMarginTop+'px'*/});
    $('.popup-close, .popup').click(function() {
        $('.popup').fadeOut();
    });
    $('.popup-content').click(function() {
        return false;
    });
}

function openModal(){
    var vLocation = window.location.href.split('/');
    if (vLocation.length > 6){
        var idModal = vLocation[6];
        $('#'+idModal).modal('show');
    }
}

function leftRightObras(){
    $(".obras-paginator a.left").on('click', function(){
       $(this).parents('.modal-projetos').find('.close').trigger('click');
        //console.log($(this).parents('.modal-projetos').next().next().html());
       if ($(this).parents('.modal-projetos').prev().prev().html()){
           $(this).parents('.modal-projetos').prev().prev().modal('show');
       }else{
           $(this).parents('.modal-projetos').parent().find('.modal-projetos:last-child').modal('show');
       }
    });

    $(".obras-paginator a.right").on('click', function(){
        $(this).parents('.modal-projetos').find('.close').trigger('click');
        //console.log($(this).parents('.modal-projetos').next().next().html());
        $(this).parents('.modal-projetos').next().next().modal('show');
    });
}