<?php

namespace App\Models;

use CodeIgniter\Model;
helper('custom_helper');

class IT_Staff extends Model
{
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
            $order = " ORDER BY nik DESC";
        } else {
            $order = " ORDER BY $key $value";
        }

        //Select sql, also check from $condition
        $sql = "SELECT *
                FROM it_staff
                WHERE 1=1 $condition $order $limit";

        $model = $this->db->query($sql);
        $model = $model->getResultArray();


        if($model) {
            foreach ($model as $key => $value) 
            {
                if($key == "site") 
                    $model[$key]['site'] = json_decode($value['site']);
                if ($key == "svc")  
                {
                    $temp_svc = json_decode($value['svc']); 
                    $temp_arr = array();
                    $this->db->transStart();
                    foreach ($temp_svc as $key2 => $value2) 
                    {
                       $sql = "SELECT id, name FROM ms_svc WHERE id LIKE '$value2'";
                       $temp_model = $this->db->query($sql)->getRowArray();
                       $temp_arr[$temp_model['id']] = $temp_model['name'];
                    }
                    $this->db->transComplete();

                    unset($model[$key]['svc']);
                    $model[$key]['svc'] = $temp_arr;         
                }
            }
        } 

        return $model;
    }
}