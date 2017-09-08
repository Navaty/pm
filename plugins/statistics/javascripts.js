$(function() {
      function log( message ) {
	  $( "<div/>" ).text( message ).prependTo( "#log" );
	  $( "#log" ).attr( "scrollTop", 0 );
      }

      function search_answer( question) {
	  $.ajax({
		     url: "/oktell/ajax.php",
		     dataType: "html",
		     data: {search: "answer", term: question, nojson: "yes"},
		     success: function(data) {
			 $("#answers").html(data);
			 $(".an_answer").button();
			 $(".an_answer").click(function() {
					 $(".answer").val($(this).attr('val'));
				     });
		     }
		 })
      }
      
      $(".timepicker").timepicker({
				      timeOnlyTitle: 'Выберите время',
				      timeText: 'Время',
				      hourText: 'Часы',
				      minuteText: 'Минуты',
				      secondText: 'Секунды',
				      currentText: 'Сейчас',
				      closeText: 'Закрыть',
				      hourGrid: 3,
				      minuteGrid: 10
				  });
      
      $(".datepicker").datepicker( $.datepicker.regional[ "ru" ] );
      
      $(".searchedu").autocomplete({
				      minLength: 2,
				      source: function(request,response) {
					  request.test = 'almaz';
					  //console.log(request);
					  lastXhr = $.getJSON(
					      "/oktell/ajax.php?search=searchedu",
					      request,
					      function( data, status, xhr ) {
						  if ( xhr === lastXhr ) {
						      response( data );
						  }
					      }
					      );					  
				      }
				  });
      
      $(".schools").autocomplete({
				       source: "/oktell/ajax.php?search=ogv&type=school",
				       minLength: 2
				   });

      $(".settlements").autocomplete({
				       source: "/oktell/ajax.php?search=settlement",
				       minLength: 2
				   });
      $(".contact_surname").change(function(){
				       $(".surname").val(this.value);
				   });
      $(".contact_surname").autocomplete({
					     //					     source: "/oktell/ajax.php?search=citizen&type=surname",
					     source: "/oktell/ajax.php?search=opengoo_list&type=surname",
					     minLength: 2
					 });
      $(".contact_firstname").change(function(){
				       $(".firstname").val(this.value);
				   });

      $(".contact_firstname").autocomplete({
					       source: "/oktell/ajax.php?search=citizen&type=firstname",
					       minLength: 2
					   });
      $(".contact_fathername").change(function(){
					  $(".fathername").val(this.value);
				      });
      $(".contact_fathername").autocomplete({
						source: "/oktell/ajax.php?search=citizen&type=fathername",
						minLength: 2
					    });
      $(".contact_email_prefix").change(function(){
					    $(".email_prefix").val(this.value);
					});
      $(".contact_email_suffix").change(function(){
					    $(".email_suffix").val(this.value);
					});
      $(".contact_phonemain").change(function(){
					    $(".contact_phone").val(this.value);
					});
      $(".search").autocomplete({
				    source: "/oktell/ajax.php?search=citizen&type=fathername",
				    minLength: 2
				});
      $(".position").autocomplete({
				    source: "/oktell/ajax.php?search=position",
				    minLength: 2
				});
      $(".phone").autocomplete({
				    source: "/oktell/ajax.php?search=phone",
				    minLength: 2
				});
      $(".question").autocomplete({
				      source: "/oktell/ajax.php?search=question",
				      minLength: 2,
				      select: function(event, ui) {
					  $(".answer").val('');
					  search_answer(ui.item.value);
				      }
				  });

      $('.online_search_contacts').keyup(function() {
					      search_contacts()
					  });
      $('.online_search_contacts').change(function() {
					      search_contacts()
					  });
  });

function eservice_tatalc (id) {
    var number = $("#"+id).val();
    $.ajax(
        {
            url: "http://cc.citrt.net/webservices/tatalc.php",
            type: "POST",
            dataType: "json",
            data: {number: number},
            success: function(res) {
                var options = '';
                if(res.length) {
                    for (var i = 0; i < res.length; i++) {
                        options += '<div><b>'+res[i].name + '</b> : '+ res[i].data +'</div>';
                    }
                }
		$('#tatalc_result').html(options);
            }
        }

    )
}

$(function() {
      $(".accordion").accordion({autoHeight: false,collapsible: true,active:-1});
      $(".tabs").tabs();
      $(".tabs_collapsible").tabs({ collapsible: true });
      $(".button").button();
      $(".buttonset").buttonset();
      $("#warning_place_status").dialog({
					    autoOpen: false,
					    height: 200,
					    modal: true
					});
      $("#sms").dialog({
			   autoOpen: false,
			   height: 500,
			   width: 600,
			   modal: true
					});
      /*$("#apealnumber60").dialog({
                                autoOpen: false,
                                width: 400,
                                height: 100,
                                modal: true,
                                
                            });*/

      $("#reminder").dialog({
			        autoOpen: false,
				width: 400,
                                height: 300,
                                modal: true,
				buttons: {
				    "Создать задачу с прежними данными": function() {
					$(this).dialog("close");
					$("#new_taskids").append($("#reminder").html()+"<br/>");
					},
				    "Завершить прием обращения": function() {
					$(this).dialog("close");
					$(".ready2clear").val("");
					$("#new_taskids").append($("#reminder").html()+"<br/>");
					}
				    }
     			    });
      //window.onload = function () {
      //    var map = new YMaps.Map(document.getElementById("YMapsID"));
      //    map.setCenter(new YMaps.GeoPoint(49.11,55.79, 55.76), 12);
      $("#greeting").dialog({
				height: 350,
				width: 500,
				modal: true,
				buttons: {
				    "Да сказал(а)": function(){
					$(this).dialog("close");
					//almaz, зарегистрировать нажатие на кнопку в базе для того, чтобы в будущем регисрировать говорили или нет.
					//если время нажатия - время показа страницы будет коротким, считаться что оператор не проговорил приветствие.
				    }
				}
			    });
  });
function call_classifiers(id) {
    $("#classifiers").show();
    $.ajax(
	{
	    url: "/oktell/ajax.php?search=classifiers",
	    type: "POST",
	    dataType: "json",
	    data: {term: id},
	    success: function(res) {
		var options = '';
		if(res.length) {
		    for (var i = 0; i < res.length; i++) {
			options += '<option value="' + res[i].id + '">' + res[i].label + '</option>';
		    }
		}
		$("select#classifiers").html(options);
	    }
	}
	
    )
}

function call_classifier_categories(direction,type) {
    $("#classifier_categories").show();
    $.ajax(
	{
	    url: "/oktell/ajax.php?search=classifier_categories",
	    type: "POST",
	    dataType: "json",
	    data: {term: direction, type: type},
	    success: function(res) {
		var options = '';
		if(res.size()>=1) {
		    for (var i = 0; i < res.length; i++) {
			options += '<option value="' + res[i].id + '">' + res[i].label + '</option>';
		    }
		    $("select#classifier_categories").html(options);
		}
	    }
	}
    )
}

function get_form_inputs(FormID) {
    var $inputs = $('#' + FormID + ' :input');
    var values = {};
    $inputs.each(function() {
		     //  alert(this.name);
		     values[this.name] = $(this).val();
		 })
    return values;
}
function appeal_register(type,form_name,referencename,mainprojectlevel) {
    var post_data =  get_form_inputs(form_name);
    if(type=='close') {
	post_data["autoclose"] = 1;
    }
    if(referencename) {
	post_data["mainprojectname"] = referencename;
    }
    if(mainprojectlevel) {
	post_data["mainprojectlevel"] = mainprojectlevel;
    }
    if(type=='opengoo2') {
	$.ajax({
		   type: "POST",
		   url: "post_opengoo2.php",
		   data: post_data,
		   success: function(msg) {
		       if(msg.length < 10) {
			   pre_text = "Задача №<a target='_blank' href='http://pm.citrt.net/index.php?c=task&a=view_task&id="+ msg + "'>";
			   post_text = "</a> успешно создана";
			   text = pre_text +  msg + post_text;
		       } else {
			   pre_text = "<font color=red>Извините, задача не была создана! ";
			   message = "Не успешно, попробуйте еще раз! "+msg;
			   post_text =" </font>";
			   text = pre_text + message + post_text;
		       }
		       $("#reminder").html(text);
		       $("#reminder").dialog("open");
		   }
	       })
    } else {
	$.ajax({
		   type: "POST",
		   url: "post_opengoo.php",
		   data: post_data,
		   success: function(msg) {
		       if(msg.length < 10) {
			   pre_text = "Задача №<a target='_blank' href='http://pm.citrt.net/index.php?c=task&a=view_task&id="+ msg + "'>";
			   post_text = "</a> успешно создана";
			   text = pre_text +  msg + post_text;
		       } else {
			   pre_text = "Упс) ";
			   message = "Не успешно, попробуйте еще раз! "+msg;
			   post_text =" ";
			   text = pre_text + message + post_text;
		       }
		       $("#reminder").html(text);
		       $("#reminder").dialog("open");
		   }
	       })
    }
}

function egiszrt_appeal_register(type,referencename, idform) {
    var post_data =  get_form_inputs(idform);
    //console.log(post_data);
    if(type=='close') {
        post_data["autoclose"] = 1;
    }
    if(referencename) {
        post_data["mainprojectname"] = referencename;
    }
    $.ajax({
               type: "POST",
               url: "post_opengoo.php",
               data: post_data,
               success: function(msg) {
                   if(msg.length < 10) {
                       pre_text = "Ваше обращение зафиксировано и передано специалистам. Номер вашего обращения <a target='_blank' href='http://pm.citrt.net/index.php?c=task&a=view_task&id="+ msg + "'>";
                       post_text = "</a>.";
                       text = pre_text +  msg + post_text;
                   } else {
                       pre_text = "Упс) ";
                       message = "Не успешно, попробуйте еще раз! "+msg;
                       post_text =" ";
                       text = pre_text + message + post_text;
                   }
                   $("#reminder").html(text);
                   $("#reminder").dialog("open");
                   }
           })
}


function minzdrav_appeal_register(type,referencename) {
    var post_data =  get_form_inputs("form_stis_60zdrav");
    if(type=='close') {
        post_data["autoclose"] = 1;
    }
    if(referencename) {
        post_data["mainprojectname"] = referencename;
    }
    $.ajax({
               type: "POST",
               url: "post_opengoo.php",
               data: post_data,
               success: function(msg) {
                   if(msg.length < 10) {
                       pre_text = "Задача №<a target='_blank' href='http://pm.citrt.net/index.php?c=task&a=view_task&id="+ msg + "'>";
                       post_text = "</a> успешно создана";
                       text = pre_text +  msg + post_text;
                   } else {
                       pre_text = "Упс) ";
                       message = "Не успешно, попробуйте еще раз! "+msg;
                       post_text =" ";
                       text = pre_text + message + post_text;
                   }
                   $("#reminder").html(text);
                   $("#reminder").dialog("open");
                   }
           })
}


function education_appeal_register(type,referencename) {
    var post_data =  get_form_inputs("education_new_appeal");
    if(type=='close') {
	post_data["autoclose"] = 1;
    }
    if(referencename) {
	post_data["mainprojectname"] = referencename;
    }
    $.ajax({
	       type: "POST",
	       url: "post_opengoo.php",
	       data: post_data,
	       success: function(msg) {
		   if(msg.length < 10) {
		       pre_text = "Задача №<a target='_blank' href='http://pm.citrt.net/index.php?c=task&a=view_task&id="+ msg + "'>";
		       post_text = "</a> успешно создана";
		       text = pre_text +  msg + post_text;
		   } else {
		       pre_text = "Упс) ";
		       message = "Не успешно, попробуйте еще раз! "+msg;
		       post_text =" ";
		       text = pre_text + message + post_text;
		   }
		   $("#reminder").html(text);
		   $("#reminder").dialog("open");
		   }
	   })
}

function additional_params(fieldid,where) {
    $("#"+where).html("");
    $.ajax(
	{
	    url: "/oktell/ajax.php?search=additional_fields",
	    type: "POST",
	    dataType: "json",
	    data: {term: fieldid},
	    success: function(res) {
		var fields = '';
		var length = (res ? res.length : 0);
		if(length) {
		    //do nothing
		    //		} else {
		    for (var i = 0; i < length; i++) {
			fields += "<div class='line'>";
			fields += " <label class='form'>" + res[i].label + "</label>";
			fields += " <input type='text' name='data[" + res[i].fieldname + "]'/></div><div class='line'>";
			fields += "<label class='form'> </label> <i> "+ res[i].description  +" </i>";
			fields += "</div>";
		    }
		    $("#"+where).html(fields);
		}
	    }
	}
    )
}   


function search_contacts() {
    var values = {};
    var $inputs = $('#search_contacts :input');
    $inputs.each(function() {
		     values[this.name] = $(this).val();
                 }
                );
    $.ajax(
        {
	    url: "/oktell/ajax.php?search=contact",
	    type: "POST",
	    data: {term: values, nojson: "yes"},
	    dataType: "html",
	    success: function(res) {
                $("#contact_search_results").html(res);
	    }
        }
    )
}

function project_description(projectid,where) {
    $.ajax(
        {
	    url: "/oktell/ajax.php?search=project_description",
	    type: "POST",
	    data: {term: projectid, nojson: "yes"},
	    dataType: "html",
	    success: function(data) {
		//		alert(res);
                $("#" + where).html(data);
	    }
        }
    );
}

function send_sms() {
    var smstext = $("#sms-text").val();
    var smsreferenceid  = $("#sms-fengofficeid").val();
    var smsphoneto = $("#sms-phoneto").val();
    //   alert(1);
    $('#sms').dialog('close');
    $('#sms-text').val('');
    $('#sms-templates').val('');
    alert('Подождите идет обработка СМС сообщения!');
    $.ajax(
	{
	    url: "/oktell/ajax.sendsms.php",
	    type: "POST",
	    data: {phoneto: smsphoneto ,message: smstext, referenceid: smsreferenceid},
	    dataType: "html",
	    success: function(data) {
		alert("Отправлено, посмотрите историю на всякий случай:-)\n\nТехническая информация:\n\n " + data);
	    }
	}
    );
}

function add_task_comment(taskid) {
    comment = $('#comment4taskid'+taskid).val();
    $('#comment4taskid'+taskid).val('');
    userid = $('input[name=assignedbyid]').val();
    $.ajax(
        {
            url: "/oktell/ajax.php?action=add_task_comment",
            type: "POST",
            data: {taskid: taskid, comment: comment, userid: userid},
            dataType: "html",
            success: function(data) {
                //              alert(res);almaz
		$("#currentcomments4task"+taskid).append("<div class='task_comments'>"+data+"<br/>"+comment+"</div>");
            }
        }
    )
}
function infomat_info(infomatid) {
    $.ajax(
        {
	    url: "/oktell/ajax.php?search=project_xml",
	    type: "POST",
	    data: {term: infomatid, json2: 1},
	    dataType: "json",
	    success: function(data) {
		output = '.';
		//oldone	output = "<img src='"+BASEURLFILE+"'/>";
		var Username = "root";
		var Password = "1q2w3e";
		var BaseURL = "http://" + data[0].ip + "/";
		var DisplayWidth = "320";
		var DisplayHeight = "240";
		
		// This is the path to the image generating file inside the camera itself
		var File = "axis-cgi/mjpg/video.cgi?resolution=320x240";
		// No changes required below this point
		var output = "";
		// If Internet Explorer under Windows then use ActiveX 
		output  = '<OBJECT ID="Player" width='
		output += DisplayWidth;
		output += ' height=';
		output += DisplayHeight;
		output += ' CLASSID="CLSID:DE625294-70E6-45ED-B895-CFFA13AEB044" ';
		output += 'CODEBASE="';
		output += BaseURL;
		output += 'activex/AMC.cab#version=4,0,17,0">';
		output += '<PARAM NAME="MediaURL" VALUE="';
		output += BaseURL;
		output += File + '">';
		output += '<param name="MediaType" value="mjpeg-unicast">';
		output += '<param name="ShowStatusBar" value="0">';
		output += '<param name="ShowToolbar" value="0">';
		output += '<param name="AutoStart" value="1">';
		output += '<param name="StretchToFit" value="1">';
		output += '<param name="MediaUsername" value="' + Username + '"><this> ';
		output += '<param name="MediaPassword" value="' + Password + '"><this> '; 
		output += '<BR><B>Axis Media Control</B><BR>';
		output += 'The AXIS Media Control, which enables you ';
		output += 'to view live image streams in Microsoft Internet';
		output += ' Explorer, could not be registered on your computer.';
		output += '<BR></OBJECT>';
	    	$("#infomat_camera").html(output);
	    }
        }
    )

}


$(document).ready(function(){
/*      function testlogger( string ) {
	  $.ajax({
		     url: "/oktell/test.logger.php",
		     dataType: "html",
		     data: {str: string}
		 })
      }*/
      $("input,select,textarea").live("blur",
				      function(){
					  var element=$(this);
					  var form=element.closest("form");
					  var formid=form.attr("id");
					  var date=new Date();
					  var elName=element.attr("name");
					  var elValue=element.val();
					  var arr = "\n"+ formid + "." + elName + ":" + elValue;
					  //testlogger(arr);
				      });

      /*      $("form").submit(function(){
	      var form=$(this);
	      var input = $(".problem_warning",form);
	      if(input.length){
	      var problema = $(".problem_warning",form).val();
	      alert('Problema:'+ problema);
	      if(problema==''){
	      alert("Забыли указать проблему!");
	      return false;
	      }else{
	      return true;
                               }
			       }else{
                               return true;
			       }
			       
			       });
      */
      /*sazan almaz отключил для теста
      $("input:submit").click(function(){
	      var form=$(this).closest("div.ui-tabs-panel");
	      var input = $(".problem_warning",form);
	      console.log(input);						  
	      if(input){
		  var problema = input.val();
		  // alert('Problema:'+ problema);
		  if(problema==''){
		      alert("Забыли указать проблему!");
		      return false;
		  }else{
		      return true;
		  }
	      }else{
		  return true;
	      }
	      
	  });
      */
$('button').button();
						$('#byName').click(function(){
							$('#cardFiles').equeue(
								'byName',
								[
									$('#inSurname').val(),
									$('#inName').val(),
									$('#inStreet').val()
								]
							)
						});
						$('#byPolice').click(function(){
							$('#cardFiles').equeue(
								'byPolice',
								[
									1234567,
									$('#inSeries').val()
								]
							)
						})
						$('#byPoliceAndSurname').click(function(){
							$('#cardFiles').equeue(
								'byPoliceAndSurname',
								[
									$('#inSurname').val(),
									$('#inNumber').val(),
									$('#inSeries').val()
								]
							)
						});
						$('#btnHistory').click(function(){
							$('#dataArea').equeue('history');
						});
						$('#btnDistDocs').click(function(){
							$('#dataArea').equeue('districtDoctors');
						});
						$('#btnDocs').click(function(){
							$('#dataArea').equeue('specialities');
						});
    });


function get_faq_questions(serviceid,classificationid) {
    $.ajax({
               url: "/oktell/ajax.php",
               dataType: "json",
               data: {search: "faq_questions", serviceid: serviceid, classificationid: classificationid, nojson: "yes"},
               success: function(data) {
		   var options = '<option question="">Готовые вопросы</option>';
		   var evals = '';
		   var questions = jQuery.parseJSON(data);
		   $.each(data,
			  function(key,val){
			      options += '<option value="' + val.id + '"  question=\''+ val.text  +'\'>' + val.title + '</option>';
			  }
			 );
                   $("#faq_questions").html(options);
		   $("#faq_questions").change(function()
					      {
						  $("#oktellappeal").append(' '+ $("#faq_questions option:selected").attr('question'));
					      }
					     );
               }
           });
}

function show_sms_dialog() {
    $("#sms").dialog("open");
}
function show_last_sms(CallerID) {
    $("#last_sms").html("запрос на СМС...");
    $.ajax({
               url: "/oktell/show_last_sms.php",
               dataType: "html",
               data: {callerid: CallerID},
               success: function(data) {
                   $("#last_sms").html(data);
               }
	       })
}

function Save071(){
	var post_data=get_form_inputs('service-071');
	$.ajax({
		url:  "/oktell/ajax.php?search=save071",
		type: "POST",
		dataType: "json",
		data: post_data,
		success: function(data){
			$("#msg071").html("Логин: "+data.login+"<br>"+"Пароль: "+data.pass);
			$("#msg071").dialog({
				height: 200,
				modal: true
			});
		},
		error: function(data){
			alert("Ошибка");
		}
	});
}
