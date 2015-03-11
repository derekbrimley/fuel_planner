<!DOCTYPE html>
<html lang="en">
	<head>
		<title><?=$title?></title>
		<style>
			.header{text-align: center}
			.left-col{float: left; margin: 10px;}
			.fuel_info{float: left; margin: 10px;}
			.fuel_plan{float: left; margin: 10px;}
		</style>
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
			<form>
				Current Location: <input type="text"/><br>
				Fuel Tank Capacity: <input type="text"/><br>
				Current Fuel Level: <input type="text"/><br>
				Destination 1: <input type="text"/><br>
				Destination 2: <input type="text"/><br>
				Destination 3: <input type="text"/><br>
				Destination 4: <input type="text"/><br>
				Destination 5: <input type="text"/><br>
				Destination 6: <input type="text"/><br>
				Destination 7: <input type="text"/><br>
				Destination 8: <input type="text"/><br>
				Destination 9: <input type="text"/><br>
				Destination 10: <input type="text"/><br>
				<input type="submit" value="Submit"/>
			</form>
		</div>
		<div class="fuel_plan">
			<h2>Fuel Plan</h2>
		<div/>
	</body>

</html>