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
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{if isset($products)}
	<!-- Products list -->
	<ul id="product_list" class="clear">
	{foreach from=$products item=product name=products}
        {assign var='groups' value=$productsGroups[$product.id_product|intval]}
        {assign var='groups_count' value=$groups|@count}

		<li class="ajax_block_product {if $smarty.foreach.products.first}first_item{elseif $smarty.foreach.products.last}last_item{/if} {if $smarty.foreach.products.index % 2}alternate_item{else}item{/if} clearfix">
        <form id="buy_block"  action="{$link->getPageLink('cart')}" method="post">
            <p class="hidden">
                <input type="hidden" name="token" value="{$static_token}" />
                <input type="hidden" name="id_product" value="{$product.id_product|intval}" id="product_page_product_id" />
                <input type="hidden" name="add" value="1" />
                <input type="hidden" name="qty" value="1" />
            </p>
			<div class="center_block">
				<h3><a href="{$product.link|escape:'htmlall':'UTF-8'}" title="{$product.name|escape:'htmlall':'UTF-8'}">{$product.name|escape:'htmlall':'UTF-8'|truncate:35:'...'}</a></h3>
				<a href="{$product.link|escape:'htmlall':'UTF-8'}" class="product_img_link" title="{$product.name|escape:'htmlall':'UTF-8'}">
					<img src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')}" alt="{$product.legend|escape:'htmlall':'UTF-8'}"/>
				</a>
				<p class="product_desc">{$product.description_short|strip_tags:'UTF-8'|truncate:90:'...'}</p>

                {if $groups_count == 0}
                <div>
                    {if $product.specific_prices}
                        {assign var='specific_prices' value=$product.specific_prices}
                        {if $specific_prices.reduction_type == 'percentage' && ($specific_prices.from == $specific_prices.to OR ($smarty.now|date_format:'%Y-%m-%d %H:%M:%S' <= $specific_prices.to && $smarty.now|date_format:'%Y-%m-%d %H:%M:%S' >= $specific_prices.from))}
                            <span class="reduction">-{$specific_prices.reduction*100|floatval}%</span>
                        {/if}
                    {/if}

                    {if (!$PS_CATALOG_MODE AND ((isset($product.show_price) && $product.show_price) || (isset($product.available_for_order) && $product.available_for_order)))}
                        {if isset($product.show_price) && $product.show_price && !isset($restricted_country_mode)}<span class="price" style="display: inline;">{if !$priceDisplay}{convertPrice price=$product.price}{else}{convertPrice price=$product.price_tax_exc}{/if}</span>{/if}
                    {/if}
				</div>
                {/if}

                <div class="footer_block">
				{if ($product.id_product_attribute == 0 || (isset($add_prod_display) && ($add_prod_display == 1))) && $product.available_for_order && !isset($restricted_country_mode) && $product.minimal_quantity <= 1 && $product.customizable != 2 && !$PS_CATALOG_MODE}
					{*if ($product.allow_oosp || $product.quantity > 0)*}
					{if (1 == 1)}

                        {if !empty($groups)}
                            <div id="attributes">
                                {foreach from=$groups key=id_attribute_group item=group}
                                    {if $group.attributes|@count}
                                        {if $groups_count > 1}
                                            <label for="id_product_attribute">{$group.name|escape:'htmlall':'UTF-8'} :</label>
                                        {/if}

                                        {assign var='groupName' value='group_'|cat:$id_attribute_group}
                                        {if ($group.group_type == 'select')}
                                            <select name="id_product_attribute">
                                                {foreach from=$group.attributes key=id_attribute item=group_attribute}

                                                    {foreach from=$productsAttributesCombinations[$product.id_product|intval] key=k item=v}
                                                        {if $v.list == $id_attribute|strval}
                                                            {assign var='id_product_attribute' value=$k}
                                                            {break}
                                                        {/if}
                                                    {/foreach}

                                                    <option value="{$id_product_attribute}"{if (isset($smarty.get.$groupName) && $smarty.get.$groupName|intval == $id_attribute) || $group.default == $id_attribute} selected="selected"{/if} title="{$group_attribute|escape:'htmlall':'UTF-8'}" {if !$group.attributes_quantity[$id_attribute]}disabled{/if}>
                                                        {$group_attribute|escape:'htmlall':'UTF-8'}
                                                    </option>
                                                {/foreach}
                                            </select>
                                        {elseif ($group.group_type == 'radio')}
                                            {foreach from=$group.attributes key=id_attribute item=group_attribute}

                                                {foreach from=$productsCombinations[$product.id_product|intval] key=k item=v}
                                                    {if $v.list == "'"|cat:$id_attribute|cat:"'"}
                                                        {assign var='id_product_attribute' value=$k}
                                                        {assign var='id_product_attribute_price' value=$v.price}
                                                        {break}
                                                    {/if}
                                                {/foreach}

                                                <input type="radio" class="attribute_radio" name="id_product_attribute" value="{$id_product_attribute}" {if (isset($smarty.get.$groupName) && $smarty.get.$groupName|intval == $id_attribute) || $group.default == $id_attribute} checked="checked"{/if} {if !$group.attributes_quantity[$id_attribute]}disabled{/if}/>
                                                {$group_attribute|escape:'htmlall':'UTF-8'}
                                                <span class="price" style="display: inline;">{convertPrice price=$id_product_attribute_price}</span>
                                                <br/>
                                            {/foreach}
                                        {/if}
                                    {/if}
                                {/foreach}
                            </div>
                        {/if}

                        <p class="button buttons_bottom_block">
                            <input type="submit" name="Submit" value="{l s='Add to cart'}" class="exclusive" />
                        </p>


                        {* if isset($static_token)}
							<a class="button ajax_add_to_cart_button exclusive" rel="ajax_id_product_{$product.id_product|intval}" href="{$link->getPageLink('cart',false, NULL, "add&amp;id_product={$product.id_product|intval}&amp;token={$static_token}", false)}" title="{l s='Add to cart'}"><span>{l s='Add to cart'}</span></a>
						{else}
							<a class="button ajax_add_to_cart_button exclusive" rel="ajax_id_product_{$product.id_product|intval}" href="{$link->getPageLink('cart',false, NULL, "add&amp;id_product={$product.id_product|intval}", false)} title="{l s='Add to cart'}"><span>{l s='Add to cart'}</span></a>
						{/if *}

					{else}
						<span class="button"><span class="addtocard">{l s='Out of stock'}</span></span>
					{/if}
				{/if}
				{if isset($comparator_max_item) && $comparator_max_item}
					<p class="compare">
						<input type="checkbox" class="comparator" id="comparator_item_{$product.id_product}" value="comparator_item_{$product.id_product}" {if isset($compareProducts) && in_array($product.id_product, $compareProducts)}checked="checked"{/if} /> 
						<label for="comparator_item_{$product.id_product}">{l s='Select to compare'}</label>
					</p>
				{/if}
                </div>
			</div>
        </form>
		</li>
	{/foreach}
	</ul>
	<!-- /Products list -->
{/if}
