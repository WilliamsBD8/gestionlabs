<?php namespace App\Database\Seeds;

class UserSeeder extends \CodeIgniter\Database\Seeder
{
    public function run()
    {
        $data = [
            [
                'name'      => 'Administrador',
                'email'     => 'iplanet@iplanetcolombia.com',
                'username'  => 'root',
                'password'  => password_hash('I49bx3kk!!', PASSWORD_DEFAULT),
                'status'    => 'active',
                'photo'     => '',
                'role_id'   => 1
            ]
        ];

        foreach ($data as $item):
            $this->db->table('users')->insert($item);
        endforeach;
    }
}