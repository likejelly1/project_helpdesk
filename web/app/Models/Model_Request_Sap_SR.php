<?php

namespace App\Models;

use CodeIgniter\Model;

class Model_Request_Sap_SR extends Model
{
    protected $table = 'request_sap_sr';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'nik', 'request', 'reason', 'modul', 'attachment', 'created_date', 'updated_date', 'status'
    ];
    protected $returnType = 'App\Entities\Request_Sap_SR';
    protected $useTimestamps = false;
}