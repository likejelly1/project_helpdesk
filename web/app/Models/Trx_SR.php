<?php

namespace App\Models;

use CodeIgniter\Model;
helper('custom_helper');

class Trx_SR extends Model
{
    public function exportTrx($data = array(), $date) {
        if(!empty($data)) {
            $data = db_escape($data);
            foreach ($data as $key => $value) {
                $condition .= ' AND '.$key." LIKE '$value' ";
            }
        }

        if(!empty($date)) {
            $date = db_escape($date);
            $start = $date['start'];
            $end = $date['end'];
        }

        $sql = "SELECT `trx_sr`.*, `ms_organizationalunit`.name as organizational_name 
                FROM trx_sr
                JOIN ms_organizationalunit ON `trx_sr`.organizational_id = `ms_organizationalunit`.id 
                WHERE created_at BETWEEN '$start' AND '$end'";
        $model = $this->db->query($sql);
        $model = $model->getResultArray();

        return $model;
    }

    public function get($data = null, $limit = null, $not = null, $order = null) {
        $condition = '';

        if(!empty($data)) {
            $data = db_escape($data);
            foreach ($data as $key => $value) {
                $condition .= ' AND '.$key." LIKE '$value' ";
            }
        }

        if(!empty($not)) {
            $not = db_escape($not);
            foreach ($not as $key => $value) {
                $condition .= ' AND '.$key." NOT LIKE '$value' ";
            }
        }

        if(!empty($limit)) {
            $limit = " LIMIT $limit";
        }

        if(empty($order)) {
            $order = " ORDER BY request_date DESC";
        } else {
            // This Code need to be fixed v
            //$order = " ORDER BY $key $value";
        }

        //Select sql, also check from $condition
        $sql = "SELECT `trx_sr`.*, 
                `rsr`.request as rsr_request, `rsr`.reason as rsr_reason, `rsr`.created_at as rsr_created_at, `rsr`.attachment as attachment, 
                `kr`.name as kr_name, `kr`.position as kr_position, `kr`.site as kr_site, `kr`.telp as kr_telp, `kr`.company as kr_company,
                `pic`.name as pic_name, `pic`.position as pic_position, `pic`.site as pic_site, `pic`.telp as pic_telp, 
                `org`.name as kr_organizational, `svc`.name as svc_name
                FROM trx_sr
                JOIN ms_svc svc ON `svc`.id  = svc
                JOIN request_sr rsr ON `rsr`.id = request_id
                JOIN ms_karyawan kr ON `kr`.nik = `trx_sr`.nik
                JOIN ms_karyawan pic ON `pic`.nik = `trx_sr`.pic
                JOIN ms_organizationalunit org ON `org`.id = `kr`.organizational_id
                WHERE 1=1 $condition $order $limit";
        $model = $this->db->query($sql);
        $model = $model->getResultArray();

        if($model) {
            foreach ($model as $key => $value) {
                $model[$key]['detail_sr'] = $this->getDetail(array("no_SR" => $value['no_SR']));
                $sql_dsvc = "SELECT id, name FROM ms_dsvc WHERE id_SVC LIKE '".$value['svc']."'
                ORDER BY name ASC";
                $model[$key]['dsvc'] = $this->db->query($sql_dsvc)->getResultArray();

                if($model[$key]['detail_sr']) {
                    foreach ($model[$key]['detail_sr'] as $key2 => $value2) {
                        $filter_action = array(
                            "id_detail_sr" => $value2['id'],
                            "status" => "1",
                        );
                        $model[$key]['detail_sr'][$key2]['action'] = $this->getAction($filter_action);
                    }
                }

                if($value['attachment'])
                    $model[$key]['attachment'] = json_decode($value['attachment']);
            } 
        } 
            
        return $model;
    }

    public function deleteSR($no_SR) {
        $filter['no_SR'] = $no_SR;
        $data = $this->get($filter);
        if($data) {
            $data = $data[0];
            $request_id = $data['request_id'];
            $detail_sr =  $data['detail_sr'];
 
            $sql[] = "DELETE FROM trx_sr WHERE no_SR LIKE '$no_SR'";
            $sql[] = "DELETE FROM detail_sr WHERE no_SR LIKE '$no_SR'";
            $sql[] = "DELETE FROM log_sr WHERE no_SR LIKE '$no_SR'";
            foreach ($detail_sr as $key => $value) {
                $id_detail_sr = $value['id'];
                $sql[] = "DELETE FROM detail_approve WHERE id_detail_sr LIKE '$id_detail_sr'";
                $sql[] = "DELETE FROM history_approve WHERE id_detail_sr LIKE '$id_detail_sr'";
                $sql[] = "DELETE FROM log_sr_action WHERE id_detail_sr LIKE '$id_detail_sr'";
            } 
            $sql[] = "UPDATE request_sr SET status = '0' WHERE id LIKE '$request_id'";

            foreach ($sql as $key => $value) {
                $this->db->transStart();
                $this->db->query($value);
                $this->db->transComplete();
            }

            return ":)";
        } else {
            return ":(";
        }
    }

    public function getAction($data = null, $limit = null, $not = null, $order = null) {
        $condition = '';

        if(!empty($data)) {
            $data = db_escape($data);
            foreach ($data as $key => $value) {
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
            $order = " ORDER BY log_date ASC";
        else
            $order = " ORDER BY $key $value";

         $sql = "SELECT `log_sr`.*, `mk`.name, `mk`.telp 
                FROM log_sr_action log_sr 
                JOIN ms_karyawan mk ON `mk`.nik = `log_sr`.created_by 
                WHERE 1=1 $condition $order $limit";

        $model = $this->db->query($sql);
        $model = $model->getResultArray();
        return $model;
    }

    public function getDetail($data = null, $limit = null, $not = null, $order = null) {
        $condition = '';

        if(!empty($data)) {
            $data = db_escape($data);
            foreach ($data as $key => $value) {
                $condition .= ' AND '.$key." LIKE '$value' ";
            }
        }

        if(!empty($not)) {
            $not = db_escape($not);
            foreach ($not as $key => $value) {
                $condition .= ' AND '.$key." NOT LIKE '$value' ";
            }
        }

        if(!empty($limit)) {
            $limit = " LIMIT $limit";
        }

        if(empty($order)) {
            $order = " ORDER BY id_DSVC ASC";
        } else {
            $order = " ORDER BY $key $value";
        }

        //Select sql, also check from $condition
        $sql = "SELECT `detail_sr`.*, name as name_DSVC 
                FROM detail_sr JOIN ms_dsvc ON `ms_dsvc`.id = id_DSVC 
                WHERE 1=1 $condition $order $limit";

        $model = $this->db->query($sql);
        $model = $model->getResultArray();
        return $model;
    }

    public function add($arr) {
        $arr = db_escape($arr);
        $year = date("Y");
        $month = date("m");

        foreach ($arr as $key => $value) {
            if(!is_array($value)) {
                $arr[$key] = strtoupper(trim($value));
                if($key == "reason" || $key == "request")
                    $arr[$key] = str_replace("\R\N","\r\n", $arr[$key]);
            }
        }

        /* - Get Latest Number */
        $sql = "SELECT no_SR, created_at FROM trx_sr 
                WHERE YEAR(created_at) LIKE '$year' AND site LIKE '".$arr['site']."' 
                ORDER BY created_at DESC LIMIT 1";
        $model = $this->db->query($sql)->getRowArray();
        /* Get Latest Number -  */

        /* - Generate New SR Number */
        if($model) {
            $sr = explode("/", $model['no_SR']);
            $sr[0] = sprintf("%04s", (int)str_replace("SR-", "", $sr[0]) + 1);
            $sr[2] = $month;
            $sr[3] = date("y");

            $arr['no_SR'] = $this->db->escapeString("SR-".implode("/", $sr));
        } else {
            $sr["no"] = sprintf("%04s", 1);
            $sr["site"] = $arr['site'];
            $sr["month"] = $month;
            $sr["year"] = date("y");

            $arr['no_SR'] = $this->db->escapeString("SR-".implode("/", $sr));
        }
        /* Generate New SR Number - */

        $arr["priority"] = $this->db->escapeString($this->matrixPriority($arr['urgency'], $arr['impact']));
        /* - Get Resolution date based on priority */
        $sql_priority = "SELECT * FROM ms_priority WHERE id LIKE '".$arr['priority']."'";
        $model_priority = $this->db->query($sql_priority)->getRowArray();
        $arr['expected_resolutionDate'] = date('Y-m-d H:i:s', strtotime("+".$model_priority['target_resolution']." day", strtotime($arr['request_date'])));
        /* Get Resolution date based on priority - */

        $this->db->transStart();
        /* Insert SR */
        $insert_trx = "INSERT INTO trx_sr (no_SR, svc, nik, request_id,request, reason, organizational_id, site, urgency, impact, priority, request_date, expected_resolutionDate, pic ,status) VALUES ('".$arr['no_SR']."', '".$arr['svc']."','".$arr['nik']."', '".$arr['request_id']."','".$arr['request']."', '".$arr['reason']."' , '".$arr['organizational_id']."', '".$arr['site']."', '".$arr['urgency']."', '".$arr['impact']."','".$arr['priority']."', '".$arr['request_date']."', '".$arr['expected_resolutionDate']."', '".$arr['pic']."','0')";
       $this->db->query($insert_trx);

        /* Insert Detail SR */
        foreach ($arr['id_DSVC'] as $key => $value) 
        {
            $id_DSVC = $value;
            $desc_DSVC = $arr['desc_DSVC'][$key];
            $qty = $arr['qty'][$key];

            $insert_detail = "INSERT INTO detail_sr (no_SR, id_DSVC, desc_DSVC, qty, status) VALUES ('".$arr['no_SR']."', '$id_DSVC' , '$desc_DSVC', '$qty', '0')";
            $this->db->query($insert_detail);
        }

        /* Update Request by User to Done */
        $update_request = "UPDATE request_sr SET status = '1' WHERE id LIKE '". $arr['request_id']."'";
        $this->db->query($update_request);

        /* Get approver */
        $sql_detail_sr = "SELECT id FROM detail_sr WHERE no_SR LIKE '".$arr['no_SR']."'";
        $sql_approve_sr = "SELECT nik, approveQueue FROM approve_sr 
                            WHERE site LIKE '%\"".$arr['site']."\"%' 
                            AND SVC LIKE '%\"".$arr['svc']."\"%'";
        $model_detail_sr = $this->db->query($sql_detail_sr)->getResultArray();
        $model_approve_sr = $this->db->query($sql_approve_sr)->getResultArray();

        $show = "0";
        foreach ($model_approve_sr as $key => $value) {
            if($value['approveQueue'] == "A1") {
                /* Detail Status to NIK for Approve*/
                $show = $value['nik'];

                /* Telegram Notif */
                $msg = "Need Approval : \n";
                $msg .= $arr['no_SR']. "\n";
                $ms_dsvc = new Ms_dSVC();
                foreach ($arr['id_DSVC'] as $key => $value) {
                    $name = $ms_dsvc->getName($value);
                    $msg .= "<i>".$name."</i> : <b>(".$arr['qty'][$key].")</b> ".$arr['desc_DSVC'][$key]."\n";
                }

                $ms_karyawan = new Ms_Karyawan();
                $pic_chat_id = $ms_karyawan->getTgChat($show);

                if($pic_chat_id)
                    tg_message($msg, $pic_chat_id);                    
            }
        }

        if($model_approve_sr && $model_detail_sr) {
            foreach ($model_detail_sr as $key => $value) {
                foreach ($model_approve_sr as $key2 => $value2) {
                    $temp_sql = "INSERT INTO detail_approve (nik, id_detail_sr, approveQueue,status)
                                VALUES ('".$value2['nik']."', '".$value['id']."', '".$value2['approveQueue']."','0')";
                    $update_detail = "UPDATE detail_sr SET status = '$show' WHERE id LIKE '".$value['id']."'";
                    $this->db->query($update_detail);
                    $this->db->query($temp_sql);
                }
            }
        }
        $this->db->transComplete();
        
        wp_redirect("SR/process_request");
    }

    public function addDetailSR($service_code, $service_desc, $no_sr) {
        $this->db->transStart();
        $service_code = db_escape($service_code);
        $service_desc = db_escape($service_desc);
        $no_sr = $this->db->escapeString($no_sr);
        $sql = "SELECT svc, site FROM trx_sr WHERE no_SR LIKE '$no_sr'";
        $model = $this->db->query($sql)->getRowArray();

        if($model) {
            $site_sr = $model['site'];
            $svc_sr = $model['svc'];
            $sql_approver = "SELECT nik, approveQueue FROM approve_sr WHERE site LIKE '%$site_sr%' AND svc LIKE '%$svc_sr%' ORDER BY approveQueue ASC";
            $model_approver = $this->db->query($sql_approver)->getResultArray();
            if($model_approver)
                $status = $model_approver[0]['nik'];
            else
                $status = 0;

            $id_detail_sr = array();
            foreach ($service_code as $key => $value) {
                $temp_desc = $service_desc[$key];
                $temp_sql = "INSERT INTO detail_sr (no_SR, id_DSVC, desc_DSVC, status) VALUES ('$no_sr', '$value', '$temp_desc', '$status')";
                $this->db->query($temp_sql);
                if($model_approver) {
                    $id_detail_sr[] = $this->db->insertID();
                }
            }

            if($model_approver) {
                foreach ($model_approver as $key => $value) {
                    $nik = $value['nik'];
                    $approveQueue = $value['approveQueue'];
                    foreach ($id_detail_sr as $key2 => $value2) {
                        $temp_sql = "INSERT INTO detail_approve (nik, id_detail_sr, approveQueue, status) VALUES ('$nik', '$value2', '$approveQueue', '0')";
                        $this->db->query($temp_sql);
                    }
                }
            }
        }
        $this->db->transComplete();
    }

    function edit($log, $sr, $urgency_impact = array()) {

        foreach ($log['new'] as $key => $value) {
            if($key != "detail_sr") {
                $temp_value = $this->db->escapeString(strtoupper(trim($value)));
                $column[] = "$key = '$temp_value'";
            } else {
                foreach ($value as $id_DSVC => $value_detail) {
                    foreach ($value_detail as $field_detail => $field_value) {
                        $temp_value = $this->db->escapeString(strtoupper(trim($field_value)));
                        $column_detail[$id_DSVC][$field_detail] =  "$field_detail = '$temp_value'";
                    }
                }
            }
        }

        /* Kalau Urgency / Impact di update dengan value yang berbeda dengan sebelumnya */
        if(isset($log['new']['urgency']) || isset($log['new']['impact'])) {
            $priority = $this->matrixPriority($urgency_impact['urgency'], $urgency_impact['impact']);
            $column[] = "priority = '$priority'";

             /* - Get Resolution date based on priority */
            $sql_priority = "SELECT * FROM ms_priority WHERE id LIKE '".$priority."'";
            $model_priority = $this->db->query($sql_priority)->getRowArray();
            $expected_resolutionDate = date('Y-m-d H:i:s', strtotime("+".$model_priority['target_resolution']." day", strtotime($urgency_impact['request_date'])));
            
            $column[] = "expected_resolutionDate = '$expected_resolutionDate'";
            /* Get Resolution date based on priority - */
        }

        $this->db->transStart();
        if(isset($column)) {
            $set_column = implode(", ", $column);
            $sql_update = "UPDATE trx_sr SET $set_column WHERE no_SR LIKE '$sr'";
            $this->db->query($sql_update);
        }

        if(isset($column_detail)) {
            foreach ($column_detail as $key => $value) {
                $set_column = implode(", ", $value);
                $sql_update = "UPDATE detail_sr SET $set_column WHERE id LIKE '$key'";
                $this->db->query($sql_update);
            }
        }

        $json_log = json_encode($log);
        $this->session = \Config\Services::session();
        $nik = $this->session->get('nik');
        $sql_log = "INSERT INTO log_sr (no_SR, log, nik) VALUES ('$sr', '$json_log', '$nik')";
        $this->db->query($sql_log);
        $this->db->transComplete();
    }

    function matrixPriority($urgency, $impact) {
        $urgency = $this->db->escape($urgency);
        $impact = $this->db->escape($impact);
        $query = "SELECT urgency, impact, priority FROM ms_matrixpriority
                        WHERE urgency LIKE $urgency AND impact LIKE $impact";
        $query = $this->db->query($query);
        $result = $query->getRowArray();    

        return $result['priority'];
    }

    public function editAction($arr) {
        $arr = db_escape($arr);
        $this->session = \Config\Services::session();
        $nik = $this->session->get('nik');

        $this->db->transStart();

        /* Edit Action log / comment */
        if(isset($arr['oaction'])) {
            foreach ($arr['oaction']['old'] as $key => $value) {
                $new_comment = trim($arr['oaction']['new'][$key]);
                if(trim($value) != $new_comment) {
                    $this->editActionLog($key, $new_comment, $nik);
                }
            }
        }

        foreach ($arr['action'] as $key => $value) 
        {
            if(trim($value)) 
            {
                $sql_select = "SELECT action_plan FROM log_sr_action 
                                WHERE id_detail_sr LIKE '$key'
                                AND created_by LIKE '$nik' AND status LIKE '1'
                                ORDER BY id DESC LIMIT 1";
                $model = $this->db->query($sql_select)->getRowArray();

                if(empty($model) || $model['action_plan'] != $value) {
                    $log_action = "INSERT INTO log_sr_action (id_detail_sr, action_plan, created_by, status) VALUES ('$key', '".$value."', '$nik', '1')";
                    $this->db->query($log_action);

                    /* Notif */
                    $user_notif = "SELECT pic, supported_by FROM trx_sr WHERE no_SR LIKE '".$arr['no_SR']."'";
                    $tmp_muser = $this->db->query($user_notif)->getRowArray();

                    $tmp_user = array();
                    if($tmp_muser['supported_by'])
                        $tmp_user = (array)json_decode($tmp_muser['supported_by']);

                    $tmp_user[$tmp_muser['pic']] = "";

                    $pic_chat_id = array();
                    $ms_karyawan = new Ms_Karyawan();
                    foreach ($tmp_user as $tmp_key => $tmp_val) {
                        if($tmp_key != $nik) {
                            $temp_TgChat = $ms_karyawan->getTgChat($tmp_key);
                            if($temp_TgChat)
                                $pic_chat_id[] = $temp_TgChat[0];
                        }
                    }
                    
                    $msg = "New Action Log :\n";
                    $msg .= $arr['no_SR']."\n";

                    if($pic_chat_id)
                        tg_message($msg, $pic_chat_id);
                }    
            }
        }

        if($arr['solved'] == 1) {
            $done_it_at= date("Y-m-d H:i:s");
            $sql_solved = "UPDATE trx_sr SET status = 'R', done_it_at = '$done_it_at' 
                            WHERE no_SR LIKE '".$arr['sr']."'";
            $this->db->query($sql_solved);
        }

        $this->db->transComplete();
    }

    public function editActionLog($action_id, $comment, $nik) {   
        $sql = "SELECT * FROM log_sr_action WHERE id LIKE '$action_id'";
        $model = $this->db->query($sql);
        $model = $model->getRowArray();

        $log_date = $model['log_date'];
        //$log_date = date("Y-m-d H:i:s", strtotime($model['log_date']." +1 seconds"));

        $id_detail_sr = $model['id_detail_sr'];
        $update_sql = "UPDATE log_sr_action SET status = '0'
                        WHERE id LIKE '$action_id'";
        $this->db->query($update_sql);
        $insert_sql = "INSERT INTO log_sr_action (id_detail_sr, action_plan, created_by, status, log_date) VALUES ('$id_detail_sr', '$comment', '$nik', '1', '$log_date')";
        $this->db->query($insert_sql);
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
        $sql = "SELECT DISTINCT $column FROM trx_sr WHERE pic LIKE '$nik' OR supported_by LIKE '%\"$nik\"%' $order";

        $model = $this->db->query($sql)->getResultArray();

        $temp_model = array();
        foreach ($model as $key => $value) {
            $temp_model[] = $value[$column];
        }
        return $temp_model;
    }

    /* - Data Tables Logic */
    public function getTrxDT($data = array()) {
        $this->session = \Config\Services::session();
        $company = $this->session->get("company");

        $sql = "SELECT `trx_sr`.*, `ms_priority`.description as priority_name, `kr_pic`.name as pic_name, `kr_pic`.company, `kr_requester`.name as requester_name 
                FROM trx_sr 
                JOIN ms_priority ON `ms_priority`.id = `trx_sr`.priority 
                JOIN ms_karyawan kr_pic ON `kr_pic`.nik = `trx_sr`.pic
                JOIN ms_karyawan kr_requester ON `kr_requester`.nik = `trx_sr`.nik 
                WHERE `kr_pic`.company LIKE '$company'";

        $column_array = ["no_SR", "`kr_pic`.nik", "`kr_pic`.name", "`kr_requester`.nik", "`kr_requester`.name"];

        if(!empty($data))
        {
            $start = $data['start'];
            $length = $data['length'];
            $search = $this->db->escapeString($data['search']);
            $order = $data['order'];
            $order_by = $data['order_by'];

            if(strpos(strtoupper($search), "/IT/HO/") !== false) {
                $temp_sql = "SELECT no_SR FROM detail_sr WHERE no_RO LIKE '$search'";
                $temp_model = $this->db->query($temp_sql)->getRowArray();

                if($temp_model) {
                    $temp_no_sr = $temp_model['no_SR'];
                    $sql .= " AND no_SR LIKE '$temp_no_sr' ";
                }
            } else {
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

        $model = $this->db->query($sql);
        $model = $model->getResultArray();

        return $model;
    }

    public function totalTrxDT() {
        $this->session = \Config\Services::session();
        $company = $this->session->get("company");

        $sql = "SELECT count(no_SR) as total FROM trx_sr 
                JOIN ms_karyawan kr_pic ON `kr_pic`.nik = `trx_sr`.pic
                JOIN ms_karyawan kr_requester ON `kr_requester`.nik = `trx_sr`.nik 
                WHERE `kr_pic`.company LIKE '$company'";

        $url = preg_match("/[^\/]+$/", current_url(), $current_url);
        if($current_url[0] == "ajaxMyService") {
            $nik = $this->session->get('nik');
            $sql .= " AND (`trx_sr`.pic LIKE '$nik' OR supported_by LIKE '%\"$nik\"%') ";
        }

        $model = $this->db->query($sql);
        $model = $model->getRowArray();

        if($model)
            return $model['total'];
        else
            return "0";
    }

    public function totalTrxFilteredDT($search) {
        $this->session = \Config\Services::session();
        $company = $this->session->get("company");

        $search = $this->db->escapeString($search);
        $sql = "SELECT count(no_SR) as total FROM trx_sr
                JOIN ms_priority ON `ms_priority`.id = `trx_sr`.priority
                JOIN ms_karyawan kr_pic ON `kr_pic`.nik = `trx_sr`.pic
                JOIN ms_karyawan kr_requester ON `kr_requester`.nik = `trx_sr`.nik 
                WHERE `kr_pic`.company LIKE '$company'";

        $column_array = ["no_SR", "`kr_pic`.nik", "`kr_pic`.name", "`kr_requester`.nik", "`kr_requester`.name"];

        if(strpos(strtoupper($search), "/IT/HO/") !== false) {
            $temp_sql = "SELECT no_SR FROM detail_sr WHERE no_RO LIKE '$search'";
            $temp_model = $this->db->query($temp_sql)->getRowArray();

            if($temp_model) {
                $temp_no_sr = $temp_model['no_SR'];
                $sql .= " AND no_SR LIKE '$temp_no_sr' ";
            }
        } else {
            if($search) 
            {
                $sql .= " AND (";
                $count_array = count($column_array);
                foreach ($column_array as $key => $value) {
                    $sql .= " $value LIKE '%$search%' ";
                    if($count_array-1 != $key)
                        $sql .= " OR ";
                }
                $sql .= ") ";
            }
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

        $url = preg_match("/[^\/]+$/", current_url(), $current_url);
        if($current_url[0] == "ajaxMyService") {
            $nik = $this->session->get('nik');
            $sql .= " AND (`trx_sr`.pic LIKE '$nik' OR supported_by LIKE '%\"$nik\"%') ";
        }
        
        $model = $this->db->query($sql);
        $model = $model->getRowArray();

        if($model)
            return $model['total'];
        else
            return "0";
    }
    /* Data Tables Logic - */
}