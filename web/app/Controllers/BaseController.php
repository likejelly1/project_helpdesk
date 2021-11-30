<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */

class BaseController extends Controller
{
	/**
	 * An array of helpers to be loaded automatically upon
	 * class instantiation. These helpers will be available
	 * to all other controllers that extend BaseController.
	 *
	 * @var array
	 */
	protected $helpers = [];
	/**
	 * Constructor.
	 *
	 * @param RequestInterface  $request
	 * @param ResponseInterface $response
	 * @param LoggerInterface   $logger
	 */
	public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
	{
		// Do Not Edit This Line
		parent::initController($request, $response, $logger);
		//--------------------------------------------------------------------
		// Preload any models, libraries, etc, here.
		//--------------------------------------------------------------------
		// E.g.: $this->session = \Config\Services::session();
		$this->session = \Config\Services::session();
		$this->checkPermission();
	}

	protected function checkPermission() {
		helper("custom_url");
		$arr = explode("/", current_url());
		$last_url = $arr[count($arr) - 1];
		$controller = $arr[count($arr) - 2];
		if($this->isLogin() == 0) {
			$arr = explode("/", current_url());
			$exception = array("login", "home");
			if(!in_array($last_url, $exception))
				wp_redirect("login");
		} else {
			
		}
	
		// $db = \Config\Database::connect();
		// $sql = "";
		// foreach ($arr as $key => $value) {
		// 	$arr[$key] = $db->escape($value);
		// }
		// return $arr;
	}

	protected function isLogin() 
	{
		helper("custom_url");
		$this->session = \Config\Services::session();

		if(!$this->session->has("nik") && !$this->session->has("role"))
			return 0;
		else
			return 1;
		
	}
}
