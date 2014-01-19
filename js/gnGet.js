$.fn.gnGet = function (obj) {
    var obj = $.extend({
        show:4,  // ���������� �������, ������� ������������ ����������, � ����� ������ �� ��� ���������
        add:8,   // ���������� �������, ������������� � ������
        category:1,
        gns:null,
        manual:null,
        summa:null,
        skidka:null,
        skidka_sum:0
    }, obj);

    var t = $(this);
    var pix = 21; // ������ ������ ������� � ��������
    var gn_show_current = obj.show; // ���������� ������������ �������

    var html = "<DIV id=gnGet>" +
        "<TABLE cellpadding=0 cellspacing=0>" +
        "<TR><TD><DIV id=dopMenu>" +
        "<A class=link val=4><I></I><B></B><DIV>�����</DIV><B></B><I></I></A>" +
        "<A class=link val=13><I></I><B></B><DIV>3 ������</DIV><B></B><I></I></A>" +
        "<A class=link val=26><I></I><B></B><DIV>�������</DIV><B></B><I></I></A>" +
        "<A class=link val=52><I></I><B></B><DIV>���</DIV><B></B><I></I></A>" +
        "</DIV>" +
        "<TD><input type=hidden id=dopDef>" +
        "</TABLE>" +

        "<TABLE cellpadding=0 cellspacing=0>" +
        "<TR><TD id=selCount><TD><DIV id=gns></DIV><DIV id=darr>&darr; &darr; &darr;</DIV>" +
        "</TABLE>" +
        "</DIV>";
    t.html(html);

    var gnGet = $("#gnGet");                 // �������� �����
    var gns = gnGet.find("#gns");            // ������ �������
    var darr = gnGet.find("#darr");          // ������ �������������� ������
    var dopMenuA = gnGet.find("#dopMenu A"); // ������ ���� � ���������
    var selCount = gnGet.find("#selCount");  // ���������� ��������� �������
    var globalDop = 0;
    var cena = 0;                            //

    switch (parseInt(obj.category)) {
        case 1:
            var dop_spisok = [{uid:'0', title:'���. �������� �� ������'}];
            for (var n = 0; n < G.ob_dop_spisok.length; n++) {
                var sp = G.ob_dop_spisok[n];
                dop_spisok.push(sp);
            }
            var dopDef = gnGet.find("#dopDef").linkMenu({
                head:'���������� ����...',
                spisok:dop_spisok,
                func:function (id) {
                    globalDop = id;
                    gn_action_active(function (sp) {
                        sp.linkMenu.val(id);
                        sp.dop = id;
                        cenaSet();
                    });
                }
            });
            break;
        case 2:
            var dop_spisok = [{uid:'0', title:'������ �� �������'}];
            for (var n = 0; n < G.polosa_spisok.length; n++) {
                var sp = G.polosa_spisok[n];
                dop_spisok.push(sp);
            }
            var dopDef = gnGet.find("#dopDef").linkMenu({
                head:'���������� ����...',
                spisok:dop_spisok,
                func:function (id) {
                    globalDop = id;
                    gn_action_active(function (sp) {
                        sp.linkMenu.val(id);
                        sp.dop = id;
                        cenaSet();
                    });
                }
            });
            break;
    }

    gns_clear();
    // ��������� ��������� ������� ��� ��������������
    if (obj.gns) {
        for (var k in obj.gns) {
            G.gn[k].sel = 1;
            G.gn[k].prev = 1;
            G.gn[k].dop = obj.gns[k].dop;
            G.gn[k].cena = obj.gns[k].summa;
        }
        gn_show_current += (k ? k - G.gn.first_active + 1 : 0);
    }
    gns_print();
    sel_calc();

    // ���������� �������� � ��������� �������
    function gn_action_active(func) {
        for(var n = G.gn.first_active; n <= G.gn.last_active; n++) {
            if (n > G.gn.last_active) break;
            var sp = G.gn[n];
            if (!sp) continue; // ���� ����� ��������, ����� ��� ��������
            if (sp.sel == 1) func(sp);
        }
    }

    // �������������� ������
    darr.on('click', function () { gn_show_current += obj.add; gns_print(); });

    // ����� ������� �� �����, 3 ������, ������� � ��� ������� �������
    dopMenuA.click(function () {
        var cl = $(this).attr('class');
        gns_clear();
        if (cl == 'link') {
            $(this).attr('class', 'linkSel'); // ��������� ���������� �������
            val = $(this).attr('val');
            gn_show_current += val * 1;       // ���������� �������� ��� ������ ���������� �������
            var begin = G.gn.first_active;
            var end = G.gn.first_active + gn_show_current;
            for(var n = begin; n < end; n++) {
                if (n > G.gn.last_active) break;
                var sp = G.gn[n];
                if (!sp) { end++; continue; } // ���� ����� ��������, ����� �� ���������
                sp.sel = 1;                   // ������� ������ ������ ��� ����������
                val--;
                if (val == 0) break;
            }
        }
        gns_print();
        sel_calc();
    });

    // ����� ������ �������
    function gns_print() {
        var html = '';
        var begin = G.gn.first_active;
        var end = G.gn.first_active + gn_show_current;
        for(var n = begin; n < end; n++) {
            if (n > G.gn.last_active) break;
            var sp = G.gn[n];
            if (!sp) { end++; continue; } // ���� ����� ��������, ����� �� ���������
            html += "<TABLE cellpadding=0 cellspacing=0>" +
                "<TR><TD>" +
                    "<TABLE cellpadding=0 cellspacing=0 class='tab" + (sp.sel == 1 ? " tabsel" : '') + (sp.prev == 1 ? " prev" : '') + "' val=" + n + ">" +
                    "<TR><TD class=td><B>" + sp.week + "</B><SPAN class=g>(" + n + ")</SPAN>" +
                    "<TD class=td align=right><SPAN class=g>�����</SPAN> " + sp.txt +
                    "<TD class=cena id=cena" + n + ">" +
                    "</TABLE>" +
                "<TD class=vdop><input type=hidden id=vdop" + n + " value=" + sp.dop + ">" +
            "</TABLE>";
        }
        gns.html(html);
        if (end > G.gn.last_active) {
            gn_show_current -= end - G.gn.last_active - 1;
            darr.hide();
        } else darr.show();
        var h = gn_show_current * pix;
        gns.animate({height:h + 'px'}, 300, frameBodyHeightSet);
        gns.find(".tab").click(gn_set);
        gn_action_active(linkMenu_create);
        cenaSet();
    } // end of gns_print()

    function linkMenu_create(sp) {
        if (obj.category < 3) {
            sp.linkMenu = $("#vdop" + sp.uid).linkMenu({
                spisok:dop_spisok,
                func:function (id) { sp.dop = id; cenaSet(); }
            }).o;
        }
    }

    // ������� ������ �������
    function gns_clear() {
        globalDop = 0;
        gn_show_current = obj.show; // ��������� ������� ������� �� ���������
        dopMenuA.attr('class', 'link');
        for(var n = G.gn.first_active; n <= G.gn.last_active; n++) {
            if (n > G.gn.last_active) break;
            var sp = G.gn[n];
            if (!sp) continue; // ���� ����� ��������, ����� ��� ��������
            sp.prev = 0;
            sp.sel = 0;
            sp.dop = 0;
            sp.uid = n;
            sp.cena = 0;
        }
    } // end of gns_clear()

    // �������� �� ������� �� ����� ������
    function gn_set() {
        dopMenuA.attr('class', 'link');
        var tab = $(this).hasClass('tabsel');
        var n = $(this).attr('val');
        var sp = G.gn[n];
        sp.sel = tab ? 0 : 1;
        if (sp.prev == 1) $(this).removeClass('prev');
        sp.prev = 0;
        $(this)[(tab ? 'remove': 'add') + 'Class']('tabsel');
        sel_calc();
        if (tab == 0) {
            linkMenu_create(sp);
        } else { $("#linkMenu_vdop" + n).remove(); }
        cenaSet();
    } // end of gn_set()

    // ����� ���������� ��������� �������
    function sel_calc() {
        var count = 0;
        gn_action_active(function () { count++; })
        if (count > 0) {
            var html = "������" + G.end(count, ['', '�']) + " " +
                        count + " �����" + G.end(count, ['', 'a', '��']) +
                        "<A>��������</A>";
            selCount.html(html);
            selCount.find('A:first').click(function () { gns_clear(); gns_print(); });
        } else selCount.html('');
    } // end of sel_calc()

    // ��������� ���� � ��������� ������
    function cenaSet() {
        var manual = obj.manual.val();
        switch (parseInt(obj.category)) {
            case 1:
                var four = 0;
                if (manual == 1) {
                    var count = 0;
                    gn_action_active(function (sp) {
                        if (sp.prev != 1) {
                            four++;
                            if (four == 4) {
                                four = 0;
                            } else count++;
                        }
                    });
                    four = 0;
                    var sum = Math.round((parseInt(obj.summa.val()) / count) * 100) / 100;
                }
                gn_action_active(function (sp) {
                    if (sp.prev != 1) {
                        four++;
                        if (four == 4) {
                            four = 0;
                            sp.cena = 0;
                        } else if (manual == 0) {
                            sp.cena = cena == 0 ? 0 : cena + G.ob_dop_cena_ass[sp.dop];
                        } else {
                            sp.cena = cena == 0 ? 0 : sum;
                        }
                    }
                    gnGet.find("#cena" + sp.uid).html(sp.cena);
                });
                break;
            case 2:
                if (manual == 1) {
                    var count = 0;
                    gn_action_active(function (sp) { if (sp.dop > 0 && sp.prev != 1) count++; });
                    var sum = Math.round((parseInt(obj.summa.val()) / count) * 100) / 100;
                }
                var skidka = obj.skidka.val();
                obj.skidka_sum = 0;
                gn_action_active(function (sp) {
                    if (sp.prev != 1) {
                        sp.cena = 0;
                        if (sp.dop > 0) {
                            if (manual == 0) {
                                sp.cena = cena * G.polosa_cena_ass[sp.dop];
                                var sk = sp.cena * skidka / 100;
                                sp.cena -= sk;
                                obj.skidka_sum += sk;
                            } else { sp.cena = sum; }
                        }
                    }
                    gnGet.find("#cena" + sp.uid).html(Math.round(sp.cena * 100) / 100);
                });
                obj.skidka_sum = Math.round(obj.skidka_sum * 100) / 100;
                break;
            default:
                var count = 0;
                gn_action_active(function (sp) { if (sp.prev != 1) count++; });
                var sum = Math.round(obj.summa.val() / count * 1000000) / 1000000;
                gn_action_active(function (sp) {
                    if (sp.prev != 1) sp.cena = sum;
                    gnGet.find("#cena" + sp.uid).html(Math.round(sp.cena * 100) / 100);
                });
                break;
        }
        $("#sumSkidka").html('');
        $("#skidka_sum").val(0);
        if (manual == 0) {
            obj.summa.val(sumGet());
            if (obj.category == 2 && obj.skidka.val() > 0) {
                $("#sumSkidka").html("����� ������: <B>" + obj.skidka_sum + "</B> ���.");
                $("#skidka_sum").val(obj.skidka_sum);
            }
        }
    };

    function sumGet() {
        sum = 0;
        gn_action_active(function (sp) {
            if (sp.prev != 1) sum += sp.cena;
        });
        return Math.round(sum * 100) / 100;
    }

    t.cenaSet = function (c) {
        if (c != undefined) cena = c;
        cenaSet();
    };
    t.gnSelected = function () {
        var spisok = [];
        var no_polosa = 0; // ��������, ��� �� ������ �������
        gn_action_active(function (sp) {
            if (obj.category == 2 && sp.dop == 0) no_polosa = 1;
            spisok.push(sp.uid + ":" + sp.cena + ":" + sp.dop);
        });
        if (no_polosa == 1) return 'no_polosa';
        else return spisok.join(',');
    };
    return t;
};
