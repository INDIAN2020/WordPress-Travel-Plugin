var geocoder;
var map;
var markers = [];
var i = 0;
  function initialize() {
    geocoder = new google.maps.Geocoder();
    var latlng = new google.maps.LatLng(0,0);
    var mapOptions = {
      zoom: 1,
      center: latlng,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    }
    map = new google.maps.Map(document.getElementById('map_canvas'), mapOptions);
  }

	function codeAddress() {
		i++
	    var point = jQuery("#points > li:nth-child(" + i + ")").text(); 
		jQuery("#points > li:nth-child(" + i + ")").each(function(){
			if (point !== null) {
				geocoder.geocode( { 'address': point}, function(results, status) {
		 			  
			 		if (status == google.maps.GeocoderStatus.OK) {
		 				var marker = new google.maps.Marker({
		 					map: map,
		 					position: results[0].geometry.location
		 				});
 			 		} else {
				 		return;
			 		}
			 	});
			 } else {
				 alert(jQuery("#points"));
				 throw new Error('Something is still wrong...');
				 return;
			 } //else
		}) //jQuery each
	} //end codeAddress
	
	function populateMap() {
		var pointCount = jQuery('#points >li').size();
		while (i <= pointCount) {
			codeAddress();
		} //endwhile
	} //end populateMap()
/* 	jQuery('body').ready(initialize()); */
