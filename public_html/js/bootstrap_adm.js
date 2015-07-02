$(function () {
    //####### COSTOMIZAÇÃO DE CSS #########
    $('input[type=checkbox]').addClass('checkbox');
    $('input[type=radio]').addClass('radio');



    //####### AUXILIAR #########
    $('.jSubmit').click(function () {
        $(this).parents('form').attr('action', $(this).attr('href'));
        $(this).parents('form').submit();
        return false;
    });
    $('.jCheckAll').each(function () {
        var checks = $(this).parents('table').find('tr td input[type=checkbox]');
        var $this = $(this);
        $(this).click(function () {
            if ($(this).is(':checked')) {
                checks.attr('checked', true);
                //$(this).parents('tr').after('<tr class="jSelecionarTodos"><th colspan="'+$(this).parents('tr').find('th').size()+'">Selecionar Todos os '+checks.size()+' registros </th></tr>');
            } else {
                checks.attr('checked', false);
                $(this).parents('table').find('.jSelecionarTodos').remove();
            }
        });
        checks.click(function () {
            $(this).parents('table').find('.jSelecionarTodos').remove();
            $this.attr('checked', '');
        });
    });

    $('.jExcluirLinha').click(function () {
        var $this = $(this);

        var r = confirm("Você deseja realmente deletar esta publicação?");
        if (r) {
            $.post($(this).attr('href'), {ajax: true}, function () {
                $this.parents('tr').fadeOut('fast');
            }, 'json');
        }
        return false;
    });
});

function sortItems() {
    var orderElement = null;
    var nextOrderElement = null;
    var prevOrderElement = null;

    var id = null;
    var idNext = null;
    var nexts = null;
    var nextsId = [];
    var idPrev = null;
    var prevsId = [];
    var prevs = null;
    var orderElement = null;
    var positionStart = null;
    var positionEnding = null;

    $('.order').on('mousedown', function () {
        orderElement = $(this);
    });


    $("#SORTABLE").sortable({
        cursor: 'move',
        handle: 'a.order',
        items: '.destaque',
        change: function (event, ui) {
            positionStart = ui.item.index();
        },
        update: function (event, ui) {
            positionEnding = ui.item.index();

            var str = orderElement.parents('tr').attr('class');
            var strArray = str.split('-');
            var strArrayForm = strArray[1].split(' ');

            id = strArrayForm[0];

            if (positionEnding > positionStart) {
                prevs = orderElement.parents('tr').prevAll();
                prevsId = [];
                $.each(prevs, function (index) {
                    var classPrev = $(this).attr('class');
                    var strPrev = classPrev.split('-');
                    var strPrevFinal = strPrev[1].split(' ');
                    prevsId[index] = strPrevFinal[0];
                });

                idNext = '';

                if (!prevsId.length) {
                    var strNext = orderElement.parents('tr').next().attr('class');
                    var strNextArray = strNext.split('-');
                    var strNextArrayForm = strArray[1].split(' ');

                    idNext = strNextArrayForm[0];
                }

                var data = {'id': id, 'idNext': idNext, 'vIdPrev': prevsId, 'type': 'previous'};
            } else {
                nexts = orderElement.parents('tr').nextAll().find('input:checked').parents('tr');
                nextsId = [];
                $.each(nexts, function (index) {
                    var classNext = $(this).attr('class');
                    var strNext = classNext.split('-');
                    var strNextFinal = strNext[1].split(' ');
                    nextsId[index] = strNextFinal[0];
                });

                idPrev = '';

                if (!nextsId.length) {
                    var strPrev = orderElement.parents('tr').prev().attr('class');
                    var strPrevArray = strPrev.split('-');
                    var strPrevArrayForm = strArray[1].split(' ');

                    idPrev = strPrevArrayForm[0];
                }

                var data = {'id': id, 'idPrev': idNext, 'vIdNext': nextsId, 'type': 'next'};

            }

            $.ajax({
                url: '/adm/pagina/sortitems',
                method: 'post',
                dataType: 'json',
                data: data,
                success: function (json) {

                },
                error: function () {
                    console.log('DEU ZICA');
                }
            });
        }
    });
}

function sortItemsInput() {
    var orderElement = null;
    var nextOrderElement = null;
    var prevOrderElement = null;

    var id = null;
    var orderValueOld = null;
    var orderValueNew = null;
    var idNext = null;
    var nexts = null;
    var nextsId = [];
    var idPrev = null;
    var prevsId = [];
    var prevs = null;
    var orderid = null;
    var positionStart = null;
    var positionEnding = null;

    $('.order-number').on('change', function () {
        orderElement = $(this);
        var idAttr = $(this).attr('id');
        var idStr = idAttr.split('-');
        id = idStr[2];
        orderValueNew = $(this).val();
        var dataId = {id: id};

        $.ajax({
            url: '/adm/pagina/getordemajax',
            method: 'get',
            dataType: 'json',
            data: dataId,
            success: function (json) {
                orderValueOld = json.order;


                if (orderValueOld > orderValueNew) {
                    nexts = orderElement.parents('tr').nextAll().find('input:checked').parents('tr');
                    nextsId = [];
                    $.each(nexts, function (index) {
                        var classNext = $(this).attr('class');
                        var strNext = classNext.split('-');
                        var strNextFinal = strNext[1].split(' ');
                        nextsId[index] = strNextFinal[0];
                    });

                    idPrev = '';

                    if (!nextsId.length) {
                        var strPrev = orderElement.parents('tr').prev().attr('class');
                        var strPrevArray = strPrev.split('-');
                        var strPrevArrayForm = strArray[1].split(' ');

                        idPrev = strPrevArrayForm[0];
                    }

                    var data = {'id': id, 'idPrev': idNext, 'vIdNext': nextsId, 'type': 'next'};
                }
                if (orderValueOld < orderValueNew) {

                }
                console.log(data);
                $.ajax({
                    url: '/adm/pagina/sortitems',
                    method: 'post',
                    dataType: 'json',
                    data: data,
                    success: function (json) {

                    },
                    error: function () {
                        console.log('DEU ZICA');
                    }
                });
            }
        });
    });
}

function lockUnlockPage() {
    $(".jLock").click(function () {
        var $this = $(this);
        $.post($(this).attr("href"), {ajax: 1}, function (data) {
            if (data.success) {
                if ($this.is('.bt_lock_on')) {
                    $this.removeClass('bt_lock_on').addClass('bt_lock_off');
                } else {
                    $this.removeClass('bt_lock_off').addClass('bt_lock_on');
                }
            }
        }, 'json');
        return false;
    });
}

function confirmDelete() {
    $('.bt_excluir').on('click', function () {
        if (!(confirm('Você deseja realmente excluir este registro?'))) {
            return false;
        }
    });
}

function activeInactivePage() {
    $(".ativo-inativo input").on('change', function () {
        var str = $(this).parent().attr('id').split('-');
        var id = str[1];
        var checked = $(this).attr('checked');
        var ativo = 1;

        if (typeof checked === "undefined") {
            ativo = 0;
        }

        var data = {id: id, ativo: ativo};

        $.ajax({
            url: '/adm/pagina/ativainativa',
            method: 'post',
            dataType: 'json',
            data: data,
            success: function (json) {
                alert(json.message);
            }
        });

    });
}

function addDestaque(controller) {
    $(".jDestaque").click(function () {
        var valor = $(this).val();
        if ($(this).is(".ativo")) {
            $(this).removeClass("ativo");
            $(this).next().addClass("hide");
            var status = 0;
            $(this).next().val('0');
            $.post("/adm/" + controller + "/adddestaqueordem/ajax/true/", {ID: valor, VALOR: 0}, function () {

            }, "json");
        } else {
            var status = 1;
            $(this).addClass("ativo");
            $(this).next().removeClass("hide");
        }
        $.post("/adm/" + controller + "/adddestaque/ajax/true/ID/" + valor + "/S/" + status, "", function () {

        }, "json");
    });
}

function addDestaqueOrdem(controller){
    $(".jDestaqueOrdem").keyup(function () {
        var str = $(this).attr('id');
        var arrStr = str.split('-');
        var id = arrStr[2];

        if ($(this).val() != 0) {
            $.post("/adm/" + controller + "/adddestaqueordem/ajax/true/", {ID: id, VALOR: $(this).val()}, function (json) {
                var v1 = json.valorAntigo - 1;
                var v2 = json.valor - 1;

                var contentAntigo = $(".padrao tbody tr td .jDestaqueOrdem[value='" + json.valorAntigo + "']");
                var contentNovo = $(".padrao tbody tr td .jDestaqueOrdem[value='" + json.valor + "']");

                var aux = contentAntigo;
                contentAntigo.attr("value", json.valor);
                contentNovo.attr("value", json.valorAntigo);
            }, "json");
        }
    });
}