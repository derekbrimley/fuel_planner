<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


//TEMPLATE: INSERT, SELECT (many), SELECT (one), UPDATE, DELETE

	//INSERT TEMPLATE
	function db_insert_template($template)
	{
		db_insert_table("template",$template);
	
	}//END db_insert_template	

	//SELECT TEMPLATES (many)
	function db_select_templates($where,$order_by = 'id',$limit = 'all')
	{
		return db_select_template($where,$order_by,$limit,"many");
		
	}//end db_select_templates() many	

	//SELECT TEMPLATE (one)
	function db_select_template($where,$order_by = 'id',$limit = 'all',$many = 'one')
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = "";
		if(!empty($where))
		{
			$where_sql = " WHERE ";
		}
		$values = array();
		if(is_array($where))
		{
			$i = 0;
			$values = array();
			foreach($where as $key => $value)
			{
				
				if ($i > 0)
				{
					$where_sql = $where_sql." AND";
				}
				
				if ($value == null)
				{
					$where_sql = $where_sql." template.".$key." is ?";
				}
				else if (substr($value,0,1) == "%" || substr($value,-1) == "%") //IF VALUE START OR ENDS WITH A %
				{
					$where_sql = $where_sql." template.".$key." LIKE ?";
				}
				else
				{
					$where_sql = $where_sql." template.".$key." = ?";
				}
				$values[$i] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else
		{
			$where_sql = $where_sql.$where;
		}
		
		$limit_txt = "";
		if($limit != "all")
		{
			$limit_txt = " LIMIT ".$limit;
		}
		
		$sql = "SELECT 
				template.id as id,
				template.recorder_id as recorder_id,
				person.f_name as f_name ,
				template.load_id as load_id,
				`load`.customer_load_number,
				template.truck_id as truck_id ,
				truck.truck_number,
				template.trailer_id as trailer_id ,
				trailer.trailer_number,
				miles_type,
				template.main_driver_id as main_driver_id ,
				main_driver.client_nickname as main_driver_nickname ,
				template.codriver_id as codriver_id ,
				codriver.client_nickname as codriver_nickname ,
				entry_type,
				entry_datetime,
				city,
				state,
				address,
				odometer,
				route,
				miles,
				out_of_route,
				gallons,
				fuel_expense,
				template.mpg AS entry_mpg,
				entry_notes
				FROM `template`
				LEFT JOIN person ON template.recorder_id = person.id 
				LEFT JOIN  `load` ON  `load_id` =  `load`.id
				LEFT JOIN truck ON template.truck_id = truck.id 
				LEFT JOIN trailer ON template.trailer_id = trailer.id 
				LEFT JOIN client as main_driver ON template.main_driver_id = main_driver.id 
				LEFT JOIN client as codriver ON template.codriver_id = codriver.id ".$where_sql." ORDER BY ".$order_by.$limit_txt;
		
		//error_log($sql." | LINE ".__LINE__." ".__FILE__);
		$query = $CI->db->query($sql,$values);
		$templates = array();
		foreach ($query->result() as $row)
		{
			$template['id'] = $row->id;
			$template['load_id'] = $row->load_id;
			$template['truck_id'] = $row->truck_id;
			$template['trailer_id'] = $row->trailer_id;
			$template['miles_type'] = $row->miles_type;
			$template['main_driver_id'] = $row->main_driver_id;
			$template['codriver_id'] = $row->codriver_id;
			$template['entry_type'] = $row->entry_type;
			$template['entry_datetime'] = $row->entry_datetime;
			$template['city'] = $row->city;
			$template['state'] = $row->state;
			$template['address'] = $row->address;
			$template['odometer'] = $row->odometer;
			$template['route'] = $row->route;
			$template['miles'] = $row->miles;
			$template['out_of_route'] = $row->out_of_route;
			$template['gallons'] = $row->gallons;
			$template['fuel_expense'] = $row->fuel_expense;
			$template['mpg'] = $row->entry_mpg;
			$template['entry_notes'] = $row->entry_notes;
			
			$recorder["f_name"] = $row->f_name;
			$template["recorder"] = $recorder;
			
			$load["customer_load_number"] = $row->customer_load_number;
			$template["load"] = $load;
			
			$truck["truck_number"] = $row->truck_number;
			$template["truck"] = $truck;
			
			$trailer["trailer_number"] = $row->trailer_number;
			$template["trailer"] = $trailer;
			
			$main_driver["client_nickname"] = $row->main_driver_nickname;
			$template["main_driver"] = $main_driver;
			
			$codriver["client_nickname"] = $row->codriver_nickname;
			$template["codriver"] = $codriver;
			
			$templates[] = $template;
			
		}// end foreach
		
		if (empty($template))
		{
			return null;
		}
		else if($many == 'one')
		{
			return $template;
		}
		else if($many == "many")
		{
			return $templates;
		}
	}//end db_select_template()

	//UPDATE TEMPLATE
	function db_update_template($set,$where)
	{
		db_update_table("template",$set,$where);
		
	}//end update template	
	
	//DELETE TEMPLATE	
	function db_delete_template($where)
	{
		db_delete_from_table("template",$where);
		
	}//end db_delete_template()	
	
	
	
	
	
//GENERIC FUNCTIONS TO A HANDLE THE VARIOUS DATABASE FUNCTIONS

	//INSERT TABLE
	function db_insert_table($table,$object)
	{
		$CI =& get_instance();
		$field_names = "";
		$field_values = "";
		foreach($object as $key => $value)
		{
			$field_names = $field_names." `".$key."`,";
			$field_values = $field_values." ?,";
			$values[] = $value;
		}
		//REMOVE REMAINING COMMA AT THE END OF THE STRING
		$field_names = substr($field_names, 0, -1);
		$field_values = substr($field_values, 0, -1);
		
		$sql = "INSERT INTO `$table` (`id`,$field_names) VALUES (NULL,$field_values)";
		$CI->db->query($sql,$values);
	
	}//END db_insert_template	
	
	//SELECT TABLES (many)  ************* NEEDS TO BE UPDATED EVERY TIME A TABLE IS ADDED TO THE DB ************
	function db_select_tables($table,$where,$order_by = 'id')
	{
		$CI =& get_instance();
		$where_sql = " ";
		$values = array();
		if(is_array($where))
		{
			$i = 0;
			foreach($where as $key => $value)
			{
				
				if ($i > 0)
				{
					$where_sql = $where_sql." And";
				}
				
				if ($value == null)
				{
					$where_sql = $where_sql." ".$key." is ?";
				}
				else
				{
					$where_sql = $where_sql." ".$key." = ?";
				}
				$values[$i] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else
		{
			$where_sql = $where;
		}
		
		$sql = "SELECT * FROM `$table` WHERE ".$where_sql." ORDER BY ".$order_by;
		$query_table = $CI->db->query($sql,$values);
		
		$object = array();
		$objects = array();
		foreach ($query_table->result() as $row)
		{
			//DEPENDING ON THE TABLE SELECT THE ROWS FROM THAT TABLE
			$object_where['id'] = $row->id;
			if($table == "account")
			{
				$object = db_select_account($object_where);
			}
			else if($table == "account_entry")
			{
				$object = db_select_account_entry($object_where);
			}
			else if($table == "client")
			{
				$object = db_select_client($object_where);
			}
			else if($table == "customer")
			{
				$object = db_select_customer($object_where);
			}
			else if($table == "drop")
			{
				$object = db_select_drop($object_where);
			}
			else if($table == "invoice")
			{
				$object = db_select_invoice($object_where);
			}
			else if($table == "invoice_allocation")
			{
				$object = db_select_invoice_allocation($object_where);
			}
			else if($table == "load")
			{
				$object = db_select_load($object_where);
			}
			else if($table == "load_expense")
			{
				$object = db_select_load_expense($object_where);
			}
			else if($table == "pick")
			{
				$object = db_select_pick($object_where);
			}
			else if($table == "permission")
			{
				$object = db_select_permission($object_where);
			}
			else if($table == "route_request")
			{
				$object = db_select_route_request($object_where);
			}
			else if($table == "settlement_adjustment")
			{
				$object = db_select_settlement_adjustment($object_where);
			}
			else if($table == "settlement_expense")
			{
				$object = db_select_settlement_expense($object_where);
			}
			else if($table == "settlement_profit_split")
			{
				$object = db_select_settlement_profit_split($object_where);
			}
			else if($table == "stop")
			{
				$object = db_select_stop($object_where);
			}
			else if($table == "truck")
			{
				$object = db_select_truck($object_where);
			}
			else if($table == "user_permission")
			{
				$object = db_select_user_permission($object_where);
			}
			else
			{
				echo "You forgot to add this table to the db select tables function";
			}
			
			
			$objects[] = $object;
		}
		
		return $objects;
	}//end db_select_tables() many		
	
	//UPDATE TABLE
	function db_update_table($table,$set,$where)
	{
		$CI =& get_instance();
		$i = 0;
		$set_sql = " ";
		$values = array();
		foreach($set as $key => $value)
		{
			if ($i > 0)
			{
				$set_sql = $set_sql.", ";
			}
			
			if ($value == null)
			{
				$set_sql = $set_sql." ".$key." = NULL ";
			}
			else
			{
				$set_sql = $set_sql." ".$key." = ?";
				$values[] = $value;
			}
			$i++;
		}
		
		$i = 0;
		$where_sql = " ";
		if(is_array($where))
		{
			$i = 0;
			foreach($where as $key => $value)
			{
				
				if ($i > 0)
				{
					$where_sql = $where_sql." And";
				}
				
				if ($value == null)
				{
					$where_sql = $where_sql." ".$key." is ?";
				}
				else
				{
					$where_sql = $where_sql." ".$key." = ?";
				}
				$values[] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else
		{
			$where_sql = $where;
		}
		
		$sql = "UPDATE `$table` SET ".$set_sql." WHERE ".$where_sql;
		//echo $sql;
		//print_r($values);
		$CI->db->query($sql,$values);
	}//end update table
	
	//DELETE FROM TABLE
	function db_delete_from_table($table,$where)
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = " ";
		$values = array();
		if(is_array($where))
		{
			$i = 0;
			foreach($where as $key => $value)
			{
				
				if ($i > 0)
				{
					$where_sql = $where_sql." And";
				}
				
				if ($value == null)
				{
					$where_sql = $where_sql." ".$key." is ?";
				}
				else
				{
					$where_sql = $where_sql." ".$key." = ?";
				}
				$values[$i] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else
		{
			$where_sql = $where;
		}
		
		$sql = "DELETE FROM `$table` WHERE ".$where_sql;
		$CI->db->query($sql,$values);
		
	}//end db_delete_from_table()	
	
	//GET LIST OF DISTINT EXPENSE CATEGORIES
	function get_distinct($column_name,$table_name,$where = null,$order_by = "none")
	{
		if($order_by = "none")
		{
			$order_by = $column_name;
		}
	
		$CI =& get_instance();
		
		$categories = array();
		
		$values = array();
		$where_sql = " ";
		if(!empty($where))
		{
			if(is_array($where))
			{
				$i = 0;
				foreach($where as $key => $value)
				{
					
					if ($i > 0)
					{
						$where_sql = $where_sql." And";
					}
					
					if ($value == null)
					{
						$where_sql = $where_sql." ".$key." is ?";
					}
					else
					{
						$where_sql = $where_sql." ".$key." = ?";
					}
					$values[$i] = $value;
					//echo "value[$i] = $value ";
					$i++;
				}
				
			}
			else
			{
				$where_sql = $where;
			}
			
			$where_sql = " WHERE ".$where_sql;
		}
		
		$sql = "SELECT distinct(".$column_name.") AS column_name FROM `".$table_name."`".$where_sql." ORDER BY ".$order_by;
		//error_log("SQL: ".$sql." | LINE ".__LINE__." ".__FILE__);
		$query = $CI->db->query($sql,$values);
		foreach ($query->result() as $row)
		{
			$categories[] = $row->column_name;
		}
		
		return $categories;
	}
	
	
	
	
	
//ROUTE_REQUEST: INSERT, SELECT (many), SELECT (one), UPDATE, DELETE

	//INSERT ROUTE_REQUEST
	function db_insert_route_request($route_request)
	{
		db_insert_table("route_request",$route_request);
	
	}//END db_insert_route_request	

	//SELECT ROUTE_REQUESTS (many)
	function db_select_route_requests($where,$order_by = 'id',$limit = 'all')
	{
		return db_select_route_request($where,$order_by,$limit,"many");
		
	}//end db_select_route_requests() many	

	//SELECT ROUTE_REQUEST (one)
	function db_select_route_request($where,$order_by = 'id',$limit = 'all',$many = 'one')
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = "";
		if(!empty($where))
		{
			$where_sql = " WHERE ";
		}
		$values = array();
		if(is_array($where))
		{
			$i = 0;
			$values = array();
			foreach($where as $key => $value)
			{
				
				if ($i > 0)
				{
					$where_sql = $where_sql." And";
				}
				
				if ($value == null)
				{
					$where_sql = $where_sql." route_request.".$key." is ?";
				}
				else if (substr($value,0,1) == "%" || substr($value,-1) == "%") //IF VALUE START OR ENDS WITH A %
				{
					$where_sql = $where_sql." route_request.".$key." LIKE ?";
				}
				else
				{
					$where_sql = $where_sql." route_request.".$key." = ?";
				}
				$values[$i] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else
		{
			$where_sql = $where_sql.$where;
		}
		
		$limit_txt = "";
		if($limit != "all")
		{
			$limit_txt = " LIMIT ".$limit;
		}
		
		$sql = "SELECT *
				FROM `route_request`
				".$where_sql." ORDER BY ".$order_by.$limit_txt;
		
		echo $sql;
		$query = $CI->db->query($sql,$values);
		$route_requests = array();
		foreach ($query->result() as $row)
		{
			$route_request['id'] = $row->id;
			$route_request['web_service'] = $row->web_service;
			$route_request['param_url'] = $row->param_url;
			$route_request['status'] = $row->status;
			$route_request['map_miles'] = $row->map_miles;
			$route_request['route_url'] = $row->route_url;
			$route_request['count'] = $row->count;
			
			$route_requests[] = $route_request;
			
		}// end foreach
		
		if (empty($route_request))
		{
			return null;
		}
		else if($many == 'one')
		{
			return $route_request;
		}
		else if($many == "many")
		{
			return $route_requests;
		}
	}//end db_select_route_request()

	//UPDATE ROUTE_REQUEST
	function db_update_route_request($set,$where)
	{
		db_update_table("route_request",$set,$where);
		
	}//end update route_request	
	
	//DELETE ROUTE_REQUEST	
	function db_delete_route_request($where)
	{		
	
		db_delete_from_table("route_request",$where);
		
	}//end db_delete_route_request()
	


	
//TRUCK_STOP: INSERT, SELECT (many), SELECT (one), UPDATE, DELETE

	//INSERT TRUCK_STOP
	function db_insert_truck_stop($truck_stop)
	{
		db_insert_table("truck_stop",$truck_stop);
	
	}//END db_insert_truck_stop	

	//SELECT TRUCK_STOPS (many)
	function db_select_truck_stops($where,$order_by = 'id',$limit = 'all')
	{
		return db_select_truck_stop($where,$order_by,$limit,"many");
		
	}//end db_select_truck_stops() many	

	//SELECT TRUCK_STOP (one)
	function db_select_truck_stop($where,$order_by = 'id',$limit = 'all',$many = 'one')
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = "";
		if(!empty($where))
		{
			$where_sql = " WHERE ";
		}
		$values = array();
		if(is_array($where))
		{
			$i = 0;
			$values = array();
			foreach($where as $key => $value)
			{
				
				if ($i > 0)
				{
					$where_sql = $where_sql." AND";
				}
				
				if ($value == null)
				{
					$where_sql = $where_sql." truck_stop.".$key." is ?";
				}
				else if (substr($value,0,1) == "%" || substr($value,-1) == "%") //IF VALUE START OR ENDS WITH A %
				{
					$where_sql = $where_sql." truck_stop.".$key." LIKE ?";
				}
				else
				{
					$where_sql = $where_sql." truck_stop.".$key." = ?";
				}
				$values[$i] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else
		{
			$where_sql = $where_sql.$where;
		}
		
		$limit_txt = "";
		if($limit != "all")
		{
			$limit_txt = " LIMIT ".$limit;
		}
		
		$sql = "SELECT *
				FROM truck_stop 
				".$where_sql." ORDER BY ".$order_by.$limit_txt;
		
		error_log($sql." | LINE ".__LINE__." ".__FILE__);
		$query = $CI->db->query($sql,$values);
		$truck_stops = array();
		foreach ($query->result() as $row)
		{
			$truck_stop['id'] = $row->id;
			$truck_stop['stop_code'] = $row->stop_code;
			$truck_stop['name'] = $row->name;
			$truck_stop['address'] = $row->address;
			$truck_stop['city'] = $row->city;
			$truck_stop['state'] = $row->state;
			$truck_stop['zip'] = $row->zip;
			$truck_stop['lat'] = $row->lat;
			$truck_stop['long'] = $row->long;
			$truck_stop['card'] = $row->card;
			
			$truck_stops[] = $truck_stop;
			
		}// end foreach
		
		if (empty($truck_stop))
		{
			return null;
		}
		else if($many == 'one')
		{
			return $truck_stop;
		}
		else if($many == "many")
		{
			return $truck_stops;
		}
	}//end db_select_truck_stop()

	//UPDATE TRUCK_STOP
	function db_update_truck_stop($set,$where)
	{
		db_update_table("truck_stop",$set,$where);
		
	}//end update truck_stop	
	
	//DELETE TRUCK_STOP	
	function db_delete_truck_stop($where)
	{
		db_delete_from_table("truck_stop",$where);
		
	}//end db_delete_truck_stop()	

	
	
	
//TRUCK_STOP_PRICE: INSERT, SELECT (many), SELECT (one), UPDATE, DELETE

	//INSERT TRUCK_STOP_PRICE
	function db_insert_truck_stop_price($truck_stop_price)
	{
		db_insert_table("truck_stop_price",$truck_stop_price);
	
	}//END db_insert_truck_stop_price	

	//SELECT TRUCK_STOP_PRICES (many)
	function db_select_truck_stop_prices($where,$order_by = 'id',$limit = 'all')
	{
		return db_select_truck_stop_price($where,$order_by,$limit,"many");
		
	}//end db_select_truck_stop_prices() many	

	//SELECT TRUCK_STOP_PRICE (one)
	function db_select_truck_stop_price($where,$order_by = 'id',$limit = 'all',$many = 'one')
	{
		$CI =& get_instance();
		$i = 0;
		$where_sql = "";
		if(!empty($where))
		{
			$where_sql = " WHERE ";
		}
		$values = array();
		if(is_array($where))
		{
			$i = 0;
			$values = array();
			foreach($where as $key => $value)
			{
				
				if ($i > 0)
				{
					$where_sql = $where_sql." AND";
				}
				
				if ($value == null)
				{
					$where_sql = $where_sql." truck_stop_price.".$key." is ?";
				}
				else if (substr($value,0,1) == "%" || substr($value,-1) == "%") //IF VALUE START OR ENDS WITH A %
				{
					$where_sql = $where_sql." truck_stop_price.".$key." LIKE ?";
				}
				else
				{
					$where_sql = $where_sql." truck_stop_price.".$key." = ?";
				}
				$values[$i] = $value;
				//echo "value[$i] = $value ";
				$i++;
			}
		}
		else
		{
			$where_sql = $where_sql.$where;
		}
		
		$limit_txt = "";
		if($limit != "all")
		{
			$limit_txt = " LIMIT ".$limit;
		}
		
		$sql = "SELECT 
				FROM `truck_stop_price`
				".$where_sql." ORDER BY ".$order_by.$limit_txt;
		
		//error_log($sql." | LINE ".__LINE__." ".__FILE__);
		$query = $CI->db->query($sql,$values);
		$truck_stop_prices = array();
		foreach ($query->result() as $row)
		{
			$truck_stop_price['id'] = $row->id;
			$truck_stop_price['truck_stop_id'] = $row->truck_stop_id;
			$truck_stop_price['date'] = $row->date;
			$truck_stop_price['price'] = $row->price;
			
			$truck_stop_prices[] = $truck_stop_price;
			
		}// end foreach
		
		if (empty($truck_stop_price))
		{
			return null;
		}
		else if($many == 'one')
		{
			return $truck_stop_price;
		}
		else if($many == "many")
		{
			return $truck_stop_prices;
		}
	}//end db_select_truck_stop_price()

	//UPDATE TRUCK_STOP_PRICE
	function db_update_truck_stop_price($set,$where)
	{
		db_update_table("truck_stop_price",$set,$where);
		
	}//end update truck_stop_price	
	
	//DELETE TRUCK_STOP_PRICE	
	function db_delete_truck_stop_price($where)
	{
		db_delete_from_table("truck_stop_price",$where);
		
	}//end db_delete_truck_stop_price()	
	



	