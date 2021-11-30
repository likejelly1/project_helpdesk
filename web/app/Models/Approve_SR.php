<?php

namespace App\Models;

use CodeIgniter\Model;

class Approve_SR extends Model
{
    public function getApprovalList($data) {
        $no_sr = $data['no_SR'];

        $sql = "SELECT `detail_approve`.*, company, name, no_SR FROM detail_approve
                JOIN detail_sr dsr ON `dsr`.id = `detail_approve`.id_detail_sr 
                JOIN ms_karyawan kr ON `kr`.nik = `detail_approve`.nik
                WHERE no_SR LIKE '$no_sr'
                GROUP BY `kr`.nik
                ORDER BY approveQueue ASC ";

        $model = $this->db->query($sql);
        $model = $model->getResultArray();

        return $model;
    }

    public function getDetailApprove($data) {
        $data = db_escape($data);
        $id_detail_sr = $data['id_detail_sr'];
        $nik = $data['nik'];

        $sql = "SELECT * FROM detail_approve WHERE id_detail_sr LIKE '$id_detail_sr' AND nik LIKE '$nik'";
        $model = $this->db->query($sql);
        $model = $model->getRowArray();

        return $model;
    }

    public function changeStatus($data) {
        $data = db_escape($data);
        $id_detail_sr = $data['id_detail_sr'];
        $status = $data['status'];
        $nik = $data['nik'];
        $no_sr = $data['no_SR'];
        $ket = $data['ket'];

        $sql_exist = "SELECT * FROM detail_approve 
                      WHERE id_detail_sr LIKE '$id_detail_sr' AND nik LIKE '$nik'";
        $model = $this->db->query($sql_exist)->getRowArray();
        $approveQueue = $model['approveQueue'];

        $this->db->transStart();
        if($model && ($model['status'] != $status || $model['ket'] != $ket)) 
        { 
            $update_dapprove = "UPDATE detail_approve SET status = '$status', ket = '$ket' 
                                WHERE id_detail_sr LIKE '$id_detail_sr' AND nik like '$nik'";
            $this->db->query($update_dapprove);

            unset($model['id']);    
            foreach ($model as $key => $value) {
                $column_history[] = $key;
                $value_history[] = "'".$value."'";
            }
            $history_approve = "INSERT INTO history_approve (".implode(",", $column_history).") 
                        VALUES (".implode(",", $value_history).")";
            $this->db->query($history_approve);
        }

        //Bila Approved
        if($status == 1) 
        {
            $sql_last_approve = "SELECT nik, approveQueue FROM detail_approve 
                    WHERE id_detail_sr LIKE '$id_detail_sr' 
                    ORDER BY approveQueue DESC LIMIT 1";
            $model_last_approve = $this->db->query($sql_last_approve)->getRowArray();

            //Jika user adalah yang terakhir approve 
            if($status == 1 && $model_last_approve && $model_last_approve['approveQueue'] == $approveQueue) 
            {       
                $update_dsr = "UPDATE detail_sr SET status = '1' 
                    WHERE id LIKE '$id_detail_sr' ";

                /* Telegram Notif */
                $msg = "RO ready to be printed : \n";
                $msg .= $no_sr. "\n";

                $sql = "SELECT site FROM trx_sr WHERE no_SR LIKE '$no_sr'";
                $site = $this->db->query($sql)->getRowArray()['site'];

                $nik_ro = is_PIC_RO($site);
                $ms_karyawan = new Ms_Karyawan();
                foreach ($nik_ro as $key => $value) {
                    $pic_chat_id = $ms_karyawan->getTgChat($value);
                    if($pic_chat_id)
                        tg_message($msg, $pic_chat_id);      
                }
            } else {
                $next_approve = "A".($approveQueue[1]+1);
                $sql_next_approve = "SELECT nik FROM detail_approve 
                                    WHERE id_detail_sr LIKE '$id_detail_sr'
                                    AND approveQueue LIKE '$next_approve'";
                $model_next_approve = $this->db->query($sql_next_approve)->getRowArray();
                $nik_next_approve = $model_next_approve['nik'];
                $status = $nik_next_approve;

                $update_dsr = "UPDATE detail_sr SET status = '$nik_next_approve' 
                    WHERE id LIKE '$id_detail_sr' ";
            }
        }
        //Bila Reject
        elseif ($status == "-") {
            $update_dsr = "UPDATE detail_sr SET status = '-' 
                           WHERE id LIKE '$id_detail_sr' ";
        } 
        //Bila On Hold
        elseif($status == ".") {
            $update_dsr = "UPDATE detail_sr SET status = '$nik' 
                           WHERE id LIKE '$id_detail_sr'";          
        }

        $this->db->query($update_dsr);

        //Check if detail is complete or not and update it 
        $this->checkUpdateSR($no_sr);
        $this->db->transComplete();

        return $status;
    }

    public function checkUpdateSR($no_sr) {
        $sql = "SELECT id, no_SR, status FROM detail_sr 
                        WHERE no_SR LIKE '$no_sr'";

        $model = $this->db->query($sql)->getResultArray();
        $approve = 0; $reject = 0; $others = 0;

        foreach ($model as $key => $value) {
            if($value['status'] == 1)
                $approve++;
            elseif ($value['status'] == "-")
                $reject++;
            else
                $others++;
        }

        $status = "0";
        if($reject != 0 && $approve == 0 && $others == 0)
            $status = "X";
        elseif ($approve != 0 && $others == 0)
            $status = "A";

        $done_it_at= date("Y-m-d H:i:s");
        $complete_sql = "UPDATE trx_sr SET status = '$status', done_it_at = '$done_it_at'
                        WHERE no_SR LIKE '$no_sr'";
        $this->db->query($complete_sql);
    } 

    /* - Data Tables Logic */
    public function getTrxDT($data = array()) {
        $this->session = \Config\Services::session();
        $company = $this->session->get("company");
        $nik = $this->session->get('nik');

        $sql = "SELECT `trx_sr`.*, `ms_priority`.description as priority_name, `ms_karyawan`.name as pic_name, `ms_karyawan`.company FROM detail_sr
                JOIN trx_sr ON `trx_sr`.no_SR = `detail_sr`.no_SR 
                JOIN ms_priority ON `ms_priority`.id = `trx_sr`.priority 
                JOIN ms_karyawan ON `ms_karyawan`.nik = `trx_sr`.pic
                WHERE `ms_karyawan`.company LIKE '$company' ";

        $column_array = ["`trx_sr`.no_SR", "`ms_karyawan`.nik", "`ms_karyawan`.name"];

        if(!empty($data))
        {
            $start = $data['start'];
            $length = $data['length'];
            $search = $this->db->escapeString($data['search']);
            $order = $data['order'];
            $order_by = $data['order_by'];

            if($search) {
                $sql .= " AND (";
                $count_array = count($column_array);
                foreach ($column_array as $key => $value) {
                    $sql .= " $value LIKE '%$search%' ";
                    if($count_array-1 != $key)
                        $sql .= " OR ";
                }
                $sql .= ") ";
            }

            /* - Filter SR */
            if($this->session->has('filterSR')) {
                $filter = $this->session->get('filterSR'); 
                foreach ($filter as $key => $value) {
                    $sql .= " AND `trx_sr`.$key LIKE '$value'";
                }
            }
            /* Filter SR -  */

            $url = preg_match("/[^\/]+$/", current_url(), $current_url);
            if ($current_url[0] == "ajaxMyApprove") 
                $sql .= " AND `detail_sr`.status LIKE '$nik'";

            $sql .= " GROUP BY `trx_sr`.no_SR ";
            $sql .= " ORDER BY $order $order_by ";
            $sql .= " LIMIT $length OFFSET $start";
        }

        $model = $this->db->query($sql);
        $model = $model->getResultArray();

        return $model;
    }

    public function totalTrxDT() {
        $this->session = \Config\Services::session();
        $company = $this->session->get("company");
        $nik = $this->session->get('nik');

        $sql = "SELECT `trx_sr`.*, `ms_karyawan`.name as pic_name, `ms_karyawan`.company FROM detail_sr 
                JOIN trx_sr ON `trx_sr`.no_SR = `detail_sr`.no_SR
                JOIN ms_karyawan ON `ms_karyawan`.nik = `trx_sr`.pic
                WHERE `ms_karyawan`.company LIKE '$company' ";

        $url = preg_match("/[^\/]+$/", current_url(), $current_url);
        if ($current_url[0] == "ajaxMyApprove")
            $sql .= " AND `detail_sr`.status LIKE '$nik'";

        $sql .= " GROUP BY `trx_sr`.no_SR ";

        $model = $this->db->query($sql);
        $model = $model->getResultArray();
        $total = count($model);

        return $total;
    }

    public function totalTrxFilteredDT($search) {
        $this->session = \Config\Services::session();
        $company = $this->session->get("company");
        $nik = $this->session->get('nik');

        $search = $this->db->escapeString($search);
        $sql = "SELECT `trx_sr`.*, `ms_priority`.description as priority_name, `ms_karyawan`.name as pic_name, `ms_karyawan`.company FROM detail_sr  
                JOIN trx_sr ON `trx_sr`.no_SR = `detail_sr`.no_SR 
                JOIN ms_priority ON `ms_priority`.id = `trx_sr`.priority
                JOIN ms_karyawan ON `ms_karyawan`.nik = `trx_sr`.pic
                WHERE `ms_karyawan`.company LIKE '$company' ";

        $column_array = ["`trx_sr`.no_SR", "`ms_karyawan`.nik", "`ms_karyawan`.name"];

        if($search) {
            $sql .= " AND (";
            $count_array = count($column_array);
            foreach ($column_array as $key => $value) {
                $sql .= " $value LIKE '%$search%' ";
                if($count_array-1 != $key)
                    $sql .= " OR ";
            }
            $sql .= ") ";
        }

        /* - Filter SR */
        $this->session = \Config\Services::session();
        if($this->session->has('filterSR')) {
            $filter = $this->session->get('filterSR'); 
            foreach ($filter as $key => $value) {
                $sql .= " AND $key LIKE '$value'";
            }
        }
        /* Filter SR -  */

        $url = preg_match("/[^\/]+$/", current_url(), $current_url);
        if ($current_url[0] == "ajaxMyApprove")
            $sql .= " AND `detail_sr`.status LIKE '$nik'";

        $sql .= " GROUP BY `trx_sr`.no_SR ";

        $model = $this->db->query($sql);
        $model = $model->getResultArray();
        $total = count($model);

        return $total;
    }
    /* Data Tables Logic - */
}