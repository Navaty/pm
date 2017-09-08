<?include_once "statusage.php"; //by almaz - usage control
?><div class="service-block zags">
  <div class="t">
    <div class="b">
      <div class="uform">
	<div class="service-logo">
	  <p style="margin-top:0"><a href="#"><img src="/design/images/feedback/faq.jpg" width="212" height="90" alt="Часто задаваемые вопросы" /></a></p>
	</div>
	<div class="data">
	  <h1>Обратная связь</h1>
	  <h2>Статистика обращений по капитальному ремонту</h2>
	  
	  <script>
	    $(function() {
	    $( ".level0" ).accordion({ collapsible: true, active: false, autoHeight: false });
	    $( ".level1" ).accordion({ collapsible: true, active: false, autoHeight: false });
	    $( ".level2" ).accordion({ collapsible: true, active: false, autoHeight: false });
	    $( ".level3" ).accordion({ collapsible: true, active: false, autoHeight: false });
	    $( ".level4" ).accordion({ collapsible: true, active: false, autoHeight: false });
	    });
	  </script>
	  
	  
	  <div class="as_table_header">
	    Муниципальный район/Городской округ<b>Кол-во обращений</b>
	  </div>
	  <div class="level0" id="visits">
	    <h3><a href="#" onfocus="this.blur()">Казань</a><b>10500</b></h3>
	    <div>
	      <div class="level1">
		<h3><a href="#">Приволжский район</a><b>500</b></h3>
		<div>
		  <div class="level2">
		    <h3><a href="#">ул. Амирхана</a><b>300</b></h3>
		    <div>
		      <div class="level3">
			<h3><a href="#">Дом № 19</a><b>200</b></h3>
			<div>
			  <div class="level4">
			    <h3><a href="#">1 обращение</a></h3>
			    <div>
			      Премьер-министр Владимир Путин впервые вступился за бывшего министра финансов Алексея Кудрина. По словам главы правительства, Кудрин продолжит "работать в команде". Алексей Кудрин был отправлен в отставку после заявлений о том, что он не согласен с некоторыми пунктами бюджетной политики Дмитрия Медведева.
			    </div>
			    <h3><a href="#">2 обращение</a></h3>
			    <div>
			      Премьер-министр Владимир Путин впервые вступился за бывшего министра финансов Алексея Кудрина. По словам главы правительства, Кудрин продолжит "работать в команде". Алексей Кудрин был отправлен в отставку после заявлений о том, что он не согласен с некоторыми пунктами бюджетной политики Дмитрия Медведева.
			    </div>
			    <h3><a href="#">3 обращение</a></h3>
			    <div>
			      Премьер-министр Владимир Путин впервые вступился за бывшего министра финансов Алексея Кудрина. По словам главы правительства, Кудрин продолжит "работать в команде". Алексей Кудрин был отправлен в отставку после заявлений о том, что он не согласен с некоторыми пунктами бюджетной политики Дмитрия Медведева.
			    </div>
			  </div>
			</div>
			<h3><a href="#">Дом № 29</a><b>100</b></h3>
			<div><p>Текст для дома №29</p></div>
			
		      </div>
		    </div>
		    
		    <h3><a href="#">ул. Ямашева</a><b>200</b></h3>
		    <div>Список домов</div>
		  </div>
		</div>
		
		<h3><a href="#">Новосавиновский район</a><b>10000</b></h3>
		<div>Номера домов...</div>
	      </div>
	    </div>
	    <h3><a href="#">Набережные Челны</a></h3>
	    <div>Second content</div>
	  </div>
	  
	  
	  <?#php $this->renderPartial('/partials/service_process/navigation', array('buttons' => array('back' => 'Назад'))) ?>
	  
	</div>
      </div>
    </div>
  </div>
</div>