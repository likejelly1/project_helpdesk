<?php

namespace App\Controllers;

use App\Models\Ro_SR;
use App\Models\Ms_Priority;

class RO extends BaseController
{
	function __construct() {
		helper("custom_helper");
		if(!is_PIC_RO())
			wp_redirect("sr/my_service");	
	}

	public function my_ro() {
		if($this->session->has('filterSR')) {
			$this->session->remove('filterSR');
		}

		$ms_priority = new Ms_Priority();
		$data['ms_priority'] = $ms_priority->findAll();

		$order_svc["svc"] = "ASC";
		$order_site["site"] = "ASC";

		$data['svc'] = array();
		$data['site'] = is_PIC_RO();

		echo view('header');
		echo view('ro/table_ro', $data);
		echo view('footer');
	}

	public function newRO() {
		$ro_sr = new Ro_SR();
		$no_SR = $this->request->getPost("no_SR");
		if($this->request->getPost('id_detail_sr')) {
	    	$data_model = $ro_sr->newRO($this->request->getPost('id_detail_sr'));
		}

		$ro_sr->ketRO($this->request->getPost('ket'));
		wp_redirect("SR/view?sr=".$no_SR);
	}

	public function printRO() {
		$post = $this->request->getPost();
		if($post) {
			$no_RO = $post['no_RO'];
		} else {
			$post["no_RO"] = "330/IT/HO/2021";
			$no_RO = $post['no_RO'];
		}

		$ro_sr = new Ro_SR();
		$data =  $ro_sr->detailRO($no_RO);
		echo view('doc/request_order', $data);
	}

	public function ajaxMyRO() {
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

	        $filter_site = is_PIC_RO();

	        $data = array();

	        $ro_sr = new Ro_SR();
	        $data_model = $ro_sr->get_DetailSR_DT($filter_data, $filter_site);
	        $total = $ro_sr->total_DetailSR_DT($filter_site);
	        $totalFiltered = $ro_sr->totalFiltered_DetailSR_DT($filter_data['search'], $filter_site);

	        foreach ($data_model as $key => $value) 
	        {
	        	$nik = $this->session->get("nik");
	        	$supported_by = (array)json_decode($value['supported_by']);
	
	        	if(!is_array($supported_by))
	        		$supported_by = array();

	        	
	        	$button = '<td><div style="display:flex">';
	        	$button .= '<a href="'.base_url('SR/view?sr='.$value['no_SR']).'" target="_blank" class="btn btn-info"><i class="far fa-eye"></i></a>&nbsp;';

	        	if($value['status'] == 0) {
		        	if($value['pic'] == $nik)
		        		$button .= '<a href="#_Modal" class="btn btn-primary sr_process" no_SR="'.$value['no_SR'].'" data-toggle="modal" data-target="#_Modal"><i class="far fa-edit"></i></a>&nbsp;';
	        	}

	        	$button .= '</td>';

	        	//Alias of PIC and Requester
	        	$temp_pic_name = explode(" ", $value['pic_name']);
	        	$pic_name = "";
	        	foreach ($temp_pic_name as $key_pic => $value_pic) {
	        		$pic_name .= $value_pic[0];
	        	}

	        	$temp_requester_name = explode(" ", $value['requester_name']);
	        	$requester_name = "";
	        	foreach ($temp_requester_name as $key_requester => $value_requester) {
	        		$requester_name .= $value_requester[0];
	        	}

	        	//Tooltip
	        	$no_SR = "<td><div text_tool='".$value['request']."'>".$value['no_SR']."</div></td>";
	        	$pic_name = "<td><div text_tool='".$value['pic_name']."'>".$pic_name."</div></td>";
	        	$requester_name = "<td><div text_tool='".$value['requester_name']."'>".$requester_name."</div></td>";

	        	$arr_status = status();
	        	$status = $arr_status[$value["status"]];

	            $data[] = array (
	                $no_SR,
	                $requester_name,
	                $value['svc'],
	                $value['site'],
	                $value['priority_name'],
	                $value['expected_resolutionDate'],
	                $pic_name,
	                $value['item_lbl'],
	                $status,
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
