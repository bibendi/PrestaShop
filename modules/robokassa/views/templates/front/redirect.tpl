{*
* robokassa payment module redirect to paysystem template.
*
* @author 0RS <admin@prestalab.ru>
* @link http://prestalab.ru/
* @copyright Copyright &copy; 2009-2012 PrestaLab.Ru
* @license    http://www.opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @version 0.2
*}
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset=utf-8 />
		<title>Оплата</title>
	</head>
	<body>
		<form name="robokassa_form" class="prestalab_ru" action="{if $robokassa_demo}http://test.robokassa.ru/Index.aspx{else}https://merchant.roboxchange.com/Index.aspx{/if}" method="post" accept-charset="windows-1251">
			<input type="hidden" name="MrchLogin" value="{$robokassa_login}"/>
			<input type="hidden" name="OutSum" value="{$total_to_pay}"/>
			<input type="hidden" name="InvId" value="{$order_number}"/>
			<input type="hidden" name="Desc" value="{if $postvalidate}{l s='Payment for cart #' mod='robokassa'}{else}{l s='Payment for order #' mod='robokassa'}{/if} {$order_number}"/>
			<input type="hidden" name="SignatureValue" value="{$signature}"/>
			<input type="hidden" name="Email" value="{$email}"/>
			<input type="submit" value="{l s='Click here to go to the payment' mod='robokassa'}"/>
		</form>
		<script>
			<!--
			 document.robokassa_form.submit();
		 -->
		</script>
	</body>
</html>