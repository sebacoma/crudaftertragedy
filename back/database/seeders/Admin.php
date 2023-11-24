<?php

use Illuminate\Database\Seeder;
use App\Models\User;

class Admin extends Seeder
{
    public function run()
    {
        // Ejemplo de inserción de un usuario
        User::create([
            'name' => 'Ochietto',
            'password' => bcrypt('Jaqamain3pals'),
        ]);

        // Puedes crear más registros con el mismo formato
    }
}
