<!DOCTYPE html>
<html lang="en">
	good bye garrett
	this is the 3rd test change by garrett
	<head>
		<title><?=$title?></title>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
	</head>

	<body>
		<h1><?=$title?></h1>
		<?php $attributes = array('name'=>'truck_stop_upload_form','id'=>'truck_stop_upload_form', )?>
		<?php echo form_open_multipart('fuel_planner/upload_truck_stops/',$attributes);?>
			<div style="margin-left:25px; margin-top:25px;">
				<input class="" type="file" name="userfile" />
			</div>
			<div style="margin-left:25px;">
				<button onclick="" style="" class="btn btn-default">Upload</button>
			</div>
		</form>
	</body>

</html>