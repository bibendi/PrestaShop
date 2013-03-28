<!-- CATEGORY FEATURE PRODUCTS -->
{if isset($feature_products) AND $feature_products}
<div class="shopbybrand">
		<ul>
		{foreach from=$feature_products item=product name=feature_products}
			<li class="{if $smarty.foreach.feature_products.first}first_item{elseif $smarty.foreach.feature_products.last}last_item{else}item{/if}">
			
			<!--image product-->
				<p class="product_img_link"><a href="{$product.link}" title="{$product.name|escape:html:'UTF-8'}" class="product_image"><img src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')}" alt="{$product.name|escape:html:'UTF-8'}" /></a></p>
			<!--name product-->
				<h3><a href="{$product.link}" title="{$product.name|escape:html:'UTF-8'}">	{$product.name|escape:'htmlall':'UTF-8'} </a></h3>
			<!--description short-->
				<p>{$product.description_short|strip_tags|truncate:90:''}</p>
			<!--logo manufacture-->
				<p class="manufacture">{if $product.id_manufacturer && file_exists($ps_manu_img_dir|cat:$product.id_manufacturer|cat:'.jpg')}<a href="{$link->getmanufacturerLink($product.id_manufacturer, $product.link_rewrite)}" title="{$product.manufacturer_name}"><img src="{$img_manu_dir}{$product.id_manufacturer|escape:'htmlall':'UTF-8'}.jpg" alt="{$product.name|escape:'htmlall':'UTF-8'}" /></a>{/if}</p>
			<!--shop now-->
			<a href="{$product.link}" title="{$product.name|escape:html:'UTF-8'}" class="button"><span>Купить</span></a>
			
				
			</li>
		{/foreach}
		</ul>
</div>
{/if}
<!-- /CATEGORY FEATURE PRODUCTS -->