<?php

namespace App\Models;

use CodeIgniter\Model;
helper('custom_helper');

class Ro_Sr extends Model
{
    public function newRO($data) {
        $data = db_escape($data);
        $ro_created = date("Y-m-d H:i:s");

        $this->db->transStart();
        $sql = "SELECT no_RO FROM detail_sr ORDER BY ro_created DESC LIMIT 1";
        $model_ro = $this->db->query($sql)->getRowArray();

        if($model_ro['no_RO']) {
            $temp_explode = explode("/", $model_ro['no_RO']);
            $temp_explode[0] = $temp_explode[0] + 1;
            $no_RO = implode("/", $temp_explode);
        } else {
            $no_RO = "333/IT/HO/2021";
        }

        foreach ($data as $key => $value) {
            $sql = "UPDATE detail_sr SET no_RO = '$no_RO', ro_created = '$ro_created' WHERE id LIKE '$value'";
            $this->db->query($sql);
        }
        $this->db->transComplete(); 
    }

    public function ketRO($data) {
        $data = db_escape($data);

        $this->db->transStart();
        foreach ($data as $key => $value) {
            $ket = trim($value);
            $sql = "UPDATE detail_sr SET ket_RO = '$ket' WHERE id LIKE '$key'";
            $this->db->query($sql);
        }
        $this->db->transComplete();
    }

    public function detailRO($no_RO) {
        $no_RO = $this->db->escapeString($no_RO);
        $sql = "SELECT * FROM detail_sr WHERE no_RO LIKE '$no_RO'";
        $model['detail_ro'] = $this->db->query($sql)->getResultArray();

        $sql = "SELECT * FROM trx_sr WHERE no_SR LIKE '".$model['detail_ro'][0]['no_SR']."'";
        $model['detail_sr'] = $this->db->query($sql)->getRowArray();

        $sql = "SELECT `ms_karyawan`.nik, name, signature FROM detail_approve 
                JOIN ms_karyawan ON `ms_karyawan`.nik = `detail_approve`.nik
                WHERE id_detail_sr LIKE '".$model['detail_ro'][0]['id']."' ORDER BY approveQueue DESC LIMIT 1";
        $model['checked_by'] = $this->db->query($sql)->getRowArray();

        $this->session = \Config\Services::session();
        $nik = $this->session->get("nik");
        $sql = "SELECT nik, name, signature FROM ms_karyawan WHERE nik LIKE '$nik'";
        $model['prepared_by'] = $this->db->query($sql)->getRowArray();

        return $model;
    }

    public function get_DetailSR_DT($data = array(), $site = array()) {
        /* Get Approved on Detail SR */
        $sql = "SELECT no_SR FROM detail_sr WHERE status LIKE '1' AND no_RO IS NULL GROUP BY no_SR";
        $model = $this->db->query($sql)->getResultArray();

        $sr_in = "";
        if($model) {
            $sr_in = array();
            foreach ($model as $key => $value) {
                $sr_in[] = "'".$value['no_SR']."'";
            }

            $sr_in = " AND no_SR IN (".implode(",", $sr_in).") ";
        }
        /* Get Approved on Detail SR */

        /* Remove SR that doesn't have item approved */
        $sql = "SELECT no_SR, status FROM detail_sr";
        $model = $this->db->query($sql)->getResultArray();
        /* Remove SR that doesn't have item approved */

        /* Site Filter based on RO PIC */
        $site_in = "";
        if($site) {
            $site_in = array();
            foreach ($site as $key => $value) {
                $site_in[] = "'".$value."'";
            }

            $site_in = " AND `trx_sr`.site IN (".implode(",", $site_in).") ";
        }
        /* Site Filter based on RO PIC */

        /* If My RO */
        $my_ro = "";
        if(strpos(current_url(), "ajaxMyRO") !== false) {
            $my_ro = " AND `trx_sr`.status NOT IN ('1','X') ";
        }
        /* If My RO */

        $this->session = \Config\Services::session();
        $company = $this->session->get("company");

        $sql = "SELECT `trx_sr`.*, `ms_priority`.description as priority_name, `kr_pic`.name as pic_name, `kr_pic`.company, `kr_requester`.name as requester_name 
                FROM trx_sr 
                JOIN ms_priority ON `ms_priority`.id = `trx_sr`.priority 
                JOIN ms_karyawan kr_pic ON `kr_pic`.nik = `trx_sr`.pic
                JOIN ms_karyawan kr_requester ON `kr_requester`.nik = `trx_sr`.nik 
                WHERE `kr_pic`.company LIKE '$company' 
                AND `trx_sr`.created_at >= '2021-09-01' 
                AND `trx_sr`.svc LIKE 'SVC03' 
                $sr_in $site_in $my_ro";

        $column_array = ["no_SR", "`kr_pic`.nik", "`kr_pic`.name", "`kr_requester`.nik", "`kr_requester`.name"];

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
            $nik = $this->session->get('nik');
            if($current_url[0] == "ajaxMyService")
                $sql .= " AND (`trx_sr`.pic LIKE '$nik' OR supported_by LIKE '%\"$nik\"%') ";

            $sql .= " ORDER BY $order $order_by ";
            $sql .= " LIMIT $length OFFSET $start";
        }

        $this->db->transStart();
        $model = $this->db->query($sql);
        $model = $model->getResultArray();

        foreach ($model as $key => $value) {
            $no_SR = $value['no_SR'];
            $sql = "SELECT * FROM detail_sr WHERE no_SR LIKE '$no_SR'";
            $tmp_model = $this->db->query($sql)->getResultArray();
            $total = count($tmp_model);
            $approve = 0;
            foreach ($tmp_model as $key2 => $value2) {
                if($value2['status'] == 1)
                    $approve++;
            }

            $model[$key]['item_lbl'] = "$approve/$total";
        }
        $this->db->transComplete();

        return $model;
    }

    public function total_DetailSR_DT($site = array()) {
        /* Get Approved on Detail SR */
        $sql = "SELECT no_SR FROM detail_sr WHERE status LIKE '1' AND no_RO IS NULL GROUP BY no_SR";
        $model = $this->db->query($sql)->getResultArray();

        $sr_in = "";
        if($model) {
            $sr_in = array();
            foreach ($model as $key => $value) {
                $sr_in[] = "'".$value['no_SR']."'";
            }

            $sr_in = " AND no_SR IN (".implode(",", $sr_in).")";
        }
        /* Get Approved on Detail SR */

        /* Site Filter based on RO PIC */
        $site_in = "";
        if($site) {
            $site_in = array();
            foreach ($site as $key => $value) {
                $site_in[] = "'".$value."'";
            }

            $site_in = " AND `trx_sr`.site IN (".implode(",", $site_in).") ";
        }
        /* Site Filter based on RO PIC */

        /* If My RO */
        $my_ro = "";
        if(strpos(current_url(), "ajaxMyRO") !== false) {
            $my_ro = " AND `trx_sr`.status NOT IN ('1','X') ";
        }
        /* If My RO */

        $this->session = \Config\Services::session();
        $company = $this->session->get("company");

        $sql = "SELECT count(no_SR) as total FROM trx_sr 
                JOIN ms_karyawan kr_pic ON `kr_pic`.nik = `trx_sr`.pic
                JOIN ms_karyawan kr_requester ON `kr_requester`.nik = `trx_sr`.nik 
                WHERE `kr_pic`.company LIKE '$company' 
                AND `trx_sr`.created_at >= '2021-09-01' 
                AND `trx_sr`.svc LIKE 'SVC03'  
                $sr_in $site_in $my_ro";

        $model = $this->db->query($sql);
        $model = $model->getRowArray();

        if($model)
            return $model['total'];
        else
            return "0";
    }

    public function totalFiltered_DetailSR_DT($search, $site = array()) {
        /* Get Approved on Detail SR */
        $sql = "SELECT no_SR FROM detail_sr WHERE status LIKE '1' AND no_RO IS NULL GROUP BY no_SR";
        $model = $this->db->query($sql)->getResultArray();

        $sr_in = "";
        if($model) {
            $sr_in = array();
            foreach ($model as $key => $value) {
                $sr_in[] = "'".$value['no_SR']."'";
            }

            $sr_in = " AND no_SR IN (".implode(",", $sr_in).")";
        }
        /* Get Approved on Detail SR */

        /* Site Filter based on RO PIC */
        $site_in = "";
        if($site) {
            $site_in = array();
            foreach ($site as $key => $value) {
                $site_in[] = "'".$value."'";
            }

            $site_in = " AND `trx_sr`.site IN (".implode(",", $site_in).") ";
        }
        /* Site Filter based on RO PIC */

        /* If My RO */
        $my_ro = "";
        if(strpos(current_url(), "ajaxMyRO") !== false) {
            $my_ro = " AND `trx_sr`.status NOT IN ('1','X') ";
        }
        /* If My RO */

        $this->session = \Config\Services::session();
        $company = $this->session->get("company");

        $search = $this->db->escapeString($search);
        $sql = "SELECT count(no_SR) as total FROM trx_sr
                JOIN ms_priority ON `ms_priority`.id = `trx_sr`.priority
                JOIN ms_karyawan kr_pic ON `kr_pic`.nik = `trx_sr`.pic
                JOIN ms_karyawan kr_requester ON `kr_requester`.nik = `trx_sr`.nik 
                WHERE `kr_pic`.company LIKE '$company' 
                AND `trx_sr`.created_at >= '2021-09-01' 
                AND `trx_sr`.svc LIKE 'SVC03' 
                $sr_in $site_in $my_ro";

        $column_array = ["no_SR", "`kr_pic`.nik", "`kr_pic`.name", "`kr_requester`.nik", "`kr_requester`.name"];

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
                $sql .= " AND `trx_sr`.$key LIKE '$value'";
            }
        }
        /* Filter SR -  */
        
        $model = $this->db->query($sql);
        $model = $model->getRowArray();

        if($model)
            return $model['total'];
        else
            return "0";
    }
}