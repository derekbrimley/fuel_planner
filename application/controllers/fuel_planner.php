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
				echo $row_number;
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
					echo $name.': '.$city.', '.$state.' '.$zip.': '.$lat.', '.$long.', '."<br/>";
					$base_api_url = "https://maps.googleapis.com/maps/api/geocode/json?";
					
					$params["latlng"] = $lat.','.$long;
					$params["key"] = 'AIzaSyCDjz2nsurAAjDt7_H40FdD1DFYRtQafeQ';
					
					//CREATE PARAM URL
					$param_url = http_build_query($params);
					
					//REQUEST ROUTE AND STORE DATA IN DATA OBJECT
					$json = file_get_contents($base_api_url.$param_url);
					$data = json_decode($json);
					
					echo $data->address_components;
					echo "<br/><br/><br/>";
						
					//ADD THE WAY POINTS TO THE GOOGLE MAPS HTTP REQUEST 
					//$route_url = $route_url."+to:".str_replace($url_search,$url_replace,$end_event["address"].", ".$end_event["city"].", ".$end_event["state"]);
					
					
					//echo $base_url.http_build_query($params);
					//echo urlencode(http_build_query($params));
					
					//CREATE PARAM URL
					$param_url = http_build_query($params);

				}//END ROW
				$row_number++;
				
			}
			fclose($csv_doc);

			
		}
	}
	
	//UPLOAD TRUCK STOP INFO
	// function upload_truck_stops($file_name,$sp_account_id)
	// {
		// $notes = "";
		// $entries = array();
		// $csv_doc = fopen("./uploads/$file_name", "r");
		// $row_number = 1;
		// //FOREACH ROW
		// while (($row = fgetcsv($csv_doc)) !== false) 
		// {
			// //echo "<br>";
			// //echo $row_number;
			// if($row_number > 1)
			// {
				// $column = 1;
				// //FOR EACH CELL
				// foreach ($row as $cell) 
				// {
					// if($column == 1)
					// {
						// $date = htmlspecialchars($cell);
					// }
					// else if($column == 2)
					// {
						// $time = htmlspecialchars($cell);
					// }
					// else if($column == 3)
					// {
						// $type = htmlspecialchars($cell);
					// }
					// elseif($column == 4)
					// {
						// $location = htmlspecialchars($cell);
					// }
					// elseif($column == 5)
					// {
						// $city = htmlspecialchars($cell);
					// }
					// elseif($column == 6)
					// {
						// $state = htmlspecialchars($cell);
					// }
					// else if($column == 9)
					// {
						// $entry_amount = round(htmlspecialchars($cell),2);
					// }
					// else if($column == 10)
					// {
						// $fee = round(htmlspecialchars($cell),2);
					// }
					
					// $column++;
				// }//END ROW
				
				// if($type == "FP TOTAL")
				// {
					// $description = "$type at $location in $city, $state";
				// }
				// else
				// {
					// $description = $type;
				// }
				
				// $entry_datetime = date("Y-m-d H:i:s",strtotime($date." ".$time));
				
				// $report_is_empty = false;
				// //IF REPORT IS EMPTY
				// if(empty($entry_datetime))
				// {
					// $report_is_empty = true;
					// break;
				// }
				
				// //DETERMINE DEBIT OR CREDIT
				// //DETERMINE DEFAULTS FOR EXPENSE, TRANSFER, OR REVENUE
				// $expense_type = "Expense";
				// if($entry_amount > 0)
				// {
					// $debit_credit = "Credit";
				// }
				// else
				// {
					// $debit_credit = "Debit";
					// $entry_amount = $entry_amount * -1;
				// }
				
				// $issuer_id = null;
				
				// $entry["issuer_id"] = $issuer_id;
				// $entry["expense_type"] = $expense_type;
				// $entry["debit_credit"] = $debit_credit;
				// $entry["entry_amount"] = $entry_amount;
				// $entry["account_id"] = $sp_account_id;
				// $entry["entry_datetime"] = $entry_datetime;
				// $entry["entry_description"] = trim("Comdata Transaction | ".$description);
				// $entry["report_guid"] = null;
				
				// //ADD ENTRY TO THE ARRAY
				// if(!empty($entry["entry_amount"]) && ($type == "FP TOTAL" || $type == "Check" || $type == "Load"))
				// {
					// $entries[] = $entry;
					// //db_insert_account_entry($entry);
				// }
				
			// }//END ROW
			
			// $row_number++;
		// }
		// fclose($csv_doc);
		
		// $where = null;
		// $where["id"] = $sp_account_id;
		// $account = db_select_account($where);
		
		// //IF REPORT IS NOT EMPTY
		// if(!$report_is_empty)
		// {
			// $data['entry_type'] = "Comdata Entry";
			// $data['entries'] = $entries;
			// $data['file_name'] = $file_name;
			// $data['account'] = $account;
			// $data['report_name'] = "Comdata Transaction Report";
			// $data['sp_account_id'] = $sp_account_id;
			// $this->load->view('expenses/accounts_transaction_table',$data);
		// }
		// else
		// {
			// $data['report_name'] = "Transaction Report";
			// $data['account'] = $account;
			// $this->load->view('expenses/empty_report_view',$data);
		// }
	// }
}

