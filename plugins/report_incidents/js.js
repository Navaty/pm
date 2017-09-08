function give_sources(param){
	$.ajax({
        	type: "POST",
                url: "templates/sourceList.php",
                data: {"command": param},
                success: function(msg){
                	data = $.parseJSON(msg);
                        verstka = '<label for="source">Источник</label>';
                        verstka += '<select name="classifier" id="classifier" class="ui-widget-content ui-corner-all">';
                        $.each(data, function(){
                        	verstka += '<option value="' + this['id'] + '">' + this['name'] + '</option>';
                        });
                        verstka += '</select>';
		}
        });
};

function change_classifier(source_id) {
	param = "change_classifier";
	$.ajax({
	         type: "POST",
	         url: "templates/model.php",
                 data: {"command": param, "source_id": source_id},
                 success: function(msg){
                 	data = $.parseJSON(msg);
			if(data != null) { // если для данного источника существуют классификаторы, то выводим их. Если классификаторов нет - выводим уведомлялку.
	                        verstka = '<option>Нажать для выбора</option>';
        	                $.each(data, function(){
                	        	verstka += '<option value="' + this['id'] + '">' + this['name'] + '</option>';
                       		});
			}
			else {
				verstka = '<option>Нет введенных классификаторов</option>';
				alert('Для данного источника не введено ни одного классификатора!');

			}
	                        $('#classifier').html(verstka);
		}
        });
};

function change_source_list() {
	param = "change_source_list";
        $.ajax({
                 type: "POST",
                 url: "templates/model.php",
                 data: {"command": param},
                 success: function(msg){
                        data = $.parseJSON(msg);
                        verstka = '<option>Нажать для выбора</option>';
                        $.each(data, function(){
                                verstka += '<option value="' + this['id'] + '">' + this['name'] + '</option>';
                        });
                        $('#source').html(verstka);
			$('#newclassifier_source').html(verstka);
                }
        });
}

function change_source_list_for_classifier() {
        param = "change_source_list";
        $.ajax({
                 type: "POST",
                 url: "templates/model.php",
                 data: {"command": param},
                 success: function(msg){
                        data = $.parseJSON(msg);
                        verstka = '<option>Нажать для выбора</option>';
                        $.each(data, function(){
                                verstka += '<option value="' + this['id'] + '">' + this['name'] + '</option>';
                        });
                        $('#source').html(verstka);
                }
        });
}

function show_all_criteria() {
	param = "show_all_criteria";
	$.ajax({
                 type: "POST",
                 url: "templates/model.php",
                 data: {"command": param},
                 success: function(msg){
                        data = $.parseJSON(msg);
			console.log(data);
			verstka = '<thead>';
			verstka += '<tr class="ui-widget-header ">';
			verstka += '<th>Источник</th>';
			verstka += '<th>Классификатор</th>';
			verstka += '<th></th>';
			verstka += '<th></th>';
			verstka += '<th></th>';
			verstka += '</tr>';
			verstka += '</thead>';
			verstka += '<tbody>';
                        $.each(data, function(){
				if(this['active']==true) {
					verstka += '<tr class="true-widget">';
				}
				else {
					verstka += '<tr class="false-widget">';
				}
				verstka += '<td>' + this["sourcename"] + '</td>';
                                verstka += '<td>' + this["classifiername"]+ '</td>';
				verstka += '<td><img src="img/reports_pencil.png" class="edit_sourfier" rec_id="' + this["id"] + '" /></td>';
				if(this['active']==true) {
                                        verstka += '<td><img src="img/lamp_active.png" class="lamp_active" rec_id="' + this["id"] + '" active="1" /></td>';
                                }
                                else {
                                        verstka += '<td><img src="img/lamp_deactive.png" class="lamp_active" rec_id="' + this["id"] + '" active="0"  /></td>';
                                }
				verstka += '<td><img src="img/delete.png" class="delete_sourfier" rec_id="' + this["id"] + '" /></td>';
				verstka += '</tr>';
                        });
			verstka += '</tbody>';
                        $('#sourfiers').html(verstka);
                }
        });
}

function show_all_sources() {
        param = "show_all_sources";
        $.ajax({
                 type: "POST",
                 url: "templates/model.php",
                 data: {"command": param},
                 success: function(msg){
                        data = $.parseJSON(msg);
//                        console.log(data);
                        verstka = '<thead>';
                        verstka += '<tr class="ui-widget-header ">';
                        verstka += '<th>Источник</th>';
			verstka += '<th></th>';
			verstka += '<th></th>';
                        verstka += '</tr>';
                        verstka += '</thead>';
                        verstka += '<tbody>';
                        $.each(data, function(){
                                if(this['active']==true) {
                                        verstka += '<tr class="true-widget">';
                                }
                                else {
                                        verstka += '<tr class="false-widget">';
                                }
                                verstka += '<td>' + this["name"] + '</td>';
				if(this['active']==true) {
                                        verstka += '<td><img src="img/lamp_active.png" class="lamp_active_source" rec_id="' + this["id"] + '" active="1" /></td>';
                                }
                                else {
                                        verstka += '<td><img src="img/lamp_deactive.png" class="lamp_active_source" rec_id="' + this["id"] + '" active="0"  /></td>';
                                }
				verstka += '<td><img src="img/delete.png" class="delete_source" rec_id="' + this["id"] + '" /></td>';
                                verstka += '</tr>';
                        });
                        verstka += '</tbody>';
                        $('#sources').html(verstka);
                }
        });
}

function show_all_classifiers() {
        param = "show_all_classifiers";
        $.ajax({
                 type: "POST",
                 url: "templates/model.php",
                 data: {"command": param},
                 success: function(msg){
                        data = $.parseJSON(msg);
                        console.log(data);
                        verstka = '<thead>';
                        verstka += '<tr class="ui-widget-header ">';
                        verstka += '<th>Классификатор</th>';
			verstka += '<th>Источник</th>';
                        verstka += '<th></th>';
                        verstka += '<th></th>';
                        verstka += '</tr>';
                        verstka += '</thead>';
                        verstka += '<tbody>';
                        $.each(data, function(){
                                if(this['active']==true) {
                                        verstka += '<tr class="true-widget">';
                                }
                                else {
                                        verstka += '<tr class="false-widget">';
                                }
				verstka += '<td>' + this["name"] + '</td>';
				verstka += '<td>' + this["source"] + '</td>';
                                if(this['active']==true) {
                                        verstka += '<td><img src="img/lamp_active.png" class="lamp_active_classifier" rec_id="' + this["id"] + '" active="1" /></td>';
                                }
                                else {
                                        verstka += '<td><img src="img/lamp_deactive.png" class="lamp_active_classifier" rec_id="' + this["id"] + '" active="0"  /></td>';
                                }
                                verstka += '<td><img src="img/delete.png" class="delete_classifier" rec_id="' + this["id"] + '" /></td>';
                                verstka += '</tr>';
                        });
                        verstka += '</tbody>';
                        $('#classifiers').html(verstka);
                }
        });
}

function pause(ms) {
	var date = new Date();
	var curDate = null;
	do { 
		curDate = new Date(); 
	}
	while(curDate-date < ms);
}

function delete_sourfier(val) {
	$.confirm({
		'title'		: 'Удаление записи',
		'message'	: 'Вы точно хотите удалить данную запись?',
		'buttons'	: {
				'Да'	: {
					'class'	: 'blue',
					'action': function(){
//						elem.slideUp();
						param = "delete_sourfier";
						$.ajax({
				                 	type: "POST",
					                url: "templates/model.php",
				                 	data: {"command": param, "record_id" : val},
				                 	success: function(msg){
				                        	data = $.parseJSON(msg);
								if(data == true) {
									pause(100);
									show_all_criteria();
								}
								else {
									alert('Ошибка при удалении записи из БД! Попробуйте внести изменения позже.');
								}
			        	        	}
					        });
					}
				},
				'Нет'	: {
					'class'	: 'gray',
					'action': function(){}	// Nothing to do in this case. You can as well omit the action property.
				}
			}
		});	
}

function delete_source(val) {
        $.confirm({
                'title'         : 'Удаление записи',
                'message'       : 'Вы точно хотите удалить данный источник?',
                'buttons'       : {
                                'Да'    : {
                                        'class' : 'blue',
                                        'action': function(){
                                                param = "delete_source";
                                                $.ajax({
                                                        type: "POST",
                                                        url: "templates/model.php",
                                                        data: {"command": param, "record_id" : val},
                                                        success: function(msg){
                                                                data = $.parseJSON(msg);
                                                                if(data == true) {
                                                                        pause(100);
                                                                        show_all_sources();
                                                                }
                                                                else {
                                                                        alert('Ошибка при удалении записи из БД! Попробуйте внести изменения позже.');
                                                                }
                                                        }
                                                });
                                        }
                                },
                                'Нет'   : {
                                        'class' : 'gray',
                                        'action': function(){}  // Nothing to do in this case. You can as well omit the action property.
                                }
			}
                });
}

function delete_classifier(val) {
        $.confirm({
                'title'         : 'Удаление классификатора',
                'message'       : 'Вы точно хотите удалить данный классификатор?',
                'buttons'       : {
                                'Да'    : {
                                        'class' : 'blue',
                                        'action': function(){
                                                param = "delete_classifier";
                                                $.ajax({
                                                        type: "POST",
                                                        url: "templates/model.php",
                                                        data: {"command": param, "record_id" : val},
                                                        success: function(msg){
                                                                data = $.parseJSON(msg);
                                                                if(data == true) {
                                                                        pause(100);
                                                                        show_all_classifiers();
                                                                }
                                                                else {
                                                                        alert('Ошибка при удалении записи из БД! Попробуйте внести изменения позже.');
                                                                }
                                                        }
                                                });
                                        }
                                },
                                'Нет'   : {
                                        'class' : 'gray',
                                        'action': function(){}  // Nothing to do in this case. You can as well omit the action property.
                                }
                        }
                });
}
function active_sourfier(rec_id, active) {
        $.confirm({
                'title'         : 'Изменение активности записи',
                'message'       : 'Вы точно хотите изменить активность данной записи?',
                'buttons'       : {
                                'Да'    : {
                                        'class' : 'blue',
                                        'action': function(){
//                                              elem.slideUp();
                                                param = "active_sourfier";
                                                $.ajax({
                                                        type: "POST",
                                                        url: "templates/model.php",
                                                        data: {"command": param, "record_id" : rec_id, "active" : active},
                                                        success: function(msg){
                                                                data = $.parseJSON(msg);
                                                                if(data == true) {
                                                                        pause(100);
                                                                        show_all_criteria();
                                                                }
                                                                else {
                                                                        alert('Ошибка при удалении записи из БД! Попробуйте внести изменения позже.');
                                                                }
                                                        }
                                                });
                                        }
                                },
                                'Нет'   : {
                                        'class' : 'gray',
                                        'action': function(){}  // Nothing to do in this case. You can as well omit the action property.
                                }
                        }
                });

}

function active_source(rec_id, active) {
        $.confirm({
                'title'         : 'Изменение активности источника',
                'message'       : 'Вы точно хотите изменить активность данной записи?',
                'buttons'       : {
                                'Да'    : {
                                        'class' : 'blue',
                                        'action': function(){
                                                param = "active_source";
                                                $.ajax({
                                                        type: "POST",
                                                        url: "templates/model.php",
                                                        data: {"command": param, "record_id" : rec_id, "active" : active},
                                                        success: function(msg){
                                                                data = $.parseJSON(msg);
								console.log(data);
                                                                if(data == true) {
                                                                        pause(100);
                                                                        show_all_sources();
                                                                }
                                                                else {
                                                                        alert('Ошибка при удалении записи из БД! Попробуйте внести изменения позже.');
                                                                }
                                                        }
                                                });
                                        }
                                },
                                'Нет'   : {
                                        'class' : 'gray',
                                        'action': function(){}  // Nothing to do in this case. You can as well omit the action property.
				}
                        }
                });

}

function active_classifier(rec_id, active) {
        $.confirm({
                'title'         : 'Изменение активности классификатора',
                'message'       : 'Вы точно хотите изменить активность данного классификатора?',
                'buttons'       : {
                                'Да'    : {
                                        'class' : 'blue',
                                        'action': function(){
                                                param = "active_classifier";
                                                $.ajax({
                                                        type: "POST",
                                                        url: "templates/model.php",
                                                        data: {"command": param, "record_id" : rec_id, "active" : active},
                                                        success: function(msg){
                                                                data = $.parseJSON(msg);
                                                                if(data == true) {
                                                                        pause(100);
                                                                        show_all_classifiers();
                                                                }
                                                                else {
                                                                        alert('Ошибка при удалении записи из БД! Попробуйте внести изменения позже.');
                                                                }
                                                        }
                                                });
                                        }
                                },
                                'Нет'   : {
                                        'class' : 'gray',
                                        'action': function(){}  // Nothing to do in this case. You can as well omit the action property.
                                }
                        }
                });

}

function edit_sourfier(rec_id) {
	param = "edit_sourfier";
        $.ajax({
       		type: "POST",
                url: "templates/model.php",
                data: {"command": param, "record_id" : rec_id},
                success: function(msg){
                	data = $.parseJSON(msg);
			console.log(data[2]);
			$('#edit_source').html(data[2]);
			$('#edit_classifier').html(data[3]);
			$('#addit_edit_active').html(data[4]);
//			$( "#additional_field").html(data);
				$( "#dialog-form-edit" ).dialog( "open" );
		}
	});
}
