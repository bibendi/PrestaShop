<?php

if (!defined('_PS_VERSION_'))
	exit;

class buddiecolorthemes extends Module
{
	function __construct()
	{
		$this->name = 'buddiecolorthemes';
		$this->tab = 'My_Modules';
		$this->version = 1.0;
		$this->author = 'Codespot';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('Color themes');
		$this->description = $this->l('Allows you to select color theme.');
	}

	function install()
	{
		Configuration::updateValue('COLOR_THEME', 'default');
		return (parent::install() AND $this->registerHook('top'));
	}

	public function uninstall()
	{
		Configuration::deleteByName('COLOR_THEME');
		return parent::uninstall();
	}

	function hookTop($params)
	{
		global $smarty, $cookie;
		$smarty->assign('skin', ($cookie->__get('buddie_color') ? $cookie->__get('buddie_color') : Configuration::get('COLOR_THEME')));
		return $this->display(__FILE__, 'buddiecolorthemes.tpl');
	}
	
	public function postProcess()
	{
		if (Tools::isSubmit('submitColorConf'))
		{
			Configuration::updateValue('COLOR_THEME', Tools::getValue('colortheme'));
			return $this->displayConfirmation($this->l('Settings are updated'));
		}
		return '';
	}
	
	public function getContent()
	{
		$output = $this->postProcess().'
		<form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" method="post" enctype="multipart/form-data">
			<fieldset>
				<legend>'.$this->l('Select default color theme').'</legend>';
		$output .= '
				<div class="margin-form">
					<input value="default" type="radio" name="colortheme"'.(Configuration::get('COLOR_THEME') == "default" ? 'checked="checked"' : '').' />
					<label class="t">'.$this->l('Default').'</label>
				</div>
				<div class="margin-form">
					<input value="style2" type="radio" name="colortheme"'.(Configuration::get('COLOR_THEME') == "style2" ? 'checked="checked"' : '').' />
					<label class="t">'.$this->l('Orange').'</label>
				</div>
				<div class="margin-form">
					<input value="style3" type="radio" name="colortheme"'.(Configuration::get('COLOR_THEME') == "style3" ? 'checked="checked"' : '').' />
					<label class="t">'.$this->l('Vintage').'</label>
				</div>
				<p class="center">
					<input class="button" type="submit" name="submitColorConf" value="'.$this->l('Save').'"/>
				</p>
			</fieldset>
		</form>
		';
		return $output;
	}
}

