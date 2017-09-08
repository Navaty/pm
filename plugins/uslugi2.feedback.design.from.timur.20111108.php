<?
include_once "statusage.php"; //by almaz - usage control
?><script type="text/javascript">
    $(function(){
	$('.datepicker').datepicker();

	$('a', birthDate.parent()).bind('click', function() {
	    birthDate.datepicker('show');
	    return false;
	});

	$('#child_document_type').bind('change', function() {
	    var documentSeries = $('#child_document_series');
	    var documentNumber = $('#child_document_number');

	    if (this.value == 0) {
		// Свидетельство о рождении

		documentSeries.removeAttr('required');
		documentSeries.attr('pattern', '^[а-яА-Яa-zA-Z0-9\\-]+$');
		documentSeries.attr('data-message', 'Серия документа не должна содержать пробелов');

		documentNumber.removeAttr('required');
		documentNumber.attr('pattern', '^\\d+$');
		documentNumber.attr('data-message', 'Номер документа должен состоять только из цифр');

	    } else {
		// Иное
		documentSeries.removeAttr('pattern');
		documentSeries.removeAttr('data-message');
		documentSeries.attr('required', 'required');

		documentNumber.removeAttr('pattern');
		documentNumber.removeAttr('data-message');
		documentNumber.attr('required', 'required');
	    }
	});
	$('#child_document_type').trigger('change');

	$.tools.validator.localize('ru', {
	    '[required]': 'Пожалуйста, заполните поле'
	});

	$("#feedbackForm").validator({
	    lang: 'ru',
	    messageClass: 'formFieldError',
	    offset: [0, 2],
	    errorInputEvent: 'change'

	}).bind('onFail', function(event, errorObjects) {

	    for (i in errorObjects) {
		var currentError = errorObjects[i];
		window.scrollTo(0, currentError.input.offset().top - 50);
		break;
	    }

	});


        // Район
        new SuggestTextField({
            textField:'#address_area_name',
            valueField:'#address_area_code',
            sourceUrl:'/ajax/area-id-list',
            depends: {
                region_id: '#region_id'
            }
        });

        // Город проживания
        new SuggestTextField({
            textField:'#address_location_name',
            valueField:'#address_location_code',
            sourceUrl:'/ajax/city-id-list',
            depends:{
                district_id:'#address_area_code',
                region_id:    '#region_id'
            }
        });

        // Улица
        new SuggestTextField({
            textField:'#address_street_name',
            valueField:'#address_street_code',
            sourceUrl:'/ajax/street-id-list',
            depends:{
                city_id: '#address_location_code'
            }
        });

        $('#problem_id').bind('change', function() {
            $('input[name="problem_name"]').val($('option[value="' + $(this).val() + '"]', $(this)).text());
        });

        $('#org_obr_id').bind('change', function() {
            $('input[name="org_obr"]').val($('option[value="' + $(this).val() + '"]', $(this)).text());
        });

        var problem_descriptions = {868:'Замечания и предложения по работе Портала/по электронной услуге «Постановка на учет в детский сад»',2176:'Причины изменения состояния очереди ',861:'Как добавить детский сад; \nКак изменить приоритетные детские сады\n\n ',2175:'Вопрос по льготным категориям',914:'Ошибка в Ф.И.О.; \nНа портале отображается неверный статус;\nНа портале  ображаются не все детские сады\n',878:'После ввода серии и номера свидетельства о рождении ребенка на Портале получили сообщение «Не существует»'};

        $('#problem_id').change(function() {
            if (problem_descriptions[$(this).val()].toLowerCase() != 'нет описания')
            {
                $('.f-hint', $(this).parent()).text(problem_descriptions[$(this).val()]);
            }
        });
    });
</script><script language="JavaScript" src="/javascript/lib/suggest-text-field.js?402110641" type="text/javascript"></script>
<script language="JavaScript" src="/javascript/lib/views/partials/payer-information.js?402110641" type="text/javascript"></script>
<script>
    function feedback_show_theme(ID) {
	if(ID==1) { showid = 1; hideid = 2;}
	else      { showid = 2; hideid = 1;}
	$('#theme_'+hideid).hide();
	$('#theme_'+showid).show();
    }
function show_feedback(fieldID) {
    $('.inputs_hide').hide();
    $('#inputfields-' + fieldID).show('slow');
    $('#faq-' + fieldID).show('slow');
}

function goto_faq() {
    //перейти на раздел ЧаВo
    $("#feedback").hide("slide",function() {
        $("#faq").css("width","990px");
        $(".faqs").show("slow");
        $("#allfaq").hide(); // скрываем кнопку перейти на раздел чаво
        $("#newfeedback").show(); //показываем кнопку хочу задать вопрос
    })
}

function goto_feedback() {
    $("#feedback").show("slow",function() {
	$("#feedback").css("width","660px");
	$("#faq").css("width","330px");
	$("#allfaq").show();
	$(".faqs").hide();
	$(".inputs_hide").hide();
    })
}


$(function() {

    new SuggestTextField({
        textField:                          '#region_name',
        valueField:                         '#region_id',
        sourceUrl:                          '/ajax/region-id-list'
    });


    new SuggestTextField({
	textField:                          '#district_name',
	valueField:                         '#district_id',
	sourceUrl:                          '/ajax/area-id-list',
	depends:                            {
	    region_id: '#region_id'
	}
    });

    new SuggestTextField({
	textField:                          '#city_name',
	valueField:                         '#city_id',
        sourceUrl:                          '/ajax/city-id-list',
	depends:                            {
	    region_id: '#region_id',
	    district_id: '#district_id'
	}
    });

    new SuggestTextField({
	textField:                            '#street_name',
	valueField:                           '#street_id',
	sourceUrl:                          '/ajax/street-id-list',
	depends:                            {
	    city_id: '#city_id'
	}
    });
});

</script>
<h1><?#php echo $this->controllerTitle ?></h1>

<div class="service-block zags">
  <div class="t">
    <div class="b">
      <div class="uform">
        <div class="service-logo">
	  <p style="margin-top:0">
	    <a href="#"  onclick="javascript: window.location.href = '/feedback/new-form?serviceid=2040&amp;themeid=2314';">
	      <img src="/design/images/feedback/faq.jpg" width="212" height="90" alt="Часто задаваемые вопросы" />
	    </a>
	  </p>
	  <a href='/feedback/new-form?&stat=true'><img src='/design/images/feedback/stat.jpg'/></a>
	</div>
	<div class="data" style="font-size:14px">
	  <h2>
	    Уважаемый посетитель!
	  </h2>
	  <p>
	    Чтобы получить оперативный и квалифицированный ответ на Ваш вопрос, обязательно заполните все поля формы. Лаконично и грамотно сформулируйте текст Вашего обращения.
	  </p>
	  <p>
	    <b>В случае если Ваш вопрос относится к компетенции Портала государственных и муниципальных услуг</b>, то Ваше обращение будет рассмотрено в течение 3-х рабочих дней с момент его регистрации.
	  </p>
	  <p>
	    <b>Если Ваш вопрос относится к компетенции Правительства Республики Татарстан</b>, то Ваше обращение будет рассмотрено в течение 30 дней с момента его регистрации.
	    <p/>
	    <p>
	      Прежде чем отправить обращение через Интернет-приемную, рекомендуем Вам просмотреть раздел <a href="#"  onclick="javascript: window.location.href = '/feedback/new-form?serviceid=2040&amp;themeid=2314';">"Часто задаваемые вопросы"</a>. Возможно, Вы сразу найдете информацию на интересующую Вас тему.
	    </p>
	    <p>
	      <b style="color:#ff0000">ВНИМАНИЕ!</b> В случае, если в письменном обращении не указаны фамилия гражданина, направившего обращение, и почтовый адрес, по которому должен быть направлен ответ, ответ на обращение не дается. (<a href='http://prav.tatar.ru/rus/fz59.htm'>Федеральный закон РФ от 2 мая 2006г. № 59-ФЗ О порядке рассмотрения обращений граждан Российской Федерации</a>)
	    </p>
	    
	    <form action="" method="POST">
	      <input type="hidden"                            name="mainprojectname"       value="Услуга" />
	      <input type="hidden" id="region_id"             name="region_id"             value="12" />
	      <input type="hidden" id="address_region_code"   name="address_region_code"   value="1600000000000" />
	      <input type="hidden" id="address_area_code"     name="address_area_code"     value="" />
	      <input type="hidden" id="address_location_code" name="address_location_code" value="" />
	      <input type="hidden" id="address_street_code"   name="address_street_code"   value="" />

	      <div class="alone-rpt">
		<div class="alone-top"><hr></div>
		<b>Укажите адрес дома, по которому Вы хотите оставить обращение</b>
		<div class="h num-lines-2">
		  <label for="district_name">
		    <span>Муниципальный район / городской округ:<ins class="rq">*</ins></span>
		  </label>
		  <input type="hidden" id="district_id" name="district_id" value=""/>
		  <select id="district_name" name="district_name" idA="Район" nameA="addons[Район]" class="s-large">
		    <option value="">Нажмите для выбора</option>
		    <option value="554014">г.Казань</option>
		    <option value="Набережные Челны">г.Набережные Челны</option>
		    <option value="Агрызский">Агрызский</option>
		    <option value="Азнакаевский">Азнакаевский</option>
		    <option value="Азнакаевский">Аксубаевский</option>
		    <option value="Актанышский">Актанышский</option>
		    <option value="Алексеевский">Алексеевский</option>
		    <option value="Алькеевский">Алькеевский</option>
		    <option value="Альметьевский">Альметьевский</option>
		    <option value="Апастовский">Апастовский</option>
		  </select>
		</div>
		
		<div class="h num-lines-2">
		  <label for="Населенный пункт">
		    <span>Населенный пункт / район города:<ins class="rq">*</ins></span>
		  </label>
		  <input id="Населенный пункт" name="addons[Населенный пункт]" value="" class="s-medium">
		  </div>
		  
		  <div class="h">
		    <label for="Улица">
		      <span>Улица:<ins class="rq">*</ins></span>
		    </label>
		    <input id="Улица" name="addons[Улица]" value="" class="s-medium">
		    </div>
		    
		    <div class="h">
		      <label for="Дом">
			<span>Дом:<ins class="rq">*</ins></span>
		      </label>
		      <input id="Дом" name="addons[Дом]" value="" class="s-medium">
		      </div>
		      
		      <div class="h">
			<label for="Корпус">
			  <span>Корпус:<ins class="rq">*</ins></span>
			</label>
			<input id="Корпус" name="addons[Корпус]" value="" class="s-medium">
                        </div>
		      </div>
		      <div class="alone-bottom"><hr></div>
		      
		      <br>
			
			<div class="alone-rpt">
			  <div class="alone-top"><hr></div>
			  <h2>Описание обращения</h2>
			  <div class="h">
                            <label for="vid_obr">
			      <span>Вид обращения:<ins class="rq">*</ins></span>
                            </label>
			    <select name='appeal_type' id="vid_obr">
			      <option value=''>Нажмите для выбора</option>
			      <option value='2359'>Благодарность</option>
			      <option value='2360'>Жалоба</option>
			      <option value='2361'>Заявление</option>
			    </select>
			  </div>
			  
			  <div class="h" style="margin:5px 0">
                            <label for="theme_type">
			      <span>Тип:<ins class="rq">*</ins></span>
                            </label>
			    <div id='theme_type' class='buttonset'>
			      <input type='radio' id='theme_type_1' name='theme_type'  onclick='feedback_show_theme(1);'/><label for='theme_type_1'>По электронной услуге</label>&nbsp;&nbsp;
			      <input type='radio' id='theme_type_2' name='theme_type'  onclick='feedback_show_theme(2);'/><label for='theme_type_2'>По жизненной ситуации</label>
			    </div>
			  </div>
			  
			  <style>
			    #theme_type label{
			    background:none;
			    float:none
			    }
			    
			    #theme_type input{
			    width:auto;
			    margin-right:5px;
			    border:0;
			    }
			  </style>
			  
			  <div class="h" id='theme_2' style="display:none">
                            <label for="themeid">
			      <span>Тема обращения:<ins class="rq">*</ins></span>
                            </label>
			    <select id="themeid" name="projectid[Тема_обращения]" class="s-large">
			      <option value=''>Нажмите для выбора</option>
			      <optgroup label='01. Финансовые вопросы'>
				<option value='2367' title='01.03. Налоговая служба, сборы и штрафы' class='theme' >01.03. Налоговая служба, сборы и штрафы</option>
				<option value='2368' title='01.03. Налоговая служба, сборы и штрафы' class='theme' >01.03. Налоговая служба, сборы и штрафы</option>
			      </optgroup>
			      <optgroup label='02. Экономическая реформа и приватизация'>
				<option value='2369' title='02.01. Экономическая реформа и рыночные отношения. Борьба с монополией.' class='theme' >02.01. Экономическая реформа и рыночные отношения. Борьба с монополией.</option>
				<option value='2370' title='02.06. Приватизация строительных организаций и жилищно-коммунального хозяйства' class='theme' >02.06. Приватизация строительных организаций и жилищно-коммунального хозяйства</option>
			      </optgroup>
			    </select>
			  </div>
			  
			  <div class="h" id='theme_1' style="display:none">
                            <label for="serviceid">
			      <span>Услуги:<ins class="rq">*</ins></span>
                            </label>
			    <select id="serviceid" name="projectid[Услуга]" onchange="show_feedback(this.value);" class="s-large">
			      <option value=''>Нажмите для выбора</option>
			      <optgroup label='ГИБДД'>
				<option value='2365' title='Выдача водительского удостоверения' >Выдача водительского удостоверения</option>
				<option value='2366' title='Проверка и оплата штрафов ГИБДД' >Проверка и оплата штрафов ГИБДД</option>
			      </optgroup>
			    </select>
			  </div>
			  
			  
			  <div class="h">
                            <label for="message">
			      <span>Сообщение:<ins class="rq">*</ins></span>
                            </label>
                            <textarea name="appeal" rows="8" style="font:14px Tahoma;padding: 3px;" id="message"></textarea>
			  </div>
			  
			  <div id='inputfields-2365' class='inputs_hide' style='display:none;'></div>
			  <div id='inputfields-2366' class='inputs_hide' style='display:none;'></div>
			  
			</div>
			<div class="alone-bottom"><hr></div>
			
			
			<br>
			  
			  <div class="alone-rpt">
			    <div class="alone-top"><hr></div>
			    <h2>Контактные данные</h2>
			    <div class="h">
			      <label for="surname">
				<span>Фамилия:<ins class="rq">*</ins></span>
			      </label>
			      <input id="surname" name="data[Фамилия]" type="text" class="s-medium" value=""/>
			    </div>
			    <div class="h">
			      <label for="name">
				<span>Имя:<ins class="rq">*</ins></span>
			      </label>
			      <input id="name" name="data[Имя]" type="text" class="s-medium" value=""/>
			    </div>
			    <div class="h">
			      <label for="farther_name">
				<span>Отчество:<ins class="rq">*</ins></span>
			      </label>
			      <input id="farther_name" name="data[Отчество]" type="text" class="s-medium" value=""/>
			    </div>
			    
			    <div class="h">
			      <label for="tel">
				<span>Контактый телефон:<ins class="rq">*</ins></span>
			      </label>
			      <input id="tel" name="data[Контактый_телефон]" type="text" class="s-medium" value=""/>
			      <p class="f-hint">Укажите полный номер мобильного телефона или городской номер с указанием кода города</p>
			    </div>
			    <div class="h">
			      <label for="job">
				<span>Место работы или учебы:</span>
			      </label>
			      <input id="job" name="data[Место_работы_или_учебы]" type="text" class="s-medium" value=""/>
			    </div>
			    <div class="h">
			      <label for="email">
				<span>E-mail:</span>
			      </label>
			      <input id="email" name="data[E-mail]" type="text" class="s-medium" value=""/>
			    </div>
			    
			    <p><strong><em>Почтовый адрес:</em></strong></p>
			    
			    <div class="h">
			      <label for="zip">
				<span>Индекс:</span>
			      </label>
			      <input id="zip" name="data[Почтовый_адрес_Индекс]" type="text" class="s-medium" value=""/>
			    </div>
			    <div class="h">
			      <label for="region">
				<span>Район:<ins class="rq">*</ins></span>
			      </label>
			      <input id="region" name="data[Почтовый_адрес_Район]" type="text" class="s-medium" value=""/>
			    </div>
			    <div class="h">
			      <label for="city">
				<span>Город/Поселение:<ins class="rq">*</ins></span>
			      </label>
			      <input id="city" name="data[Почтовый_адрес_Город]" type="text" class="s-medium" value=""/>
			    </div>
			    <div class="h">
			      <label for="street">
				<span>Улица:<ins class="rq">*</ins></span>
			      </label>
			      <input id="street" name="data[Почтовый_адрес_Улица]" type="text" class="s-medium" value=""/>
			    </div>
			    <div class="h">
			      <label for="corp">
				<span>Корпус:</span>
			      </label>
			      <input id="corp" name="data[Почтовый_адрес_Корпус]" type="text" class="s-medium" value=""/>
			    </div>
			    <div class="h">
                            <label for="house">
			      <span>Дом:</span>
                            </label>
			    <input id="house" name="data[Почтовый_адрес_Дом]" type="text" class="s-medium" value=""/>
			    </div>
			    <div class="h">
			      <label for="apartment">
				<span>Квартира:</span>
			      </label>
			      <input id="apartment" name="data[Почтовый_адрес_Квартира]" type="text" class="s-medium" value=""/>
			    </div>
			  </div>
			  <div class="alone-bottom"><hr></div>
			</div>
			
			<div class="extra-submit-text clearfix">
			  <p class="back" id="backStageButton" onclick="javascript:history.back()">
			    <ins><input type="button" value="">Назад</ins>
			  </p>
			  
			  <p class="next-stage"><ins><input type="submit" value="" name="action_send">Отправить</ins></p>
			</div>
			
		      </form>
		      
		    </div>
		  </div>
		</div>
	      </div>
	      -----------
	      <h1><?#= $this->controllerTitle ?></h1>
	      <#? $this->renderPartial('partials/pm_feedback') ?>