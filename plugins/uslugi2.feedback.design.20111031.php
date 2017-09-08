<?
include_once "statusage.php"; //by almaz - usage control
?><h1><?#$this->controllerTitle ?></h1>
<?# $this->renderPartial('partials/pm_feedback') ?>

----------

<style>
  .uform select{
  font-size:15px;
  height:26px
  }
  
  .uform input[type="text"]{
  font-size:15px;
  width:240px
  }
  
  .uform p.f-hint{
  padding-left:170px
  }
  
  .uform .h label:first-child{
  width:170px;
  }
  
  .uform .h label:first-child span{
  position:relative;
  top:2px;
  }
  
  .uform textarea{
  font-size:15px;
  width:400px;
  font-family:tahoma
  }
</style>
<div class="uform" style="font-size:14px">
  <div class="h">
    <label>
      <span>Вид обращения:</span>
    </label>
    <select name="Name">
      <option value="value1">Благодарность</option>
      <option value="value1">Жалоба</option>
      <option value="value1">Заявление</option>
      <option value="value1">Обращение</option>
      <option value="value1">Поздравление</option>
      <option value="value1">Предложение</option>
      <option value="value1">Приглашение</option>
      <option value="value1">Просьба</option>
      <option value="value1">Рапорт</option>
    </select>
  </div>
  
  <div class="h" style="margin:5px 0 5px 170px;">
    <input name="Name" type="radio" value="" style="width:auto;border:0" id="in1" checked="checked"><label for="in1" style="background:none;width:auto;float:none">По электронной услуге</label> &nbsp;&nbsp;
    <input name="Name" type="radio" value="" style="width:auto;border:0" id="in2"><label for="in2" style="background:none;width:auto;float:none">По жизненной ситуации</label>
  </div>
  
  
  <div class="h">
    <label>
      <span>Тема обращения:</span>
    </label>
    <select name="Name2">
      <option value="value1">Проверка и оплата штрафов ГИБДД</option>
      <option value="value1">Регистрация транспортного средства</option>
      <option value="value1">Гостехосмотр транспортных средств</option>
      <option value="value1">Замена водительского удостоверения</option>
      <option value="value1">Судебные приставы: проверка и оплата задолженности</option>
      <option value="value1">Оплата счетов</option>
      <option value="value1">Оплата страховых взносов</option>
      <option value="value1">Получение загранпаспорта</option>
      <option value="value1">Постановка на учет в детский сад</option>
      <option value="value1">Запись на прием к врачу</option>
      <option value="value1">Регистрация заключения брака</option>
      <option value="value1">Получение выписки из ЕГРП</option>
      <option value="value1">Получение выписки из ГКН</option>
      <option value="value1">Оплата штрафов Роспотребнадзора</option>
      <option value="value1">Оплата соципотеки</option>
      <option value="value1">Проверка и оплата налоговой задолженности</option>
      <option value="value1">Проверка стадии рассмотрения обращения</option>
      <option value="value1">Информация о капремонте</option>
      <option value="value1">Проверка решения на получение разрешения на строительство</option>
      <option value="value1">Благотворительные взносы</option>
      <option value="value1">Подача заявок на получение грантов (субсидий) в АИР РТ</option>
      <option value="value1">Налоговый калькулятор</option>
      <option value="value1">Оплата страховых взносов (для ИП)</option>
      <option value="value1">Проверка легальности алкогольной продукции</option>
    </select>
  </div>
  
  <div class="h">
    <label>
      <span>Тема обращения:</span>
    </label>
    <select name="Name2">
      <option value="value1">Финансовые вопросы</option>
      <option value="value1">Экономическая реформа и приватизация</option>
      <option value="value1">Вопросы промышленности</option>
      <option value="value1">Нарушения сотрудниками ОВД</option>
      <option value="value1">Вопросы строительства и архитектуры</option>
      <option value="value1">Вопросы транспорта</option>
      <option value="value1">Вопросы связи</option>
      <option value="value1">Вопросы труда и зарплаты</option>
      <option value="value1">Агропромышленный комплекс</option>
      <option value="value1">Экология</option>
      <option value="value1">Жилищные вопросы</option>
      <option value="value1">Коммунально-бытовое обслуживание</option>
      <option value="value1">Социальное обеспечение и социальная защита населения</option>
      <option value="value1">Наука, культура, информация</option>
      <option value="value1">Образование</option>
      <option value="value1">Вопросы здравоохранения</option>
      <option value="value1">Торговля и потребительские услуги</option>
      <option value="value1">Обеспечение законности и правопорядка</option>
      <option value="value1">Служба в Вооруженных силах</option>
      <option value="value1">Государство, общество и политика</option>
      <option value="value1">Вопросы СНГ и субъектов России</option>
      <option value="value1">Обращения иностранных граждан</option>
      <option value="value1">Приветствия, поздравления, соболезнования</option>
      <option value="value1">Работа с обращениями граждан</option>
      <option value="value1">Другие вопросы</option>
      <option value="value1">Видеофиксация</option>
      <option value="value1">Предложения по улучшению дорожного движения</option>
    </select>
  </div>
  
  
  <div class="h">
    <label>
      <span>Сообщение:</span>
    </label>
    <textarea rows="10"></textarea>
  </div>
  
  
  <div class="h" style="margin-top:10px">
    <b>Контактная информация</b>
  </div>
  <div class="h">
    <label>
      <span>Фамилия: <ins class="rq">*</ins></span>
    </label>
    <input class="ss-small" id="doc_nm" name="doc_nm" type="text" value="" />
  </div>
  <div class="h">
    <label>
      <span>Имя: <ins class="rq">*</ins></span>
    </label>
    <input class="ss-small" id="doc_nm" name="doc_nm" type="text" value="" />
  </div>
  <div class="h">
    <label>
      <span>Отчество: </span>
    </label>
    <input class="ss-small" id="doc_nm" name="doc_nm" type="text" value="" />
  </div>
  <div class="h">
    <b>Почтовый адрес</b>
  </div>
  <div class="h">
    <label>
      <span>Индекс:</span>
    </label>
    <input class="ss-small" id="doc_nm" name="doc_nm" type="text" value="" />
  </div>
  <div class="h">
    <label>
      <span>Район:</span>
    </label>
    <input class="ss-small" id="doc_nm" name="doc_nm" type="text" value="" />
  </div>
  <div class="h">
    <label>
      <span>Город/Поселение:</span>
    </label>
    <input class="ss-small" id="doc_nm" name="doc_nm" type="text" value="" />
  </div>
  <div class="h">
    <label>
      <span>Улица:</span>
    </label>
    <input class="ss-small" id="doc_nm" name="doc_nm" type="text" value="" />
  </div>
  <div class="h">
    <label>
      <span>Корпус:</span>
    </label>
    <input class="ss-small" id="doc_nm" name="doc_nm" type="text" value="" />
  </div>
  <div class="h">
    <label>
      <span>Дом:</span>
    </label>
    <input class="ss-small" id="doc_nm" name="doc_nm" type="text" value="" />
  </div>
  <div class="h">
    <label>
      <span>Квартира:</span>
    </label>
    <input class="ss-small" id="doc_nm" name="doc_nm" type="text" value="" />
  </div>
  <div class="h">
    <label>
      <span>Контактный телефон:</span>
    </label>
    <input class="ss-small" id="doc_nm" name="doc_nm" type="text" value="" />
    <p class="f-hint">Укажите полный номер мобильного телефона<br />или городской номер с указанием кода города</p>
  </div>
  <div class="h">
    <label>
      <span>Место работы/учебы:</span>
    </label>
    <input class="ss-small" id="doc_nm" name="doc_nm" type="text" value="" />
  </div>
  <div class="h">
    <label>
      <span>E-mail:</span>
    </label>
    <input class="ss-small" id="doc_nm" name="doc_nm" type="text" value="" />
  </div>
</div>
