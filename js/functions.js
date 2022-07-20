function isObjectEmpty(obj) {
    return Object.keys(obj).length === 0;
}


function equipConnectionAssoc(equip_id)
{
    console.log(equip_id);
    $.ajax({ method: "GET", url: "webservice.php", data: { 'equip_connection_assoc': '1', 'equip_id': equip_id, 'type': $("#con").html()}})
        .done(function( data )
        {

            var result = $.parseJSON(data);
            $("#equip_conn_assoc").html("<br>"+result['msg']);
            $("#equip_assoc_not").val(result['eq_check']);

        });
}

function checkbox_equip_rec(equip_des, type)
{
    var prop_id = $("#prop_id").val();
    var con_id = $("#con_id").val();
    if (equip_des.checked)
    {
        if(type == "GPON")
        {
            $("#models").attr("disabled", true);
            $("#fsan").attr("disabled", true);
            $("#olt_id").attr("disabled", true);
            $("#pons").attr("disabled", true);

            // Colocar os equipamentos Inicial do tipo de conexao GPON




        }

        else if(type == "FWA")
        {
            $("#fsan").attr("disabled", true);
            $("#models").attr("disabled", true);
            $("#antenna").attr("disabled", true);

            // Colocar os equipamentos Inicial do tipo de conexao GPON
        }

        $("#equip_conn_assoc").html('');
        $("#equip_assoc_not").val(0);
        putInitialEquipPropCon(prop_id, con_id, type);

    }
    else
    {
        if($("#con").html() == "GPON")
        {
            $("#models").attr("disabled", false);
            $("#fsan").attr("disabled", false);
            $("#olt_id").attr("disabled", false);
            $("#pons").attr("disabled", false);
        }
        if($("#con").html() == "FWA")
        {
            $("#models").attr("disabled", false);
            $("#fsan").attr("disabled", false);
            $("#antenna").attr("disabled", false);
        }

    }
}

function putInitialEquipPropCon(prop_id, con_id, type)
{
    var i=0;
    $.ajax({ method: "GET", url: "webservice.php", data: { 'initial_equip_con_prop': '1', 'type': type, 'prop_id': prop_id, 'con_id': con_id}})
        .done(function( data )
        {
            console.log(data);
            var result = $.parseJSON(data);
            $("#fsan").val(result['equip_id']);


            if(type == "GPON")
            {
                //cpe_model
                $("#models").val(result['cpe_model']);
                // olt
                $("#olt_id").val(result['olt']);
                // pons

                var html = '';
                $.each( result['pon'], function( key, value )
                {
                    var val = value['card']+"-"+value['pon'];
                    var text =   value['card']+"-"+value['pon']+" - "+value['name'];

                    html += '<option value="' + val + '">' + text + '</option>';

                });

                $('#pons').html(html);




            }
            else if(type == "FWA")
            {
                //cpe_model
                $("#models").val(result['model_fwa']);

                $("#antenna").val(result['antenna']);
            }
        });


}

function con_prop_rec(prop_id)
{
    var checked = $("#disabled_prop_services:checked").length;
    var con_type = $("#con_type_rec").val();
    if(checked == 1)
    {

        var text_select_dis_ser = $("#refe_rec_7 option:selected").text().trim();
        const myArray = text_select_dis_ser.split("Services:");

        var services = myArray[1];
        // Serviços das Conexoes Desativados
        $.ajax({ method: "GET", url: "webservice.php", data: { 'con_prop_serv_des': '1', 'con_type': con_type, 'prop_id': prop_id, 'services': services}})
            .done(function( data )
            {
                var result = $.parseJSON(data);
                if(result['conn_id'] != null) {
                    var result = $.parseJSON(data);
                    $("#conn_id_des").val(result['conn_id']);
                    $("#conn_assoc_prop_status_7").html('<input type=hidden name=rec_assoc value=1>');
                }
                else if(result['conn_id'] == null)
                {
                    $("#conn_assoc_prop_status_7").html('<font color=red>Cannot exists connections on property number '+prop_id+' to make a reconnection</font><input type=hidden name=rec_assoc value=0>');
                }
            });
    }
    else
    {
        // Listar as Propriedades das conxoes deste tipo de conexao
        $.ajax({ method: "GET", url: "webservice.php", data: { 'con_prop_rec': '1', 'prop_id': prop_id, 'con_type': con_type}})
            .done(function( data )
            {
                var result = $.parseJSON(data);
                if(result['con_assoc'] <= 0)
                {
                    $("#conn_assoc_prop_status_7").html('<font color=red>Cannot associate the property on connections</font><input type=hidden name=rec_assoc value=0>');
                }
                else
                {
                    $("#conn_assoc_prop_status_7").html('<input type=hidden name=rec_assoc value=1>');
                }
                //conn_assoc_prop_status_7
                //prop_conn_servicos_des

            });
    }

}

function chg_type_conn_not_check(con_type)
{
    $.ajax({ method: "GET", url: "webservice.php", data: { 'prop_conn_type': '1', 'con_type': con_type}})
        .done(function( data )
        {
            console.log(data);
            var refe_rec_7=document.getElementById("refe_rec_7");
            if(data != null)
            {
                var result = $.parseJSON(data);
                var prop_id_con = result['prop_conn_type'][0][1];
                //console.log(result['prop_conn_type'][0][1]);
                refe_rec_7.innerHTML = "";
                var refe_rec_7_b;
                $.each( result['prop_conn_type'], function( key, value )
                {
                    refe_rec_7_b = new Option(value[0], value[1]);
                    refe_rec_7.options.add(refe_rec_7_b);
                });
                con_prop_rec(prop_id_con);
            }
            else
            {
                refe_rec_7.innerHTML = "";
                var refe_rec_7_b;
                refe_rec_7_b = new Option("Nao tem propriedade com esta conexão", "0");
                refe_rec_7.options.add(refe_rec_7_b);
            }
        });


}


function chg_type_conn_check(con_type)
{
    $.ajax({ method: "GET", url: "webservice.php", data: { 'prop_conn_type_serv_des': '1', 'con_type': con_type}})
        .done(function( data )
        {
            console.log(data);
            var refe_rec_7=document.getElementById("refe_rec_7");
            if(data != null)
            {
                var result = $.parseJSON(data);
                console.log(result);
                if(result['prop_conn_type'] != null && result['prop_conn_type_not_bel'] == null)
                {
                    var prop_id_con = result['prop_conn_type'][0][1];
                    refe_rec_7.innerHTML = "";
                    var refe_rec_7_b;
                    $.each( result['prop_conn_type'], function( key, value )
                    {
                        refe_rec_7_b = new Option(value[0], value[1]);
                        refe_rec_7.options.add(refe_rec_7_b);
                        $("#conn_id_des").val(value[2]);
                        //$("#serv_id_des").val(value[3]);
                    });
                    con_prop_rec(prop_id_con);
                    $("#conn_assoc_prop_status_7").html('<input type=hidden name=rec_assoc value=1>');
                }
                else if(result['prop_conn_type'] == null && result['prop_conn_type_not_bel'] != null)
                {
                    refe_rec_7.innerHTML = "";
                    var refe_rec_7_b;
                    refe_rec_7_b = new Option("", "0");
                    refe_rec_7.options.add(refe_rec_7_b);
                    /*$.each( result['prop_conn_type_not_bel'], function( key, value )
                    {
                        refe_rec_7_b = new Option(value[0], value[1]);
                        refe_rec_7.options.add(refe_rec_7_b);
                        $("#conn_id_des").val('');
                        //$("#serv_id_des").val('');
                    });*/
                    $("#conn_assoc_prop_status_7").html('<font color=red>Cannot associate the property to make a reconnection</font><input type=hidden name=rec_assoc value=0>');

                }




            }
        });
}

function change_type_connection(con_type)
{
    var checked = $("#disabled_prop_services:checked").length;
    var prop_id = $("#refe_rec_7").val();
    if(checked == 1)
    {
        // Propriedades dos servicos desativados do tipo de conexao coreespondente

        chg_type_conn_check(con_type);

    }
    else
    {
        chg_type_conn_not_check(con_type);

    }
    //console.log(prop_id);

}


function changePropServicesDisabled(prop_services)
{
    var text_des_ser = '';
    if (prop_services.checked)
    {
        text_des_ser += '<input type=hidden id=conn_id_des name=conn_id_des><br>';
        $("#prop_conn_servicos_des").html(text_des_ser);
        // Propriedades dos servicos desativados
        chg_type_conn_check($("#con_type_rec").val());
    }
    else
    {
        text_des_ser += '';
        $("#prop_conn_servicos_des").html(text_des_ser);
        // Propriedades do tipo de conxao coreespondente
        chg_type_conn_not_check($("#con_type_rec").val());
    }
}

// Actualizar a reconnection do tipo de conexao "GPON"
function updatepon_reconn(olt)
{
    $.ajax({ method: "GET", url: "webservice.php", data: { 'ponsbyolt': olt}})
        .done(function( data )
        {
            var result = $.parseJSON(data);
            var pona=document.getElementById("pon_id_reconn");
            pona.innerHTML = "";
            var ponb;
            $.each( result, function( key, value )
            {
                ponb = new Option(value[0]+' - '+value[1], value[0]);
                pona.options.add(ponb);
            });
        });
}

// Fazer uma nova conexao caso que a propriedade nao tem conexoes
function new_con_prop(prop_id)
{
    var loca_conn = '?props=1&conadd='+prop_id;

    location.replace(loca_conn);
}

function rec_prop_subs(prop_id)
{
    console.log(prop_id);
    $.ajax({ method: "GET", url: "webservice.php", data: { 'prop_id_owner': '1', 'prop_id': prop_id}})
        .done(function( data )
        {
            console.log(data);
            var result = $.parseJSON(data);
            var id_cust = result['cust_prop'][0]['id'];

            $("#owner_rec").val(id_cust);


        });
}

/*function owner_prop(owner_id)
{
    $.ajax({ method: "GET", url: "webservice.php", data: { 'owner_id_prop': '1', 'owner_id': owner_id}})
        .done(function( data )
        {
            var result = $.parseJSON(data);
            if(result != null)
            {
                var id_prop = result['prop_cust'][0]['id'];
                var type = result['prop_cust'][0]['type'];
                $("#refe_rec").val(id_prop);
                $("#con_type").val(type);
                updatecpe(type);
            }





        });
}*/


function changeOverState(change_over)
{
    //console.log($("#refe").val());

    if (change_over.checked)
    {
        change_over.checked = true; // Check
        document.getElementById("conexao_changeOVER").style.display = "block";
        //document.getElementById("conexao_RECONNECTION").style.display = "none";
        //conexao_RECONNECTION

        $("#ref").prop('disabled', true);
        $("#owner_id").prop('disabled', true);

        connectionsProperties('connection', $("#refe").val());

        $("#text_conn_prop").html('Property Change Over Connection:');

        // SELECCIONE AS PROPERTIES DIFERNTES DAS CONEXOES

        $.ajax({ method: "GET", url: "webservice.php", data: { 'conn_prop_id_diff': '1', 'type': $("#con_type").val()}})
            .done(function( data )
            {
                //console.log(data);
                var result = $.parseJSON(data);
                var refe=document.getElementById("refe");
                if (typeof(refe) != 'undefined' && refe != null)
                {
                    refe.innerHTML = "";
                    var refeb;
                    $.each( result, function( key, value )
                    {

                        refeb = new Option(value[0], value[1]);
                        refe.options.add(refeb);
                    });

                    con_prop_type(result[0][1]);
                }
            });



    }
    else
    {
        $("#ref").prop('disabled', false);
        $("#owner_id").prop('disabled', false);
        connectionsProperties('');
        document.getElementById("conexao_changeOVER").style.display = "none";
        //document.getElementById("conexao_RECONNECTION").style.display = "none";
    }

}

function connectionsProperties(conn, prop_id)
{
    if(prop_id == "")
    {
        $.ajax({ method: "GET", url: "webservice.php", data: { 'properties_id_leads': '1', 'prop_id': prop_id}})
            .done(function( data )
            {

                var result = $.parseJSON(data);
                var pona=document.getElementById("refe");
                pona.innerHTML = "";
                var ponb;

                if(conn == "reconnection")
                {
                    ponb = new Option("New Reconnection", "0");
                    pona.options.add(ponb);
                    $.each( result, function( key, value )
                    {
                        ponb = new Option(value[0], value[1]);
                        //console.log(ponb);
                        pona.options.add(ponb);
                    });

                }
                else if(conn == "connection")
                {
                    ponb = new Option("New Connection", "0");
                    pona.options.add(ponb);
                    $.each( result, function( key, value )
                    {
                        ponb = new Option(value[0], value[1]);
                        pona.options.add(ponb);
                    });
                }

                else if(conn == "")
                {
                    $.each( result, function( key, value )
                    {
                        ponb = new Option(value[0], value[1]);
                        pona.options.add(ponb);
                    });
                }





            });
    }



}

function changeConOver(con_type)
{

    var checked = $("#is_changeover:checked").length;

    if(checked == 1)
    {
        $.ajax({ method: "GET", url: "webservice.php", data: { 'conn_prop_id_diff': '1', 'type': con_type}})
            .done(function( data )
            {
                //console.log(data);
                var result = $.parseJSON(data);
                var refe=document.getElementById("refe");
                if (typeof(refe) != 'undefined' && refe != null)
                {
                    refe.innerHTML = "";
                    var refeb;
                    $.each( result, function( key, value )
                    {

                        refeb = new Option(value[0], value[1]);
                        refe.options.add(refeb);
                    });

                    con_prop_type(result[0][1]);
                }
            });
    }
}

function connections_list(conn_id)
{
    $.ajax({ method: "GET", url: "webservice.php", data: { 'prop_id_conn_id_type': '1', 'conn_id': conn_id}})
        .done(function( data )
        {
            var result = $.parseJSON(data);
            var t = result['conn_type_diff'][0]['type'];
            var prop_id = $("#refe").val();
            var form_conn = '';
            $.ajax({ method: "GET", url: "webservice.php", data: { 'conexao_id_prop_equ': '1', 'prop_id': prop_id, 'tipo': t, 'conn_id': conn_id}})
                .done(function( data )
                {
                    var result2 = $.parseJSON(data);
                    var c = $("#con_id").val();
                    form_conn += '<td>Conexao Numero <input type=hidden id=con_id_edit_change name=con_id_edit_change value='+c+'>'+c;
                    form_conn += '<td>Equipment: '+result2['equip'];
                    form_conn += '<td>Type Connection OLD: '+result['t_conn'][0]['type'];
                    $("#conn_type_fsan").html(form_conn);

                    $("#con_type").val(t);


                    updatecpe(t);

                });
        });







}

function connection_equip(conn_id)
{
    connections_list(conn_id);
}








function con_prop_type(prop_id)
{
    $.ajax({ method: "GET", url: "webservice.php", data: { 'prop_id_type_connection': '1', 'prop_id': prop_id}})
        .done(function( data )
        {
            var result = $.parseJSON(data);
            console.log(result);
            if(!isObjectEmpty(result))
            {
                // se existir conexao na changeover

                if($("#is_changeover_val").val() == 1)
                {
                    $( "#is_changeover" ).prop( "checked", true );
                }


                $("#type_con_sta_30").css('display', 'block');

                $("#is_changeover").attr("disabled", false);
                $("#is_reconnection").attr("disabled", false);

                connection_equip($("#con_id").val());

                //con_id
                var con_id=document.getElementById("con_id");
                con_id.innerHTML = "";

                var conn_id;

                // Connection da Change Over
                $.each( result['conexoes'], function( key, value )
                {
                    conn_id = new Option(value['connection_id']+ "- "+value['referencia'], value['connection_id']);
                    //console.log(ponb);
                    con_id.options.add(conn_id);


                });

                // Connection da reconnection

                $.each( result['t_conn'], function( key, value )
                {
                    //console.log(value['type']);
                    $("#con_type_id").val(value['type']);
                });

                // Subscriber


                $.each( result['subs'], function( key, value )
                {
                    //console.log(value['type']);
                    $("#owner_chg").val(value['id']);
                });

            }
            else
            {
                document.getElementById("conexao_RECONNECTION").style.display = "none";
            }
        });





}


function updatepon(olt)
{
    $.ajax({ method: "GET", url: "webservice.php", data: { 'ponsbyolt': olt}})
        .done(function( data )
        {
            var result = $.parseJSON(data);
            var pona=document.getElementById("pons");
            pona.innerHTML = "";
            var ponb;
            $.each( result, function( key, value )
            {
                ponb = new Option(value[0]+' - '+value[1], value[0]);
                pona.options.add(ponb);
            });
        });
}

function chanegeConnectionPropSubs(con_type, refe_rec)
{
    console.log(con_type, refe_rec);
    var owner_id = '';
    $.ajax({ method: "GET", url: "webservice.php", data: { 'prop_conn_type_rec_7': '1', 'type': con_type, 'prop_id': refe_rec}})
        .done(function( data )
        {
            //console.log(data);
            var result = $.parseJSON(data);
            if(result['prop_id'] != null && result['owner_id'] != null)
            {
                $('#refe_rec').prop('disabled', false);
                $('#owner_rec').prop('disabled', false);
                $("#refe_rec").val(result['prop_id']);
                $("#owner_rec").val(result['owner_id']);
                $("#error_rec_prop_subs").html('');
            }
            else if(result['prop_con_o_b'] != null && result['owner_cust_prop'] != null)
            {
                $('#refe_rec').prop('disabled', false);
                $('#owner_rec').prop('disabled', false);
                console.log(result['prop_con_o_b']);
                var html = '';
                $.each( result['prop_con_o_b'], function( key, value )
                {
                    var val = value[1];
                    var text =   value[0];

                    html += '<option value="' + val + '">' + text + '</option>';

                });

                $('#refe_rec').html(html);

                var html2 = '';
                $.each( result['owner_cust_prop'], function( key, value )
                {
                    var val = value[1];
                    var text =   value[0];

                    html2 += '<option value="' + val + '">' + text + '</option>';

                });

                $("#owner_rec").html(html2);

                owner_id = result['prop_con_o_b'][0][2];

                $("#owner_rec").val(owner_id);

                $("#error_rec_prop_subs").html('');
            }

            else if(result['prop_con_o_b_not_type'] != null)
            {
                $('#refe_rec').prop('disabled', true);
                $('#owner_rec').prop('disabled', true);
                $('#refe_rec').html('');
                $("#owner_rec").html('');

                $("#error_rec_prop_subs").html('' +
                    '<font color=red>Select a Property to make a reconnection *</font><br>' +
                    '<font color=red>Select a Owner to associate a Property to make a reconnection *</font><br>');
            }
        });
}




function updatecpe(type,model) {

    var models = document.getElementById('models');
    if(models != null)
    {
        models.innerHTML = "";
        var model_cpe = document.getElementById('model_cpe');
        var cpe_text = document.getElementById('cpe_text');
        var text;


        if (type == "GPON") {
            if(models  != null){
                document.getElementById('models').style.display = 'block';
            }
            if(cpe_text != null)
            {
                document.getElementById('cpe_text').style.display = 'block';
            }

            text = '<option  value=zhone-2427 ';
            if (model == 'zhone-2427' || model == '' || model == false) {
                text += ' selected';
            }
            text += '>zhone-2427</option><option value=zhone-2727a ';

            if (model == 'zhone-2727a') {
                text += ' selected';
            }
            text += '>zhone-2727a</option><option value=zhone-2427-spf ';

            if (model == 'zhone-2427-spf') {
                text += ' selected';
            }
            text += '>SFP zhone-2427</option><option value=SFP ';

            if (model == 'SPF') {
                text += ' selected';
            }
            text += '>DIA connection</option> <option value=mininode ';

            if (model == 'mininode') {
                text += ' selected';
            }
            text += '>fibre mininode</option>';
        } else if (type == "COAX") {
            if(models  != null){
                document.getElementById('models').style.display = 'block';
            }
            if(cpe_text != null)
            {
                document.getElementById('cpe_text').style.display = 'block';
            }
            text = '<option  value=cve ';
            if (model == 'cve' || model == '' || model == false) {
                text += ' selected';
            }
            text += '>hitron cve</option> <option value=cva ';

            if (model == 'cva') {
                text += ' selected';
            }
            text += '>hitron cva</option><option value=k310i ';

            if (model == 'k310i') {
                text += ' selected';
            }
            text += '>kathrein 310i</option> <option value=k10 ';

            if (model == 'k10') {
                text += ' selected';
            }
            text += '>kathrein router</option>';


        } else if (type == "FWA") {
            if(models  != null){
                document.getElementById('models').style.display = 'block';
            }
            if(cpe_text != null)
            {
                document.getElementById('cpe_text').style.display = 'block';
            }
            text = '<option  value=ltulite ';
            if (model == 'ltulite' || model == '' || model == false) {
                text += ' selected';
            }
            text += '>LTU lite</option> <option value=ltulr ';

            if (model == 'ltulr') {
                text += ' selected';
            }
            text += '>LTU LR</option> <option value=ltupro ';

            if (model == 'ltupro') {
                text += ' selected';
            }
            text += '>LTU pro</option>';
        } else if (type == "DIA") {
            if(models  != null){
                document.getElementById('models').style.display = 'block';
            }
            if(cpe_text != null)
            {
                document.getElementById('cpe_text').style.display = 'block';
            }
            text = '<option  value=sfp_bidi_1Gb';
            if (model == 'sfp_bidi_1Gb' || model == '' || model == false) {
                text += ' selected';
            }
            text += '>SFP bidi 1Gb</option> <option value=sfpp_bidi_10Gb ';

            if (model == 'sfpp_bidi_10Gb') {
                text += ' selected';
            }
            text += '>sfpp bidi 10Gb</option> ';
        } else if (type == "DARKF") {
            if(models  != null){
                document.getElementById('models').style.display = 'block';
            }
            if(cpe_text != null)
            {
                document.getElementById('cpe_text').style.display = 'block';
            }
            text = '<option  value=no_cpe';
            if (model == 'no_cpe' || model == '' || model == false) {
                text += ' selected';
            }
            text += '>no_cpe</option> ';
        } else if (type == "ETH") {
            if(models  != null){
                document.getElementById('models').style.display = 'block';
            }
            if(cpe_text != null)
            {
                document.getElementById('cpe_text').style.display = 'block';
            }
            text = '<option  value=tplink ';
            if (model == 'tplink' || model == '' || model == false) {
                text += ' selected';
            }
            text += '>tplinkrt</option> <option value=zyxelrt ';

            if (model == 'zyxelrt') {
                text += ' selected';
            }
            text += '>zyxelrt</option><option value=comega ';

            if (model == 'comega') {
                text += ' selected';
            }
            text += '>comega</option> <option value=switch ';

            if (model == 'switch') {
                text += ' selected';
            }
            text += '>switch</option> <option value=switch ';

            if (model == 'sfp') {
                text += ' selected';
            }
            text += '>sfp</option>';

            // status == 30




        } else {
            //document.getElementById('models')
            if(models  != null){
                document.getElementById('models').style.display = 'none';
            }
            if(cpe_text != null)
            {
                document.getElementById('cpe_text').style.display = 'none';
            }
        }
        $("#models").html(text);

        var status = $("#idstatus").val();
        console.log(status);

        if(status == 30 && $("#is_rec_input").val() == 1)
        {
            var con_type= $("#con_type").val();
            var refe_rec = $("#refe_rec").val();

            chanegeConnectionPropSubs(con_type, refe_rec);
        }

        else if(status == 50)
        {
            var prop_id = $("#prop_id").val();
            var con_id = $("#con_id").val();
            putInitialEquipPropCon(prop_id, con_id, type);
        }
    }





    //var status = 50;

    /*
    if(status == 30 && $("#is_rec_input").val() == 1)
    {
        var con_type= $("#con_type").val();
        var refe_rec = $("#refe_rec").val();

        chanegeConnectionPropSubs(con_type, refe_rec);
    }


    // Colocar os Equipamentos Inicial (Des ou Activado para manter o Equipamento)
    if(status == 50)
    {
        var prop_id = $("#prop_id").val();
        var con_id = $("#con_id").val();
        putInitialEquipPropCon(prop_id, con_id, type);
    }*/


    //models.innerHTML = text;








}

function updatevlan(olt)
{
    $.ajax({ method: "GET", url: "webservice.php", data: { 'vlansbyolt': olt}})
        .done(function( data )
        {
            var result = $.parseJSON(data);
            var pona=document.getElementById("vlans");
            pona.innerHTML = "";
            var ponb;
            $.each( result, function( key, value )
            {
                ponb = new Option(value[1]+' - '+value[3]+' of '+value[2], value[0]);
                pona.options.add(ponb);
            });
        });
}