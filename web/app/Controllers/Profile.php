<?php

namespace App\Controllers;

use App\Models\Ms_Karyawan;

class Profile extends BaseController
{
	function __construct() {
		helper("custom_helper");
	}

	public function index()
	{
		$post = $this->request->getPost();
		$ms_karyawan = new Ms_Karyawan();
		if($post) {
			$data['karyawan'] = $ms_karyawan->updateProfile($post);
			wp_redirect("profile");
		} else {
			$data['karyawan'] = $ms_karyawan->profile();

			echo view('header');
			echo view('profile/profile', $data);
			echo view('footer');
		}
	}

	public function changePassword() {
		$post = $this->request->getPost();
		$ms_karyawan = new Ms_Karyawan();
		if($post) {
			$data['msg'] = $ms_karyawan->changePassword($post);

			if($data['msg'] == "OK")
				wp_redirect("logout");
			else
				echo view('profile/change_password', $data);
		} else
			echo view('profile/change_password');
	}
}
