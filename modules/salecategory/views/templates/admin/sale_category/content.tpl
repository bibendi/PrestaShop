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
*  @version  Release: $Revision: 13573 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<h1>SaleCategory</h1>
<fieldset>
	<legend>{l s='Set reduction/sale for products of category' mod='salecategory'}</legend>
	<form method="post" action="{$link->getAdminLink('AdminSaleCategory')}">
		<label>{l s='On category:' mod='salecategory'}</label>
		<select name="categories" id="categories">
        {if isset($categories)}
            {$categories}
        {/if}
		    </select>
		    <br />
		    <input type="checkbox" name="recurse">
		  <label style="font-size:12px;font-weight:normal">{l s='Recurse subcategories' mod='salecategory'}</label>  
		    <br /><br /><br />
            <fieldset style="font-size:13px">
                <legend  >{l s='Reduction' mod='salecategory'}</legend>
                <label>{l s='Set reduction for products' mod='salecategory'}</label> 
                <input type="checkbox" name="on_reduction">		
                <br />		
                <br />		
                <label class="clear">{l s='From:' mod='salecategory'} </label>
                <div class="margin-form">
                <input type="text" size="20" id="date_from" name="date_from" value="" class="datepicker" />
                <p class="clear">{l s='Start date/time from which reduction should be applied' mod='salecategory'}<br />Format: YYYY-MM-DD HH:MM:SS</p>
                </div>
                <label>{l s='To:' mod='salecategory'} </label>
                <div class="margin-form">
                <input type="text" size="20" id="date_to" name="date_to" value="" class="datepicker" />
                <p class="clear">{l s='End date/time at which reduction is no longer valid' mod='salecategory'}<br />Format: YYYY-MM-DD HH:MM:SS</p>
                </div>
                <label>Reduction: </label>
                <div class="margin-form">
                    <input type="text" name="sp_reduction" value="0.00" size="11" />
                    <select name="sp_reduction_type">
                        <option selected="selected">---</option>
                        <option value="amount">{l s='Amount' mod='salecategory'}</option>
                        <option value="percentage">{l s='Percentage' mod='salecategory'}</option>
                    </select>
                    {l s='(if set to "amount", the tax is included)' mod='salecategory'}
                </div>
                <label>{l s='Quantity:' mod='salecategory'} </label>
                <div class="margin-form">
                    <input type="text" name="sp_from_quantity" value="1" size="11" /> {l s='(minimum quantity required to apply the discount)' mod='salecategory'}
                </div>              
            </fieldset>				    
    		    <br />	
    		    <fieldset style="font-size:13px">
    		    <legend>{l s='Sale' mod='salecategory'}</legend>
    		    <label>{l s='Set products as "on sale"' mod='salecategory'} </label> 
    		    <input type="checkbox" name="on_sale">
    		    </fieldset>
    		    <br />	
    		    <input type="submit" name="submitSetReductionSale" class="button" value="{l s='Apply' mod='salecategory'}"/>
	</form>	
</fieldset>	
			
			
			
			
<br /><br />
			<fieldset>
			<legend>{l s='Remove reduction/sale for products of category' mod='salecategory'}</legend>
			<form method="post" action="{$link->getAdminLink('AdminSaleCategory')}">
				<label>{l s='On category:' mod='salecategory'} </label>
				<select name="categories" id="categories">		
        {if isset($categories)}
            {$categories}
        {/if}
	    </select>
	    <br />
	    <input type="checkbox" name="recurse">
	    <label style="font-size:12px;font-weight:normal">{l s='Recurse subcategories' mod='salecategory'}</label>  
	    <br /><br /><br />       
	    <fieldset style="font-size:13px">
	    <legend>{l s='Reduction' mod='salecategory'}</legend>
	    <label>{l s='Remove reduction for products' mod='salecategory'} </label> 
	    <input type="checkbox" name="on_reduction">		
	    <br />		
	    </fieldset>				    
	    <br />	
	    <fieldset style="font-size:13px">
	    <legend>{l s='Sale' mod='salecategory'}</legend>
	    <label>{l s='Unset products as "on sale"' mod='salecategory'} </label> 
	    <input type="checkbox" name="on_sale">
	    </fieldset>
	    <br />	
	    <input type="submit" name="submitUnsetReductionSale" class="button" value="{l s='Apply' mod='salecategory'}"/>
</form>	
</fieldset>
{if isset($content)}
	{$content}
{/if}

<script type="text/javascript">
	$(document).ready(function() {
		$('.datepicker').datetimepicker({
			prevText: '',
			nextText: '',
			dateFormat: 'yy-mm-dd',

			// Define a custom regional settings in order to use PrestaShop translation tools
			currentText: '{l s='Now' mod='salecategory'}',
			closeText: '{l s='Done' mod='salecategory'}',
			ampm: false,
			amNames: ['AM', 'A'],
			pmNames: ['PM', 'P'],
			timeFormat: 'hh:mm:ss tt',
			timeSuffix: '',
			timeOnlyTitle: "{l s='Choose Time' mod='salecategory'}",
			timeText: '{l s='Time' mod='salecategory'}',
			hourText: '{l s='Hour' mod='salecategory'}',
			minuteText: '{l s='Minute' mod='salecategory'}',
		});        
	});
</script>
