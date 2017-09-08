<script type="text/javascript">
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
</script>