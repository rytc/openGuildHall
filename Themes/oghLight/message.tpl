{extends file='layout.tpl'}

{block name=body}

<p>{$text}</p>

<ul id="message">
	{foreach $links as $url => $name}
		<li><a href="{$name}">{$url}</a></li>
	{/foreach}
</ul>

{/block}