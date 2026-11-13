<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class InitialSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        // 1. Users
        $users = [
            [
                'name'       => 'Administrator',
                'username'   => 'admin',
                'password'   => password_hash('admin123', PASSWORD_BCRYPT),
                'role'       => 'admin',
                'is_active'  => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name'       => 'Kasir 1',
                'username'   => 'kasir',
                'password'   => password_hash('kasir123', PASSWORD_BCRYPT),
                'role'       => 'kasir',
                'is_active'  => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        foreach ($users as $user) {
            $existing = $this->db->table('users')->where('username', $user['username'])->get()->getRow();
            if (! $existing) {
                $this->db->table('users')->insert($user);
            }
        }

        // 2. Services
        $services = [
            [
                'name'       => 'Cuci Mobil Kecil (City Car)',
                'category'   => 'mobil',
                'price'      => 40000,
                'is_active'  => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name'       => 'Cuci Mobil Sedang (MPV/SUV)',
                'category'   => 'mobil',
                'price'      => 50000,
                'is_active'  => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name'       => 'Cuci Mobil Besar (Pick-up/Minibus)',
                'category'   => 'mobil',
                'price'      => 65000,
                'is_active'  => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name'       => 'Cuci + Vakum Interior',
                'category'   => 'mobil',
                'price'      => 75000,
                'is_active'  => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name'       => 'Cuci Helm Half Face',
                'category'   => 'helm',
                'price'      => 20000,
                'is_active'  => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name'       => 'Cuci Helm Full Face',
                'category'   => 'helm',
                'price'      => 30000,
                'is_active'  => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name'       => 'Cuci Helm + Disinfektan Busa',
                'category'   => 'helm',
                'price'      => 40000,
                'is_active'  => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        foreach ($services as $service) {
            $existing = $this->db->table('services')->where('name', $service['name'])->get()->getRow();
            if (! $existing) {
                $this->db->table('services')->insert($service);
            }
        }

        // 3. Settings
        $settings = [
            [
                'key'        => 'business_name',
                'value'      => 'AutoClean Car & Helmet Wash',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key'        => 'address',
                'value'      => 'Jl. Raya Utama No. 123, Jakarta',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key'        => 'phone',
                'value'      => '0812-3456-7890',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key'        => 'receipt_footer',
                'value'      => 'Terima kasih atas kunjungan Anda! Bersih, Cepat, dan Mengkilap.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key'        => 'invoice_prefix',
                'value'      => 'CW',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        foreach ($settings as $setting) {
            $existing = $this->db->table('settings')->where('key', $setting['key'])->get()->getRow();
            if (! $existing) {
                $this->db->table('settings')->insert($setting);
            }
        }

        // 4. Sample Customers
        $customers = [
            [
                'name'       => 'Budi Santoso',
                'phone'      => '081298765432',
                'notes'      => 'Mobil Avanza Hitam B 1234 CD',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name'       => 'Siti Rahma',
                'phone'      => '081387654321',
                'notes'      => 'Helm KYT Full Face Hitam Doff',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        foreach ($customers as $customer) {
            $existing = $this->db->table('customers')->where('name', $customer['name'])->get()->getRow();
            if (! $existing) {
                $this->db->table('customers')->insert($customer);
            }
        }
    }
}
