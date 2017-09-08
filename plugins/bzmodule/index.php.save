<?php
ini_set('display_errors', '1');
include_once 'init.php';
include("../functions.php");

$file = 'etwas222.txt';

file_put_contents($file, print_r($_REQUEST, true));


$fields = get_additional_fields();
$spheres = get_spheres();
$places = get_places2();
?><!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Карта жителя</title>

    <link href="http://cc.citrt.net/oktell/css/default/jquery-ui-1.8.7.custom.css" type="text/css" rel="stylesheet"/>

    <!-- Path to Template7 lib -->
    <script src="ext-libraries/Template7-1.5.2/template7.min.js"></script>

    <!-- JSON with demo data-->

    <script src="https://code.jquery.com/jquery-2.2.4.min.js"
            integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
    <script src="http://cc.citrt.net/oktell/js/jquery.blockUI.js"></script>
    <script src="http://cc.citrt.net/oktell/js/jquery-ui.custom.min.js"></script>
    <script src="http://cc.citrt.net/oktell/js/jquery.ui.datepicker-ru.js"></script>
    <script src="http://cc.citrt.net/oktell/js/jquery-ui-timepicker-addon.js"></script>
    <script src="http://cc.citrt.net/oktell/js/modules/autoresize.jquery.min.js"></script>


</head>
<body>
<script src="bzmodule.js" type="text/javascript"></script>
<div id="places" class="buttons">
    <? #=show_places($places); ?>
</div>
<script>
    show_spheres(5919);
</script>
<br/>
<form id="form_incidents2">
    <fieldset class="ui-widgeta ui-widget-contenta">
        <legend>Регистрация инцидента</legend>
        <input type="hidden" name="projectid[Источник]" value="5919"/>
        <input type="hidden" name="isname" value="incidents2"/>
        <input type="hidden" id="opengoosphereid" name="projectid[Источник]"/>
        <input type="hidden" name="title" value="Карта жителя">

        <div id="infomatplaces">1</div>
        <div id="spheres">2</div>
        <div id="services">3</div>
        <div id="classificators">4</div>
        <div id="sfields">5</div>
        <div id="fields">6</div>
        <div id="incidents_action">7</div>
    </fieldset>
</form>

<div id="rendered-time"></div>
<div id="content-wrap"></div>
<div id="reminder" title="Уведомление"></div>
<script type="text/template" id="show-template">
    <input type="hidden" name="title" value="Карта жителя">
    <div class="bg" style="background-image:url({{images.fanart}})"></div>
    <div class="header" style="background-image:url({{images.fanart}})">
        <div class="gradient"></div>
        <div class="left">
            <div class="genres">{{join genres delimeter=", "}}</div>
            <div class="year">{{year}}</div>
            <h1>{{title}}</h1>
        </div>
        <ul class="props">
            <li>
                <b>Airs:</b> {{air_day}} at {{air_time}} on {{network}}
            </li>
            <li>
                <b>Runtime:</b> {{runtime}}m
            </li>
            <li>
                <b>Country:</b> {{country}}
            </li>
            <li>
                <b>Status:</b> {{status}}
            </li>
        </ul>
    </div>
    <div class="overview">
        <p>{{overview}}</p>
    </div>
    {{#each seasons reverse="true"}}
    <div class="season{{#if @first}} season-first{{/if}}{{#if @last}} season-last{{/if}}" data-index="{{@index}}">
        <div class="season-title">Season {{season}}</div>
        {{#episodes}}
        <div class="episode" data-index="{{@index}}">
            <div class="pic">
                {{#if screen}}
                <img src="{{screen}}">
                {{/if}}
            </div>
            <div class="info">
                <div class="episode-title">Episode {{episode}}{{#if title}}: {{title}}{{/if}}</div>
                {{#if first_aired_iso}}
                <div class="date">{{formatDate first_aired_iso}}</div>
                {{/if}}
                {{#if ratings.percentage}}{{#if ratings.votes}}
                <div class="ratings">
                    <span>{{ratings.percentage}}%</span>
                    <span>{{ratings.votes}} votes</span>
                </div>
                {{/if}}{{/if}}
                {{#if overview}}
                <p>{{overview}}</p>
                {{/if}}
            </div>
        </div>
        {{/episodes}}
    </div>
    {{/each}}

</script>

</body>
</html>
