<?php

namespace App\Controllers;
use App\Models\Trx_SR;
use App\Models\Ms_Priority;
use App\Models\Request_SR;

class SR_user extends BaseController
{
	function __construct() {
		helper("custom_helper");
	}

	public function index()
	{
		if($this->session->has('filterSR')) {
			$this->session->remove('filterSR');
			// $data['filterSR'] = ["status" => "0"];
			// $this->session->set($data);
		}

		$ms_priority = new Ms_Priority();
		$data['ms_priority'] = $ms_priority->findAll();

		$order_svc["svc"] = "ASC";
		$order_site["site"] = "ASC";

		$request_sr = new Request_SR();
		$data['svc'] = $request_sr->distinctColumnMyService("svc", $order_svc);

		echo view('header');
		echo view('svc/user/table_myrequest', $data);
		echo view('footer');	
	}

	public function my_request() {
		if($this->session->has('filterSR')) {
			$this->session->remove('filterSR');
			// $data['filterSR'] = ["status" => "0"];
			// $this->session->set($data);
		}

		$ms_priority = new Ms_Priority();
		$data['ms_priority'] = $ms_priority->findAll();

		$order_svc["svc"] = "ASC";
		$order_site["site"] = "ASC";

		$request_sr = new Request_SR();
		$data['svc'] = $request_sr->distinctColumnMyService("svc", $order_svc);

		echo view('header');
		echo view('svc/user/table_myrequest', $data);
		echo view('footer');	
	}

	public function loadComplete() {
		$data = $this->request->getPost();
		return view('svc/user/review', $data);
	}

	public function complete() {
		$post = $this->request->getPost();
		$request_sr = new Request_SR();
		$request_sr->complete($post);

		wp_redirect("SR_user/my_request");
	}

	public function ajaxMyRequest()
	{
		if ($this->request->isAJAX())
        {
	        $order_index    = $this->request->getPost('order')[0]['column'];
	        $order          = "`".$this->request->getPost('columns')[$order_index]['name']."`";
	        $order_by       = $this->request->getPost('order')[0]['dir'];

	        $filter_data = array (
	            "start"     => $this->request->getPost('start'),
	            "length"    => $this->request->getPost('length'),
	            "search"    => trim($this->request->getPost('search')['value']),
	            "order"     => $order,
	            "order_by"  => $order_by,
	        );

	        $data = array();

	        $request_sr = new Request_SR();
	        $data_model = $request_sr->getTrxDT($filter_data);
	        $total = $request_sr->totalTrxDT();
	        $totalFiltered = $request_sr->totalTrxFilteredDT($filter_data['search']);

	        foreach ($data_model as $key => $value) 
	        {
	        	$nik = $this->session->get("nik");
	        	$supported_by = (array)json_decode($value['supported_by']);
	
	        	if(!is_array($supported_by))
	        		$supported_by = array();
	        	
	        	$button = '<td><div style="display:flex">';
	        	$button .= '<a href="'.base_url('SR/view?sr='.$value['no_SR']).'" target="_blank" class="btn btn-info"><i class="far fa-eye"></i> View</a>&nbsp;';

	        	$temp_status = ["A", "R"];
	        	if(in_array($value['status'], $temp_status)) {
	        		$button .= '<a href="#_Modal" class="btn btn-success sr_check" no_SR="'.$value['no_SR'].'" data-toggle="modal" data-target="#_normalModal"><i class="fa fa-check"></i> Complete</a>&nbsp;';
	        	}
	        	$button .= '</td>';

	        	$arr_status = status();
	        	$status = $arr_status[$value["status"]];

	            $data[] = array (
	                $value['no_SR'],
	                $value['svc'],
	                $value['priority_name'],
	                $value['expected_resolutionDate'],
	                $value['pic_name'],
	                $status,
	                emoji_score($value['score']),
	                $button,
	            );
	        }

	        $output = array(
	            "draw" => $this->request->getPost('draw'),
	            "recordsTotal" => $total,
	            "recordsFiltered" => $totalFiltered,
	            "data" => $data
	        );

	        echo json_encode($output);
	        exit();
	    }
	}
}
