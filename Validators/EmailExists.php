<?php

namespace Validators;


use Classes\Validator;

class EmailExists extends Validator
{

  protected $message = 'Email já cadastrado';

  public function validation($value, \Form\Form $instance, $id = null)
  {


    if (!$value) {
      return false;
    }

    global $wpdb;

    $query = $wpdb->prepare(
      "SELECT COUNT(post_id) FROM {$wpdb->postmeta}"
        . " where meta_key='vivasul_parceiro_email' AND meta_value=%s",
      $value
    );

    $result = $wpdb->get_var($query);

    if ($result) {
      return false;
    }

    return true;
  }
}