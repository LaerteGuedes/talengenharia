jQuery.ajaxSetup({async: false});
jQuery.getScript('/library/jquery-ui-1.10.2.custom/jquery-ui-1.10.2.custom.min.js');
jQuery.ajaxSetup({async: true});
$(function(){
	$('.jData').datepicker({
		  dateFormat: 'dd/mm/yy',
		  buttonImage: "/imagem/icone/calendar.png",
		  showOn: "button",
//		  buttonImageOnly: true,
		  dayNamesMin: ['Do', '2ª', '3ª', '4ª', '5ª', '6ª', 'Sa'],
		  monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro']
		  });
//	 $('.jData').mask('99/99/9999 99:99');

//	$('.jDataehora').on("click", function(){
//	  $(this).datepicker({
//		duration: '',
//		showTime: true,
//		time24h: true,
//		buttonImage: "../../imagem/icone/calendar.png",
//		constrainInput: false,
//		dateFormat: 'dd/mm/yy',
//		dayNamesMin: ['Do', '2ª', '3ª', '4ª', '5ª', '6ª', 'Sa'],
//		monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro']
//		}).focus();
//	});
});
