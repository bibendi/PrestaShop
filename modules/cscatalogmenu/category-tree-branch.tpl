{assign var='css_class' value='level-'|cat:$level}
{if isset($last) && $last == 'true'}
    {assign var='css_class' value=$css_class|cat:' last'}
{/if}
<li class="{$css_class}">
	<a href="{$node.link|escape:html:'UTF-8'}" {if isset($currentCategoryId) && $node.id == $currentCategoryId}class="selected"{/if} title="">{$node.name|escape:html:'UTF-8'}</a>
	{if $node.children|@count > 0}
		<ul>
		{foreach from=$node.children item=child name=categoryTreeBranch}
			{if $smarty.foreach.categoryTreeBranch.last}
				{include file=$branche_tpl_path node=$child last='true' level=$level+1}
			{else}
				{include file=$branche_tpl_path node=$child last='false' level=$level+1}
			{/if}
		{/foreach}
		</ul>
	{/if}
</li>
