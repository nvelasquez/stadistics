var map;
var marker;



function initialize() {
  var mapOptions = {
    zoom: 8,
    center:new google.maps.LatLng(18.93333,-70.41667),
    mapTypeId: google.maps.MapTypeId.ROUTE,
  };
  map = new google.maps.Map(document.getElementById("content_map"),
      mapOptions);
	  
	marker = new google.maps.Marker({
		map:map,
		position:map.getCenter(),
		animation: google.maps.Animation.BOUNCE,
		title:":D"
		
	});
	
	google.maps.event.addListener(map, "click", function(e){

		makeMarkers(e.latLng, map);
	});
	function makeMarkers(position, map)
	{
		var marker2=new google.maps.Marker({
			map:map,
			position:position,
			animation: google.maps.Animation.BOUNCE,
			draggable:true
		});
		
		
		map.panTo(position);
	}
	
	

}


google.maps.event.addDomListener(window, 'load', initialize);