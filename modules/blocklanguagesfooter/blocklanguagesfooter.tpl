{*
* 2007-2011 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 9140 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<!-- Block languages module -->

<div id="languages_block_top">
	<!--<ul id="first-languages">-->
	<p class="label">{l s='Language' mod='blocklanguagesfooter'}</p>
	<select onchange="javascript:location.href=this.value">
	
			{foreach from=$languages key=k item=language name="languages"}
				<option {if $language.iso_code == $lang_iso}selected='selected'{/if} value='{$link->getLanguageLink($language.id_lang)}'>
					{$language.iso_code|upper}
				</option>
			{/foreach}
		</select>
	<!--</ul>-->
</div>
<script type="text/javascript">
	$('ul#first-languages li:not(.selected_language)').css('opacity', 0.3);
	$('ul#first-languages li:not(.selected_language)').hover(function(){ldelim}
		$(this).css('opacity', 1);
	{rdelim}, function(){ldelim}
		$(this).css('opacity', 0.3);
	{rdelim});
</script>
<!-- /Block languages module -->

