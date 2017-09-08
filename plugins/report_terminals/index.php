<?php
include("../connect_db_func.php");
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Отчет по задачам СТИС. Минздрав. Терминалы ЭО и инфоматы.</title>
    <style>
        #ajax_loader .transparency {
            opacity: 0.5;
            filter: alpha(opacity=50);
            -moz-opacity: 0.5;
            background-color: #ffffff;
            width: 100%;
            height: 100%;
            position: absolute;
            top: 0px;
            left: 0px;
            z-index: -1;
        }

        #ajax_loader img {
            position: absolute;
            top: 50%;
            left: 50%;
        }

        #ajax_loader {
            display: none;
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            margin: auto;
            z-index: 10;
        }

    </style>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css">
    <script src="//code.jquery.com/jquery-1.10.2.js"></script>
    <script src="//code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
    <script>
        $(function () {
            $("#tabs").tabs();

            $("#from").datepicker({
                defaultDate: "+1w",
                changeMonth: true,
                numberOfMonths: 3,
                dateFormat: "dd.mm.yy",
                onClose: function (selectedDate) {
                    $("#to").datepicker("option", "minDate", selectedDate);
                }
            });

            $("#to").datepicker({
                defaultDate: "+1w",
                changeMonth: true,
                numberOfMonths: 3,
                dateFormat: "dd.mm.yy",
                onClose: function (selectedDate) {
                    $("#from").datepicker("option", "maxDate", selectedDate);
                }
            });

            $('#get_report').on('click', ".get_report", function () {
                $('.report_link').remove();
                console.log('Я работаю');
                $("#ajax_loader").css("display", "block");
                $.ajax({
                    type: "POST",
                    url: "report_excel.php",
                    data: {"starttime": $('#from').val(), "endtime": $('#to').val()},
                    success: function (msg) {
                        console.log('Статистика сформирована...');
                        data = $.parseJSON(msg);
                        $("#ajax_loader").css("display", "none");
                        $("#get_report").append(data);
                    },
                    error: function (msg_err) {
                        $("#ajax_loader").css("display", "none");
                        alert('Ошибка экспорта отчета. Обратитесь к разработчику системы.');
                    }
                });
            });
        });
    </script>
</head>
<body>
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">О системе</a></li>
        <li><a href="#tabs-2">Выбрать период отчета</a></li>
    </ul>
    <div id="tabs-1">
        Данная система позволяет получить отчет по данным из СУП FengOffice. Выборка ведется только из раздела СТИС.
        Минздрав. Терминалы ЭО и инфоматы.
    </div>
    <div id="tabs-2">
        <div id="get_report">
            <br/>
            <label for="from">&nbsp;Сформировать отчет с: </label>
            <input type="text" id="from" name="from">
            <label for="to">&nbsp;по:</label>
            <input type="text" id="to" name="to">
            <br/>
            <button class="get_report">Получить отчет</button>
            &nbsp;&nbsp;&nbsp;&nbsp;
        </div>
    </div>
</div>
<div id="ajax_loader">
    <div class="transparency">
        <!-- Это прозрачный блок-->
    </div>
    <img src="pacman.gif"/>

</div>

</body>
</html>
