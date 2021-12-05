<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Request_Sap_SR extends Entity
{
    public function setGambar($file)
    {
        # code...
        $fileName = $file->getRandomName();
        $writePath = './uploads';
        $file->move($writePath, $fileName);
        $this->attributes['attachment'] = $fileName;
        return $this;
    }
}