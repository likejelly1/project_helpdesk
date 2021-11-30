<?php

namespace App\Controllers;
use App\Models\Trx_SR;

// Load library phpspreadsheet
require('./web/vendor/autoload.php');

use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
// End load library phpspreadsheet

class Export extends BaseController
{
	function __construct() {
		helper("custom_helper");
		$this->session = \Config\Services::session();
		$nik = $this->session->get('nik');
		if(!in_array($nik, nik_exportSR()))
			wp_redirect("SR/my_service");
	}

	public function index() {
		echo view('header');
		echo view('export');
		echo view('footer');
	}

	public function trx() {
		$post = $this->request->getPost();
		$trx_sr = new Trx_SR();
		$data = $trx_sr->exportTrx("", $post);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        foreach (range('A','T') as $col) 
        {	
        	$exception = ["D", "E", "O"];
        	if(!in_array($col, $exception))
   				$sheet->getColumnDimension($col)->setAutoSize(true);
   		}

   		$sheet->setCellValue('A1', "SR");
        $sheet->setCellValue('B1', "SVC");
        $sheet->setCellValue('C1', "NIK");
        $sheet->setCellValue('D1', "Request");
        $sheet->setCellValue('E1', "Reason");
        $sheet->setCellValue('F1', "Departemen");
        $sheet->setCellValue('G1', "Site");
        $sheet->setCellValue('H1', "Urgency");
        $sheet->setCellValue('I1', "Impact");
        $sheet->setCellValue('J1', "Priority");
        $sheet->setCellValue('K1', "Status");
        $sheet->setCellValue('L1', "Request Date");
        $sheet->setCellValue('M1', "Expected Resolution");
        $sheet->setCellValue('N1', "PIC");
        $sheet->setCellValue('O1', "Supported By");
        $sheet->setCellValue('P1', "SR Created At");
        $sheet->setCellValue('Q1', "SR ActionLog Solved At");
        $sheet->setCellValue('R1', "SR Compelted At");
        $sheet->setCellValue('S1', "Score");
        $sheet->setCellValue('T1', "Review");

        foreach ($data as $key => $value) {
            $row = $key+2;
            $sheet->setCellValue('A'.$row, $value["no_SR"]);
            $sheet->setCellValue('B'.$row, $value["svc"]);
            $sheet->setCellValue('C'.$row, $value["nik"]);
            $sheet->setCellValue('D'.$row, $value["request"]);
            $sheet->setCellValue('E'.$row, $value["reason"]);
            $sheet->setCellValue('F'.$row, $value["organizational_name"]);
            $sheet->setCellValue('G'.$row, $value["site"]);
            $sheet->setCellValue('H'.$row, $value["urgency"]);
            $sheet->setCellValue('I'.$row, $value["impact"]);
            $sheet->setCellValue('J'.$row, $value["priority"]);
            $sheet->setCellValue('K'.$row, $value["status"]);
            $sheet->setCellValue('L'.$row, $value["request_date"]);
            $sheet->setCellValue('M'.$row, $value["expected_resolutionDate"]);
            $sheet->setCellValue('N'.$row, $value["pic"]);
            $sheet->setCellValue('O'.$row, $value["supported_by"]);
            $sheet->setCellValue('P'.$row, $value["done_it_at"]);
            $sheet->setCellValue('R'.$row, $value["completed_at"]);
            $sheet->setCellValue('S'.$row, $value["score"]);
            $sheet->setCellValue('T'.$row, $value["review"]);
        }

        $writer = new Xlsx($spreadsheet);
 
        $filename = $post['start']."__".$post['end'];
 
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');  // download file
    }
}
