<?php

/**
 * Class for create Form
 */

namespace Form;

use Form\Traits\HelperHTML;

final class Form
{
  use HelperHTML;

  private $currentFieldID = '';

  private $data = array();

  private $decorators = array();

  private $decoratorBefore = array(
    'valid'     => '<p>',
    'invalid'  => '<p>',
    'initial'  => '<p>',
  );

  private $decoratorAfter = array(
    'valid'     => '</p>',
    'invalid'  => '<span class="error" >%s</span></p>',
    'initial'  => '</p>',
  );

  private $fieldsStatus = array();

  private $fields = array();

  private $formAttrs = array();

  private $fieldCSSClass = array('valid' => 'is-valid', 'invalid' => 'is-invalid', 'initial' => '');

  private $fieldValidationCSS = array();

  private $messagesErrors = array();

  private $status = array('initial', 'valid', 'invalid');

  private $validators = array();

  public function __construct(array $attrs = array())
  {
    foreach ($attrs as $attr => $value) {
      $this->setFormAttr($attr, $value);
    }
  }

  /**
   * Set Form Attrs;
   */
  public function setFormAttr(string $attr, string $value)
  {
    $this->formAttrs[$attr] = $value;

    return $this;
  }

  private function setCurrentFieldID(string $id)
  {
    $this->currentFieldID = $id;
  }

  /**
   * Set global css class for any fields
   * @param array $css_classes array('valid' => 'valid', 'invalid' => 'invalid', 'initial' => '')
   * @return $this
   */
  public function setFieldsCSSClass(array $css_classes)
  {
    $this->fieldCSSClass = array_merge($this->fieldCSSClass, $css_classes);

    return $this;
  }

  public function resetFieldsCSSClass()
  {
    $this->fieldCSSClass = array('valid' => 'is-valid', 'invalid' => 'is-invalid', 'initial' => '');

    return $this;
  }

  /**
   * Set decorator for any inputs did you replace use method setFieldDecorator
   * @param string $status accepts valid, invalid and initial. use "," for multiples status
   * @return $this
   */
  public function setFieldsDecorator($before = '', $after = '', $status = 'initial')
  {
    
    foreach (explode(',', $status) as $s) {
      $this->decoratorBefore[$s] = $before;
      $this->decoratorAfter[$s] = $after;
    }

    return $this;
  }

  /**
   * Set decorator for input
   * @return $this
   */
  public function setFieldDecorator($before = '', $after = '', $status = 'initial', $id = null)
  {
    $id = $id ?? $this->currentFieldID;

    if (!in_array($status, $this->status)) {
      throw new \Exception('Incorrect value for parameter $status. Use: initial, valid or invalid');
      $status = 'initial';
    }

    $this->decorators[$id][$status]['before'] = $before;
    $this->decorators[$id][$status]['after'] = $after;

    return $this;
  }

  /**
   * Set Valid or Invalid css class for field
   * @param string $id element id
   * @param string $type : valid or invalid
   */
  public function setCSSClass($id = null, $type = '')
  {
    $class = isset($this->fieldCSSClass[$type]) ? $this->fieldCSSClass[$type] : '';
    $this->fieldValidationCSS[$id] = $class;
  }

  public function setFieldStatus($id = null, $type = 'initial')
  {
    $this->fieldsStatus[$id] = $type;
  }

  public function getCSSClass($id)
  {
    return $this->fieldValidationCSS[$id];
  }

  /**
   * Set Field
   * @param string $id unique name
   * @param array $attrs of field
   * @param Classes\Field $field class of field
   * @return $this
   */
  public function setField(string $id, array $attrs, $field)
  {

    if (empty($id)) {
      throw new \Exception('ID not defined');
    }

    if (!$field instanceof \Form\Base\Field) {
      throw new \Exception('Invalid Class');
      return false;
    }


    if (!empty($this->fieldCSSClass['initial'])) {
      $attrs['class'] = isset($attrs['class']) ? $this->fieldCSSClass['initial'] . ' ' . $attrs['class'] : $this->fieldCSSClass['initial'];
    }

    if (!isset($attrs['id'])) {
      $attrs['id'] = $id;
    }

    if (!isset($attrs['name'])) {
      $attrs['name'] = $id;
    }

    if (isset($attrs['label']) && !isset($attrs['placeholder'])) {
      $attrs['placeholder'] = $attrs['label'];
    }

    $this->fields[$id] = [
      $attrs,
      $field
    ];

    $this->setCurrentFieldID($id);

    return $this;
  }

  /**
   * @param \Form\Base\Validator $validator
   * @param string $id use if replace current fieldname
   * @return $this 
   */
  public function setFieldValidator(\Form\Base\Validator $validator, $id = null)
  {

    if (!$validator instanceof \Form\Base\Validator) {
      throw new \Exception('Invalid Class');
      return false;
    }

    //Update fieldname
    if ($id) {
      $this->setCurrentFieldID($id);
    }

    if ($this->currentFieldID == null) {
      throw new \Exception('Fieldname not defined');
      return $this;
    }

    $this->validators[$this->currentFieldID][] = $validator;

    return $this;
  }

  public function setData($data)
  {
    foreach (array_keys($this->getFields()) as $id) {

      list($attrs, $field) = $this->getField($id);

      if (isset($attrs['type']) && strtolower($attrs['type']) == 'file' && isset($_FILES[$id])) {
        $this->data[$id] = $_FILES[$id];
        continue;
      }

      // Prevent add \Form\Fields\_Empty Decorator into data
      // and verify if exist the field into request
      if (!$field instanceof \Form\Fields\_Empty && isset($data[$id])) {
        $this->data[$id] = $data[$id];
      }
    }
  }

  public function getData()
  {
    return $this->data;
  }

  public function getFields()
  {
    return $this->fields;
  }

  public function getField($id = null)
  {
    if (\is_null($id)) {
      throw new \Exception('ID not defined');
      return false;
    }

    return $this->fields[$id];
  }

  public function getFieldStatus($id = null)
  {
    return isset($this->fieldsStatus[$id]) ? $this->fieldsStatus[$id] : 'initial';
  }

  /**
   * Retrive all valdiations of any elements
   */
  public function getAllValidators()
  {
    return $this->validators;
  }

  /**
   * Retrive all validators of element
   */
  public function getFieldValidators($id = null)
  {
    if (\is_null($id) || !isset($this->validators[$id])) {
      throw new \Exception('ID not defined');
      return false;
    }

    return $this->validators[$id];
  }

  /**
   * Return array pair key for id element and value for message error
   */
  public function getMessagesErros()
  {
    return $this->messagesErrors;
  }

  public function getFieldErrorMessage($id = null)
  {
    if (is_null($id)) return null;

    return isset($this->messagesErrors[$id]) ? $this->messagesErrors[$id] : null;
  }

  public function getFieldDecorator($id, $after = false)
  {

    $status = $this->getFieldStatus($id);

    if ($after) {
      return sprintf((isset($this->decorators[$id][$status]) ? $this->decorators[$id][$status]['after'] : $this->decoratorAfter[$status]),  $this->getFieldErrorMessage($id));
    }

    return sprintf((isset($this->decorators[$id][$status]) ? $this->decorators[$id][$status]['before'] : $this->decoratorBefore[$status]), $this->getCSSClass($id));
  }

  /**
   * Render all fields
   */
  public function renderFields()
  {
    foreach ($this->getFields() as $id => $field) {
      $field = $this->getField($id);

      if (is_bool($field)) {
        continue;
      }

      list($attrs, $field) = $field;

      echo $this->getFieldDecorator($id);

      if (isset($attrs['label'])) {
        $this->renderLabel($attrs['label'], $id);
        unset($attrs['label']);
      }

      if (isset($this->fieldValidationCSS[$id]) && isset($attrs['class'])) {
        $attrs['class'] = $attrs['class'] . ' ' . $this->fieldValidationCSS[$id];
      }

      echo $field->render($attrs);

      echo $this->getFieldDecorator($id, true);
    }
  }

  public function renderLabel($label, $id)
  {
    echo '<label for="' . $id . '" class="control-label" >' . $label . '</label>';
  }

  /**
   * Render a field
   */
  public function renderField($id = null)
  {
    //Update fieldname
    if (!$id) {
      return false;
    }

    list($attrs, $field) = $this->getField($id);

    if (isset($this->fieldValidationCSS[$id]) && isset($attrs['class'])) {
      $attrs['class'] = $attrs['class'] . ' ' . $this->fieldValidationCSS[$id];
    }

    echo $field->render($attrs);
  }

  /**
   * Render form
   */
  public function render()
  {
    $this->renderTagForm();
    $this->renderFields();
    $this->renderTagForm(true);
  }

  public function renderTagForm($closeTag = false)
  {
    if ($closeTag) {
      echo "</form>\n";
      return;
    }
    echo "<form {$this->placeAttrs($this->formAttrs)}>\n";
  }

  /**     
   * @return bool
   */
  public function hasPost()
  {
    $method = isset($this->formAttrs['method']) ? strtolower($this->formAttrs['method']) : 'get';

    switch ($method) {
      case 'post':
        $this->setData($_POST);
        break;
      case 'get':
        $this->setData($_GET);
        break;
      default:
        $this->setData($_REQUEST);
        break;
    }

    if (!empty($this->getData())) {
      return true;
    }

    return false;
  }

  /**
   * Valid all field
   */
  public function isValid()
  {
    $data = $this->getData();
    $fields = $this->getFields();

    if (empty($data)) return false;

    foreach ($this->getAllValidators() as $id => $validators) {

      list($attrs, $field) = $fields[$id];

      if (
        $field instanceof \Form\Fields\_Empty ||
        (isset($attrs['type']) && $attrs['type'] == 'submit')
      ) continue;

      $this->setCSSClass($id, 'valid');
      $this->setFieldStatus($id, 'valid');

      foreach ($validators as $validator) {
        $value = isset($data[$id]) ? $data[$id] : null;

        if (isset($this->messagesErrors[$id])) continue;

        if (false == $validator->validation($value, $this, $id)) {
          $this->messagesErrors[$id] = $validator->getMessage();

          //Set invalid CSS Class
          $this->setCSSClass($id, 'invalid');
          $this->setFieldStatus($id, 'invalid');
        }
      }
    }

    //IF is empty return true
    if (empty($this->messagesErrors)) {
      return true;
    }
    return false;
  }

  /**
   * Fill in the fields with other values, not the requisition
   * @param array $data fill external values
   */
  public function populate($data = array())
  {
    $data = array_merge($this->getData(), $data);

    foreach ($this->fields as $id => $fieldArr) {
      list($attrs, $field) = $fieldArr;

      if (
        $field instanceof \Form\Fields\_Empty
        || ($field instanceof \Form\Fields\Input && in_array(strtolower($attrs['type']), ['file', 'submit']))
      ) {
        continue;
      }

      /**
       * @todo alterar
       */
      if ($field instanceof \Form\Fields\Input && in_array($attrs['type'], ['checkbox', 'radio'])) {
        $checkedValue = $data[$id];

        if ($attrs['value'] == $checkedValue) {
          $attrs['checked'] = 'checked';
        } else {
          unset($attrs['checked']);
        }

        $this->fields[$id][0] = $attrs;

        continue;
      }

      $attrs['value'] = $data[$id];
      $this->fields[$id][0] = $attrs;
    }

    return $this;
  }

  /**
   * Clear Data and CSS validations class of all fields
   * @return $this
   */
  public function clear()
  {
    $this->data = array();
    $this->fieldValidationCSS = array();

    return $this;
  }

  /**
   * Remove all fields from form
   * @return $this
   */
  public function removeFields()
  {
    $this->fields = array();

    return $this;
  }
}
