define(function (require) {
	var elgg = require('elgg');
	var $ = require('jquery');
	
    $('#users_geolocation_btn').click(function(){
        $('#users_geolocation-loader').removeClass('hidden');
        $('#users_geolocation-result').html('');

        elgg.get('ajax/view/amap_maps_api/users_geolocation', {
            data: $(this).serialize(),
            success: function(data){
                $('#users_geolocation-result').html(data);
            },
            error: function(jqXHR, textStatus, errorThrown){
                elgg.register_error(elgg.echo('amap_maps_api:error:request'));
            },
            complete: function(){
                $('#users_geolocation-loader').addClass('hidden');
            }
        });

        return false;
    });
});
