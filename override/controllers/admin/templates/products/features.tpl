{*
* 2007-2012 PrestaShop
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
*  @copyright  2007-2012 PrestaShop SA
*  @version    1.5.3.1
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{if isset($product->id)}
	<input type="hidden" name="submitted_tabs[]" value="Features" />
	<h4>{l s='Assign features to this product:'}</h4>
	<div class="separation"></div>
				<ul>
					<li>{l s='You can specify a value for each relevant feature regarding this product, empty fields will not be displayed.'}</li>
					<li>{l s='You can either create a specific value or select among existing pre-defined values you added previously.'}</li>
				</ul>
			</td>
		</tr>
	</table>
	<br />
	<table border="0" cellpadding="0" cellspacing="0" class="table" style="width:100%;">
		<colgroup>
			<col width="300">
			<col width="">
			<col width="300">
		</colgroup>
		<tr>
			<th height="39px">{l s='Feature'}</th>
			<th>{l s='Pre-defined value'}</th>
			<th><u>{l s='or'}</u> {l s='Customized value'}</th>
		</tr>
	</table>
	{foreach from=$available_features item=available_feature}
	<table cellpadding="5" style="background-color:#fff; width: 100%;border:1px solid #ccc; border-top:none;  padding:4px 6px;">
			<colgroup>
			<col width="300">
			<col width="">
			<col width="300">
		</colgroup>
	<tr>
		<td>{$available_feature.name}</td>
		<td>
		{if sizeof($available_feature.featureValues)}
			<div style="max-width:300px;max-height:200px;margin:4px 4px;padding:2px;border:1px solid #e0d0b1;overflow: auto;text-align:left;">
			<input type="checkbox" style="display:none;" name="feature_{$available_feature.id_feature}_value[]" id="feature_{$available_feature.id_feature}_value" value="" {if $available_feature.custom}checked="checked"{/if}/>
			{foreach from=$available_feature.featureValues item=value}
				<label style="padding:2px;text-align:left;cursor:pointer;width:99%;{if in_array($value.id_feature_value, $available_feature.current_item)}background-color:#acd8fe;{/if}">
				<input type="checkbox" name="feature_{$available_feature.id_feature}_value[]" class="feature_{$available_feature.id_feature}_value" value="{$value.id_feature_value}" {if in_array($value.id_feature_value, $available_feature.current_item)}checked="checked"{/if}
				onchange="$('.custom_{$available_feature.id_feature}_').val(''); $('#feature_{$available_feature.id_feature}_value').attr('checked', false); if ($(this).attr('checked')) $(this).closest('label').css('background-color', '#acd8fe'); else $(this).closest('label').css('background-color', '#ffffff');">
				{$value.value|truncate:40}&nbsp;</label><br />
			{/foreach}
			</div>
		{else}
			<input type="hidden" name="feature_{$available_feature.id_feature}_value" value="0" />
				<span>{l s='N/A'} -
				<a href="{$link->getAdminLink('AdminFeatures')|escape:'htmlall':'UTF-8'}&amp;addfeature_value&id_feature={$available_feature.id_feature}"
				 class="confirm_leave button"><img src="../img/admin/add.gif" alt="values_first" title="{l s='Add pre-defined values first'}" />&nbsp;{l s='Add pre-defined values first'}</a>
			</span>
		{/if}
		</td>
		<td class="translatable">
		{foreach from=$languages key=k item=language}
			<div class="lang_{$language.id_lang}" style="{if $language.id_lang != $default_form_language}display:none;{/if}float: left;">
			<textarea class="custom_{$available_feature.id_feature}_" name="custom_{$available_feature.id_feature}_{$language.id_lang}" cols="40" rows="1"
				onkeyup="if (isArrowKey(event)) return; $('#feature_{$available_feature.id_feature}_value').attr('checked', true); $('.feature_{$available_feature.id_feature}_value').attr('checked', false); $('.feature_{$available_feature.id_feature}_value').closest('label').css('background-color', '#ffffff');" >{$available_feature.val[$k].value|escape:'htmlall':'UTF-8'|default:""}</textarea>
			</div>
		{/foreach}
		</td>
	</tr>
	
	{foreachelse}
		<tr><td colspan="3" style="text-align:center;">{l s='No features defined'}</td></tr>
	{/foreach}
	
	</table>
	<div class="separation"></div>
	<div>
		<a href="{$link->getAdminLink('AdminFeatures')|escape:'htmlall':'UTF-8'}&amp;addfeature" class="confirm_leave button">
			<img src="../img/admin/add.gif" alt="new_features" title="{l s='Add a new feature'}" />&nbsp;{l s='Add a new feature'}
		</a>
	</div>
{/if}
