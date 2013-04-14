{*
* robokassa payment module display in payment list template.
*
* @author 0RS <admin@prestalab.ru>
* @link http://prestalab.ru/
* @copyright Copyright &copy; 2009-2012 PrestaLab.Ru
* @license    http://www.opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @version 0.2
*}
<p class="payment_module">
	<a href="{$link->getModuleLink('robokassa', 'redirect', ['id_cart'=>$id_cart], true)}" title="{l s='RoboKassa' mod='robokassa'}" class="prestalab_ru">
		<img src="{$this_path}robokassa.png" alt="{l s='RoboKassa' mod='robokassa'}" style="float:left;" />
		<br />{l s='Payment by plastic cards, in e-currency or using mobile commerce' mod='robokassa'}
		<br style="clear:both;" />
	</a>
</p>