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

    private $decoratorOpen = array(
        'valid'     => '<p>',
        'invalid'  => '<p>',
        'initial'  => '<p>',
    );

    private $decoratorClose = array(
        'valid'     => '</p>',
        'invalid'  => '<span class="error" >%s</span></p>',
        'initial'  => '</p>',
    );

    private $fieldsStatus = array();

    private $fields = array();

    private $formAttrs = array();

    private $globalCSSValidation = array('valid' => 'is-valid', 'invalid' => 'is-invalid', 'initial' => '');

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
    public function setGlobalCSSValidation(array $css_classes)
    {
        $this->globalCSSValidation = array_merge($this->globalCSSValidation, $css_classes);

        return $this;
    }

    public function resetGlobalCSSValidation()
    {
        $this->globalCSSValidation = array('valid' => 'is-valid', 'invalid' => 'is-invalid', 'initial' => '');

        return $this;
    }

    /**
     * Set decorator for any inputs did you replace use method setFieldDecorator
     * @param string $status accepts valid, invalid and initial. use "," for multiples status
     * @return $this
     */
    public function setFieldsDecorator($open = '', $close = '', $status = 'initial')
    {

        foreach (explode(',', $status) as $s) {
            $this->decoratorOpen[$s] = $open;
            $this->decoratorClose[$s] = $close;
        }

        return $this;
    }

    public function notUseFieldDecorator($id = null)
    {
        $id = $id ?? $this->currentFieldID;

        foreach ($this->status as $status) {
            $this->decorators[$id][$status]['open'] = '';
            $this->decorators[$id][$status]['close'] = '';
        }
    }

    /**
     * Set decorator for input
     * @return $this
     */
    public function setFieldDecorator($open = '', $close = '', $status = 'initial', $id = null)
    {
        $id = $id ?? $this->currentFieldID;

        if (!in_array($status, $this->status)) {
            throw new \Exception('Incorrect value for parameter $status. Use: initial, valid or invalid');
            $status = 'initial';
        }

        $this->decorators[$id][$status]['open'] = $open;
        $this->decorators[$id][$status]['close'] = $close;

        return $this;
    }

    /**
     * Set Valid or Invalid css class for field
     * @param string $id element id
     * @param string $type : valid or invalid
     */
    public function setCSSClass($id = null, $type = '')
    {
        $class = isset($this->globalCSSValidation[$type]) ? $this->globalCSSValidation[$type] : '';
        $this->fieldValidationCSS[$id] = $class;
    }

    public function setFieldStatus($id = null, $type = 'initial')
    {
        $this->fieldsStatus[$id] = $type;
    }

    public function getCSSClass($id)
    {
        return isset($this->fieldValidationCSS[$id]) ? $this->fieldValidationCSS[$id] : '';
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


        if (!empty($this->globalCSSValidation['initial'])) {
            $attrs['class'] = isset($attrs['class']) ? $this->globalCSSValidation['initial'] . ' ' . $attrs['class'] : $this->globalCSSValidation['initial'];
        }

        if (!isset($attrs['id'])) {
            $attrs['id'] = $id;
        }

        if (!isset($attrs['name'])) {
            $attrs['name'] = $id;
        }

        $this->fields[$id] = array(
            $attrs,
            $field
        );

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
        $this->data = $data;
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

    public function getFieldDecorator($id, $close = false)
    {

        $status = $this->getFieldStatus($id);

        if ($close) {
            $render = sprintf((isset($this->decorators[$id][$status]) ? $this->decorators[$id][$status]['close'] : $this->decoratorClose[$status]),  $this->getFieldErrorMessage($id));
        } else {
            $render = sprintf((isset($this->decorators[$id][$status]) ? $this->decorators[$id][$status]['open'] : $this->decoratorOpen[$status]), $this->getCSSClass($id));
        }

        if ($render) {
            return "\n\t" . $render . "\n";
        }
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

            $decoratorOpen = $this->getFieldDecorator($id);

            if (isset($this->fieldValidationCSS[$id]) && isset($attrs['class'])) {
                $attrs['class'] = $attrs['class'] . ' ' . $this->fieldValidationCSS[$id];
            }

            if ($decoratorOpen) {
                echo $decoratorOpen;
                echo "\t\t";
            }

            echo $field->render($attrs);

            echo $this->getFieldDecorator($id, true);
        }
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

        echo "\t\t" . $field->render($attrs);
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
            echo "\n</form>\n";
            return;
        }
        echo "\n<form {$this->placeAttrs($this->formAttrs)}>\n";
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
            $value = $this->getValueFromData($data, $attrs['name']);

            if (
                $field instanceof \Form\Fields\_Empty
                || ($field instanceof \Form\Fields\Input && in_array(strtolower($attrs['type']), ['file', 'submit']))
            ) {
                continue;
            }

            if ($field instanceof \Form\Fields\Input and in_array($attrs['type'], array('radio', 'checkbox'))) {
                $attrs['values'] = $value;
            } else {
                $attrs['value'] = $value;
            }

            $this->fields[$id][0] = $attrs;
        }

        return $this;
    }


    protected function getValueFromData($data, $name)
    {
        if (is_string($name)) {
            $name = preg_replace('/\[\]$/', '', $name);
            parse_str($name, $ref_walker);
        } else {
            $ref_walker = $name;
        }

        $return = '';
        foreach ($ref_walker as $key => $ref) {
            if (isset($data[$key]) && is_array($ref)) {
                $return = $this->getValueFromData($data[$key], $ref);
            } else if ($key) {
                $return = $data[$key];
            }
        }

        return $return;
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
