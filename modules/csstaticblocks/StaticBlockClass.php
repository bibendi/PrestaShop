<?php

class StaticBlockClass extends ObjectModel
{
	public $id_block;
	public $title;
	public $content;
	public $identifier_block;
	public $is_active;
	public $hook;
	
	protected $tables = array ('staticblock', 'staticblock_lang');
	protected $table = 'staticblock';
	protected $identifier = 'id_block';
	
	protected $fieldsRequiredLang = array('title');
	protected $fieldsRequired = array('identifier_block');
	
	protected $fieldsValidate = array(
		'identifier_block' => 'isConfigName',
		'is_active' => 'isBool',
		'hook' => 'isUnsignedId'
	);
	protected 	$fieldsValidateLang = array(
		'title' => 'isGenericName',
		'content' => 'isString'
	);
	
	public $temp_url = "{static_block_url}";
	
	public function __construct($id = NULL, $id_lang = NULL)
	{
		parent::__construct($id, $id_lang);
		
		if ($id)
		{	
			$languages = Language::getLanguages(false);
			foreach ($languages as $language)
			{
				if (isset($this->content[(int)($language['id_lang'])]) AND !empty($this->content[(int)($language['id_lang'])]))
				{
					$temp = str_replace($this->temp_url, _PS_BASE_URL_.__PS_BASE_URI__, $this->content[(int)($language['id_lang'])]);
					$this->content[(int)($language['id_lang'])] = $temp;
				}
			}
		}
	}
	
	public function getFields()
	{
		//parent::validateFields();
		
		$fields['id_block'] = pSQL($this->id_block);
		$fields['is_active'] = pSQL($this->is_active);
		$fields['hook'] = pSQL($this->hook);
		$fields['identifier_block'] = pSQL($this->identifier_block);

		return $fields;
	}
	
	public function getTranslationsFieldsChild()
	{
		self::validateFieldsLang();

		$fieldsArray = array('title', 'content');
		$fields = array();
		$languages = Language::getLanguages(false);
		$defaultLanguage = (int)(Configuration::get('PS_LANG_DEFAULT'));
		foreach ($languages as $language)
		{
			$fields[$language['id_lang']]['id_lang'] = (int)($language['id_lang']);
			$fields[$language['id_lang']][$this->identifier] = (int)($this->id);
			foreach ($fieldsArray as $field)
			{
				if (!Validate::isTableOrIdentifier($field))
					die(Tools::displayError());
				if (isset($this->{$field}[$language['id_lang']]) AND !empty($this->{$field}[$language['id_lang']]))
					$fields[$language['id_lang']][$field] = pSQL($this->{$field}[$language['id_lang']], true);
				elseif (in_array($field, $this->fieldsRequiredLang))
				{
					if ($this->{$field} != '')
						$fields[$language['id_lang']][$field] = pSQL($this->{$field}[$defaultLanguage]);
				}
				else
					$fields[$language['id_lang']][$field] = '';
			}
		}
		return $fields;
	}
	
	public function copyFromPost()
	{
		foreach ($_POST AS $key => $value)
			if (key_exists($key, $this) AND $key != 'id_'.$this->table)
				$this->{$key} = $value;

		if (sizeof($this->fieldsValidateLang))
		{
			$languages = Language::getLanguages(false);
			foreach ($languages AS $language)
				foreach ($this->fieldsValidateLang AS $field => $validation)
					if (isset($_POST[$field.'_'.(int)($language['id_lang'])]))
					{
						$temp = str_replace(_PS_BASE_URL_.__PS_BASE_URI__, $this->temp_url, $_POST[$field.'_'.(int)($language['id_lang'])]);
						$this->{$field}[(int)($language['id_lang'])] = $temp;
					}
		}
	}
	
	public function uniqueIndentifier_block($identifier_block, $id_block = null)
	{
		$result = Db::getInstance()->getRow('
			SELECT `identifier_block`
			FROM `'._DB_PREFIX_.'staticblock` 
			WHERE `identifier_block` LIKE \''.$identifier_block.'\''.($id_block ? ' AND `id_block` != '.$id_block : ''));
		$isUnique = ($result['identifier_block']) ? false : true ;
		
		return $isUnique;
	}
	public function updateStatus($status)
	{
		if (!isset($status))
			return false;
		if($status == 0)
			$status = 1;
		else 
			$status = 0;
		$result = (Db::getInstance()->Execute('
			UPDATE `'._DB_PREFIX_.'staticblock`
			SET `is_active`='.$status.'
			WHERE `id_block`='.(int)($this->id_block)));
		return $result;
	}
	public function validateController($htmlentities = true, $copy_post = false)
	{
		$errors = array();
		
		$field = "identifier_block";
		if (Tools::getValue($field, $this->{$field}) == false AND (string)$value != '0')
			$errors[] = '<b>Identifier block</b> '.Tools::displayError('is required.');
		
		$field = "identifier_block";
		if ($value = Tools::getValue($field, $this->{$field}))
		{
			if (!Validate::isConfigName($value))
				$errors[] = '<b>Identifier block</b> '.Tools::displayError('is invalid.');
		}
		
		if ($this->identifier_block and !$this->uniqueIndentifier_block($this->identifier_block, (int)($this->id_block)))
		{
			$errors[] = '<b>Identifier </b> '.Tools::displayError('must be unique.');
		}
		
		$defaultLanguage = (int)(Configuration::get('PS_LANG_DEFAULT'));
		$field = "title";

		if (!$this->{$field} OR !sizeof($this->{$field}) OR ($this->{$field}[$defaultLanguage] !== '0' AND empty($this->{$field}[$defaultLanguage])))
		{
			$errors[] = '<b>Title</b> '.Tools::displayError('is empty for default language.');
		}
	

		$validate = new Validate();
		foreach ($this->fieldsValidateLang as $fieldArray => $method)
		{
			if (!is_array($this->{$fieldArray}))
				continue ;
			foreach ($this->{$fieldArray} as $k => $value)
				if (!method_exists($validate, $method))
					die (Tools::displayError('Validation function not found.').' '.Tools::safeOutput($method));
				elseif (!empty($value) AND !call_user_func(array('Validate', $method), $value))
				{
					$errors[] = Tools::displayError('The following field is invalid according to the validate method ').'<b>'.$method.'</b>:<br/> ('.self::displayFieldName($fieldArray, get_class($this), $htmlentities).' = '.$value.' '.Tools::displayError('for language').' '.$k;
				}
		}
		return $errors;
	}
	
}

?>
