<?php

namespace App\Controllers;

use App\Libraries\AuditLogger;
use App\Models\SettingModel;

class Settings extends BaseController
{
    protected SettingModel $settingModel;

    public function __construct()
    {
        $this->settingModel = new SettingModel();
    }

    public function index()
    {
        if ($this->request->is('post')) {
            $rules = [
                'business_name'  => 'required|min_length[3]|max_length[100]',
                'address'        => 'permit_empty|max_length[255]',
                'phone'          => 'permit_empty|max_length[50]',
                'receipt_footer' => 'permit_empty|max_length[255]',
                'invoice_prefix' => 'required|alpha_numeric|min_length[1]|max_length[10]',
            ];

            if (! $this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $fields = ['business_name', 'address', 'phone', 'receipt_footer', 'invoice_prefix'];
            foreach ($fields as $key) {
                $val = trim((string) $this->request->getPost($key));
                $this->settingModel->setSetting($key, $val);
            }

            AuditLogger::log(
                (int) session()->get('user_id'),
                'UPDATE_SETTINGS',
                'setting',
                null,
                'Memperbarui informasi profil dan identitas usaha'
            );

            return redirect()->to('/settings')->with('success', 'Pengaturan usaha berhasil disimpan.');
        }

        $settings = $this->settingModel->getAllAssoc();

        return view('settings/index', [
            'title'    => 'Pengaturan Usaha',
            'settings' => $settings,
        ]);
    }
}
