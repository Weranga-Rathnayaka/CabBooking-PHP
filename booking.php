<?php
require_once('./config.php');
if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT * from `booking_list` where id = '{$_GET['id']}' ");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=$v;
        }
    }
}
?>

<div class="container-fluid">
    <form action="" id="booking-form">
        <input type="hidden" name="id" value="<?= isset($id) ? $id : '' ?>">
        <input type="hidden" name="driver_id" value="<?= isset($_GET['cid']) ? $_GET['cid'] : (isset($driver_id) ? $driver_id : "") ?>">
        
        <!-- Pickup Location -->
        <div class="form-group">
            <label for="pickup_zone" class="control-label">Pickup Location</label>
            <input type="text" name="pickup_zone" id="pickup_zone" class="form-control form-control-sm rounded-0" value="<?= isset($pickup_zone) ? $pickup_zone : '' ?>" required>
            <div id="pickup_suggestions" class="autocomplete-suggestions"></div>
        </div>

        <!-- Drop-off Location -->
        <div class="form-group">
            <label for="drop_zone" class="control-label">Drop-off Location</label>
            <input type="text" name="drop_zone" id="drop_zone" class="form-control form-control-sm rounded-0" value="<?= isset($drop_zone) ? $drop_zone : '' ?>" required>
            <div id="drop_suggestions" class="autocomplete-suggestions"></div>
        </div>

        <!-- Distance and Fee -->
        <div class="form-group">
            <label for="distance" class="control-label">Distance (km)</label>
            <input type="text" name="distance" id="distance" class="form-control form-control-sm rounded-0" readonly>
        </div>
        <div class="form-group">
            <label for="fee" class="control-label">Fee (LKR)</label>
            <input type="text" name="fee" id="fee" class="form-control form-control-sm rounded-0" readonly>
        </div>
        
        <!-- <button type="submit" class="btn btn-primary">Submit</button> -->
    </form>
</div>

<!-- <div class="container-fluid">
    <form action="" id="booking-form">
        <input type="hidden" name="id" value="<?= isset($id) ? $id : '' ?>">
        <input type="hidden" name="driver_id" value="<?= isset($_GET['cid']) ? $_GET['cid'] : (isset($driver_id) ? $driver_id : "") ?>">
        <div class="form-group">
            <label for="pickup_zone" class="control-label">Pickup Location</label>
            <textarea name="pickup_zone" id="pickup_zone" rows="2" class="form-control form-control-sm rounded-0" required></textarea>
        </div>
        <div class="form-group">
            <label for="drop_zone" class="control-label">Drop-off Location</label>
            <textarea name="drop_zone" id="drop_zone" rows="2" class="form-control form-control-sm rounded-0" required></textarea>
        </div>
    </form>
</div> -->

<!-- <script>
	$(document).ready(function(){
		$('#booking-form').submit(function(e){
			e.preventDefault();
            var _this = $(this)
			 $('.err-msg').remove();
			start_loader();
			$.ajax({
				url:_base_url_+"classes/Master.php?f=save_booking",
				data: new FormData($(this)[0]),
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                type: 'POST',
                dataType: 'json',
				error:err=>{
					console.log(err)
					alert_toast("An error occured",'error');
					end_loader();
				},
				success:function(resp){
					if(typeof resp =='object' && resp.status == 'success'){
						location.href = './?p=booking_list';
					}else if(resp.status == 'failed' && !!resp.msg){
                        var el = $('<div>')
                            el.addClass("alert alert-danger err-msg").text(resp.msg)
                            _this.prepend(el)
                            el.show('slow')
                            $("html, body").animate({ scrollTop: _this.closest('.card').offset().top }, "fast");
                            end_loader()
                    }else{
						alert_toast("An error occured",'error');
						end_loader();
                        console.log(resp)
					}
				}
			})
		})
	})
</script> -->

<script>
$(document).ready(function(){
    function fetchPlaces(query, suggestionsBox) {
        $.ajax({
            url: `https://nominatim.openstreetmap.org/search?format=json&q=${query}`,
            method: 'GET',
            success: function(data) {
                let suggestions = '';
                data.forEach(function(place) {
                    suggestions += `<div class="autocomplete-item" data-lat="${place.lat}" data-lon="${place.lon}">${place.display_name}</div>`;
                });
                suggestionsBox.html(suggestions);
            }
        });
    }

    // Fetch places when typing in the pickup location field
    $(document).on('input', '#pickup_zone', function() {
        const query = $(this).val();
        if (query.length > 2) {
            fetchPlaces(query, $('#pickup_suggestions'));
        } else {
            $('#pickup_suggestions').empty();
        }
    });

    // Fetch places when typing in the drop-off location field
    $(document).on('input', '#drop_zone', function() {
        const query = $(this).val();
        if (query.length > 2) {
            fetchPlaces(query, $('#drop_suggestions'));
        } else {
            $('#drop_suggestions').empty();
        }
    });

    function calculateDistance(lat1, lon1, lat2, lon2) {
        const R = 6371; // Radius of the Earth in km
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                  Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                  Math.sin(dLon/2) * Math.sin(dLon/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        const distance = R * c;
        return distance.toFixed(2); // Return distance in km
    }

    function calculateFee(distance) {
        const baseRate = 125; // Base rate per km
        return (distance * baseRate).toFixed(2);
    }

    // Handle the selection of a suggested place
    $(document).on('click', '.autocomplete-item', function() {
        const lat = $(this).data('lat');
        const lon = $(this).data('lon');
        const input = $(this).closest('.form-group').find('input[type="text"]');
        input.val($(this).text());
        input.data('lat', lat);
        input.data('lon', lon);
        $(this).parent().empty();
        
        const pickupLat = $('#pickup_zone').data('lat');
        const pickupLon = $('#pickup_zone').data('lon');
        const dropLat = $('#drop_zone').data('lat');
        const dropLon = $('#drop_zone').data('lon');

        if (pickupLat && pickupLon && dropLat && dropLon) {
            const distance = calculateDistance(pickupLat, pickupLon, dropLat, dropLon);
            $('#distance').val(distance);
            $('#fee').val(calculateFee(distance));
        }
    });

    // Handle form submission
    $('#booking-form').submit(function(e){
        e.preventDefault();
        var _this = $(this);
        $('.err-msg').remove();
        start_loader();
        $.ajax({
            url: _base_url_ + "classes/Master.php?f=save_booking",
            data: new FormData($(this)[0]),
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            type: 'POST',
            dataType: 'json',
            error: err => {
                console.log(err);
                alert_toast("An error occurred", 'error');
                end_loader();
            },
            success: function(resp) {
                if (typeof resp == 'object' && resp.status == 'success') {
                    location.href = './?p=booking_list';
                } else if (resp.status == 'failed' && !!resp.msg) {
                    var el = $('<div>');
                    el.addClass("alert alert-danger err-msg").text(resp.msg);
                    _this.prepend(el);
                    el.show('slow');
                    $("html, body").animate({ scrollTop: _this.closest('.card').offset().top }, "fast");
                    end_loader();
                } else {
                    alert_toast("An error occurred", 'error');
                    end_loader();
                    console.log(resp);
                }
            }
        });
    });
});
</script>
