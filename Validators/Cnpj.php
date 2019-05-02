<?php

namespace Validators;


use Classes\Validator;

class Cnpj extends Validator
{

    protected $message = 'CNPJ inválido';

    public function validation($value, \Form\Form $instance, $id = null)
    {
        $cnpjs = array(
            '00000000000000',
            '11111111111111',
            '22222222222222',
            '33333333333333',
            '44444444444444',
            '55555555555555',
            '66666666666666',
            '77777777777777',
            '88888888888888',
            '99999999999999'
        );

        // Verifica se um número foi informado
        if (empty($value)) {            
            return false;
        }

        // Elimina possivel mascara
        $value = preg_replace('/[^0-9]/i', '', $value);
        //$value = str_pad ( $value, 11, '0', STR_PAD_LEFT );

        // Verifica se o numero de digitos informados é igual a 14
        if (strlen($value) != 14) {            
            return false;
        } // Verifica se nenhuma das sequências invalidas abaixo
        // foi digitada. Caso afirmativo, retorna falso

        else if (in_array($value, $cnpjs)) {            
            return false;
            // Calcula os digitos verificadores para verificar se o
            // CPF é válido
        } else {

            $valide = $this->valide_cnpj($value);
            if ($valide == false) {                
                return false;
            }

            return true;
        }
    }

    public function valide_cnpj($cnpj)
    {
        // Valida primeiro dígito verificador
        for ($i = 0, $j = 5, $soma = 0; $i < 12; $i++) {
            $soma += $cnpj{$i} * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }
        $resto = $soma % 11;
        if ($cnpj{12} != ($resto < 2 ? 0 : 11 - $resto)) {
            return false;
        }

        // Valida segundo dígito verificador
        for ($i = 0, $j = 6, $soma = 0; $i < 13; $i++) {
            $soma += $cnpj{$i} * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }
        $resto = $soma % 11;
        if ($cnpj{13} != ($resto < 2 ? 0 : 11 - $resto)) {
            return false;
        }
        return true;
    }
}
