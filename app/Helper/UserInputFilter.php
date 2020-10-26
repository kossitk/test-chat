<?php

namespace App\Helper;

/**
 *  Validates arrays or properties by setting up simple arrays.
 *  Note that some of the regexes are for dutch input!
 *  Example:
 *
 *  $validations = array('name' => 'anything','email' => 'email','alias' => 'anything','pwd'=>'anything','gsm' => 'phone','birthdate' => 'date');
 *  $required = array('name', 'email', 'alias', 'pwd');
 *  $sanitize = array('alias');
 *
 *  $validator = new FormValidator($validations, $required, $sanitize);
 *
 *  if($validator->validate($_POST))
 *  {
 *      $_POST = $validator->sanitize($_POST);
 *      // now do your saving, $_POST has been sanitized.
 *      die($validator->getScript()."<script type='text/javascript'>alert('saved changes');</script>");
 *  }
 *  else
 *  {
 *      die($validator->getScript());
 *  }
 *
 * To validate just one element:
 * $validated = new FormValidator()->validate('blah@bla.', 'email');
 *
 * To sanitize just one element:
 * $sanitized = new FormValidator()->sanitize('<b>blah</b>', 'string');
 */
class UserInputFilter
{
    public static $regexes = Array(
        'date' => "^[0-9]{1,2}[-/][0-9]{1,2}[-/][0-9]{4}\$",
        'amount' => "^[-]?[0-9]+\$",
        'number' => "^[-]?[0-9,]+\$",
        'alfanum' => "^[0-9a-zA-Z ,.-_\\s\?\!]+\$",
        'username' => "^[0-9a-zA-Z]{3,}\$",
        'uuid4' => "^[a-f0-9]{8}-[a-f0-9]{4}-4[a-f0-9]{3}-[89aAbB][a-f0-9]{3}-[a-f0-9]{12}$",
        'not_empty' => "[a-z0-9A-Z]+",
        'words' => "^[A-Za-z]+[A-Za-z \\s]*\$",
        'anything' => "^[\d\D]{1,}\$",
        'password' => "^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d\w\D]{8,}$",
    );
    private $validations;
    private $sanitations;
    private $mandatories;
    private $errors;
    private $corrects;
    private $fields;


    public function __construct($validations=array(), $mandatories = array(), $sanitations = array())
    {
        $this->validations = $validations;
        $this->sanitations = $sanitations;
        $this->mandatories = $mandatories;
        $this->errors = array();
        $this->corrects = array();
    }

    /**
     * Validates an array of items (if needed) and returns true or false
     *
     */
    public function validate($items)
    {
        $this->fields = $items;
        $haveFailures = false;
        foreach($items as $key=>$val)
        {
            if((strlen($val) == 0 || array_search($key, $this->validations) === false) && array_search($key, $this->mandatories) === false)
            {
                $this->corrects[] = $key;
                continue;
            }

            if ($this->validations[$key] === 'confirmation'){
                $result = $val === @$items[substr($key, 8)]; // start after 'confirm-'
            }
            else{
                $result = self::validateItem($val, $this->validations[$key]);
            }
            if($result === false) {
                $haveFailures = true;
                $this->addError($key, $this->validations[$key]);
            }
            else
            {
                $this->corrects[] = $key;
            }
        }

        foreach ($this->mandatories as $key){
            if (!isset($items[$key])){
                $haveFailures = true;
                $this->addError($key, 'required');
            }
        }

        return(!$haveFailures);
    }


    /**
     *
     * Sanitizes an array of items according to the $this->sanitations
     * sanitations will be standard of type string, but can also be specified.
     * For ease of use, this syntax is accepted:
     * $sanitations = array('fieldname', 'otherfieldname'=>'float');
     */
    public function sanitize($items)
    {
        foreach ($items as $key => $val) {
            if (array_search($key, $this->sanitations) === false && !array_key_exists($key, $this->sanitations)) {
                continue;
            }
            $items[$key] = self::sanitizeItem($val, $this->sanitations[$key]);
        }
        return ($items);
    }


    /**
     *
     * Adds an error to the errors array.
     */
    private function addError($field, $type='string')
    {
        $this->errors[$field] = $type;
    }

    /**
     *
     * Sanitize a single var according to $type.
     * Allows for static calling to allow simple sanitization
     */
    public static function sanitizeItem($var, $type)
    {
        $flags = NULL;
        switch($type)
        {
            case 'url':
                $filter = FILTER_SANITIZE_URL;
                break;
            case 'int':
                $filter = FILTER_SANITIZE_NUMBER_INT;
                break;
            case 'float':
                $filter = FILTER_SANITIZE_NUMBER_FLOAT;
                $flags = FILTER_FLAG_ALLOW_FRACTION | FILTER_FLAG_ALLOW_THOUSAND;
                break;
            case 'email':
                $var = substr($var, 0, 254);
                $filter = FILTER_SANITIZE_EMAIL;
                break;
            case 'string':
            default:
                $filter = FILTER_SANITIZE_STRING;
                $flags = FILTER_FLAG_NO_ENCODE_QUOTES;
                break;

        }
        $output = filter_var($var, $filter, $flags);
        return($output);
    }

    /**
     *
     * Validates a single var according to $type.
     * Allows for static calling to allow simple validation.
     *
     */
    public static function validateItem($var, $type)
    {
        if(array_key_exists($type, self::$regexes))
        {
            $returnval =  filter_var($var, FILTER_VALIDATE_REGEXP, array("options"=> array("regexp"=>'!'.self::$regexes[$type].'!i'))) !== false;
            return($returnval);
        }
        $filter = false;
        switch($type)
        {
            case 'email':
                $var = substr($var, 0, 254);
                $filter = FILTER_VALIDATE_EMAIL;
                break;
            case 'int':
                $filter = FILTER_VALIDATE_INT;
                break;
            case 'boolean':
                $filter = FILTER_VALIDATE_BOOLEAN;
                break;
            case 'ip':
                $filter = FILTER_VALIDATE_IP;
                break;
            case 'url':
                $filter = FILTER_VALIDATE_URL;
                break;
        }
        return ($filter === false) ? false : filter_var($var, $filter) !== false ? true : false;
    }

    /**
     * @return array
     */
    public function getSanitations(): array
    {
        return $this->sanitations;
    }

    /**
     * @return array
     */
    public function getCorrects(): array
    {
        return $this->corrects;
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }


}