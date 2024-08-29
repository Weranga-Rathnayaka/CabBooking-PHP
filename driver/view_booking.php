<?php
require_once('./../config.php');
if(isset($_GET['id']) && $_GET['id'] > 0){
    // Fetch the booking, client, and client contact information
    $qry = $conn->query("SELECT 
                            b.*, 
                            CONCAT(c.lastname, ', ', c.firstname, ' ', c.middlename) AS client, 
                            c.contact,
                            c.email,
                            c.firstname
                         FROM 
                            `booking_list` b 
                         INNER JOIN 
                            `client_list` c 
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
                                `category_list` cc 
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
                    <dt class="">Vehicle Category</dt>
                    <dd class="pl-4"><?= isset($category) ? $category : "" ?></dd>
                    <dt class="">Client Contact No</dt>
                    <dd class="pl-4"><?= isset($contact) ? $contact : "" ?></dd>
                    <dt class="">Driver Name</dt>
                    <dd class="pl-4"><?= isset($driver_name) ? $driver_name : "" ?></dd>
                    <dt class="">Client Email</dt>
                    <dd class="pl-4"><?= isset($email) ? $email : "" ?></dd>
                    <dt class="">Driver Contact</dt>
                    <dd class="pl-4"><?= isset($driver_contact) ? $driver_contact : "" ?></dd>
                    <dt class="">Driver Address</dt>
                    <dd class="pl-4"><?= isset($driver_address) ? $driver_address : "" ?></dd>
                </dl>
            </fieldset>
            <!-- <div class="clear-fix my-2"></div> -->
            
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
    
    <!-- <div class="clear-fix my-3"></div> -->
    <div class="text-right">
        <?php if(isset($status) && $status == 0): ?>
        <button class="btn btn-primary btn-flat bg-gradient-primary" type="button" id="confirm_booking">Confirm Booking</button>
        <?php elseif(isset($status) && $status == 1): ?>
        <button class="btn btn-warning btn-flat bg-gradient-warning" type="button" id="pickup_booking">Picked Up</button>
        <?php elseif(isset($status) && $status == 2): ?>
        <button class="btn btn-success btn-flat bg-gradient-success" type="button" id="dropoff_booking">Dropped Off</button>
        <?php endif; ?>
        <button class="btn btn-dark btn-flat bg-gradient-dark" type="button" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
    </div>
</div>
<script>
    $(function(){
        $('#confirm_booking').click(function(){
            _conf("Are you sure to confirm this booking [Ref. Code: <b><?= isset($ref_code) ? $ref_code : "" ?></b>]?", "update_booking_status",["<?= isset($id) ? $id : "" ?>",1])
        })
        $('#pickup_booking').click(function(){
            _conf("Mark [Ref. Code: <b><?= isset($ref_code) ? $ref_code : "" ?></b>] booking as Picked Up?", "update_booking_status",["<?= isset($id) ? $id : "" ?>",2])
        }) 
        $('#dropoff_booking').click(function(){
            _conf("Mark [Ref. Code: <b><?= isset($ref_code) ? $ref_code : "" ?></b>] booking as Dropped Off?", "update_booking_status",["<?= isset($id) ? $id : "" ?>",3])
        })
    })
    // function update_booking_status($id,$status){
    //     start_loader();
	// 	$.ajax({
	// 		url:_base_url_+"classes/Master.php?f=update_booking_status",
	// 		method:"POST",
	// 		data:{id: $id,status:$status},
	// 		dataType:"json",
	// 		error:err=>{
	// 			console.log(err)
	// 			alert_toast("An error occured.",'error');
	// 			end_loader();
	// 		},
	// 		success:function(resp){
	// 			if(typeof resp== 'object' && resp.status == 'success'){
	// 				location.reload();
	// 			}else{
	// 				alert_toast("An error occured.",'error');
	// 				end_loader();
	// 			}
	// 		}
	// 	})
    // }
    function update_booking_status($id, $status) {
    start_loader();

    // Send email before updating booking status
    $.ajax({
        url: _base_url_ + "driver/send_email.php",
        method: "POST",
        data: {
            email: '<?= isset($email) ? $email : "" ?>', // Retrieve the client's email
            status: $status,
            ref_code: '<?= isset($ref_code) ? $ref_code : "" ?>',
            pickup_zone: '<?= isset($pickup_zone) ? $pickup_zone : "" ?>',
            drop_zone: '<?= isset($drop_zone) ? $drop_zone : "" ?>',
            driver_name: '<?= isset($driver_name) ? $driver_name : "" ?>',
            driver_contact: '<?= isset($driver_contact) ? $driver_contact : "" ?>',
            firstname: '<?= isset($firstname) ? $firstname : "" ?>'
        },
        dataType: "json",
        success: function(emailResp) {
            if (typeof emailResp == 'object' && emailResp.status == 'success') {
                // Email sent successfully, now update the booking status
                $.ajax({
                    url: _base_url_ + "classes/Master.php?f=update_booking_status",
                    method: "POST",
                    data: {id: $id, status: $status},
                    dataType: "json",
                    success: function(resp) {
                        if (typeof resp == 'object' && resp.status == 'success') {
                            location.reload();
                        } else {
                            alert_toast("An error occurred while updating the booking status.", 'error');
                            end_loader();
                        }
                    },
                    error: function(err) {
                        console.log(err);
                        alert_toast("An error occurred while updating the booking status.", 'error');
                        end_loader();
                    }
                });
            } else {
                alert_toast("An error occurred while sending the email.", 'error');
                end_loader();
            }
        },
        error: function(err) {
            console.log(err);
            alert_toast("An error occurred while sending the email.", 'error');
            end_loader();
        }
    });
}


</script>