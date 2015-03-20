<!DOCTYPE html>
<html>

	<head>
		<title><?=$title?></title>
		<style>
			img{
				max-height: 500px;
				max-width: 500px;
			}
			@media print {
				img {page-break-after: always;}
			}
		</style>
	</head>

	<body>
		<div>
			<h1><?=$title?></h1>
		</div>
		<div class="images">
			<img style="page-break-after:always;" src="http://i.huffpost.com/gen/1394713/images/o-TRUCKS-facebook.jpg"/>
			<img src="http://i.huffpost.com/gen/1394713/images/o-TRUCKS-facebook.jpg"/>
		</div>
	</body>

</html>