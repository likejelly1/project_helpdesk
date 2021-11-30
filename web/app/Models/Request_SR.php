<?php

namespace App\Models;

use CodeIgniter\Model;

class Request_SR extends Model
{
    protected $table      = 'request_sr';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['nik', 'request', 'reason', 'status', 'attachment'];

    protected $useTimestamps = false;
    //protected $createdField  = 'created_at';
    //protected $updatedField  = 'updated_at';
    //protected $deletedField  = 'deleted_at';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;

    public function get($data = null, $limit = null, $not = null, $order = null) {
        $condition = '';

        if(!empty($data)) {
            $data = db_escape($data);
            foreach ($data as $key => $value) {
                if($key == "site") {
                    $arr_site = array();
                    foreach ($value as $key => $value) {
                        $arr_site[] = '"'.$value.'"';
                    }
                    $temp_site = implode(",", $arr_site);
                    $condition .= " AND site IN ($temp_site) ";
                }
                else
                    $condition .= ' AND '.$key." LIKE '$value' ";
            }
        }

        if(!empty($not)) {
            $not = db_escape($not);
            foreach ($not as $key => $value) {
                $condition .= ' AND '.$key." NOT LIKE '$value' ";
            }
        }

        if(!empty($limit))
            $limit = " LIMIT $limit";

        if(empty($order))
            $order = " ORDER BY `request_sr`.created_at ASC";

        //Select sql, also check from $condition
        $sql = "SELECT `request_sr`.*, `ms_karyawan`.name, `ms_karyawan`.company, `ms_karyawan`.site
                FROM request_sr
                JOIN ms_karyawan ON `ms_karyawan`.nik = `request_sr`.nik
                WHERE 1=1 $condition $order $limit";

        $model = $this->db->query($sql);
        $model = $model->getResultArray();
            
        return $model;
    }

    public function getReject() {
        $this->session = \Config\Services::session();
        $nik = $this->session->get('nik');

        $sql = "SELECT * FROM request_sr WHERE nik LIKE '$nik' AND status IN ('0', 'x')
                ORDER BY created_at DESC";
                
        $data = $this->db->query($sql)->getResultArray();
        return $data;
    }

    public function reject($id) {
        $this->session = \Config\Services::session();
        $site = $this->session->get('site');

        $sql = "SELECT `rsr`.id, `mk`.site FROM request_sr rsr 
                JOIN ms_karyawan mk ON `rsr`.nik = `mk`.nik
                WHERE `mk`.site LIKE '$site' AND `rsr`.id LIKE '$id'";

        $model = $this->db->query($sql)->getRowArray();
        if($model) {
            $delete_id = $model['id'];
            $sql = "UPDATE request_sr SET status = 'x' WHERE id LIKE '$delete_id'";
            $this->db->query($sql);
        }
    }

    public function getRequest ($id) {
        $query = "SELECT `request_sr`.attachment as attachment, `request_sr`.id as request_id, request as rsr_request, reason as rsr_reason, created_at as rsr_created_at, `ms_karyawan`.nik as kr_nik, `ms_karyawan`.name as kr_name, position as kr_position, `ms_organizationalunit`.name as kr_organizational, organizational_id ,site as kr_site, telp as kr_telp FROM request_sr 
                    JOIN ms_karyawan ON `request_sr`.nik = `ms_karyawan`.nik
                    JOIN ms_organizationalunit ON organizational_id = `ms_organizationalunit`.id
                    WHERE `request_sr`.id LIKE '$id'";

        $query = $this->db->query($query);
        $result = $query->getRowArray();
        if($result['attachment'])
            $result['attachment'] = json_decode($result['attachment']);

        return $result;
    }

    public function distinctColumnMyService($column, $order) {
        if(empty($order))
            $order = " ORDER BY request_date DESC";
        else {
            foreach ($order as $key => $value) {
                $order = " ORDER BY $key $value";
            }   
        }

        $this->session = \Config\Services::session();
        $nik = $this->session->get('nik');
        $sql = "SELECT DISTINCT $column FROM trx_sr WHERE nik LIKE '$nik' $order";

        $model = $this->db->query($sql)->getResultArray();

        $temp_model = array();
        foreach ($model as $key => $value) {
            $temp_model[] = $value[$column];
        }
        return $temp_model;
    }

    public function addAttach($data) {
        $data = db_escape($data);
        $request_id = $data['request_id'];
        $attachment = $data['attachment'];

        $sql = "SELECT attachment FROM request_sr WHERE id LIKE '$request_id'";
        $model = $this->db->query($sql)->getRowArray();

        if($model) {
            if($model['attachment']) 
                $model['attachment'] = json_decode($model['attachment']);
            else
                $model['attachment'] = array();      
        }

        foreach ($attachment as $key => $value) {
            $model['attachment'][] = $value;
        }

        $final_attachment = json_encode($model['attachment']);
        $update_sql = "UPDATE request_sr SET attachment = '$final_attachment' WHERE id LIKE '$request_id'";
        $this->db->query($update_sql);
    }

    public function complete($data) {
        $data = db_escape($data);
        $score = $data['score'];
        $review = $data['review'];
        $no_sr = $data['no_sr'];
        $current_time = date("Y-m-d H:i:s");

        $sql = "UPDATE trx_sr SET score = '$score', 
                review = '$review', 
                completed_at = '$current_time',
                status = '1' 
                WHERE no_SR LIKE '$no_sr'";

        $this->db->query($sql);
    }

    /* - Data Tables Logic */
    public function getTrxDT($data = array()) {
        $this->session = \Config\Services::session();
        $company = $this->session->get("company");
        $nik = $this->session->get("nik");

        $sql = "SELECT `trx_sr`.*, `ms_priority`.description as priority_name, `kr_pic`.name as pic_name, `kr_user`.company FROM trx_sr
                JOIN ms_priority ON `ms_priority`.id = `trx_sr`.priority 
                JOIN ms_karyawan kr_pic ON `kr_pic`.nik = `trx_sr`.pic
                JOIN ms_karyawan kr_user ON `kr_user`.nik = `trx_sr`.nik
                WHERE `kr_user`.company LIKE '$company' AND `kr_user`.nik LIKE '$nik' ";

        $column_array = ["no_SR", "`kr_pic`.nik", "`kr_pic`.name", "`kr_user`.nik", "`kr_user`.name"];

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
        $nik = $this->session->get("nik");

        $sql = "SELECT count(no_SR) as total FROM trx_sr 
                JOIN ms_priority ON `ms_priority`.id = `trx_sr`.priority 
                JOIN ms_karyawan kr_pic ON `kr_pic`.nik = `trx_sr`.pic
                JOIN ms_karyawan kr_user ON `kr_user`.nik = `trx_sr`.nik
                WHERE `kr_user`.company LIKE '$company' AND `kr_user`.nik LIKE '$nik' ";

        $model = $this->db->query($sql);
        $model = $model->getRowArray();

        return $model['total'];
    }

    public function totalTrxFilteredDT($search) {
        $this->session = \Config\Services::session();
        $company = $this->session->get("company");
        $nik = $this->session->get("nik");

        $search = $this->db->escapeString($search);
        $sql = "SELECT count(no_SR) as total FROM trx_sr
                JOIN ms_priority ON `ms_priority`.id = `trx_sr`.priority 
                JOIN ms_karyawan kr_pic ON `kr_pic`.nik = `trx_sr`.pic
                JOIN ms_karyawan kr_user ON `kr_user`.nik = `trx_sr`.nik
                WHERE `kr_user`.company LIKE '$company' AND `kr_user`.nik LIKE '$nik' ";

        $column_array = ["no_SR", "`kr_pic`.nik", "`kr_pic`.name", "`kr_user`.nik", "`kr_user`.name"];

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

        $model = $this->db->query($sql);
        $model = $model->getRowArray();

        return $model['total'];
    }
    /* Data Tables Logic - */
}