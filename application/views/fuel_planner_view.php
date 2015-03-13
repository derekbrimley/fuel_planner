<!DOCTYPE html>
<html lang="en">
	<head>
		<title><?=$title?></title>
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
		<style>
			.header{text-align: center}
			.left-col{float: left; margin: 10px;}
			.fuel_info{float: left; margin: 10px;}
			.fuel_plan{float: left; margin: 10px;}
			.text_box{float: right;}
		</style>
		<script>
		
		function generate_fuel_plan()
		{
			
			//-------------- AJAX -------------------
			// GET THE DIV IN DIALOG BOX
			
			var ajax_div = $('#ajax_plan');
			
			//POST DATA TO PASS BACK TO CONTROLLER
			
			var dataString = $("#fuel_plan_form").serialize();
			// var dataString = "&id="+row;
			// AJAX!
			$.ajax({
				url: "<?= base_url("index.php/fuel_planner/generate_fuel_plan")?>", // in the quotation marks
				type: "POST",
				data: dataString,
				cache: false,
				context: ajax_div, // use a jquery object to select the result div in the view
				statusCode: {
					200: function(response){
						// Success!
						//alert(response);
						ajax_div.html(response);
					},
					404: function(){
						// Page not found
						alert('page not found');
					},
					500: function(response){
						// Internal server error
						alert("500 error!")
					}
				}
			});//END AJAX
		
		}
		
		</script>
	</head>

	<body>
		<div class="header">
			<h1><?=$title?></h1>
		</div>
		<div class="left-col">
			<h2>Upload Prices</h2>
			<?php $attributes = array('name'=>'truck_stop_upload_form','id'=>'truck_stop_upload_form', )?>
			<?php echo form_open_multipart('fuel_planner/upload_stop_prices/',$attributes);?>
				<div style="margin-left:25px; margin-top:25px;">
					<input class="" type="file" name="userfile" />
				</div>
				<div style="margin-left:25px;">
					<button onclick="" style="">Upload</button>
				</div>
			</form>
		</div>
		<div class="fuel_info">
			<h2>Fuel Information</h2>
			<?php $attributes = array('name'=>'fuel_plan_form','id'=>'fuel_plan_form', )?>
			<?=form_open('fuel_planner/generate_fuel_plan',$attributes);?>
				Current Latitude: <input value="38.559930" id="current_latitude" name="current_latitude" class="text_box" type="text"/><br>
				Current Longitude: <input value="-121.495098" id="current_longitude" name="current_longitude" class="text_box" type="text"/><br>
				Fuel Tank Capacity: <input id="fuel_tank_capacity" name="fuel_tank_capacity"class="text_box" type="text"/><br>
				Current Fuel Level: <input  id="current_fuel_level" name="current_fuel_level"class="text_box" type="text"/><br>
				Destination 1: <input id="destination_1" name="destination_1" class="text_box" type="text"/><br>
				Destination 2: <input id="destination_2" name="destination_2" class="text_box" type="text"/><br>
				Destination 3: <input id="destination_3" name="destination_3" class="text_box" type="text"/><br>
				Destination 4: <input id="destination_4" name="destination_4" class="text_box" type="text"/><br>
				Destination 5: <input id="destination_5" name="destination_5" class="text_box" type="text"/><br>
				Destination 6: <input id="destination_6" name="destination_6" class="text_box" type="text"/><br>
				Destination 7: <input id="destination_7" name="destination_7" class="text_box" type="text"/><br>
				Destination 8: <input id="destination_8" name="destination_8" class="text_box" type="text"/><br>
				Destination 9: <input id="destination_9" name="destination_9" class="text_box" type="text"/><br>
				Final Destination: <input id="final_destination" name="final_destination" class="text_box" type="text"/><br>
			</form>
			<button onclick="generate_fuel_plan()">Generate Fuel Plan</button>
		</div>
		<div id="fuel_plan">
			<h2>Fuel Plan</h2>
			<div id="ajax_plan"></div>
		<div/>
	</body>

</html>