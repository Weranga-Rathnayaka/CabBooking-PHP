<?php
require_once('./config.php'); // Include the configuration file for DB connection and environment variables
require __DIR__ . '/vendor/autoload.php';
use Twilio\Rest\Client;

if(isset($_GET['id']) && $_GET['id'] > 0){
    // Fetch the booking and client information, including contact
    $qry = $conn->query("SELECT 
                            b.*, 
                            CONCAT(c.lastname, ', ', c.firstname, ' ', c.middlename) AS client, 
                            c.contact 
                         FROM 
                            `booking_list` b 
                         INNER JOIN 
                            client_list c 
                         ON 
                            b.client_id = c.id 
                         WHERE 
                            b.id = '{$_GET['id']}'");
                         
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k = $v;
        }
        
        // Fetch the driver information
        $qry2 = $conn->query("SELECT 
                                c.*, 
                                cc.name AS category 
                              FROM 
                                `driver_list` c 
                              INNER JOIN 
                                category_list cc 
                              ON 
                                c.category_id = cc.id 
                              WHERE 
                                c.id = '{$driver_id}'");
                              
        if($qry2->num_rows > 0){
            foreach($qry2->fetch_assoc() as $k => $v){
                if(!isset($$k)) $$k = $v;
            }
        }
    }
}



?>

<!-- HTML and Modal Logic -->
<style>
    #uni_modal .modal-footer{
        display:none
    }
</style>
<div class="container-fluid">

    <div class="row">
        <div class="col-md-6">
            <fieldset class="bor">
                <legend class="h5 text-muted"> </legend>
                <dl>
                    <dt class="">Fee</dt>
                    <dd class="pl-4"><?= isset($fee) ? "LKR " . $fee : "" ?></dd>

                    <dt class="">Client Contact No</dt>                   
                    <dd class="pl-4"><?= isset($contact) ?  $contact : "" ?></dd>

                    <dt class="">Vehicle Category</dt>
                    <dd class="pl-4"><?= isset($category) ? $category : "" ?></dd>
                    <dt class="">Driver</dt>
                    <dd class="pl-4"><?= isset($driver_name) ? $driver_name : "" ?></dd>
                    <dt class="">Driver Contact</dt>
                    <dd class="pl-4"><?= isset($driver_contact) ? $driver_contact : "" ?></dd>
                    <dt class="">Driver Address</dt>
                    <dd class="pl-4"><?= isset($driver_address) ? $driver_address : "" ?></dd>
                </dl>
            </fieldset>
        
            
        </div>

        <div class="col-md-6">
            <fieldset class="bor">
                <legend class="h5 text-muted"> </legend>
                <dl>
                    <dt class="">Ref. Code</dt>
                    <dd class="pl-4"><?= isset($ref_code) ? $ref_code : "" ?></dd>
                    <dt class="">Pickup Zone</dt>
                    <dd class="pl-4"><?= isset($pickup_zone) ? $pickup_zone : "" ?></dd>
                    <dt class="">Drop off Zone</dt>
                    <dd class="pl-4"><?= isset($drop_zone) ? $drop_zone : "" ?></dd>
                    <dt class="">Status</dt>
                    <dd class="pl-4">
                        <?php 
                            switch($status){
                                case 0:
                                    echo "<span class='badge badge-secondary bg-gradient-secondary px-3 rounded-pill'>Pending</span>";
                                    break;
                                case 1:
                                    echo "<span class='badge badge-primary bg-gradient-primary px-3 rounded-pill'>Driver Confirmed</span>";
                                    break;
                                case 2:
                                    echo "<span class='badge badge-warning bg-gradient-warning px-3 rounded-pill'>Picked-up</span>";
                                    break;
                                case 3:
                                    echo "<span class='badge badge-success bg-gradient-success px-3 rounded-pill'>Dropped off</span>";
                                    break;
                                case 4:
                                    echo "<span class='badge badge-danger bg-gradient-danger px-3 rounded-pill'>Cancelled</span>";
                                    break;
                            }
                        ?>
                    </dd>
                </dl>
            </fieldset>
        </div>
    </div>
    
    <div class="text-right">
        <?php if(isset($status) && $status == 0): ?>
        <button class="btn btn-danger btn-flat bg-gradient-danger" type="button" id="cancel_booking">Cancel Bookings</button>
        <?php endif; ?>
        <button class="btn btn-dark btn-flat bg-gradient-dark" type="button" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
    </div>
</div>

<script>
    $(function(){
        $('#cancel_booking').click(function(){
            _conf("Are you sure to cancel your cab booking [Ref. Code: <b><?= isset($ref_code) ? $ref_code : "" ?></b>]?", "cancel_booking",["<?= isset($id) ? $id : "" ?>"])
        })
    })

    // function cancel_booking($id){
    //     start_loader();
	// 	$.ajax({
	// 		url:_base_url_+"classes/Master.php?f=update_booking_status",
	// 		method:"POST",
	// 		data:{id: $id,status:4},
	// 		dataType:"json",
	// 		error:err=>{
	// 			console.log(err)
	// 			alert_toast("An error occured.",'error');
	// 			end_loader();
	// 		},
	// 		success:function(resp){
	// 			if(typeof resp== 'object' && resp.status == 'success'){
    //                 // Send the cancellation SMS
    //                 sendCancellationSMS(

    //                 );
	// 				location.reload();
	// 			}else{
	// 				alert_toast("An error occured.",'error');
	// 				end_loader();
	// 			}
	// 		}
	// 	})
    // }

    function cancel_booking($id) {
    start_loader();
    $.ajax({
        url: _base_url_ + "classes/Master.php?f=update_booking_status",
        method: "POST",
        data: {id: $id, status: 4},
        dataType: "json",
        error: err => {
            console.log(err);
            alert_toast("An error occurred.", 'error');
            end_loader();
        },
        success: function (resp) {
            if (typeof resp == 'object' && resp.status == 'success') {
                // Prepare SMS details
                let smsDetails = {
                    ref_code: "<?= $ref_code ?>",
                    pickup_zone: "<?= $pickup_zone ?>",
                    drop_zone: "<?= $drop_zone ?>",
                    driver_name: "<?= $driver_name ?>",
                    driver_contact: "<?= $driver_contact ?>",
                    client: "<?= $client ?>",
                    contact: "<?= $contact ?>",
                    fee: "<?= $fee ?>",
                    client_contact: "<?= $contact ?>" // Ensure this is correct
                };

                $.ajax({
                    url: _base_url_ + "send_sms.php",
                    method: "POST",
                    data: {details: JSON.stringify(smsDetails)},
                    dataType: "json",
                    success: function (smsResp) {
                        if (smsResp.status === 'success') {
                            alert_toast("Booking cancelled and SMS sent successfully.", 'success');
                        } else {
                            alert_toast("Failed to send SMS: " + smsResp.message, 'error');
                        }
                        location.reload();
                    },
                    error: function (err) {
                        console.log(err);
                        alert_toast("An error occurred while sending SMS.", 'error');
                    }
                });
            } else {
                alert_toast("An error occurred while updating booking status.", 'error');
            }
            end_loader();
        }
    });
}

</script>



