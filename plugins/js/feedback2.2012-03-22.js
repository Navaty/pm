<script type="text/javascript">

$(function(){
    $('.datepicker').datepicker();
    
    $('.max280').live('click',function() {
	var mid = this.id;
	alert(mid);
//	$(this).limit('280','#ch'+mid);
//	$('#".$rid."').limit('280','#".$chid."');
    });



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

//    $('.buttonset').button();

    $('#problem_id').bind('change', function() {
        $('input[name="problem_name"]').val($('option[value="' + $(this).val() + '"]', $(this)).text());
    });

    $('#org_obr_id').bind('change', function() {
        $('input[name="org_obr"]').val($('option[value="' + $(this).val() + '"]', $(this)).text());
    });

    var problem_descriptions = {868:'Замечания и предложения по работе Портала/по электронной услуге «Постановка на учет в детский\
сад»',2176:'Причины изменения состояния очереди ',861:'Как добавить детский сад; \nКак изменить приоритетные детские сады\n\n ',2175:\
				'Вопрос по льготным категориям',914:'Ошибка в Ф.И.О.; \nНа портале отображается неверный статус;\nНа портале  ображаются не все детски\
е сады\n',878:'После ввода серии и номера свидетельства о рождении ребенка на Портале получили сообщение «Не существует»'};

    $('#problem_id').change(function() {
        if (problem_descriptions[$(this).val()].toLowerCase() != 'нет описания')
        {
            $('.f-hint', $(this).parent()).text(problem_descriptions[$(this).val()]);
        }
    });
});
</script>
<script language="JavaScript" src="/javascript/lib/suggest-text-field.js?402110641" type="text/javascript"></script>
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
//    $('#faq-' + fieldID).css('position','fixed');
//    $('#faq-' + fieldID).css('top','10');
//    $('#faq-' + fieldID).css('width','200px');
}

function goto_faq() {
    //перейти на раздел ЧаВo
    $("#feedback").hide("slide",function() {
        $("#allfaq_content").css("position","relative");
        $("#allfaq_content").css("left","-710px");
        $("#allfaq_content").css("width","680px");
	$("#allfaq_content").css("color","black");
        $(".faqs").show("slow");
        $("#allfaq_button").hide(); // скрываем кнопку перейти на раздел чаво
        $("#stat").hide(); // скрываем кнопку перейти на раздел чаво
        $("#newfeedback").show(); //показываем кнопку хочу задать вопрос
    })
}

function open_sphere(SphereID,SphereNAME) {
//    alert(SphereNAME);
    $('#SphereID').html("Вы выбрали ключевое слово: <font color=red>"+SphereNAME+"</font>");
    $('#SphereID2').html("Заполните дополнительные поля описания ключевого слова");
    $("#message").removeAttr('disabled');
    $(".contact").removeAttr('disabled');
    $("#send_message_form").show();
    var html = $('#pc'+SphereID).html();
    //alert(html);
    $('#Services').html(html);
    $(this).css('font-color','red');
    test2();
}

function goto_feedback() {
    $("#feedback").show("slow",function() {
//        $("#feedback").css("width","660px");
        $("#allfaq_content").css("width","");
        $("#allfaq_content").css("position","");
        $("#allfaq_content").css("left","");
	$("#allfaq_content").css("color","");
        $("#allfaq_button").show();
        $("#stat").show();
        $(".faqs").hide();
        $(".inputs_hide").hide();
        $("#newfeedback").hide(); //показываем кнопку хочу задать вопрос
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
