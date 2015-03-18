<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//GET MAP MILES AND ROUTE FOR GIVEN EVENT ARRAY
	function get_map_info($map_events) //RETURNS AN ARRAY WITH MAP MILES AND ROUTE
	{
		//echo "<br>--- new function --<br>";
		$first_event = null;
		$end_event = null;
		$map_requests = array();
		if(!empty($map_events))
		{
			//GET THE ROUTE URL AND BREAK THE MAP EVENTS INTO SEPERATE REQUESTS (8 AT A TIME)
			$previous_event = null;
			$request_number = 1;
			$event_count = 0;
			$i = 1;
			foreach($map_events as $event)
			{
				
				
				$event_count++;
				//$event_list = null;
				//$event_list[] = $previous_event;
				if($event_count > 4)
				{
					//INCREMENT THE REQUEST NUMBER
					$request_number++;
					$event_count = 0;
					//echo "event list count ".count($event_list)."<br>";
					//echo "reset event list<br>";
					$event_list = null;
					$event_list[] = $previous_event;
				}
				
				//echo "add to event list ".$event["city"]." ".$event["state"]." ";
				//echo "event count = ".$event_count."<br>";
				$event_list[] = $event;
				
				$map_requests[$request_number] = $event_list;
				
				$previous_event = $event;
				
				//CREATE URL FOR THE LINK
				if($i == 1)
				{
					$first_event = $event;
				
					$url_search = array(" ","&");
					$url_replace = array("+","and");
					$route_url = "https://maps.google.com/maps?saddr=".str_replace($url_search,$url_replace,$event["address"]." ".$event["city"]." ".$event["state"]);
					
				}
				else if($i == 2)
				{
					$route_url = $route_url."&daddr=".str_replace($url_search,$url_replace,$event["address"]." ".$event["city"]." ".$event["state"]);
				}
				else
				{
					$route_url = $route_url."+to:".str_replace($url_search,$url_replace,$event["address"]." ".$event["city"]." ".$event["state"]);
				}
				
				$i++;
				
				
			}//END FOREACH EVENT
			
			//echo count($map_requests)."<br>";
			
			
			$total_map_miles = 0;
			$this_request = 1;
			foreach($map_requests as $these_events)
			{
				//echo "<br>--- new request --<br>";
				
				$i = 0;
				$previous_waypoint = "";
				$waypoints = "";
				foreach($these_events as $event)
				{
					$i++;
					
					//error_log($event["city"]." ".$event["state"]." line ".__LINE__." ".__FILE__);
					if($i == 1)
					{
						//STORE THE FIRST EVENT - ORIGIN
						$first_event = $event;
					
						//$url_search = array(" ","&");
						//$url_replace = array("+","and");
						//$route_url = "https://maps.google.com/maps?saddr=".str_replace($url_search,$url_replace,$event["address"].", ".$event["city"].", ".$event["state"]);
					}
					else if($i > 1)
					{
						//$route_url = $route_url."&daddr=".str_replace($url_search,$url_replace,$event["address"].", ".$event["city"].", ".$event["state"]);
						//$origin = str_replace($url_search,$url_replace,$event["address"]." ".$event["city"]." ".$event["state"]);
						
						//ADD THE PREVIOUS WAYPOINT TO THE URL AND ADD THE | TO THE END TO PREPARE FOR THE NEXT
						$waypoints = $waypoints.$previous_waypoint."|";
						//$previous_waypoint = $waypoints."via:".str_replace($url_search,$url_replace,$event["address"]." ".$event["city"]." ".$event["state"]);
						$previous_waypoint = "via:".str_replace($url_search,$url_replace,$event["address"]." ".$event["city"]." ".$event["state"]);
					
					}
					
					
					
					//GET THE LAST EVENT - DESTINATION
					$end_event = $event;
					
				}
				
				//echo $i."<br>";
					
				//GET MAP MILES
				$origin = str_replace($url_search,$url_replace,$first_event["address"]." ".$first_event["city"].", ".$first_event["state"]);
				$destination = str_replace($url_search,$url_replace,$end_event["address"]." ".$end_event["city"].", ".$end_event["state"]);
				$base_url = 'http://maps.googleapis.com/maps/api/directions/json?';
				$params["waypoints"] = "";
				$params["origin"] = $origin;
				$params["destination"] = $destination;
				if($i >= 3) //IF THERE ARE ANY WAYPOINTS - ADD THEM TO THE PARAMS ---- changed from if($i > 3)
				{
					$params["waypoints"] = substr($waypoints,5);
				}
				$params["mode"] = 'driving';
				$params["sensor"] = 'false';
				
				//ADD THE WAY POINTS TO THE GOOGLE MAPS HTTP REQUEST 
				//$route_url = $route_url."+to:".str_replace($url_search,$url_replace,$end_event["address"].", ".$end_event["city"].", ".$end_event["state"]);
				
				
				//echo $base_url.http_build_query($params)."<br/>";
				//echo urlencode(http_build_query($params));
				
				//CREATE PARAM URL
				$param_url = http_build_query($params);
				
				//error_log($base_url.$param_url." line ".__LINE__." ".__FILE__);
				
				//SEARCH DB FOR PREVIOUS REQUEST
				$where = null;
				$where["param_url"] = $param_url;
				$previous_rr = db_select_route_request($where);
				
				//$previous_rr = null;
				
				//IF PREVIOUS REQUEST EXISTS IN DB
				if(!empty($previous_rr))
				{
					//echo "Previous request found";
					
					//SET MAP MILES FROM PREVIOUS REQUEST
					$map_miles = $previous_rr["map_miles"];
					
					//INCREMENT COUNT ON ROUTE REQUEST
					$update_rr = null;
					$update_rr["count"] = $previous_rr["count"]+1;
					
					$where = null;
					$where["id"] = $previous_rr["id"];
					db_update_route_request($update_rr,$where);
				
				}
				else//ELSE IF PREVIOUS REQUEST DOES NOT EXIST
				{
					//CHECK HOW MANY REQUEST HAVE BEEN MADE IN THE LAST 24 HOURS
					$CI =& get_instance();
					$sql = "SELECT COUNT(*) as hit_count FROM route_request WHERE request_datetime > '".date("Y-m-d H:i:s")."'";
					$query = $CI->db->query($sql);
					
					foreach ($query->result() as $row)
					{
						$request_count = $row->hit_count;
					}
					
					if($request_count > 2000)
					{
						echo "You are nearing the Google limit - You're at $request_count";
					}
					
					//REQUEST ROUTE AND STORE DATA IN DATA OBJECT
					$json = file_get_contents($base_url.$param_url);
					$data = json_decode($json);

					
					
					//IF STATUS IS OK GET THE MAP MILES FROM THE ROUTES
					$map_miles = 0;
					if ($data->status === 'OK') 
					{
						$route = $data->routes[0];
						foreach($route->legs as $gleg)
						{
							$map_miles = $map_miles + $gleg->distance->value;
						}
					}
					//else
					//{
						//echo $data->status;
					//}
					
					$map_miles = round($map_miles/1609.34); //CONVERT FROM KM TO MILES
					
					date_default_timezone_set('America/Denver');
					
					//INSERT ROUTE REQUEST INTO DB
					$rr = null;
					$rr["request_datetime"] = date("Y-m-d H:i:s");
					$rr["web_service"] = $base_url;
					$rr["param_url"] = $param_url;
					$rr["status"] = $data->status;
					$rr["map_miles"] = $map_miles;
					$rr["route_url"] = $route_url;
					db_insert_route_request($rr);
		
				}
				
				//echo $first_event["city"]." -> ".$end_event["city"]." ".$map_miles."<br>";
		
				$total_map_miles = $total_map_miles + $map_miles;
				$this_request++;
			}
		}
		
		$map_info["route_url"] = $route_url;
		$map_info["map_miles"] = $total_map_miles;
		
		
		
		return $map_info;
		
	}	
	
	function closest_in_route_fuel_stop($current_lat,$current_long,$waypoints)
	{
		$closet_in_route_fuel_stop = null;
		
		//CONVERT LAT AND LONG TO ADDRESS
		$current_address = $waypoints[0]["address"];
		// $final_destination = $waypoints[10]["address"];
		// $final_lat = $waypoints[10]["lat"];
		// $final_long = $waypoints[10]["long"];
		
		$where = null;
		$where = "1 =1";
		$truck_stops = db_select_truck_stops($where);
		
		$closest_truck_stop = null;
		$least_distance = null;
		$truck_stops_w_distance = array();
		
		foreach( $truck_stops as $truck_stop )
		{
			
			$lat = $truck_stop["lat"];
			$long = $truck_stop["long"];
			
			//CALCULATE DISTANCE BETWEEN CURRENT LOCATION AND FUEL STOPS
			$a = $current_lat - $lat;
			$b = $current_long - $long;
			$distance_from_current = sqrt(($a*$a)+($b*$b));
						
			if($distance_from_current > 0)
			{
				
				$truck_stop_w_distance["truck_stop"] = $truck_stop;
				$truck_stop_w_distance["distance_from_current"] = $distance_from_current;
				$truck_stops_w_distance[] = $truck_stop_w_distance;
				
			}
		}
		
		$sorted = array_orderby($truck_stops_w_distance,"distance_from_current",SORT_ASC);
	
		$i = 0;
		//FOREACH WAYPOINT AS THISWAYPOINT
		foreach($waypoints as $thiswaypoint)
		{
			if($i > 0)
			{
				
				// echo "Current waypoint: $i"."<br>";
				//ADD THISWAYPOINT
				$current_to_this_waypoint = null;
				$current_to_this_waypoint[] = $waypoints[0];
				$current_to_this_waypoint[] = $thiswaypoint;
				
				$current_to_this_wp_map_info = get_map_info($current_to_this_waypoint);
				
				// echo "Current to WP $i: ".$current_to_this_wp_map_info["map_miles"];
				// echo "<br><br>";
				
				foreach($sorted as $stop)
				{
					
					// echo "Current Truck Stop: ".$stop["truck_stop"]["name"]."<br>";
					
					//CREATE CURRENT TRUCK STOP
					$truck_stop_waypoint = null;
					$truck_stop_waypoint["address"] = $stop["truck_stop"]["address"];
					$truck_stop_waypoint["city"] = $stop["truck_stop"]["city"];
					$truck_stop_waypoint["state"] = $stop["truck_stop"]["state"];
					
					$current_to_truckstop_waypoints = null;
					$current_to_truckstop_waypoints[] = $waypoints[0];
					$current_to_truckstop_waypoints[] = $truck_stop_waypoint;
					
					$current_to_truckstop_map_info = get_map_info($current_to_truckstop_waypoints);
					
					//IF THISWAYPOINT IS CLOSER THAN STOP
					if($current_to_truckstop_map_info["map_miles"] > $current_to_this_wp_map_info["map_miles"])
					{
						// echo "Farther than current waypoint!"."<br>";
						// echo "<br>__________________________________________<br>";
						// //BREAK
						// break;
					}
					
					//CREATE NEW WAYPOINTS ARRAY WITH TRUCKSTOP WAYPOINT INSERTED BETWEEN CURRENT AND WP1
					$current_to_wp_w_truckstop_waypoints = null;
					$current_to_wp_w_truckstop_waypoints[] = $waypoints[0];
					$current_to_wp_w_truckstop_waypoints[] = $truck_stop_waypoint;
					$current_to_wp_w_truckstop_waypoints[] = $thiswaypoint;
					
					$current_to_wp_w_truckstop_map_info = get_map_info($current_to_wp_w_truckstop_waypoints);
					
					$oor_distance = $current_to_wp_w_truckstop_map_info["map_miles"] - $current_to_this_wp_map_info["map_miles"];
					
					$oor_tolerance = 10;
					
					//COMPARE MAP MILES FROM BOTH RESULTS
					if($oor_distance < $oor_tolerance)
					{
						//STORE TRUCK STOP OUTSIDE OF LOOP
						$closet_in_route_fuel_stop = $stop["truck_stop"];
						
						// echo "Closest In-Route Truck Stop: ".$stop["truck_stop"]["name"]." ".$truck_stop_waypoint["city"]." ".$truck_stop_waypoint["state"];
						// echo "<br><br>";
						// echo "Current to WP $i w/ truckstop: ".$current_to_wp_w_truckstop_map_info["map_miles"];
						// echo "<br><br>";
						// echo "In route!"."<br>";
						// echo "<a href=".$current_to_wp_w_truckstop_map_info['route_url'].">Route Link</a>";
						// echo "<br>__________________________________________<br>";
						
						//RETURN TRUCKSTOP
						return $closet_in_route_fuel_stop;
						//BREAK
						break;
					}
					else
					{
						// echo "Truck stop is OOR: ".$oor_distance;
						// echo "<br>__________________________________________";
						// echo "<br><br>";
						
					}
				}//end foreach sorted stops
			}
			$i++;
		}//END FOREACH WAYPOINTS
		echo "null";	
		return null;
	}
	
	
	function array_orderby()
	{
		$args = func_get_args();
		$data = array_shift($args);
		foreach ($args as $n => $field) {
			if (is_string($field)) {
				$tmp = array();
				foreach ($data as $key => $row)
					$tmp[$key] = $row[$field];
				$args[$n] = $tmp;
				}
		}
		$args[] = &$data;
		call_user_func_array('array_multisort', $args);
		return array_pop($args);
	}
	
	
	function get_address_from_gps($lat, $long)
	{
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
		$city = $data->results[0]->address_components[3]->long_name;
		$state = $data->results[0]->address_components[5]->long_name;
		$zip = $data->results[0]->address_components[7]->long_name;
		
		return $street_number.' '.$street.' '.$city.' '.$state.' '.$zip;
		
	}
	
	function test($param)
	{
		return $param * 2;
	}