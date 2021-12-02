<?php

namespace Form\Validators;


use Form\Base\Validator;

class Cpf extends Validator
{

  protected $message = 'CPF inválido';

  public function validation($value, \Form\Form $instance, $id = null)
  {
    $cpfs = array(
      '00000000000',
      '11111111111',
      '22222222222',
      '33333333333',
      '44444444444',
      '55555555555',
      '66666666666',
      '77777777777',
      '88888888888',
      '99999999999'
    );


    // Verifica se um número foi informado
    if (empty($value)) {
      return false;
    }

    // Elimina possivel mascara
    $value = preg_replace('/[^0-9]/i', '', $value);
    //$value = str_pad ( $value, 11, '0', STR_PAD_LEFT );

    // Verifica se o numero de digitos informados é igual a 11
    if (strlen($value) != 11) {
      return false;
    }         // Verifica se nenhuma das sequências invalidas abaixo
    // foi digitada. Caso afirmativo, retorna falso

    else if (in_array($value, $cpfs)) {
      return false;
      // Calcula os digitos verificadores para verificar se o
      // CPF é válido
    } else {

      for ($t = 9; $t < 11; $t++) {

        for ($d = 0, $c = 0; $c < $t; $c++) {
          $d += $value[$c] * (($t + 1) - $c);
        }

        $d = ((10 * $d) % 11) % 10;

        if ($value[$c] != $d) {
          return false;
        }
      }

      return true;
    }
  }
}