<?php

namespace App\Controllers;

use App\Models\Ms_Priority;
use App\Models\Trx_SR;
use App\Models\Ms_Karyawan;
use App\Models\Approve_SR;

class Approve extends BaseController
{
	function __construct() {
		helper("custom_helper");
	}

	public function index()
	{
		if($this->session->has('filterSR'))
			$this->session->remove('filterSR');

		$ms_priority = new Ms_Priority();
		$data['ms_priority'] = $ms_priority->findAll();

		$order_svc["svc"] = "ASC";
		$order_site["site"] = "ASC";

		$trx_sr = new Trx_SR();
		$data['svc'] = $trx_sr->distinctColumnMyService("svc", $order_svc);
		$data['site'] = $trx_sr->distinctColumnMyService("site", $order_site);

		echo view('header');
		echo view('svc/approve/table_approve', $data);
		echo view('footer');
	}

	public function subtmitApprove() {
		$data['no_SR'] = $this->request->getPost("no_SR");
		$data['nik'] = $this->session->get("nik");

		$status_approve = array();
		foreach ($this->request->getPost('dsr') as $key => $value) {
			$data['id_detail_sr'] = $key;
			$data['status'] = $value;
			$data['ket'] = trim($this->request->getPost("ket")[$key]);

			$approve_sr = new Approve_SR();
			$status_approve[] = $approve_sr->changeStatus($data);
		}

		$status_approve = array_unique($status_approve);
		$filter['no_SR'] = $data['no_SR'];
		$trx_sr = new Trx_SR();
		$tmp_trxsr = $trx_sr->get($filter)[0];

		foreach ($status_approve as $key => $tmp_status)
		{
			if(strlen($tmp_status) > 1 &&  $tmp_status != $data['nik']) 
			{
		        /* Telegram Notif */
		        $msg = "Need Approval : \n";
		        $msg .= $data['no_SR']. "\n";
		        foreach ($tmp_trxsr['detail_sr'] as $key => $value) {
		            $msg .= "<i>".$value['name_DSVC']."</i> : ".$value['desc_DSVC']."\n";
		        }

		        $ms_karyawan = new Ms_Karyawan();
		        $pic_chat_id = $ms_karyawan->getTgChat($tmp_status);

		        if($pic_chat_id)
		            tg_message($msg, $pic_chat_id);
	       	}
		}

		wp_redirect("SR/view?sr=".$data['no_SR']);
	}

	public function ajaxMyApprove()
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

	        $approve_sr = new Approve_SR();
	        $data_model = $approve_sr->getTrxDT($filter_data);
	        $total = $approve_sr->totalTrxDT();
	        $totalFiltered = $approve_sr->totalTrxFilteredDT($filter_data['search']);

	        foreach ($data_model as $key => $value) 
	        {
	        	$nik = $this->session->get("nik");
	        	$supported_by = (array)json_decode($value['supported_by']);
	
	        	if(!is_array($supported_by))
	        		$supported_by = array();
	        	
	        	$button = '<td><div style="display:flex">';
	        	$button .= '<a href="'.base_url('SR/view?sr='.$value['no_SR']).'" target="_blank" class="btn btn-info"><i class="far fa-eye"></i> View</a>&nbsp;';

	        	if($value['status'] == 0) {
		        	if($value['pic'] == $nik)
		        		$button .= '<a href="#_Modal" class="btn btn-info sr_process" no_SR="'.$value['no_SR'].'" data-toggle="modal" data-target="#_Modal"><i class="far fa-edit"></i></a>&nbsp;';

		        	/* Munculkan action plan selain SVC03 
					Jika PIC nya sesuai dengan yang login / Support pada SR tersebut */	
		   			if($value['svc'] != "SVC03" && ($value['pic'] == $nik || isset($supported_by[$nik])))
		        	$button .= '<a href="#_Modal" class="btn btn-info sr_action" no_SR="'.$value['no_SR'].'" data-toggle="modal" data-target="#_Modal"><i class="fas fa-diagnoses"></i></a>';
	        	}
	        	$button .= '</td>';

	        	$temp_pic_name = explode(" ", $value['pic_name']);
	        	$pic_name = "";
	        	foreach ($temp_pic_name as $key_pic => $value_pic) {
	        		$pic_name .= $value_pic[0];
	        	}

	        	$arr_status = status();
	        	$status = $arr_status[$value["status"]];

	            $data[] = array (
	                $value['no_SR'],
	                $value['svc'],
	                $value['site'],
	                $value['priority_name'],
	                $value['expected_resolutionDate'],
	                $pic_name,
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
