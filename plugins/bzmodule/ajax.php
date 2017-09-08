<?
ini_set('display_errors', '1');

//include_once "statusage.php"; //by almaz - usage control
include_once 'init.php';
include_once '../bz_functions/functions_2016-12-20-1.php';

$choose_title = "Необходимо выбрать";
$actionname = $_REQUEST["actionname"];

switch ($actionname) {

    case "spheres":
        $placeid = $_REQUEST["placeid"];
        $onchange = " onchange=\"show_services(" . $placeid . ",this.value);\"";
        echo make_wrapper("Сфера", make_form_select(get_spheres_by_places($placeid),
            'projectid[Сфера]', 'sphereid', false, $onchange, array($choose_title)));
        echo "&nbsp;";
        break;

    case "services":
        $placeid = $_REQUEST["placeid"];
        $sphereid = $_REQUEST["sphereid"];
        if ($sphereid) {
            $onchange = " onchange=\"var placeid='" . $placeid . "'; var sphereid=$('#sphereid').val();show_classificators(placeid,sphereid,this.value);\"";
            echo make_wrapper("Услуга",
                make_form_select(get_services_by_sphere($placeid, $sphereid),
                    'projectid[Услуга]', 'serviceid', false, $onchange, array($choose_title)));
            echo "&nbsp;";
        }
        break;

    case "classificators":
        $placeid = $_REQUEST["placeid"];
        $sphereid = $_REQUEST["sphereid"];
        $serviceid = $_REQUEST["serviceid"];
        $onchange = " onchange=\"var placeid='" . $placeid . "'; var sphereid=$('#sphereid').val(); show_fields(this.value);\"";
        if ($sphereid && $serviceid) {
            echo make_wrapper("Классификатор",
                make_form_select(get_classificators_by_sphere_service($placeid, $sphereid, $serviceid),
                    'projectid[Классификатор]', 'classificatorid', false, $onchange, array($choose_title)));
            echo "&nbsp;";
        }
        break;

    case "sfields":
        $serviceid = $_REQUEST["serviceid"] + 0;
        //  $onchange = " onchange=\"show_sfields(this.value);\"";
        if ($serviceid > 0) {
            echo make_form_input(get_fields_by_service($serviceid), 'service2name', 'service2id', false, $onchange, array('Заполните поля'));
            echo "&nbsp;";
        }
        break;

    case "fields":
        $incidentid = $_REQUEST["incidentid"];
        $onchange = " onchange=\"show_fields(this.value);\"";
        if ($incidentid) {
            //print_r(get_fields_by_service($incidentid));
            echo make_form_input(get_fields_by_incident($incidentid), 'incidentname', 'incidentidid', false, $onchange, array('Заполните поля'));
            echo "&nbsp;";
        }
        echo make_wrapper("Контактный номер заявителя", "<input type='text' name='data[Внешний_номер]' value='' style='width: 450px;'/>");
        echo "&nbsp;";
	echo make_wrapper("Описание инцидента", make_appeal_form());
        break;
}
