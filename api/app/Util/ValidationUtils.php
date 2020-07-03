<?php 

namespace App\Util;

class ValidationUtils
{
	public static  $rules =  [
        'email'    => 'required|email|unique:users,email',
        'name' 	   => 'required|alpha_spaces',
        'password' => 'required'
    ];

    public static  $messages =  [
        'email.required' => 'REQUIRED_:attribute',
        'email.email' => 'WRONG_TYPE_:attribute',
        'email.unique' => 'DUPLICATED_:attribute',
        'name.required' => 'REQUIRED_:attribute',
        'name.alpha_spaces' => 'ALPHA_SPACE_:attribute',
        'password.required' => 'REQUIRED_:attribute'
    ];

	/**
	 * var $index String
	 * Espera un String cocatenado con |
	 * Retorna un array, posicion 0 reglas, posicion 1 mensajes
	 */
	public static function getRules($index){
		//Separa los indices
		$indices = explode('|', $index);
		$r = array();
		$m = array();

		foreach ($indices as $indice) {
			if( array_key_exists($indice, ValidationUtils::$rules) ){
				$r[$indice] = ValidationUtils::$rules[$indice];
			}
		}

		foreach ($r as $rule => $value) {
			$messages = 
			array_filter(ValidationUtils::$messages, function($k) use($rule){
  				return preg_match("#^$rule#i", $k) === 1;
			}, ARRAY_FILTER_USE_KEY);
			
			$m = array_merge($m, $messages);

		}
		return array($r, $m);
	}

	public static function getRulesFromArray($index){
		return ValidationUtils::getRules(implode('|',$index));
	}
}