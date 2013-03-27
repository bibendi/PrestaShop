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
*  @version  Release: $Revision: 11823 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<script type="text/javascript">
	$(document).ready(function() {
		$("#bestsellerpro").multipleElementsCycle({
			prev: '#cprev',
			next: '#cnext',
			container: '#block_content',
			start: 0,
			show: 4,
			scrollCount: 4
		});
	});
</script>
<!-- MODULE Home Block best sellers -->
<div id="bestsellerpro">
<div id="best-sellers_block_center" class="block products_block">
	<h4>{l s='Top sellers' mod='buddietopseller'}</h4>
	<div class="nav">
		<a href="#" id="cprev" class="prev">prev</a>
		<a href="#" id="cnext" class="next">next</a>
	</div>
	{if isset($best_sellers) AND $best_sellers}
	
		<div class="block_content" id="block_content">
			<ul class="product-list">
			{foreach from=$best_sellers item=product name=myLoop}
				<li style="border-bottom:0" class="ajax_block_product {if $smarty.foreach.myLoop.first}first_item{elseif $smarty.foreach.myLoop.last}last_item{else}item{/if}">
					<h3><a href="{$product.link}" title="{$product.name|truncate:32:'...'|escape:'htmlall':'UTF-8'}">{$product.name|escape:'htmlall':'UTF-8'}</a></h3>
					<a href="{$product.link}" title="{$product.name|escape:html:'UTF-8'}" class="product_image"><img src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')}" alt="{$product.name|escape:html:'UTF-8'}" /></a>
					<div class="product_desc">{$product.description_short|strip_tags|truncate:60:'...'}</div>
					<div>
						{if !$PS_CATALOG_MODE}<p class="price_container"><span class="price">{$product.price}</span></p>{else}<div style="height:21px;"></div>{/if}			
					</div>
					<div>
					{if ($product.id_product_attribute == 0 OR (isset($add_prod_display) AND ($add_prod_display == 1))) AND $product.available_for_order AND !isset($restricted_country_mode) AND !$PS_CATALOG_MODE}
						{if ($product.quantity > 0 OR $product.allow_oosp) AND $product.customizable != 2}
						<a class="exclusive ajax_add_to_cart_button" rel="ajax_id_product_{$product.id_product}" href="{$link->getPageLink('cart.php')}?qty=1&amp;id_product={$product.id_product}&amp;token={$static_token}&amp;add" title="{l s='Add to cart' mod='buddietopseller'}"><span class="addtocard">{l s='Add to cart' mod='buddietopseller'}</span></a>
						{else}
						<span class="exclusive"><span class="addtocard">{l s='Out of stock' mod='buddietopseller'}</span></span>
						
						{/if}
					{else}
						<div style="height:23px;"></div>
					{/if}
					</div>
					
				</li>
			{/foreach}
			</ul>
			
		</div>
	{else}
		<p>{l s='No best sellers at this time' mod='blockbestsellers'}</p>
	{/if}
	<br class="clear"/>
</div>
</div>
<!-- /MODULE Home Block best sellers -->
