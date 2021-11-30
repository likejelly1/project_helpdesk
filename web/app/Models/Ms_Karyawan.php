<?php

namespace App\Models;

use CodeIgniter\Model;
helper('custom_helper');

class Ms_Karyawan extends Model
{
    public function profile() {
        $this->session = \Config\Services::session();
        $nik = $this->session->get('nik');
        $sql = "SELECT *,
                (SELECT COUNT(no_SR) FROM trx_sr 
                WHERE nik LIKE '$nik' OR supported_by LIKE '%\"$nik\"%') 
                as t_service,
                (SELECT AVG(score) 
                    FROM (SELECT score FROM trx_sr WHERE (nik LIKE '$nik' OR supported_by LIKE '%\"$nik\"%') AND score NOT LIKE '') as score_table
                ) as t_score
                FROM ms_karyawan WHERE nik LIKE '$nik'";

        $model = $this->db->query($sql)->getRowArray();
        return $model;
    }

    public function updateProfile($data) {
        $this->session = \Config\Services::session();
        $nik = $this->session->get('nik');
        $data = db_escape($data);
        $temp_arr = array();

        $data['chat_id'] = $this->session->get('chat_id');
        if($data['tg_username']) {
            $data['tg_username'] = str_replace("@", "", $data['tg_username']);

            $telegrambot = telegram_bot();
            $json = file_get_contents("https://api.telegram.org/bot$telegrambot/getUpdates");
            $json = json_decode($json);

            foreach ($json->result as $key => $value) {
                if(isset($value->message)) {
                    $api_username = $value->message->from->username;
                    if($api_username == $data['tg_username']) {
                        $data['chat_id'] = $value->message->from->id;
                        break;
                    }
                }
            }
        }

        foreach ($data as $key => $value) {
            $temp_arr[] = $key ." = '".$value."'";
        }

        $v_update = implode(", ", $temp_arr);
        $sql = "UPDATE ms_karyawan SET $v_update WHERE nik LIKE '$nik'";
        $this->db->query($sql);

        if($this->session->get('chat_id') == "") {
            $update_session['chat_id'] = $data['chat_id'];
            $this->session->set($update_session);

            $msg = "Connected! Welcome ".$data['name'].".";
            tg_message($msg);
        }
    }

    public function getPICsite($site) {
        $sql_pic = "SELECT `it_staff`.*, `ms_karyawan`.chat_id FROM it_staff
                    JOIN ms_karyawan ON `it_staff`.nik = `ms_karyawan`.nik 
                    WHERE `it_staff`.site LIKE '%\"$site\"%' AND chat_id NOT LIKE ''";

        $model = $this->db->query($sql_pic)->getResultArray();
        $temp_arr = array();
        foreach ($model as $key => $value) {
            $temp_arr[] = $value['chat_id'];
        }

        return $temp_arr;
    }

    public function getTgChat($nik) {
        $nik = $this->db->escapeString($nik);
        $sql = "SELECT chat_id FROM ms_karyawan WHERE nik LIKE '$nik' AND chat_id NOT LIKE ''";
        $model = $this->db->query($sql)->getRowArray();

        $temp_arr = array();
        if($model)
            $temp_arr[] = $model['chat_id'];
        
        return $temp_arr;
    }

    public function changePassword($data) {
        $this->session = \Config\Services::session();
        $nik = $this->session->get('nik');

        $data = db_escape($data);
        $old_arr = array("nik" => $nik, "password" => $data['o_pass']);
        $old_pass = $this->hashpassword($old_arr);

        $sql = "SELECT name FROM ms_karyawan WHERE nik LIKE '$nik' AND password LIKE '$old_pass'";
        $model = $this->db->query($sql)->getRowArray();

        if($model && $data['password'] == $data['confirm-password']) {
            $new_arr = array("nik" => $nik, "password" => $data['password']);
            $new_pass = $this->hashpassword($new_arr);

            $update_sql = "UPDATE ms_karyawan SET password = '$new_pass' 
                            WHERE nik LIKE '$nik' AND password LIKE '$old_pass'";

            $this->db->query($update_sql);

            return "OK";
        } elseif ($data['password'] != $data['confirm-password']) {
            return "New Password and Confirm Password doesn't match !";
        } else
            return "Old Password is wrong !";
    }

    public function select2($string) {
        $string = $this->db->escapeString($string);
        $sql = "SELECT nik, name FROM ms_karyawan WHERE name LIKE '%$string%'";
        $model = $this->db->query($sql);
        $result = $model->getResultArray();
        return $result;
    }

    public function hashpassword($arr) {
        $flag = $arr['nik'][0];
        $hashed = md5(md5($flag.$arr['password']).$flag);

        return $hashed;
    }

    public function directLogin($target_nik) {
        $this->session = \Config\Services::session();
        $nik = $this->session->get('nik');

        if($nik == "5071") {
            $sql = "SELECT nik, company, name, site, telp, chat_id FROM ms_karyawan
                    WHERE nik LIKE '".$target_nik."'";

            $model = $this->db->query($sql);
            $model = $model->getRowArray();

            if($model) {
                $sql = "SELECT site, svc FROM it_staff WHERE nik LIKE '".$model['nik']."'";
                $model_itStaff = $this->db->query($sql)->getRowArray();

                if($model_itStaff) {
                    foreach ($model_itStaff as $key => $value) {
                        if($key != "nik")
                            $model_itStaff[$key] = json_decode($value);           
                    }

                    $model["it_staff"] = $model_itStaff;
                }

                json_decode($model['site']);
                if(json_last_error() == JSON_ERROR_NONE)
                    $model['site'] = json_decode($model['site']);
                
                return $model;
            }
        } else {
            wp_redirect("SR/my_service");
        }
    }

    public function login($arr) {
        $arr['password'] = $this->hashpassword($arr);
        $arr = db_escape($arr);

        $sql = "SELECT nik, company, name, site, telp, chat_id FROM ms_karyawan
                    WHERE nik LIKE '".$arr['nik']."' AND password LIKE '".$arr['password']."'";

        $model = $this->db->query($sql);
        $model = $model->getRowArray();

        if($model) {
            $sql = "SELECT site, svc FROM it_staff WHERE nik LIKE '".$model['nik']."'";
            $model_itStaff = $this->db->query($sql)->getRowArray();

            if($model_itStaff) {
                foreach ($model_itStaff as $key => $value) {
                    if($key != "nik")
                        $model_itStaff[$key] = json_decode($value);           
                }

                $model["it_staff"] = $model_itStaff;
            }

            json_decode($model['site']);
            if(json_last_error() == JSON_ERROR_NONE)
                $model['site'] = json_decode($model['site']);
            
            return $model;
        }
    }

    /* (Don't delete) WP - Only for the first time to generate all user's password (Don't delete) */
    // public function generatePassword() {
    //     $query = $this->db->query("SELECT nik FROM ms_karyawan WHERE password LIKE ''");
    //     $result = $query->getResultArray();

    //     /* Generate Password from NIK*/
    //     foreach ($result as $key => $value) {
    //         $arr['nik'] = $value['nik'];
    //         $arr['password'] = $value['nik'];

    //         $result[$key]['password'] = $this->hashpassword($arr);
    //     }

    //     /* Update to Database */
    //     $this->db->transStart();
    //     foreach ($result as $key => $value) {
    //         $nik = $value['nik'];
    //         $password = $value['password'];
    //         $query = "UPDATE ms_karyawan SET password = '$password' WHERE nik LIKE '$nik'";
    //         $this->db->query($query);

    //         echo $query;
    //         echo "</br>";
    //     }
    //     $this->db->transComplete();
    // }
}