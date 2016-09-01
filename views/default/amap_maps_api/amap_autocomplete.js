define(function (require) {
	var elgg = require('elgg');
	var $ = require('jquery');
    require('amap_ma_googleapis_js');
	
    $(document).ready(function(){ 
        autocomplete = new google.maps.places.Autocomplete(
            /** @type {HTMLInputElement} */(document.getElementById('autocomplete')),
            { types: ['geocode'] 
		});
		google.maps.event.addListener(autocomplete, 'place_changed', function() {
			
        });    
    });
    
	// prevent form submitted when press enter to autocomplete
	$("#autocomplete").keydown(function(event){
		if(event.keyCode == 13) {
		  event.preventDefault();
		  return false;
		}
	});    

});
