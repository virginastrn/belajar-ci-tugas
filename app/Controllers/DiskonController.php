<?php

namespace App\Controllers;
use App\Models\DiskonModel;
use CodeIgniter\Controller;

class DiskonController extends BaseController
{
    protected $diskonModel;

    public function __construct()
    {
        $this->diskonModel = new DiskonModel();
        helper(['form']);
    }

    public function index()
    {
        $data['diskon'] = $this->diskonModel->findAll();
        return view('admin/diskon/index', $data);
    }

    public function create()
    {
        return view('admin/diskon/create');
    }

    public function store()
    {
        $rules = [
            'tanggal' => 'required|is_unique[diskon.tanggal]',
            'nominal' => 'required|numeric'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->diskonModel->save([
            'tanggal' => $this->request->getPost('tanggal'),
            'nominal' => $this->request->getPost('nominal'),
        ]);

        return redirect()->to('/admin/diskon')->with('success', 'Data diskon berhasil ditambahkan');
    }

    public function edit($id)
    {
        $data['diskon'] = $this->diskonModel->find($id);
        return view('admin/diskon/edit', $data);
    }

    public function update($id)
    {
        $rules = [
            'nominal' => 'required|numeric'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->diskonModel->update($id, [
            'nominal' => $this->request->getPost('nominal'),
        ]);

        return redirect()->to('/admin/diskon')->with('success', 'Data diskon berhasil diupdate');
    }

    public function delete($id)
    {
        $this->diskonModel->delete($id);
        return redirect()->to('/admin/diskon')->with('success', 'Data diskon berhasil dihapus');
    }
}
