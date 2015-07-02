
$(function(){
	function vetificaAtivo(){
		if($(this).is(':checked')){
			$(this).next().css({visibility:'visible',position:'relative'});
		}else{
			$(this).next().css({visibility:'hidden',position:'absolute'});
		}
	}
	$('.jMapaAtiva').each(vetificaAtivo).click(vetificaAtivo);
	
	jQuery.fn.gShowMap = function(settings){
		var defaults = {
			width:'400',
			height:'300',
			lat:'',
			lon: '',
			icon: '',
			msg: ''
		};
		var settings = $.extend(defaults, settings);
		if(settings.lat == '' || settings.lon == ''){
			alert("Não foi possível carregar o mapa.");	return false;
		}
		$(this).css({width:settings.width+'px',height:settings.height+'px'});
		var myLatlng = new google.maps.LatLng(settings.lat,settings.lon);
		var myZoon = 16;
		var myOptions = {
		  zoom: myZoon,
		  center: myLatlng,
		  mapTypeId: google.maps.MapTypeId.ROADMAP//SATELLITE;HYBRID;TERRAIN;ROADMAP
		};
		var map = new google.maps.Map(document.getElementById($(this).attr('id')), myOptions);
		var marker = new google.maps.Marker({
			title: settings.msg,
			map: map,
			icon: settings.icon,
			position: myLatlng
		});

	}
	
	jQuery.fn.gMaps = function(settings){
		var defaults = {
			width:'400',
			height:'300',
			lat:'',
			lon: '',
			icon: '',
			msg: 'O local que você deseja está aqui!',
			el_endereco: '',
			el_busca: '',
			el_lat: '',
			el_lon: ''
		};
		var settings = $.extend(defaults, settings);

		if(settings.el_lat == '' || settings.el_lon == ''){
			alert("Você deve passar os elementos 'el_lat' e 'el_lon' ");
			return false;
		}
		//define o tamanho da janela
		$(this).css({width:settings.width,height:settings.height});

		//inicializa o geolocalizador
		var geocoder = new google.maps.Geocoder();

//		function getByEndereco(endereco){
//			var teste;
//			geocoder.geocode( {'address': endereco}, function(results, status) {
//				if (status == google.maps.GeocoderStatus.OK) {
//					initContinued(results[0].geometry.location);
//				}
//			});
//			return teste;
//		};


		//cria variavel com latitude e logitude
		if(settings.lat && settings.lon){
			var myLatlng = new google.maps.LatLng(settings.lat,settings.lon);
			var myZoon = 14;
		}else{
			//getByEndereco('Belém-Pará,Brasil');
			var myLatlng = new google.maps.LatLng('-15.837178','-47.892151');
			var myZoon = 10;
		}
		
		//cria variavel com as configurações
		var myOptions = {
		  zoom: myZoon,
		  center: myLatlng,
		  mapTypeId: google.maps.MapTypeId.ROADMAP//SATELLITE;HYBRID;TERRAIN;ROADMAP
		};
		
		//cria o mapa
		var map = new google.maps.Map(document.getElementById($(this).attr('id')), myOptions);

		//cria o marcador
		var marker = new google.maps.Marker({
			title: settings.msg,
			map: map,
			draggable: true,
			cursor: 'move',
			icon: settings.icon
		});
		
		if(settings.lat && settings.lon){
			marker.setPosition(myLatlng);
		}
//		var infowindow = new google.maps.InfoWindow({
//			content: 'Teste Mensagem',
//		    size: new google.maps.Size(50,50)
//		});
//		google.maps.event.addListener(marker, 'mouseover', function(event) {
//			infowindow.open(map,marker);
//		});
//		google.maps.event.addListener(marker, 'mouseout', function(event) {
//			infowindow.close(map,marker);
//		});

		
		//adiciona um evento ao marcador
		google.maps.event.addListener(marker, 'click', function(event) {
			map.setZoom(16);
			map.setCenter(myLatlng);
		});

		function codeAddress(endereco) {
			geocoder.geocode( {'address': endereco}, function(results, status) {
			  if (status == google.maps.GeocoderStatus.OK) {
				myLatlng = results[0].geometry.location;
				map.setZoom(14);
				map.setCenter(myLatlng);
				marker.setPosition(myLatlng);
				settings.el_lat.val(myLatlng.lat());
				settings.el_lon.val(myLatlng.lng());
			  } else {
				alert("Não foi possivel localizar seu endereço. Tente localiza-lo manualmente.");
			  }
			});
		}


		settings.el_busca.click(function(){
			codeAddress(settings.el_endereco.val());
		});

		//add um evento ao mapa
		google.maps.event.addListener(map, 'click', function(event) {
			var clickedLocation = new google.maps.LatLng(location);
			marker.setPosition(event.latLng);//altera a posição do mapa
			settings.el_lat.val(event.latLng.lat());
			settings.el_lon.val(event.latLng.lng());

			//$('#Form_Imovel [name=MAPA_ZOOM]').val(map.getZoom()));
	   });
	   google.maps.event.addListener(marker, 'dragend', function(event) {
			var clickedLocation = new google.maps.LatLng(location);
			marker.setPosition(event.latLng);//altera a posição do mapa
			myLatlng = event.latLng;
			settings.el_lat.val(event.latLng.lat());
			settings.el_lon.val(event.latLng.lng());
	   });
	};


});