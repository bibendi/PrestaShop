{*
* robokassa success payment page with download.
*
* @author 0RS <admin@prestalab.ru>
* @link http://prestalab.ru/
* @copyright Copyright &copy; 2009-2012 PrestaLab.Ru
* @license    http://www.opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @version 0.2
*}

<p>{l s='Payment is successfully received' mod='robokassa'}</p>
<h2>{l s='List of paid products' mod='robokassa'}</h2>

<ul>
{foreach from=$products item=product}
	<li>{if $product.download_hash}
		<a href="{$base_dir}get-file.php?key={$product.filename|escape:'htmlall':'UTF-8'}-{$product.download_hash|escape:'htmlall':'UTF-8'}">
			<img src="{$img_dir}icon/download_product.gif" class="icon" alt="" />
		</a>
		<a href="{$base_dir}get-file.php?key={$product.filename|escape:'htmlall':'UTF-8'}-{$product.download_hash|escape:'htmlall':'UTF-8'}">
			{l s='Download' mod='robokassa'} {$product.product_name|escape:'htmlall':'UTF-8'}
		</a>
		{else}
		{$product.product_name|escape:'htmlall':'UTF-8'}
	{/if}
	</li>
{/foreach}
</ul>
