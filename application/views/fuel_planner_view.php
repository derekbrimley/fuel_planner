<!DOCTYPE html>
<html lang="en">
	<head>
		<title><?=$title?></title>
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
		<style>
			.header
			{
				text-align: center;
			}
			
			.text_box
			{
				float: right;
			}
		</style>
		<script>
		
		function generate_fuel_plan()
		{
			
			//-------------- AJAX -------------------
			// GET THE DIV IN DIALOG BOX
			
			var ajax_div = $('#ajax_plan');
			
			ajax_div.html("Generating Fuel Plan... ");
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

	<body style="font-family:calibri;">
		<div class="header">
			<h1><?=$title?></h1>
		</div>
		<div id="action_column" style="float:left;margin-left:30px;">
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
		<div id="fuel_info" style="float:left;margin-left:30px;">
			<h2>Fuel Information</h2>
			<div>
				<?php $attributes = array('name'=>'fuel_plan_form','id'=>'fuel_plan_form', )?>
				<?=form_open('fuel_planner/generate_fuel_plan',$attributes);?>
					<table>
						<tr>
							<td>
								Fuel Tank Capacity:
							</td>
							<td>
								<input id="fuel_tank_capacity" name="fuel_tank_capacity"class="text_box" type="text"/>
							</td>
						</tr>
						<tr>
							<td>
								Current Fuel Level:
							</td>
							<td>
								<input  id="current_fuel_level" name="current_fuel_level"class="text_box" type="text"/>
							</td>
						</tr>
						<tr>
							<td>
								Current Latitude:
							</td>
							<td>
								<input value="40.720206" id="current_latitude" name="current_latitude" class="text_box" type="text"/>
							</td>
						</tr>
						<tr>
							<td>
								Current Longitude:
							</td>
							<td>
								<input value="-112.018735" id="current_longitude" name="current_longitude" class="text_box" type="text"/>
							</td>
						</tr>
						<tr>
							<td>
								Destination 1:
							</td>
							<td>
							<input id="destination_1" name="destination_1" class="text_box" type="text"/>
							</td>
						</tr>
						<tr>
							<td>
								Destination 2:
							</td>
							<td>
								<input id="destination_2" name="destination_2" class="text_box" type="text"/>
							</td>
						</tr>
							<td>
								Destination 3:
							</td>
							<td>
								<input id="destination_3" name="destination_3" class="text_box" type="text"/>
							</td>
						</tr>
						<tr>
							<td>
								Destination 4:
							</td>
							<td>
								<input id="destination_4" name="destination_4" class="text_box" type="text"/>
							</td>
						</tr>
						<tr>
							<td>
								Destination 5:
							</td>
							<td>
								<input id="destination_5" name="destination_5" class="text_box" type="text"/>
							</td>
						</tr>
						<tr>
							<td>
								Destination 6:
							</td>
							<td>
								<input id="destination_6" name="destination_6" class="text_box" type="text"/>
							</td>
						</tr>
						<tr>
							<td>
								Destination 7:
							</td>
							<td>
								<input id="destination_7" name="destination_7" class="text_box" type="text"/>
							</td>
						</tr>
						<tr>
							<td>
								Destination 8:
							</td>
							<td>
								<input id="destination_8" name="destination_8" class="text_box" type="text"/>
							</td>
						</tr>
						<tr>
							<td>
								Destination 9:
							</td>
							<td>
								<input id="destination_9" name="destination_9" class="text_box" type="text"/>
							</td>
						</tr>
					</table>
				</form>
				<button onclick="generate_fuel_plan()">Generate Fuel Plan</button>
			</div>
		</div>
		<div id="fuel_plan" style="float:left; width:500px;margin-left:30px;">
			<h2>Fuel Plan</h2>
			<div id="ajax_plan"></div>
		<div/>
	</body>

</html>