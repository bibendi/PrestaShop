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

<!-- Block permanent links module HEADER -->
<ul id="header_links">
	{*
	<li id="header_link_home"><a href="{$base_dir}" title="{$shop_name|escape:'htmlall':'UTF-8'}" {if $page_name=='index'}class="active"{/if}>{l s='Home' mod='blockpermanentlinks'}</a></li>
	*}
	<li><a href="{$link->getCMSLink(4)}" title="{l s='About Us' mod='blockpermanentlinks'}"  {if $smarty.get.id_cms == '4'}class="active"{/if}>{l s='О магазине' mod='blockpermanentlinks'}</a></li>
	<li><a href="{$link->getCMSLink(3)}" title="{l s='About Us' mod='blockpermanentlinks'}"  {if $smarty.get.id_cms == '3'}class="active"{/if}>{l s='Как купить' mod='blockpermanentlinks'}</a></li>
	<li><a href="{$link->getCMSLink(1)}" title="{l s='About Us' mod='blockpermanentlinks'}"  {if $smarty.get.id_cms == '1'}class="active"{/if}>{l s='Доставка' mod='blockpermanentlinks'}</a></li>
	{*<li><a href="#" title="{l s='Help' mod='blockpermanentlinks'}"  {if $page_name=='Help'}class="active"{/if}>{l s='Help' mod='blockpermanentlinks'}</a></li>*}
	<li id="header_link_sitemap"><a href="{$link->getPageLink('sitemap')}" title="{l s='sitemap' mod='blockpermanentlinks'}" {if $page_name=='sitemap'}class="active"{/if}>{l s='sitemap' mod='blockpermanentlinks'}</a></li>
	<li id="header_link_contact"><a href="{$link->getPageLink('contact', true)}" title="{l s='contact' mod='blockpermanentlinks'}" {if $page_name=='contact'}class="active"{/if}>{l s='contact' mod='blockpermanentlinks'}</a></li>
</ul>
<!-- /Block permanent links module HEADER -->
