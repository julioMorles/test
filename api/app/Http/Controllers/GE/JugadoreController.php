<?php

namespace App\Http\Controllers\GE;

use App\Http\Controllers\BaseController;
use App\Models\Jugadore;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class JugadoreController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->onlyStore = array(
            "nombre",
            "apellidos",
            "nick",
            "email",
            "fondos",
            "apuesta",
        );
        $this->onlyUpdate = array(
            "nombre",
            "apellidos",
            "nick",
            "email",
            "fondos",
            "apuesta",
        );
    }

    public function setModel()
    {
        $this->model = '\App\Models\Jugadore';
    }

    public function setCollection()
    {
        $this->collection = '\App\Http\Resources\GE\Jugadore';
    }

    public function setEager()
    {
        $this->eager = array();
    }

    public function setWhereable()
    {
        $this->whereable = array('nombre');
    }

    public function getcustomRules($request)
    {

        if ($this->isStore($request)) {
            return array(
                array('nombre' => 'required',
                    'apellidos' => 'required',
                    'nick' => 'required',
                    'email' => 'required',
                ),

                array('nombre.required' => 'REQUIRED_:attribute',
                    'apellidos.required' => 'REQUIRED_:attribute',
                    'nick.required' => 'REQUIRED_:attribute',
                    'email.required' => 'REQUIRED_:attribute',
                )
            );

        } else if ($this->isUpdate($request)) {

            return array(
                array(
                ),

                array(
                )
            );

        }
    }

    public function apuesta()
    {
        $ruleta = $this->aleatorio();
        $jugadores = Jugadore::all();
        foreach ($jugadores as $jugadore) {// realizo la apuesta para cada jugador
            $apuestaJugador = $this->aleatorio();
            $newApuesta = Jugadore::find($jugadore->id);//objeto con cada jugador para hacer update
            if($jugadore->fondos > 0 ){
                if($jugadore->fondos > 1000){//si es mayor a 1000 para apostar entre 8 y 15
                    $porApuesta = rand(8,15); //porcentaje de apuesta
                    $totalApuesta = ($porApuesta / 100) * $jugadore->fondos;
                    if($ruleta === $apuestaJugador){
                        if($ruleta === 3){
                            $newApuesta->fondos =  $totalApuesta * 15;
                        } else {
                            $newApuesta->fondos =  $totalApuesta * 2;
                        }
                    } else {
                        $newApuesta->fondos =   $jugadore->fondos - $totalApuesta;
                    }
                    $newApuesta->apuesta = $apuestaJugador;
                } else {
                    if($ruleta === $apuestaJugador){
                        if($ruleta === 3){
                            $newApuesta->fondos =  $jugadore->fondos * 15;
                        } else {
                            $newApuesta->fondos =  $jugadore->fondos * 2;
                        }
                    } else {
                        $newApuesta->fondos = 0;
                    }
                    $newApuesta->apuesta = $apuestaJugador;
                }

            } else {
                $newApuesta->fondos = 0;
                $newApuesta->apuesta = null;
            }

            $newApuesta->save();
        }

        return $ruleta;

    }

    public function aleatorio(){ //Saco un numero del 1 al 3, con 1 para el rojo, dos para el negro y 3 para el verde.
        $numeros = [1,1,1,1,2,2,2,2,3,3];
        $indice = rand(0,9);

        return $numeros[$indice];
    }
}
