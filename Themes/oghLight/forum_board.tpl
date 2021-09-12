{extends file='layout.tpl'}

{block name=head}
	<link href="{$theme_path}/forum.css" rel='stylesheet' type='text/css' />
{/block}

{block name=body}
	<a href="{$SITE_PATH}forum/">Forum Index</a> &raquo;
	<a href="{$SITE_PATH}forum/category/{$category.title|cleanForURL}-{$category.id}">{$category.title}</a> &raquo;
	{$board.title}
	{$pagination}

	{*ACL if can has new post*}
		<a href="{$SITE_PATH}forum/newthread/{$board.id}" class="button">Post a new thread</a>
	{*ACL /if*}

	<div class="category">
	<div class="cattitle">
		{$category.title}
	</div>
	<div class="boards">
		<table>
		<thead>
			<tr class="heading">
			<td>&nbsp;</td>
			<td>Threads</td>
			<td>Author</td>
			<td>Stats</td>
			<td>Last Post</td>
			</tr>
		</thead>
		<tbody>
			{if is_array($threads)}
				{foreach $threads as $thread}
					<tr>
						<td class="status">
							{if $thread.locked >= 1}
								<div class="sprite locked">&nbsp;</div>
							{*elseif {threadIsUnread boardid=$thread.board id=$thread.id*}
								{if $thread.sticky >= 1}
									<div class="sprite newsticky">&nbsp;</div>
								{else}
									<div class="sprite newposts">&nbsp;</div>
								{/if}
							{else}
								{if $thread.sticky >= 1}
									<div class="sprite nonewsticky">&nbsp;</div>
								{else}
									<div class="sprite nonewposts">&nbsp;</div>
								{/if}
							{/if}
						</td>
						<td class="board">	
							<a href="{$SITE_PATH}>forum/thread/{$thread.title|cleanForURL}-{$thread.id}" class="primeurl">{$thread.title|parseUserText:false:false}</a>
						</td>
						<td class="author"><a href="{$SITE_PATH}user/profile/{$thread.author}">{$thread.author}</a></td>
						<td class="stats"><span class="postlabel">{$thread.post_count - 1} Posts</span><br />
										  <span class="viewslabel">{$thread.views} Views</span></td>
						<td class="lastpost">
						{if !empty($thread.last_post)}
							{$thread.date|formatDate:true}<br />
							By <a href="{$SITE_PATH}user/profile/{$thread.last_post.author}">{$thread.last_post.author}</a>
							<div class="sprite goto"><a href="{$SITE_PATH}forum/thread/{$thread.title|cleanForURL}-{$thread.id}/last">&nbsp;&nbsp;</a></div>
						{else}
							None
						{/if}
						</td>
					</tr>
				{/foreach}
			{else}
				<tr>
					<td>&nbsp;</td>
					<td>No threads have been posted in this board.</td>
				</tr>
			{/if}
			</tbody>
			</table>
		</div>
	</div>

{$pagination}
{/block}