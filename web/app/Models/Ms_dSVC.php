<?php

namespace App\Models;

use CodeIgniter\Model;

class Ms_dSVC extends Model
{
    protected $table      = 'ms_dsvc';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['id_svc, name'];

    protected $useTimestamps = false;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    //protected $deletedField  = 'deleted_at';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;

    public function getName($id) {
        $id = $this->db->escapeString($id);
        $sql = "SELECT name FROM ms_dsvc WHERE id LIKE '$id'";
        $model = $this->db->query($sql);
        $model = $model->getRowArray();

        return $model['name'];
    }
}