<?php

class Esignature extends CI_Controller{
	
	function index()
	{
		
		$data['title'] = 'Esignature Test';
		$this->load->view('esignature_view',$data);
		
	}
}