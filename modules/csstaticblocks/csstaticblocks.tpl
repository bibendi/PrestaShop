<!-- Static Block module -->
{foreach from=$block_list item=block}
	{if $block->identifier_block == 'top_image'}
		{if $page_name== 'index'}
			{$block->content[(int)$cookie->id_lang]}
		{/if}
	{/if}
	{if $block->identifier_block != 'top_image'}
		{$block->content[(int)$cookie->id_lang]}
	{/if}
{/foreach}
<!-- /Static block module -->












