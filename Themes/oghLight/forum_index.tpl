{extends file='layout.tpl'}

{block name=head}
	<link href="{$theme_path}/forum.css" rel='stylesheet' type='text/css' />
{/block}

{block name=body}
	{if empty($categories)}
		<p>The forum has not yet been setup.</p>
	{/if}

	{foreach $categories as $category}
	<div class="category">
		<div class="cattitle">
			{assign var=url value="{$SITE_PATH}forum/category/{$category.title|cleanForURL}-{$category.id}"}
			<a href="{$url}">{$category.title}</a>
		</div>
		<div class="boards">
			<table>
			<thead>
				<tr class="heading">
					<td>&nbsp;</td>
					<td><span>Boards</span></td>
					<td><span>Stats</span></td>
					<td><span>Last Post</span></td>
				</tr>
			</thead>
			<tbody>
				{assign var=catId value=$category.id}
				{if !empty($boards[$catId])}
					{foreach $boards[$catId] as $board}
					<tr>
						<td class="status">
							
							{if {boardIsUnread id=$board.id}}
							
								<div class="sprite newposts">&nbsp;</div>
							{else}
								<div class="sprite nonewposts">&nbsp;</div>
							{/if}
						</td>
						<td class="board">
							{assign var=url value="{$SITE_PATH}forum/board/{$board.title|cleanForURL}-{$board.id}"}
							<a href="{$url}" class="nonew">{$board.title}</a>
							<p>{$board.description}</p>
						</td>
						<td class="stats">
							<span class="postlabel">{$board.thread_count} Threads</span><br />
							<span class="postlabel">{$board.post_count} Posts</span>
						</td>
						<td class="lastpost">
							{if !empty($board.last_post)}
								{assign var=lastPost value=$board.last_post}
								{assign var=lastPostURL value="{$SITE_PATH}forum/thread/{$lastPost.thread_title|cleanForURL}-{$lastPost.thread_id}"}
								In <a href="{$lastPostURL}">{$lastPost.thread_title|parseUserText}</a>
								<div class="sprite goto"><a href="{$lastPstURL}/last">&nbsp;&nbsp;</a></div>
								by <a href="{$SITE_PATH}user/profile/{$lastPost.author}">{$lastPost.author}</a><br />
								on {$lastPost.date|formatDate:true}
							{else}
								No new posts
							{/if}
						</td>
					</tr>
					{/foreach}
				{/if}
			</tbody>
			</table>
		</div>
	</div>
	{/foreach}
{/block}