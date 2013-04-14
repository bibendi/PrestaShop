{*
* robokassa wayting payment page.
*
* @author 0RS <admin@prestalab.ru>
* @link http://prestalab.ru/
* @copyright Copyright &copy; 2009-2012 PrestaLab.Ru
* @license    http://www.opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @version 0.2
*}
{capture name=path}{l s='Waiting for payment' mod='robokassa'}{/capture}
{include file="$tpl_dir./breadcrumb.tpl"}

<h2>{l s='Waiting for payment' mod='robokassa'}</h2>

{assign var='current_step' value='payment'}
{include file="$tpl_dir./order-steps.tpl"}

<h3>{l s='Waiting for payment' mod='robokassa'}</h3>

<p>{l s='At the moment of payment is not received. Once it is received you will be able to see your order in your account' mod='robokassa'}</p>
<p>{l s='If you do not receive notification of payment please send his number' mod='robokassa'} {$ordernumber} <a href="{$link->getPageLink('contact-form.php', true)}">{l s='to support services' mod='robokassa'}</a></p>