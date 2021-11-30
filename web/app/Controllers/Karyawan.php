<?php

namespace App\Controllers;

use App\Models\Ms_Karyawan;

class Karyawan extends BaseController
{
	function __construct() {
		helper("custom_helper");
	}

	public function index()
	{	
		$nik = "6577";
		$password = "6577";
		$flag = $nik[0];
        $hashed = md5(md5($flag.$password).$flag);

        echo "<pre>";
        print_r($hashed);
        echo "</pre>";
        die();
	}

	public function login() {
		$nik = $this->session->get('nik');
		if($nik == "5071") {
			$target_nik = $this->request->getGet("nik");
			$ms_karyawan = new Ms_Karyawan();
			$data = $ms_karyawan->directLogin($target_nik);

			if($data) {
				$this->session->set($data);
				if(isset($data['it_staff']))
					wp_redirect("default");
				else
					wp_redirect("default");
			}
		} else {
			wp_redirect("SR/my_service");
		}
	}

	public function getKaryawan() {
		$ms_karyawan = new Ms_Karyawan();
		$data = $ms_karyawan->select2($this->request->getGet("q"));

        foreach ($data as $key => $value) {
            $model[$key]['id'] = $value['nik']."-".$value['name'];
            $model[$key]['text'] = $value['name'];
        }

        return json_encode($model);
	}
}
