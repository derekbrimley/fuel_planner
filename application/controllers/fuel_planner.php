<?php		
// REVERSE GEOCODING API URL: https://maps.googleapis.com/maps/api/geocode/json?latlng=40.714224,-73.961452&key=API_KEY
// API KEY: AIzaSyCDjz2nsurAAjDt7_H40FdD1DFYRtQafeQ
class Fuel_planner extends CI_Controller {
	
	function index()
	{
		$data['title'] = 'Fuel Planner';
		$this->load->view('fuel_planner_view',$data);
	}
	
	//UPLOAD CSV FILE ... REDIRECTS TO PROPER REPORT
	function upload_truck_stops()
	{
		
		$config = null;
		$config['upload_path'] = './uploads/';
		$config['allowed_types'] = 'csv';
		$config['max_size']	= '1000';
		
		$this->load->library('upload', $config);

		//IF ERRORS
		if ( ! $this->upload->do_upload())
		{
			echo $this->upload->display_errors();
		}
		else //SUCCESS
		{
			$file = $this->upload->data();
			$file_name = $file["file_name"];
			
			//PARSE THROUGH CSV FILE
			
			$csv_doc = fopen("./uploads/$file_name", "r");
			$row_number = 1;
			//FOREACH ROW
			while (($row = fgetcsv($csv_doc)) !== false) 
			{
				echo "<br>";
				// echo $row_number;
				if($row_number > 1)
				{
					$column = 1;
					//FOR EACH CELL
					foreach ($row as $cell) 
					{
						if($column == 3)
						{
							$name = htmlspecialchars($cell);
						}
						else if($column == 5)
						{
							$city = htmlspecialchars($cell);
						}
						else if($column == 6)
						{
							$state = htmlspecialchars($cell);
						}
						elseif($column == 7)
						{
							$zip = htmlspecialchars($cell);
						}
						elseif($column == 2)
						{
							$lat = htmlspecialchars($cell);
						}
						elseif($column == 1)
						{
							$long = htmlspecialchars($cell);
						}
						
						$column++;
					}//END COLUMN
					
					//GET FORMATTED ADDRESS FROM GOOGLE API
					echo $name.': '.$city.', '.$state.' '.$zip.': '.$lat.', '.$long.', '."<br/>";
					$base_api_url = "https://maps.googleapis.com/maps/api/geocode/json?";
					$params["latlng"] = $lat.','.$long;
					$params["key"] = 'AIzaSyCDjz2nsurAAjDt7_H40FdD1DFYRtQafeQ';
					//CREATE PARAM URL
					$param_url = http_build_query($params);
					//REQUEST ROUTE AND STORE DATA IN DATA OBJECT
					$json = file_get_contents($base_api_url.$param_url);
					$data = json_decode($json);
					$street_number = $data->results[0]->address_components[0]->long_name;
					$street = $data->results[0]->address_components[1]->long_name;
					
					//BUILD TRUCK_STOP ARRAY
					$new_truck_stop = null;
					$new_truck_stop["name"] = $name;
					$new_truck_stop["address"] = $street_number.' '.$street;
					$new_truck_stop["city"] = $city;
					$new_truck_stop["state"] = $state;
					$new_truck_stop["zip"] = $zip;
					$new_truck_stop["lat"] = $lat;
					$new_truck_stop["long"] = $long;
					$new_truck_stop["card"] = '1';

					db_insert_truck_stop($new_truck_stop);
					
				}//END ROW
				$row_number++;
				
			}
			fclose($csv_doc);

			
		}
	}
	
	//DAILY UPLOAD FUEL PRICE CSV
	function upload_stop_prices()
	{
		date_default_timezone_set('America/Denver');
		$current_datetime = date("Y-m-d H:i:s");
		##pull csv file
		$config = null;
		$config['upload_path'] = './uploads/';
		$config['allowed_types'] = 'csv';
		$config['max_size']	= '1000';
		
		$this->load->library('upload', $config);
		
		//IF ERRORS
		if ( ! $this->upload->do_upload())
		{
			echo $this->upload->display_errors();
		}
		
		//SUCCESS
		else 
		{
			$file = $this->upload->data();
			$file_name = $file["file_name"];
			
			//PARSE THROUGH CSV FILE
			
			$csv_doc = fopen("./uploads/$file_name", "r");
			$row_number = 1;
			//FOREACH ROW
			while (($row = fgetcsv($csv_doc)) !== false) 
			{
				echo "<br>";
				// echo $row_number;
				if($row_number > 7)
				{
					$column = 1;
					//FOR EACH CELL
					foreach ($row as $cell) 
					{
						if($column == 2)
						{
							$code = htmlspecialchars($cell);
						}
						else if($column == 4)
						{
							$state = htmlspecialchars($cell);
						}
						else if($column == 24)
						{
							$price = htmlspecialchars($cell);
						}
						$column++;
					}//END COLUMN
					
					echo "<br/>Code: ".$code;
					
					//GET TRUCK STOP ID
					$where = null;
					$where["stop_code"] = $code;
					//$where['stop_code'] = "TA TUSCALOOSA AL";
					$truck_stop = db_select_truck_stop($where);
					
					print_r("Truck Stop: ".$truck_stop['name']."<br/>");
					$truck_stop_id = $truck_stop["id"];
					print_r("Truck Stop ID: ".$truck_stop_id."<br/>");
					
					//BUILD TRUCK_STOP_PRICE ARRAY
					$new_truck_stop_price = null;
					$new_truck_stop_price["truck_stop_id"] = $truck_stop_id;
					$new_truck_stop_price["date"] = $current_datetime;
					$new_truck_stop_price["price"] = $price;
					
					db_insert_truck_stop_price($new_truck_stop_price);
					
					$where = null;
					$where["id"] = $truck_stop_id;
					
					$update_truck_stop = null;
					$update_truck_stop["current_price"] = $price;
					$update_truck_stop["date_updated"] = $current_datetime;
					
					echo "Price: ".$price."<br/>";
					echo "date: ".$current_datetime."<br/>";
					
					db_update_truck_stop($update_truck_stop,$where);
					
				}//END ROW
				$row_number++;
				
			}
			fclose($csv_doc);
			
		}
	}
	
	//ONE-TIME SCRIPT TO REPLACE TRUCK STOP NAMES WITH CODE
	function update_truck_stop_name()
	{
		$where = null;
		$where = "1=1";
		$truck_stops = db_select_truck_stops($where);
		
		//FOR EACH TRUCK STOP, UPDATE 
		
		$config = null;
		$config['upload_path'] = './uploads/';
		$config['allowed_types'] = 'csv';
		$config['max_size']	= '1000';
		
		$this->load->library('upload', $config);
		
		//IF ERRORS
		if ( ! $this->upload->do_upload())
		{
			echo $this->upload->display_errors();
		}
		
		//SUCCESS
		else 
		{
			$file = $this->upload->data();
			$file_name = $file["file_name"];
			
			//PARSE THROUGH CSV FILE
			
			$csv_doc = fopen("./uploads/$file_name", "r");
			$row_number = 1;
			//FOREACH ROW
			while (($row = fgetcsv($csv_doc)) !== false) 
			{
				echo "<br>";
				// echo $row_number;
				if($row_number > 7)
				{
					$column = 1;
					//FOR EACH CELL
					foreach ($row as $cell) 
					{
						if($column == 2)
						{
							$code = htmlspecialchars($cell);
						}
						if($column == 3)
						{
							$name = htmlspecialchars($cell);
						}
						else if($column == 4)
						{
							$state = htmlspecialchars($cell);
						}
						else if($column == 23)
						{
							$price = htmlspecialchars($cell);
						}
						$column++;
					}//END COLUMN
					
					$name = trim($name);
					$state = trim($state);
					$stop_code = trim($name.' '.$state);
					
					$update_truck_stop = null;
					$update_truck_stop["stop_code"] = $code;
					
					$where = null;
					$where["stop_code"] = $stop_code;
					
					db_update_truck_stop($update_truck_stop,$where);
					
					
				}//END ROW
				$row_number++;
				
			}
			fclose($csv_doc);
		}
	}
	
	//ONE TIME SCRIPT
	function populate_stop_code()
	{
		//GET EVERY TRUCK STOP IN DATABASE
		//where 1=1
		
		$where = null;
		$where = "1=1";
		$truck_stops = db_select_truck_stops($where);
		
		//for each truck stops, insert where id = truck_stop_id
		foreach($truck_stops as $truck_stop)
		{
			
			$stop_code = $truck_stop["name"].' '.$truck_stop["state"];
			
			$update_truck_stop = null;
			$update_truck_stop["stop_code"] = $stop_code;
			
			$where = null;
			$where["id"] = $truck_stop["id"];		
			db_update_truck_stop($update_truck_stop,$where);
		}
		//update stop_code
		
	}
	
}

