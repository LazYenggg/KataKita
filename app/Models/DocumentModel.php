<?php

namespace App\Models;

use CodeIgniter\Model;

class DocumentModel extends Model
{
    protected $table            = 'documents';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    
    // PERBAIKAN UTAMA DI SINI:
    // Kita izinkan owner_id dan is_public untuk disimpan
    protected $allowedFields    = ['owner_id', 'judul', 'isi', 'is_public'];

    protected $useTimestamps = true;
}