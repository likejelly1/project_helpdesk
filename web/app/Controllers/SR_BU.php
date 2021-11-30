<?php

namespace App\Controllers;

use App\Models\Request_SR;
use App\Models\Ms_SVC;
use App\Models\Ms_dSVC;
use App\Models\Ms_Priority;
use App\Models\Ms_Karyawan;
use App\Models\Trx_SR;
use App\Models\IT_Staff;
use App\Models\Approve_SR;

class SR extends BaseController
{
	function __construct() {
		helper("custom_helper");
		if(!$this->isLogin())
			wp_redirect("login");
	}

	public function dashboard() {
		if(!isHO())
			wp_redirect("SR/my_service");
		
		echo view('header');
		echo '<iframe style="width: 100%" height="1060" src="https://app.powerbi.com/view?r=eyJrIjoiOTcxNWUxNTktYjFkNC00ZGIwLTk4YjAtZjU2YzlkMWIyY2I1IiwidCI6ImE1ODc4MTNhLTVhODUtNGFjOS05MmFlLTc5NGU4Y2E2YmMxZSIsImMiOjEwfQ%3D%3D" frameborder="0" allowFullScreen="true"></iframe>';
	 	echo view('footer');
	}

	public function view() {
		if($this->request->getGet("sr")) {
			$filter["no_SR"] = $this->request->getGet("sr");

			$trx_sr = new Trx_SR();
			$data['trx_sr'] = $trx_sr->get($filter);
			if($data['trx_sr']) 
			{
				$data['trx_sr'] = $data['trx_sr'][0];
				$approve_sr = new Approve_SR();
				$arr = array ("no_SR" => $data['trx_sr']['no_SR']);
				$temp_approve = $approve_sr->getApprovalList($arr);

				foreach ($data['trx_sr']['detail_sr'] as $key => $value) {
					foreach ($temp_approve as $key2 => $value2) {
						$arr_approve = array(
							"id_detail_sr" => $value['id'],
							"nik" => $value2['nik'],
						);

						$data['trx_sr']['detail_sr'][$key]['approve'][$value2['nik']] = $approve_sr->getDetailApprove($arr_approve);;
					}
				}

				if($data['trx_sr']['status'] == 0 && $temp_approve) {
					$data['approve_sr'] = $temp_approve;
				}

				echo view('header');
	   			echo view('svc/modal_view', $data);
	   			echo view('footer');
	   		} else {
				wp_redirect("SR/my_service");
			}
		} else {
			wp_redirect("SR/my_service");
		}
	}

	public function delete() {
		$nik = $this->session->get('nik');
		if($nik == "5071") {
			$no_SR = $this->request->getGet("sr");
			$trx_sr = new Trx_SR();
			$result = $trx_sr->deleteSR($no_SR);

			echo $result;
		} else {
			wp_redirect("SR/my_service");
		}
	}

	public function pending_request() {
		$nik = $this->session->get('nik');
		$request_sr = new Request_SR();
		$data['request_sr'] = $request_sr->getReject();
    						
		echo view('header');
		echo view('svc/request/table_pending', $data);
		echo view('footer');	
	}

	public function pending_request_cancel() {
		if($this->request->isAJAX())
		{
			$id = $this->request->getPost("id");
			$request_sr = new Request_SR();
			$request_sr ->set("status", "-")
						->where("id", $id)
						->update();
		}
	}

	public function my_request() {
		$post = $this->request->getPost();
		if($post) {

		} else {
			echo view('header');
			echo view('footer');	
		}	
	}

	public function addAttach() {
		$post = $this->request->getPost();
		if($post) 
		{
			if($imagefile = $this->request->getFiles())
			{
			   $attachment = array();
			   foreach($imagefile['attachment'] as $img)
			   {
			   	  $mime_type = $img->getClientMimeType();
			   	  $type = explode("/", $mime_type);	

			      if ($img->isValid() && ! $img->hasMoved() && $type[0] == "image")
			      {
			           $newName = $img->getRandomName();
			           $img->move("public/stisla/attachment/", $newName);
			           $attachment[] = $newName;
			      }
			   }

			   	$post['attachment'] = $attachment;

				$request_sr = new Request_SR();
				$request_sr->addAttach($post);
			}

			wp_redirect("SR/view?sr=".$post['no_SR']);
		}
	}

	public function request() {
		$post = $this->request->getPost();
		if($post) 
		{
			if($imagefile = $this->request->getFiles())
			{
			   $attachment = array();
			   foreach($imagefile['attachment'] as $img)
			   {
			   	  $mime_type = $img->getClientMimeType();
			   	  $type = explode("/", $mime_type);	

			      if ($img->isValid() && ! $img->hasMoved() && $type[0] == "image")
			      {
			           $newName = $img->getRandomName();
			           $img->move("public/stisla/attachment/", $newName);
			           $attachment[] = $newName;
			      }
			   }
			}

			$post['nik'] = $this->session->get('nik');
			$post['status'] = 0;
			$post['attachment'] = json_encode($attachment);
			$request_sr = new Request_SR();
			$request_sr->insert($post);

			$site_pic = $this->session->get('site');
			$msg = $this->session->get('name')." make new request!\n<i>Request</i> : ".$post['request'];

			$ms_karyawan = new Ms_Karyawan();
			$pic_chat_id = $ms_karyawan->getPICsite($site_pic);

			if($pic_chat_id)
				tg_message($msg, $pic_chat_id);
			
			wp_redirect("SR/pending_request");
		} else {
			echo view('header');
			echo view('svc/request/request');
			echo view('footer');	
		}		
	}

	/* IT - SR */
	public function index()
	{
		// $ms_svc = new Ms_SVC();
		// $data['ms_svc'] = $ms_svc->findAll();
		// echo view('header');
		// echo view('svc/svc', $data);
		// echo view('footer');
	}

	public function my_service() 
	{
		if($this->session->has('filterSR')) {
			$this->session->remove('filterSR');
		}

		$ms_priority = new Ms_Priority();
		$data['ms_priority'] = $ms_priority->findAll();

		$order_svc["svc"] = "ASC";
		$order_site["site"] = "ASC";

		$trx_sr = new Trx_SR();
		$data['svc'] = $trx_sr->distinctColumnMyService("svc", $order_svc);
		$data['site'] = $trx_sr->distinctColumnMyService("site", $order_site);

		echo view('header');
		echo view('svc/table_myservice', $data);
		echo view('footer');
	}

	public function process_request() 
	{
		$data["handling_site"] = $this->session->get('it_staff')["site"];
		if(!isHO()) //isHO
			$filter["site"] = $data["handling_site"];

		$filter["company"] = $this->session->get('company');
		$filter["status"] = "0";

		$request_sr = new Request_SR();
		$data['request_sr'] = $request_sr->get($filter);
    						
		echo view('header');
		echo view('svc/request/table_process', $data);
		echo view('footer');	
	}

	public function all() 
	{
		if(!isHO())
			wp_redirect("SR/my_service");

		if($this->session->has('filterSR'))
			$this->session->remove('filterSR');

		$ms_svc = new Ms_SVC();
		$data['ms_svc'] = $ms_svc->findAll();

		$ms_priority = new Ms_Priority();
		$data['ms_priority'] = $ms_priority->findAll();

		echo view('header');
		echo view('svc/table_svc', $data);
		echo view('footer');
	}

	public function rejectSVC() {
		$post = $this->request->getPost();
		$request_sr = new Request_SR();
		$request_sr->reject($post['id']);
	}

	public function loadSVC() 
	{
		if ($this->request->isAJAX())
        {
        	$id = $this->request->getPost("id");
        	$no_sr = $this->request->getPost("no_sr");

        	$ms_svc = new Ms_SVC();
			$data['ms_svc'] = $ms_svc->findAll();

			$request_sr = new Request_SR();
			$data['trx_sr'] = $request_sr->getRequest($id);

   			return view('svc/request/modal_request', $data);
        }
	}

	public function loadDSVC() 
	{
		if ($this->request->isAJAX())
        {
        	$ms_dsvc = new Ms_dSVC();
        	$svc = $this->request->getPost('svc');
        	$data['svc'] = $svc;
        	$data['ms_dsvc'] = $ms_dsvc
        						->where('id_SVC', $svc)
        						->orderBy('name', 'asc')
        						->findAll();
   			return view('svc/dsvc', $data);
        }
	}

	public function submitSR()
	{
		$post = $this->request->getPost();
		if($post) {
			$post['pic'] = $this->session->get('nik');
			$trx_sr = new Trx_SR();
			$trx_sr->add($post);
		}
		else
			wp_redirect("default");
	}

	public function ajaxFilterSR() 
	{
		if ($this->request->isAJAX())
        {
        	if($this->request->getPost("value") == "-") {
        		$name = $this->request->getPost('name');
        		unset($_SESSION['filterSR'][$name]);
        	} else {
        		if($this->session->has('filterSR'))
        			$filterSR = $this->session->get('filterSR');
	        	$filterSR[$this->request->getPost("name")] = $this->request->getPost("value");
	        	$data['filterSR'] = $filterSR;
	        	$this->session->set($data);
        	}
        }
	}

	public function ajaxSRAll()
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

	        $trx_sr = new Trx_SR();
	        $data_model = $trx_sr->getTrxDT($filter_data);
	        $total = $trx_sr->totalTrxDT();
	        $totalFiltered = $trx_sr->totalTrxFilteredDT($filter_data['search']);

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
		        		$button .= '<a href="#_Modal" class="btn btn-primary sr_process" no_SR="'.$value['no_SR'].'" data-toggle="modal" data-target="#_Modal"><i class="far fa-edit"></i> Edit</a>&nbsp;';

		        	/* Munculkan action plan bila status bukan Resolved
					Jika PIC nya sesuai dengan yang login / Support pada SR tersebut */
		   			/*if($value['status'] != "R" && ($value['pic'] == $nik || isset($supported_by[$nik])))
		        	$button .= '<a href="#_Modal" class="btn btn-info sr_action" no_SR="'.$value['no_SR'].'" data-toggle="modal" data-target="#_Modal"><i class="fas fa-diagnoses"></i></a>';*/
		    	}
	        	
	        	$button .= '</div></td>';

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

	public function ajaxMyService()
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

	        $trx_sr = new Trx_SR();
	        $data_model = $trx_sr->getTrxDT($filter_data);
	        $total = $trx_sr->totalTrxDT();
	        $totalFiltered = $trx_sr->totalTrxFilteredDT($filter_data['search']);

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
		        		$button .= '<a href="#_Modal" class="btn btn-primary sr_process" no_SR="'.$value['no_SR'].'" data-toggle="modal" data-target="#_Modal"><i class="far fa-edit"></i> Edit</a>&nbsp;';

		        	/* Munculkan action plan bila status bukan Resolved
					Jika PIC nya sesuai dengan yang login / Support pada SR tersebut */	
		   			/*if($value['status'] != "R" && ($value['pic'] == $nik || isset($supported_by[$nik])))
		        	$button .= '<a href="#_Modal" class="btn btn-info sr_action" no_SR="'.$value['no_SR'].'" data-toggle="modal" data-target="#_Modal"><i class="fas fa-diagnoses"></i></a>';*/
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

	public function loadSVC_edit() {
		if ($this->request->isAJAX())
        {
        	$post = $this->request->getPost();

			$trx_sr = new Trx_SR();
			$data['trx_sr'] = $trx_sr->get($post)[0];

   			return view('svc/modal_svc', $data);
        }
	}

	public function editSR() {
		$post = $this->request->getPost();
		$log = array();

		if($post) 
		{
			/* Define Normal Field here */
			$normal_field = ["request", "reason", "urgency", "impact"];

			foreach ($normal_field as $key => $value) {
				if(isset($post[$value])) 
				{
					if($post[$value]['old'] != $post[$value]['new']) 
					{
						$log['old'][$value] = $post[$value]['old'];
						$log['new'][$value] = $post[$value]['new'];
					}
				}
			}

			if(isset($post['supported_by_new'])) {
				foreach ($post['supported_by_new'] as $key => $value) {
					$temp = explode("-", $value);
					$temp_arr['supported_by_new'][$temp[0]] = $temp[1];
				}

				if($post['supported_by']['old'] != json_encode($temp_arr['supported_by_new'])) {
					$log['old']['supported_by'] = $post["supported_by"]['old'];
					$log['new']['supported_by'] = json_encode($temp_arr['supported_by_new']);
				}
			} else {
				if($post['supported_by']['old'] != "") {
					$log['old']['supported_by'] = $post["supported_by"]['old'];
					$log['new']['supported_by'] = "";
				}
			}

			$detail_sr_field = ["id_DSVC", "desc_DSVC", "qty"];
			foreach ($detail_sr_field as $key_detail => $value_detail) {
				foreach ($post[$value_detail] as $key => $value) {
					if($value['old'] != $value['new']) {
						$log['old']['detail_sr'][$key][$value_detail] = $value['old'];
						$log['new']['detail_sr'][$key][$value_detail] = $value['new']; 	
					}
				}
			}

			$trx_sr = new Trx_SR();;
			/* Untuk Detail Baru */
			if(isset($post['newdsvc'])) {
				$trx_sr->addDetailSR($post['newdsvc'], $post['newdesc_dsvc'], $post['no_SR']);
			}

			$urgency_impact['urgency'] = $post['urgency']['new'];
			$urgency_impact['impact'] = $post['impact']['new'];
			$urgency_impact['request_date'] = $post['request_date'];

			if($log)
				$data['trx_sr'] = $trx_sr->edit($log, $post['no_SR'], $urgency_impact);
		}
		
		wp_redirect("SR/my_service");
	}

	public function actionSR() {
		$post = $this->request->getPost();
		if($post) {
			$trx_sr = new Trx_SR();
			$data['trx_sr'] = $trx_sr->editAction($post);

			wp_redirect("SR/view?sr=".$post['no_SR']);
		}
	}
}
