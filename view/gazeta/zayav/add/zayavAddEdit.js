var zayav = {
    rubrika:null,
    gn:null,     // ����������� � �������� �����
    sending:0    // ������ ���� ���������� �� �������� ��� ���
};


// ������� �����������, ���� ������ �����������
function zayavAdd() {
    $("#category").vkSel({
        width:120,
        spisok: G.category_spisok,
        func:function (id) {
            $("#skidka_tab").hide();
            zayav.skidka.val(0);

            $("#manual_tab").hide();
            $("#summa_manual").val(0);
            $("#summa").css('background-color','#FFF').attr('readonly',true);
            // ������� ������ ������� ������
            gnReload(id);

            switch (id) {
                case '1':
                    $("#for_ob").show();
                    $("#txt").focus();
                    $("#manual_tab").show();
                    break;
                case '2':
                    $("#for_rek").show();
                    $("#size_x").focus();
                    $("#manual_tab").show();
                    $("#skidka_tab").show();
                    break;
                default:
                    $("#summa_manual").val(1);
                    $("#summa").css('background-color','#FF8').removeAttr('readonly');
                    break;
            }
        }
    });

    $("#client_id").clientSel();

    rubrikaSet();

    // ��������� � ��������� ������ ��� ��������� �� ������
    $("#vkSel_rubrika .add:first").vkHint({
        msg:"������� � ��������� ������",
        indent:60,
        top:-58,
        left:-59,
        correct:0
    });

    $("#txt").autosize({callback:frameBodyHeightSet}).focus().keyup(calcSummaOb);

    $("#size_x").keyup(calcSummaRek);
    $("#size_y").keyup(calcSummaRek);

    fotoUpload();

    zayav.skidka = $("#skidka").vkSel({
        width:60,
        title0:'���',
        spisok:G.skidka_spisok,
        func:function () { zayav.gn.cenaSet(); }
    }).o;

    gnReload(1);

    moneyCreate();

    $("#note").autosize({callback:frameBodyHeightSet});
} // end of zayavAdd()

function zayavEdit(category, client, gns) {
    if (client == 0) $("#client_id").clientSel();

    // ����� �����������
    var foto_link = $("#foto_link").val();
    if (foto_link) {
        $("#foto").html("<img src=" + foto_link + "s.jpg><div class=img_del />");
        $("#foto .img_del").click(fotoUpload);
        $("#foto img").click(function () {
            G.fotoView({spisok:[{link:foto_link}]});
        });
    } else fotoUpload();

    gnReload(category, gns);
    moneyCreate();
    switch (category) {
        case 1:
            rubrikaSet();
            podrubrikaSet(zayav.rubrika.val());
            $("#txt").autosize({callback:frameBodyHeightSet}).focus().keyup(calcSummaOb);
            calcSummaOb();
            break;
        case 2:
            $("#size_x").keyup(calcSummaRek);
            $("#size_y").keyup(calcSummaRek);
            $("#skidka_tab").show();
            zayav.skidka = $("#skidka").vkSel({
                width:60,
                title0:'���',
                spisok:G.skidka_spisok,
                func:function () { zayav.gn.cenaSet(); }
            }).o;
            zayav.gn.cenaSet($("#kv_sm").val());
            break;
        default:
            $("#manual_tab").hide();
            $("#summa_manual").val(1);
            $("#summa").css('background-color','#FF8').removeAttr('readonly');
            break;
    }
} // end of zayavEdit()






function moneyCreate() {
    $("#summa").keyup(function(){
        if(!G.reg_sum.test($(this).val()))
            $(this).vkHint({
                msg:"<SPAN class=red>�� ��������� ������� �����.<BR>����������� ����� � ����� ��� �����.</SPAN>",
                remove:1,
                indent:40,
                show:1,
                top:-70,
                left:0
            });
        else {
            $(this).prev().remove('.hint');
            zayav.gn.cenaSet();
        }
    });
} // end of moneyCreate()


