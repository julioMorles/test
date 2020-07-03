<?php
/**
 * Created by PhpStorm.
 * User: Daniel
 * Date: 13/09/2016
 * Time: 3:14 PM
 */

namespace App\Util;

use App\Models\GE\Geparameter;

class ParametrosUtils
{

    /**
     * Retorna un parametro del sistema
     * @param $key String contiene el parametro del sistema.
     * @return string Parametro del sistema del tipo String. Retorna VALUE_NOT_FOUND si no encuentra el valor.
     */
    public static function getStrSysParam($key)
    {

        $result = Geparameter::whereRaw("upper(geparameters.key) = ?", array(strtoupper($key)))->select('strval')->first();

        if (is_null($result) == 0 || !isset($result["strval"])) {
            return "VALUE_NOT_FOUND";
        }

        return $result["strval"];
    }

    /**
     * Retorna un parametro del sistema
     * @param $key String Llave que contiene el parametro del sistema.
     * @return Double Parametro del sistema del tipo numerico. Retorna VALUE_NOT_FOUND si no encuentra el valor.
     */
    public static function getNumSysParam($key)
    {

        $result = Geparameter::whereRaw("upper(geparameters.key) = ?", array(strtoupper($key)))->select('numval')->first();

        if (is_null($result) || !isset($result["numval"])) {
            return "VALUE_NOT_FOUND";
        }

        return $result["numval"];
    }

}