<?php

use Illuminate\Database\Seeder;

class generalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        echo "Iniciando ejecuion de Seeder " . __CLASS__ . "\n";

        echo "Creando Usuario Maestro - Line:" . __LINE__ . "\n";
        $newUser = \App\Models\User::create([
            'name' => 'admin',
            'email' => 'admin@sistemas.com',
            'password' => 'admin'
        ]);

        echo "Creando Rol SAdmin - Line:" . __LINE__ . "\n";

        $newRolSadmin = \App\Models\Role::create([
            'nombre' => 'SAdmin'
        ]);


        echo "Asignando rol SAdmin a usuario maestro - Line:" . __LINE__ . "\n";

        \App\Models\UserRole::create([
            'user_id' => $newUser->id,
            'role_id' => $newRolSadmin->id
        ]);

        echo "Creando Rol User - Line:" . __LINE__ . "\n";

        $newRolUser = \App\Models\Role::create([
            'nombre' => 'User'
        ]);


        echo "Asignando rol User a usuario maestro - Line:" . __LINE__ . "\n";

        \App\Models\UserRole::create([
            'user_id' => $newUser->id,
            'role_id' => $newRolUser->id
        ]);



        echo "Creando Unidadaes - Line:" . __LINE__ . "\n";
        $newU = \App\Models\GE\Unidade::create([
            'descripcion' => 'Unidad'
        ]);
        $newD = \App\Models\GE\Unidade::create([
            'descripcion' => 'Docena'
        ]);

        $newEmpresa = \App\Models\GE\Empresa::create([
            'nombre'=>'EgoTex',
            'telefono'=>'4102429',
            'ciudad'=>'Cali',
            'direccion'=>'',
            'rangoini'=>0,
            'rangofn'=>200

        ]);

        $newcc = \App\Models\GE\Tipo::create([
            'descripcion'=>'Cedula',
        ]);
        $newnit = \App\Models\GE\Tipo::create([
            'descripcion'=>'Nit',
        ]);

        $newpago = \App\Models\GE\Tipopago::create([
            'descripcion'=>'Contado',
        ]);

        $newnpago = \App\Models\GE\Tipopago::create([
            'descripcion'=>'Credito',
        ]);
    }
}
