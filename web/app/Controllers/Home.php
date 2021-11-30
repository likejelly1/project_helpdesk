<?php

namespace App\Controllers;

use App\Models\Ms_Karyawan;

class Home extends BaseController
{
	function __construct() {
		helper("custom_helper");
	}

	public function index()
	{
		/* If Login Redirect */
		if($this->isLogin() == 1)
			wp_redirect("default");

		/* Attempt Login */
		if($this->request->getPost()) {
			$post = $this->request->getPost();
			$_validate =  $this->validate([
				'nik' => 'required',
	            'password' => 'required',
			]);

			if (!$_validate) {
				$this->session->setFlashdata('fail_login', '1');
	            wp_redirect("login");
	        }
			else {
	        	$ms_karyawan = new Ms_Karyawan();
				$data = $ms_karyawan->login($this->request->getPost());
				if($data) {
					$this->session->set($data);
					if(isset($data['it_staff']))
						wp_redirect("default");
					else
						wp_redirect("SR_user/");
				} else {
					$this->session->setFlashdata('fail_login', '1');
					wp_redirect("login");
				}
	        }
		}
        else
            return view('welcome_message');
	}

	public function logout()
	{
		$this->session->destroy();
		wp_redirect("login");
	}
}
