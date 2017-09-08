$(function () {
    //$(".buttons").buttonset();
});
function show_spheres(PlaceID) {
    $("#opengoosphereid").val(PlaceID);
    $.ajax({
        url: "ajax.php",
        dataType: "html",
        data: {placeid: PlaceID, actionname: "spheres"},
        success: function (data) {
            if (PlaceID == '2555') {
                var varinfomats = $("#infomat_place_incidents").html();
                $("#infomatplaces").html(varinfomats);
            } else {
                $("#infomatplaces").html('');
            }
            $("#spheres").html('');
            $("#services").html('');
            $("#classificators").html('');
            $("#sfields").html('');
            $("#fields").html('');
            $("#incidents_action").html('');
            $("#spheres").html(data);
        }
    });
}
function show_services(PlaceID, SphereID) {
    $.ajax({
        url: "ajax.php",
        dataType: "html",
        data: {placeid: PlaceID, sphereid: SphereID, actionname: "services"},
        success: function (data) {
            $("#services").html('');
            $("#classificators").html('');
            $("#sfields").html('');
            $("#fields").html('');
            $("#incidents_action").html('');
            $("#services").html(data);
        }
    });
}
function show_classificators(PlaceID, SphereID, ServiceID) {
    //  alert("show_classificators("+PlaceID+","+SphereID+","+ServiceID+")");
    $.ajax({
        url: "ajax.php",
        dataType: "html",
        data: {placeid: PlaceID, sphereid: SphereID, serviceid: ServiceID, actionname: "classificators"},
        success: function (data) {
            $("#classificators").html('');
            $("#sfields").html('');
            $("#fields").html('');
            $("#incidents_action").html('');
            $("#classificators").html(data);
            show_sfields(ServiceID);
        }
    });
}
function show_sfields(ServiceID) {
    $.ajax({
        url: "ajax.php",
        dataType: "html",
        data: {serviceid: ServiceID, actionname: "sfields"},
        success: function (data) {
            $("#sfields").html(data);
        }
    });
}
function show_fields(IncidentID) {
    var senddata = '<input type="button" value="Зарегистрировать ИНЦИДЕНТ" onclick="appeal_register(\'opengoo2\',\'form_incidents2\',\'Классификатор\');"/>';
    $.ajax({
        url: "ajax.php",
        dataType: "html",
        data: {incidentid: IncidentID, actionname: "fields"},
        success: function (data) {
            $("#fields").html(data);
            $("#incidents_action").html(senddata);

        }
    });
}
function get_form_inputs(FormID) {
    var $inputs = $('#' + FormID + ' :input');
    var values = {};
    $inputs.each(function () {
        //  alert(this.name);
        values[this.name] = $(this).val();
    })
    return values;
}
function appeal_register(type, form_name, referencename) {
 //   $.blockUI({message: '<img src="busy.gif" /> Работаем...'});
    var post_data = get_form_inputs(form_name);
    // console.log(post_data);
    // return;
    if (type == 'close') {
        post_data["autoclose"] = 1;
    }
    if (referencename) {
        post_data["mainprojectname"] = referencename;
    }
    if (type == 'opengoo2') {
        $.ajax({
            type: "POST",
            // url: "../opengooweb.php",
            url: 'post_opengoo.php',
            data: post_data,
            success: function (msg) {
                if (msg.length < 10) {
                    pre_text = "Задача №<a target='_blank' href='http://pm2.citrt.net/index.php?c=task&a=view_task&id=" + msg + "'>";
                    post_text = "</a> успешно создана";
                    text = pre_text + msg + post_text;
                } else {
                    pre_text = "<font color=red>Извините, задача не была создана! ";
                    message = "Не успешно, попробуйте еще раз! " + msg;
                    post_text = " </font>";
                    text = pre_text + message + post_text;
                }
                $("#reminder").html(text);
                $("#reminder").dialog();
                $.unblockUI();
            }
        })
    } else {
        $.ajax({
            type: "POST",
            url: "post_opengoo.php",
            data: post_data,
            success: function (msg) {
                if (msg.length < 10) {
                    pre_text = "Задача №<a target='_blank' href='http://pm2.citrt.net/index.php?c=task&a=view_task&id=" + msg + "'>";
                    post_text = "</a> успешно создана";
                    text = pre_text + msg + post_text;
                } else {
                    pre_text = "Упс) ";
                    message = "Не успешно, попробуйте еще раз! " + msg;
                    post_text = " ";
                    text = pre_text + message + post_text;
                }
                $("#reminder").html(text);
                $("#reminder").dialog("open");
                $.unblockUI();
            }
        })
    }
}
