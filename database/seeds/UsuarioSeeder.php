<?php

use Illuminate\Database\Seeder;

class UsuarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('usuarios')->insert(
                                    [
                                        'usuario' => 'demo',
                                        'contrasena' => Hash::make('abcd1234'), // password
                                        'estado' => 1,
                                    ]
                                );

    }
}
