<?php
include_once(dirname(__FILE__).'/StaticBlockClass.php');

class CsStaticBlocks extends Module
{
	protected $error = false;
	private $_html;
	private $myHook = array('displaytop','displayleftColumn','displayrightColumn','displayfooter','displayhome', 'mytop', 'myright');
	
	public function __construct()
	{
	 	$this->name = 'csstaticblocks';
	 	$this->tab = 'MyBlocks';
	 	$this->version = '1.0';
		$this->author = 'Codespot';

	 	parent::__construct();

		$this->displayName = $this->l('Static block');
		$this->description = $this->l('Adds static blocks with free content');
		$this->confirmUninstall = $this->l('Are you sure that you want to delete your static blocks?');
	
	}
	public function init_data()
	{
		$content_block1 = '<div class="top_header_banner">
		<ul>
		<li><a href="#"><img src="{static_block_url}themes/buddie/img/cms/b1.png" alt="" width="225" height="195" /></a></li>
		<li><a href="#"><img src="{static_block_url}themes/buddie/img/cms/b2.png" alt="" width="225" height="195" /></a></li>
		<li><a href="#"><img src="{static_block_url}themes/buddie/img/cms/b3.png" alt="" width="225" height="195" /></a></li>
		<li class="last"><a href="#"><img src="{static_block_url}themes/buddie/img/cms/b4.png" alt="" width="225" height="195" /></a></li>
		</ul>
		</div>';
		$content_block1_fr='<div class="top_header_banner">
		<ul>
		<li><a href="#"><img src="{static_block_url}themes/buddie/img/cms/b1.png" alt="" width="225" height="195" /></a></li>
		<li><a href="#"><img src="{static_block_url}themes/buddie/img/cms/b2.png" alt="" width="225" height="195" /></a></li>
		<li><a href="#"><img src="{static_block_url}themes/buddie/img/cms/b3.png" alt="" width="225" height="195" /></a></li>
		<li class="last"><a href="#"><img src="{static_block_url}themes/buddie/img/cms/b4.png" alt="" width="225" height="195" /></a></li>
		</ul>
		</div>';
		$content_block2 = '<div class="top_block_footer">
		<div class="left_video"><object width="560" height="315" data="http://vimeo.com/moogaloop.swf?clip_id=33219961&amp;server=vimeo.com&amp;show_title=0&amp;show_byline=1&amp;show_portrait=0&amp;color=00adef&amp;fullscreen=1&amp;autoplay=0&amp;loop=0" type="application/x-shockwave-flash"><param name="allowfullscreen" value="true" /><param name="allowscriptaccess" value="always" /><param name="src" value="http://vimeo.com/moogaloop.swf?clip_id=33219961&amp;server=vimeo.com&amp;show_title=0&amp;show_byline=1&amp;show_portrait=0&amp;color=00adef&amp;fullscreen=1&amp;autoplay=0&amp;loop=0" /></object>
		<p><a href="http://vimeo.com/33219961">Dogs in Cars</a> from <a href="http://vimeo.com/keith">keith</a> on <a href="http://vimeo.com">Vimeo</a>.</p>
		</div>
		<div class="right_sample_block">
		<h4>Sample Of Static Block</h4>
		<ul>
		<li><img src="{static_block_url}themes/buddie/img/cms/1_1.jpg" alt="" width="52" height="50" />
		<h5><a href="#">Feugiat liberos ditea pharetras</a></h5>
		<p>Ut vel tempus hendrerit curabitur adipiscing molestie uma ue meacenas nec.</p>
		</li>
		<li><img src="{static_block_url}themes/buddie/img/cms/2.jpg" alt="" width="52" height="52" />
		<h5><a href="#">Feugiat liberos ditea pharetras</a></h5>
		<p><span>Ut vel tempus hendrerit curabitur adipiscing molestie uma ue meacenas nec.</span></p>
		</li>
		<li class="last"><img src="{static_block_url}themes/buddie/img/cms/3.jpg" alt="" width="52" height="52" />
		<h5><a href="#">Feugiat liberos ditea pharetras</a></h5>
		<p><span>Ut vel tempus hendrerit curabitur adipiscing molestie uma ue meacenas nec.</span></p>
		</li>
		</ul>
		<p class="viewall"><a href="#">View All</a></p>
		</div>
		</div>';
		$content_block2_fr='<div class="top_block_footer">
		<div class="left_video"><object width="560" height="315" data="http://vimeo.com/moogaloop.swf?clip_id=33219961&amp;server=vimeo.com&amp;show_title=0&amp;show_byline=1&amp;show_portrait=0&amp;color=00adef&amp;fullscreen=1&amp;autoplay=0&amp;loop=0" type="application/x-shockwave-flash"><param name="allowfullscreen" value="true" /><param name="allowscriptaccess" value="always" /><param name="src" value="http://vimeo.com/moogaloop.swf?clip_id=33219961&amp;server=vimeo.com&amp;show_title=0&amp;show_byline=1&amp;show_portrait=0&amp;color=00adef&amp;fullscreen=1&amp;autoplay=0&amp;loop=0" /></object>
		<p><a href="http://vimeo.com/33219961">Dogs in Cars</a> from <a href="http://vimeo.com/keith">keith</a> on <a href="http://vimeo.com">Vimeo</a>.</p>
		</div>
		<div class="right_sample_block">
		<h4>Exemples de bloc statique</h4>
		<ul>
		<li><img src="{static_block_url}themes/buddie/img/cms/1_1.jpg" alt="" width="52" height="50" />
		<h5><a href="#">Feugiat libéros ditea pharetras</a></h5>
		<p>Pour changer lheure ou la pression meacenas stockage des employés fictifs ou accolades.</p>
		</li>
		<li><img src="{static_block_url}themes/buddie/img/cms/2.jpg" alt="" width="52" height="52" />
		<h5><a href="#">Feugiat libéros ditea pharetras</a></h5>
		<p><span>Pour changer lheure ou la pression meacenas stockage des employés fictifs ou accolades.</span></p>
		</li>
		<li class="last"><img src="{static_block_url}themes/buddie/img/cms/3.jpg" alt="" width="52" height="52" />
		<h5><a href="#">Feugiat libéros ditea pharetras</a></h5>
		<p><span>Pour changer lheure ou la pression meacenas stockage des employés fictifs ou accolades.</span></p>
		</li>
		</ul>
		<p class="viewall"><a href="#">Voir tous</a></p>
		</div>
		</div>';
		$content_block3 = '
		<div class="main_footer_cms_2">
		<div class="cms_2 information">
		<h4>INFORMATION</h4>
		<ul>
		<li><a href="#">Loresum ipsum die</a></li>
		<li><a href="#">Loresum ipsum</a></li>
		<li><a href="#">Sit ament</a></li>
		<li class="last"><a href="#">Del orno sit</a></li>
		</ul>
		</div>
		<div class="cms_2 our_offers">
		<h4>OUR OFFERS</h4>
		<ul>
		<li><a href="#">Sit ament</a></li>
		<li><a href="#">Del orno sit</a></li>
		<li><a href="#">Lamentloresum del orno</a></li>
		<li class="last"><a href="#">Dorus conseuturis</a></li>
		</ul>
		</div>
		<div class="cms_2 store_location">
		<h4>STORE LOCATION</h4>
		<p>1234 Fake address name,<br /> Fake city name, <br /> Country 01234<br /> info@yourdomain.com</p>
		</div>
		<div class="cms_2 follow_us">
		<p><span>Follow with us on</span><a title="facebook" href="#"><img src="{static_block_url}themes/buddie/img/cms/facebook.png" alt="" width="24" height="24" /></a><a title="twitter" href="#"><img src="{static_block_url}themes/buddie/img/cms/twitter_2.png" alt="" width="24" height="24" /></a><a title="rss" href="#"><img src="{static_block_url}themes/buddie/img/cms/rss.png" alt="" width="23" height="24" /></a></p>
		<ul class="we_accept">
		<li><a title="visa" href="./cms.php?id_cms=5"> <img src="{static_block_url}themes/buddie/img/cms/logo_paiement_visa.png" alt="" width="54" height="22" /></a></li>
		<li><a title="americanexpress" href="./cms.php?id_cms=5"><img src="{static_block_url}themes/buddie/img/cms/americanexpress.png" alt="" width="23" height="22" /></a></li>
		<li><a title="mastercard" href="./cms.php?id_cms=5"><img src="{static_block_url}themes/buddie/img/cms/mastercard.png" alt="" width="29" height="22" /></a></li>
		<li><a title="paypal" href="./cms.php?id_cms=5"><img src="{static_block_url}themes/buddie/img/cms/logo_paiement_paypal.png" alt="" width="53" height="22" /></a></li>
		</ul>
		</div>
		</div>';
		$content_block3_fr = '<div class="main_footer_cms_2">
		<div class="cms_2 information">
		<h4>RENSEIGNEMENTS</h4>
		<ul>
		<li><a href="#">Lorem ipsum meurent</a></li>
		<li><a href="#">Lorem ipsum meurent</a></li>
		<li><a href="#">Asseyez-ament</a></li>
		<li class="last"><a href="#">Del est un ornement</a></li>
		</ul>
		</div>
		<div class="cms_2 our_offers">
		<h4>NOS OFFRES</h4>
		<ul>
		<li><a href="#">Asseyez-ament</a></li>
		<li class="last"><a href="#">Del est un ornement</a></li>
		<li><a href="#">Lamentloresum del</a></li>
		<li class="last"><a href="#">Dorus conseuturis</a></li>
		</ul>
		</div>
		<div class="cms_2 store_location">
		<h4>ADRESSE DU MAGASIN</h4>
		<p>1234 Nom Adresse Faux,<br /> Faux nom de la ville, <br /> pays 01234<br /> info@yourdomain.com</p>
		</div>
		<div class="cms_2 follow_us">
		<p><span>Suivez avec nous sur</span><a title="facebook" href="#"><img src="{static_block_url}themes/buddie/img/cms/facebook.png" alt="" width="24" height="24" /></a><a title="twitter" href="#"><img src="{static_block_url}themes/buddie/img/cms/twitter_2.png" alt="" width="24" height="24" /></a><a title="rss" href="#"><img src="{static_block_url}themes/buddie/img/cms/rss.png" alt="" width="23" height="24" /></a></p>
		<ul class="we_accept">
		<li><a title="visa" href="./cms.php?id_cms=5"> <img src="{static_block_url}themes/buddie/img/cms/logo_paiement_visa.png" alt="" width="54" height="22" /></a></li>
		<li><a title="americanexpress" href="./cms.php?id_cms=5"><img src="{static_block_url}themes/buddie/img/cms/americanexpress.png" alt="" width="23" height="22" /></a></li>
		<li><a title="mastercard" href="./cms.php?id_cms=5"><img src="{static_block_url}themes/buddie/img/cms/mastercard.png" alt="" width="29" height="22" /></a></li>
		<li><a title="paypal" href="./cms.php?id_cms=5"><img src="{static_block_url}themes/buddie/img/cms/logo_paiement_paypal.png" alt="" width="53" height="22" /></a></li>
		</ul>
		</div>
		</div>';
		$content_block4 = '<p class="copy">©2012 Buddie Pets Store Template﻿. All rights reserved.<br /> <a title="PrestaShop Theme" href="#">PrestaShop Theme</a> by <a title="PresThemes" href="#">PresThemes</a></p>';
		$content_block4_fr = '<p class="copy">© 2012 Template Buddie magasin Animaux. Tous droits réservés.<br /> <a title="Thème PrestaShop" href="#">Thème PrestaShop</a> by <a title="PresThemes" href="#">PresThemes</a></p>';
		$id_hook_mytop = Hook::get('mytop');
		$id_hook_myright = Hook::get('myright');
		
		$id_en = Language::getIdByIso('ru');
		$id_fr = Language::getIdByIso('en');
		if(!Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'staticblock` (`id_block`, `identifier_block`, `hook`, `is_active`) VALUES ( "1", "top_image","14", "1")') OR 
		!Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'staticblock` (`id_block`, `identifier_block`, `hook`, `is_active`) VALUES ( "2", "Sample-Static-Block","21", "1")') OR
		!Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'staticblock` (`id_block`, `identifier_block`, `hook`, `is_active`) VALUES ( "3", "cms_footer_page","21", "1")') OR
		!Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'staticblock` (`id_block`, `identifier_block`, `hook`, `is_active`) VALUES ( "4", "All_right_footer","21", "1")') OR
		!Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'staticblock_lang` (`id_block`, `id_lang`, `title`, `content`) VALUES ( "1",  \''.$id_en.'\',"Top Image", \''.$content_block1.'\')') OR
		!Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'staticblock_lang` (`id_block`, `id_lang`, `title`, `content`) VALUES ( "1",  \''.$id_fr.'\',"Top Image", \''.$content_block1_fr.'\')') OR
		!Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'staticblock_lang` (`id_block`, `id_lang`, `title`, `content`) VALUES ( "2",  \''.$id_en.'\',"Sample of Static Block", \''.$content_block2.'\')') OR
		!Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'staticblock_lang` (`id_block`, `id_lang`, `title`, `content`) VALUES ( "2",  \''.$id_fr.'\',"Sample of Static Block", \''.$content_block2_fr.'\')') OR
		!Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'staticblock_lang` (`id_block`, `id_lang`, `title`, `content`) VALUES ( "3",  \''.$id_en.'\',"CMS footer page", \''.$content_block3.'\')') OR
		!Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'staticblock_lang` (`id_block`, `id_lang`, `title`, `content`) VALUES ( "3", \''.$id_fr.'\',"CMS footer page", \''.$content_block3_fr.'\')') OR
		!Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'staticblock_lang` (`id_block`, `id_lang`, `title`, `content`) VALUES ( "4",  \''.$id_en.'\',"All rights Footer", \''.$content_block4.'\')') OR
		!Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'staticblock_lang` (`id_block`, `id_lang`, `title`, `content`) VALUES ( "4", \''.$id_fr.'\',"All rights Footer", \''.$content_block4_fr.'\')'))
			return false;
		return true;
		
	}
	
	
	public function install()
	{
	 	if (parent::install() == false OR !$this->registerHook('header'))
	 		return false;
		foreach ($this->myHook AS $hook){
			if ( !$this->registerHook($hook))
				return false;
		}
	 	if (!Db::getInstance()->Execute('CREATE TABLE '._DB_PREFIX_.'staticblock (`id_block` int(10) unsigned NOT NULL AUTO_INCREMENT, `identifier_block` varchar(255) NOT NULL DEFAULT \'\', `hook` int(10) unsigned, `is_active` tinyint(1) NOT NULL DEFAULT \'1\', PRIMARY KEY (`id_block`),UNIQUE KEY `identifier_block` (`identifier_block`)) ENGINE=InnoDB default CHARSET=utf8'))
	 		return false;
		if (!Db::getInstance()->Execute('CREATE TABLE '._DB_PREFIX_.'staticblock_lang (`id_block` int(10) unsigned NOT NULL, `id_lang` int(10) unsigned NOT NULL, `title` varchar(255) NOT NULL DEFAULT \'\', `content` mediumtext, UNIQUE KEY `staticblock_lang_index` (`id_block`,`id_lang`)) ENGINE=InnoDB default CHARSET=utf8'))
	 		return false;
		$this->init_data();
	 	return true;
	}
	
	public function uninstall()
	{
	 	if (parent::uninstall() == false)
	 		return false;
	 	if (!Db::getInstance()->Execute('DROP TABLE '._DB_PREFIX_.'staticblock') OR !Db::getInstance()->Execute('DROP TABLE '._DB_PREFIX_.'staticblock_lang'))
	 		return false;
	 	return true;
	}
	
	private function _displayHelp()
	{
		$this->_html .= '
		<br/>
	 	<fieldset>
			<legend><img src="'.$this->_path.'logo.gif" alt="" title="" /> '.$this->l('Static block Helper').'</legend>
			<div>This module customize static contents on the site. Static contents are displayed at the position of the hook : top, left, home,right, footer.</div>
		</fieldset>';
	}
	
	public function getContent()
   	{
		$this->_html = '<h2>'.$this->displayName.'</h2>';
		
		
		$this->_postProcess();
		
		if (Tools::isSubmit('addBlock'))
			$this->_displayAddForm();
		elseif (Tools::isSubmit('editBlock'))
			$this->_displayUpdateForm();
		elseif (Tools::isSubmit('changeStatusStaticblock') AND Tools::getValue('id_block'))
		{
			$stblock = new StaticBlockClass(Tools::getValue('id_block'));
			$stblock->updateStatus(Tools::getValue('status'));
			$this->_displayForm();
		}
		else
			$this->_displayForm();
		$this->_displayHelp();
		return $this->_html;
	}
	
	private function _postProcess()
	{
		global $currentIndex;
		$errors = array();
		if (Tools::isSubmit('saveBlock'))
		{
			
			$block = new StaticBlockClass(Tools::getValue('id_block'));
			$block->copyFromPost();
			
			$errors = $block->validateController();
						
			if (sizeof($errors))
			{
				$this->_html .= $this->displayError(implode('<br />', $errors));
			}
			else
			{
				Tools::getValue('id_block') ? $block->update() : $block->add();
				Tools::redirectAdmin($currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&saveBlockConfirmation');
			}
		}
		elseif (Tools::isSubmit('deleteBlock') AND Tools::getValue('id_block'))
		{
			$block = new StaticBlockClass(Tools::getValue('id_block'));
			$block->delete();
			Tools::redirectAdmin($currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&deleteBlockConfirmation');
		}
		elseif (Tools::isSubmit('saveBlockConfirmation'))
			$this->_html = $this->displayConfirmation($this->l('Static block has been saved successfully'));
		elseif (Tools::isSubmit('deleteBlockConfirmation'))
			$this->_html = $this->displayConfirmation($this->l('Static block deleted successfully'));
		
	}
	
	private function getBlocks()
	{
		$defaultLanguage = intval(Configuration::get('PS_LANG_DEFAULT'));
	 	if (!$result = Db::getInstance()->ExecuteS(
			'SELECT sb.*, sbl.`title`, sbl.`content` 
			FROM `'._DB_PREFIX_.'staticblock` sb 
			LEFT JOIN `'._DB_PREFIX_.'staticblock_lang` sbl ON (sb.`id_block` = sbl.`id_block` AND sbl.`id_lang` = '.(int)($defaultLanguage).')'))
	 		return false;
	 	return $result;
	}
	
	private function getHookTitle($id_hook)
	{
		if (!$result = Db::getInstance()->getRow('
			SELECT `name`,`title`
			FROM `'._DB_PREFIX_.'hook` 
			WHERE `id_hook` = '.(int)($id_hook)))
			return false;
		return ($result['title'] != "" ? $result['title'] : $result['name']);
	}
	
	private function _displayForm()
	{
		global $currentIndex, $cookie;
	 	$this->_html .= '
		
	 	<fieldset>
			<legend><img src="'.$this->_path.'logo.gif" alt="" title="" /> '.$this->l('List of static blocks').'</legend>
			<p><a href="'.$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&addBlock"><img src="'._PS_ADMIN_IMG_.'add.gif" alt="" /> '.$this->l('Add a new block').'</a></p><br/>
			<table width="100%" class="table" cellspacing="0" cellpadding="0">
			<thead>
			<tr class="nodrag nodrop">
				<th>'.$this->l('ID').'</th>
				<th class="center">'.$this->l('Title').'</th>
				<th class="center">'.$this->l('Identifier').'</th>
				<th class="center">'.$this->l('Hook into').'</th>
				<th class="right">'.$this->l('Active').'</th>
			</tr>
			</thead>
			<tbody>';
		$s_blocks = $this->getBlocks();
		if (is_array($s_blocks))
		{
			static $irow;
			foreach ($s_blocks as $block)
			{
				$this->_html .= '
				<tr class="'.($irow++ % 2 ? 'alt_row' : '').'">
					<td class="pointer" onclick="document.location = \''.$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&editBlock&id_block='.$block['id_block'].'\'">'.$block['id_block'].'</td>
					<td class="pointer center" onclick="document.location = \''.$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&editBlock&id_block='.$block['id_block'].'\'">'.$block['title'].'</td>
					<td class="pointer center" onclick="document.location = \''.$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&editBlock&id_block='.$block['id_block'].'\'">'.$block['identifier_block'].'</td>
					<td class="pointer center" onclick="document.location = \''.$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&editBlock&id_block='.$block['id_block'].'\'">'.(Validate::isInt($block['hook']) ? $this->getHookTitle($block['hook']) : '').'</td>
					<td class="pointer center"> <a href="'.$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&changeStatusStaticblock&id_block='.$block['id_block'].'&status='.$block['is_active'].'">'.($block['is_active'] ? '<img src="'._PS_ADMIN_IMG_.'enabled.gif" alt="Enabled" title="Enabled" />' : '<img src="'._PS_ADMIN_IMG_.'disabled.gif" alt="Disabled" title="Disabled" />').'</a> </td>
				</tr>';
			}
		}
		$this->_html .= '
			</tbody>
			</table>
		</fieldset>';
			
		
	}
	
	private function _displayAddForm()
	{
		global $currentIndex, $cookie;
	 	// Language 
	 	$defaultLanguage = (int)(Configuration::get('PS_LANG_DEFAULT'));
		$languages = Language::getLanguages(false);
		$divLangName = 'titlediv¤contentdiv';
		// TinyMCE
		$iso = Language::getIsoById((int)($cookie->id_lang));
		$isoTinyMCE = (file_exists(_PS_ROOT_DIR_.'/js/tiny_mce/langs/'.$iso.'.js') ? $iso : 'en');
		$ad = dirname($_SERVER["PHP_SELF"]);
		$this->_html .=  '
		<script type="text/javascript">	
		var iso = \''.$isoTinyMCE.'\' ;
		var pathCSS = \''._THEME_CSS_DIR_.'\' ;
		var ad = \''.$ad.'\' ;
		</script>
		<script type="text/javascript" src="'.__PS_BASE_URI__.'js/tiny_mce/tiny_mce.js"></script>
		<script type="text/javascript" src="'.__PS_BASE_URI__.'js/tinymce.inc.js"></script>
		<script type="text/javascript">id_language = Number('.$defaultLanguage.');</script>	
		<script type="text/javascript">
		$(document).ready(function(){		
			tinySetup({});});
		</script>
		';
		// Form
		$this->_html .= '
		<fieldset>
			<legend><img src="'.$this->_path.'logo.gif" alt="" title="" /> '.$this->l('New block').'</legend>
			<form method="post" action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" enctype="multipart/form-data">
				<label>'.$this->l('Title:').'</label>
				<div class="margin-form">';
					foreach ($languages as $language)
					{
						$this->_html .= '
					<div id="titlediv_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').'; float: left;">
						<input type="text" name="title_'.$language['id_lang'].'" value="'.Tools::getValue('title_'.$language['id_lang']).'" size="55" /><sup> *</sup>
					</div>';
					}
					$this->_html .= $this->displayFlags($languages, $defaultLanguage, $divLangName, 'titlediv', true);	
					$this->_html .= '
					<div class="clear"></div>
				</div>
				
				<label>'.$this->l('Identifier:').'</label>
				<div class="margin-form">
					<div id="identifierdiv" style="float: left;">
						<input type="text" name="identifier_block" value="'.Tools::getValue('identifier_block').'" size="55" /><sup> *</sup>
					</div>
					<p class="clear">'.$this->l('Identifier must be unique').'. '.$this->l('Match a-zA-Z-_0-9').'</p>
					<div class="clear"></div>
				</div>
				
				<label>'.$this->l('Hook into:').'</label>
				<div class="margin-form">
					<div id="hookdiv" style="float: left;">
						<select name="hook">
							<option value="0">'.$this->l('None').'</option>';

		foreach ($this->myHook AS $hook){
			$id_hook = Hook::getIdByName($hook);
			$this->_html .= '<option value="'.$id_hook.'"'.($id_hook == Tools::getValue('hook') ? 'selected="selected"' : '').'>'.$this->getHookTitle($id_hook).'</option>';
		}
		
		$this->_html .= '
						</select>
					</div>
					<div class="clear"></div>
				</div>
				
				<label>'.$this->l('Active:').'</label>
				<div class="margin-form">
					<div id="activediv" style="float: left;">
						<input type="radio" name="is_active" value="1"'.(Tools::getValue('is_active',1) ? 'checked="checked"' : '').' />
						<label class="t"><img src="'._PS_ADMIN_IMG_.'enabled.gif" alt="Enabled" title="Enabled" /></label>
						<input type="radio" name="is_active" value="0"'.(Tools::getValue('is_active',1) ? '' : 'checked="checked"').' />
						<label class="t"><img src="'._PS_ADMIN_IMG_.'disabled.gif" alt="Disabled" title="Disabled" /></label>
					</div>
					<div class="clear"></div>
				</div>
				
				<label>'.$this->l('Content:').'</label>
				<div class="margin-form">';									
					foreach ($languages as $language)
					{
						$this->_html .= '
					<div id="contentdiv_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').'; float: left;">
						<textarea class="rte" name="content_'.$language['id_lang'].'" id="contentInput_'.$language['id_lang'].'" cols="100" rows="20">'.Tools::getValue('content_'.$language['id_lang']).'</textarea>
					</div>';
					}
					$this->_html .= $this->displayFlags($languages, $defaultLanguage, $divLangName, 'contentdiv', true);
					$this->_html .= '
					<div class="clear"></div>
				</div>			
				<div class="margin-form">';
					$this->_html .= '<input type="submit" class="button" name="saveBlock" value="'.$this->l('Save Block').'" id="saveBlock" />
									';
					$this->_html .= '					
				</div>
				
			</form>
			<a href="'.$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'"><img src="'._PS_ADMIN_IMG_.'arrow2.gif" alt="" />'.$this->l('Back to list').'</a>
		</fieldset>';
	}
	
	private function _displayUpdateForm()
	{
		global $currentIndex, $cookie;
		if (!Tools::getValue('id_block'))
		{
			$this->_html .= '<a href="'.$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'"><img src="'._PS_ADMIN_IMG_.'arrow2.gif" alt="" />'.$this->l('Back to list').'</a>';
			return;
		}

		$block = new StaticBlockClass((int)Tools::getValue('id_block'));
	 	// Language 
	 	$defaultLanguage = (int)(Configuration::get('PS_LANG_DEFAULT'));
		$languages = Language::getLanguages(false);
		$divLangName = 'titlediv¤contentdiv';
		// TinyMCE
		$iso = Language::getIsoById((int)($cookie->id_lang));
		$isoTinyMCE = (file_exists(_PS_ROOT_DIR_.'/js/tiny_mce/langs/'.$iso.'.js') ? $iso : 'en');
		$ad = dirname($_SERVER["PHP_SELF"]);
		$this->_html .=  '
		<script type="text/javascript">	
		var iso = \''.$isoTinyMCE.'\' ;
		var pathCSS = \''._THEME_CSS_DIR_.'\' ;
		var ad = \''.$ad.'\' ;
		</script>
		<script type="text/javascript" src="'.__PS_BASE_URI__.'js/tiny_mce/tiny_mce.js"></script>
		<script type="text/javascript" src="'.__PS_BASE_URI__.'js/tinymce.inc.js"></script>
		<script type="text/javascript">id_language = Number('.$defaultLanguage.');</script>	
		<script type="text/javascript">
		$(document).ready(function(){		
			tinySetup({});});
		</script>
		';
		// Form
		$this->_html .= '
		<fieldset>
			<legend><img src="'.$this->_path.'logo.gif" alt="" title="" /> '.$this->l('Edit block').' '.$block->identifier_block.'</legend>
			<form method="post" action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" enctype="multipart/form-data">
				<input type="hidden" name="id_block" value="'.(int)$block->id_block.'" id="id_block" />
				<div class="margin-form">
					<input type="submit" class="button " name="deleteBlock" value="'.$this->l('Delete Block').'" id="deleteBlock" onclick="if (!confirm(\'Are you sure that you want to delete this static blocks?\')) return false "/>
				</div>
				<label>'.$this->l('Title:').'</label>
				<div class="margin-form">';
					foreach ($languages as $language)
					{
						$this->_html .= '
					<div id="titlediv_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').'; float: left;">
						<input type="text" name="title_'.$language['id_lang'].'" value="'.(isset($block->title[$language['id_lang']]) ? $block->title[$language['id_lang']] : '').'" size="55" /><sup> *</sup>
					</div>';
					}
					$this->_html .= $this->displayFlags($languages, $defaultLanguage, $divLangName, 'titlediv', true);	
					$this->_html .= '
					<div class="clear"></div>
				</div>
				
				<label>'.$this->l('Identifier:').'</label>
				<div class="margin-form">
					<div id="identifierdiv" style="float: left;">
						<input type="text" name="identifier_block" value="'.$block->identifier_block.'" size="55" /><sup> *</sup>
					</div>
					<p class="clear">'.$this->l('Identifier must be unique').'. '.$this->l('Match a-zA-Z-_0-9').'</p>
					<div class="clear"></div>
				</div>
				
				<label>'.$this->l('Hook into:').'</label>
				<div class="margin-form">
					<div id="hookdiv" style="float: left;">
						<select name="hook">
							<option value="0">'.$this->l('None').'</option>';
		foreach ($this->myHook AS $hook){
			$id_hook = Hook::getIdByName($hook);
			$this->_html .= '<option value="'.$id_hook.'"'.($id_hook == $block->hook ? 'selected="selected"' : '').'>'.$this->getHookTitle($id_hook).'</option>';
		}
		$this->_html .= '
						</select>
					</div>
					<div class="clear"></div>
				</div>
				
				<label>'.$this->l('Status:').'</label>
				<div class="margin-form">
					<div id="activediv" style="float: left;">
						<input type="radio" name="is_active" '.($block->is_active ? 'checked="checked"' : '').' value="1" />
						<label class="t"><img src="'._PS_ADMIN_IMG_.'enabled.gif" alt="Enabled" title="Enabled" /></label>
						<input type="radio" name="is_active" '.($block->is_active ? '' : 'checked="checked"').' value="0" />
						<label class="t"><img src="'._PS_ADMIN_IMG_.'disabled.gif" alt="Disabled" title="Disabled" /></label>
					</div>
					<div class="clear"></div>
				</div>
				
				<label>'.$this->l('Content:').'</label>
				<div class="margin-form">';									
					foreach ($languages as $language)
					{
						$this->_html .= '
					<div id="contentdiv_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').'; float: left;">
						<textarea class="rte" name="content_'.$language['id_lang'].'" id="contentInput_'.$language['id_lang'].'" cols="100" rows="20">'.(isset($block->content[$language['id_lang']]) ? $block->content[$language['id_lang']] : '').'</textarea>
					</div>';
					}
					$this->_html .= $this->displayFlags($languages, $defaultLanguage, $divLangName, 'contentdiv', true);
					$this->_html .= '
					<div class="clear"></div>
				</div>			
				<div class="margin-form">';
					$this->_html .= '<input type="submit" class="button" name="saveBlock" value="'.$this->l('Save Block').'" id="saveBlock" />';
					$this->_html .= '					
				</div>
				
			</form>
			<a href="'.$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'"><img src="'._PS_ADMIN_IMG_.'arrow2.gif" alt="" />'.$this->l('Back to list').'</a>
		</fieldset>';
	}

	public function contentById($id_block)
	{
		global $cookie;

		$staticblock = new StaticBlockClass($id_block);
		return ($staticblock->is_active ? $staticblock->content[(int)$cookie->id_lang] : '');
	}
	
	public function contentByIdentifier($identifier)
	{
		global $cookie;

		if (!$result = Db::getInstance()->getRow('
			SELECT `id_block`,`identifier_block`
			FROM `'._DB_PREFIX_.'staticblock` 
			WHERE `identifier_block` = \''.$identifier.'\''))
			return false;
		$staticblock = new StaticBlockClass($result['id_block']);
		return ($staticblock->is_active ? $staticblock->content[(int)$cookie->id_lang] : '');
	}
	
	private function getBlockInHook($hook_name)
	{
		$block_list = array();
		$id_hook = Hook::getIdByName($hook_name);
		if ($id_hook)
		{
			$results = Db::getInstance()->ExecuteS('SELECT `id_block` FROM `'._DB_PREFIX_.'staticblock` WHERE `hook` = '.(int)($id_hook));
			foreach ($results as $row)
			{
				$temp = new StaticBlockClass($row['id_block']);
				if ($temp->is_active)
					$block_list[] = $temp;
			}
		}	
		
		return $block_list;
	}
	
	public function hookDisplayTop()
	{
		global $smarty, $cookie;
		
		$block_list = $this->getBlockInHook('displaytop');
		
		$smarty->assign(array(
			'block_list' => $block_list
		));
		
		return $this->display(__FILE__, 'csstaticblocks.tpl');
	}
	
	public function hookDisplayLeftColumn()
	{
		global $smarty, $cookie;
		
		$block_list = $this->getBlockInHook('displayleftColumn');
		
		$smarty->assign(array(
			'block_list' => $block_list
		));
		
		return $this->display(__FILE__, 'csstaticblocks.tpl');
	}
	
	public function hookDisplayRightColumn()
	{
		global $smarty, $cookie;
		
		$block_list = $this->getBlockInHook('displayrightColumn');
		
		$smarty->assign(array(
			'block_list' => $block_list
		));
		
		return $this->display(__FILE__, 'csstaticblocks.tpl');
	}
	
	public function hookDisplayFooter()
	{
		global $smarty, $cookie;
		
		$block_list = $this->getBlockInHook('displayfooter');
		
		$smarty->assign(array(
			'block_list' => $block_list
		));
		
		return $this->display(__FILE__, 'csstaticblocks.tpl');
	}
	
	public function hookDisplayHome()
	{
		global $smarty, $cookie;
		
		$block_list = $this->getBlockInHook('displayhome');
		
		$smarty->assign(array(
			'block_list' => $block_list
		));
		
		return $this->display(__FILE__, 'csstaticblocks.tpl');
	}
	
	function hookHeader($params)
	{
		global $smarty;
		$smarty->assign(array(
			
			'HOOK_MY_TOP' => Hook::Exec('mytop'),
			'HOOK_MY_RIGHT' => Hook::Exec('myright')
		));
	}
	
	public function hookMyTop()
	{
		global $smarty, $cookie;
		
		$block_list = $this->getBlockInHook('mytop');
		
		$smarty->assign(array(
			'block_list' => $block_list
		));
		
		return $this->display(__FILE__, 'csstaticblocks.tpl');
	}
	
	public function hookMyRight()
	{
		global $smarty, $cookie;
		
		$block_list = $this->getBlockInHook('myright');
		
		$smarty->assign(array(
			'block_list' => $block_list
		));
		return $this->display(__FILE__, 'csstaticblocks.tpl');
	}
}
?>
