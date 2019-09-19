<?php

/**
 * Class for create Form
 */

namespace Form;

use Traits\HelperHTML;

final class Form
{
  use HelperHTML;

  private $fields = array();
  private $messagesErrors = array();
  private $formAttrs = array();
  private $validators = array();
  private $data = array();
  private $currentFieldID = '';
  private $fieldCSSClass = array('valid' => 'is-valid', 'invalid' => 'is-invalid', 'initial' => '');
  private $fieldValidationCSS = array();
  private $decorators = array();
  private $decoratorBefore = '<p>';
  private $decoratorAfter = '<span class="error" >%s</span></p>';

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

  /**
   * Set decorator for any inputs did you replace use method setFieldDecorator
   * @return $this
   */
  public function setFieldsDecorator($before = '', $after = '')
  {
    $this->decoratorBefore = $before;
    $this->decoratorAfter = $after;

    return $this;
  }

  /**
   * Set decorator for input
   * @return $this
   */
  public function setFieldDecorator($before = '', $after = '', $id = null)
  {
    $id = $id ?? $this->currentFieldID;

    $this->decorators[$id]['before'] = $before;
    $this->decorators[$id]['after'] = $after;

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

    if (!$field instanceof \Classes\Field) {
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
   * @param $validator
   * @param string $id use if replace current fieldname
   * @return $this 
   */
  public function setFieldValidator(\Classes\Validator $validator, $id = null)
  {

    if (!$validator instanceof \Classes\Validator) {
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

      // Prevent add \Fields\_Empty Decorator into data
      // and verify if exist the field into request
      if (!$field instanceof \Fields\_Empty && isset($data[$id])) {
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
    if (\is_null($id)) {
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
    if ($after) {
      return sprintf((isset($this->decorators[$id]) ? $this->decorators[$id]['after'] : $this->decoratorAfter),  $this->getFieldErrorMessage($id));
    }

    return sprintf((isset($this->decorators[$id]) ? $this->decorators[$id]['before'] : $this->decoratorBefore), $this->getCSSClass($id));
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
    echo '<form ' . $this->placeAttrs($this->formAttrs) . '>';

    $this->renderFields();

    echo '</form>';
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
      case 'request':
        $this->setData($_REQUEST);
        break;
      case 'get':
      default:
        $this->setData($_GET);
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

      if ($field instanceof \Fields\_Empty or (isset($attrs['type']) == 'submit' && $attrs['type'] == 'submit')) {
        continue;
      }

      foreach ($validators as $validator) {
        $value = isset($data[$id]) ? $data[$id] : null;

        if (false == $validator->validation($value, $this, $id) && false == isset($this->messagesErrors[$id])) {
          $this->messagesErrors[$id] = $validator->getMessage();

          //Set invalid CSS Class
          $this->setCSSClass($id, 'invalid');
        }
      }

      if (!isset($this->messagesErrors[$id])) {
        //Set valid CSS Class
        $this->setCSSClass($id, 'valid');
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
        $field instanceof \Fields\_Empty
        or ($field instanceof \Fields\Input && in_array(strtolower($attrs['type']), ['file', 'submit']))
      ) {
        continue;
      }

      /**
       * @todo alterar
       */
      if ($field instanceof \Fields\Input && in_array($attrs['type'], ['checkbox', 'radio'])) {
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
