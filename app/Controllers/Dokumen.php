<?php

namespace App\Controllers;

use App\Models\DocumentModel;
use App\Models\UserModel;

class Dokumen extends BaseController
{
    protected $documentModel;
    protected $db;

    public function __construct()
    {
        $this->documentModel = new DocumentModel();
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $userId = session()->get('user_id');
        if (!$userId) return redirect()->to('/login');

        $sql = "SELECT d.*, u.name as owner_name 
                FROM documents d 
                JOIN users u ON d.owner_id = u.id
                LEFT JOIN collaborators c ON d.id = c.document_id 
                WHERE d.owner_id = ? OR c.user_id = ? 
                ORDER BY d.updated_at DESC";
        $query = $this->db->query($sql, [$userId, $userId]);
        $data['docs'] = $query->getResultArray();
        return view('dokumen/list', $data);
    }

    public function create()
    {
        $userId = session()->get('user_id');
        
        // Cek Keamanan Session
        $userModel = new UserModel();
        $cekUser = $userModel->find($userId);
        
        if (!$cekUser) {
            session()->destroy();
            return redirect()->to('/login')->with('error', 'Sesi tidak valid. Login ulang.');
        }

        // Simpan
        $this->documentModel->save([
            'owner_id' => $userId,
            'judul' => 'Dokumen Baru ' . date('H:i'),
            'isi'   => '<p>Mulai mengetik...</p>',
            'is_public' => 0
        ]);
        return redirect()->to('/');
    }

    public function edit($id)
    {
        $doc = $this->documentModel->find($id);
        if (!$doc) return redirect()->to('/');
        if (!$this->checkAccess($doc)) return redirect()->to('/')->with('error', 'Akses Ditolak');

        $data['doc'] = $doc;
        $userModel = new UserModel();
        $data['owner'] = $userModel->find($doc['owner_id']);
        return view('dokumen/editor', $data);
    }

    public function update($id)
    {
        $doc = $this->documentModel->find($id);
        if ($this->checkAccess($doc)) {
            $this->documentModel->update($id, ['isi' => $this->request->getVar('isi')]);
            return $this->response->setJSON(['status' => 'success']);
        }
    }

    public function rename($id)
    {
        $doc = $this->documentModel->find($id);
        if ($this->checkAccess($doc)) {
            $this->documentModel->update($id, ['judul' => $this->request->getVar('judul')]);
            return $this->response->setJSON(['status' => 'success']);
        }
    }

    public function toggle_public($id)
    {
        $doc = $this->documentModel->find($id);
        if ($doc['owner_id'] == session()->get('user_id')) {
            $this->documentModel->update($id, ['is_public' => $this->request->getVar('is_public')]);
            return $this->response->setJSON(['status' => 'success']);
        }
    }

    public function invite_user($id)
    {
        $doc = $this->documentModel->find($id);
        if ($doc['owner_id'] != session()->get('user_id')) return;

        $targetUsername = $this->request->getVar('username');
        $userModel = new UserModel();
        $targetUser = $userModel->where('username', $targetUsername)->first();

        if ($targetUser) {
            $exists = $this->db->table('collaborators')->where('document_id', $id)->where('user_id', $targetUser['id'])->countAllResults();
            if (!$exists && $targetUser['id'] != $doc['owner_id']) {
                $this->db->table('collaborators')->insert(['document_id' => $id, 'user_id' => $targetUser['id']]);
                return $this->response->setJSON(['status' => 'success', 'msg' => 'User diundang!']);
            }
            return $this->response->setJSON(['status' => 'error', 'msg' => 'User sudah ada / owner']);
        }
        return $this->response->setJSON(['status' => 'error', 'msg' => 'User tidak ditemukan']);
    }

    public function delete($id)
    {
        $doc = $this->documentModel->find($id);
        if ($doc['owner_id'] == session()->get('user_id')) {
            $this->documentModel->delete($id);
            return $this->response->setJSON(['status' => 'success']);
        }
    }

    public function get_content($id)
    {
        $doc = $this->documentModel->find($id);
        return $this->response->setJSON(['isi' => $doc['isi'], 'is_public' => $doc['is_public']]);
    }

    private function checkAccess($doc)
    {
        $userId = session()->get('user_id');
        if ($doc['is_public'] == 1) return true;
        if ($doc['owner_id'] == $userId) return true;
        $isCollab = $this->db->table('collaborators')->where('document_id', $doc['id'])->where('user_id', $userId)->countAllResults();
        return $isCollab > 0;
    }
}