{extends file='layout.tpl'}


{block name=head}
	<link href="{$theme_path}/forum.css" rel='stylesheet' type='text/css' />
{/block}

{block name=body}
	<a href="{$SITE_PATH}forum/">Forum Index</a> &raquo;
	{$category.title}
	<div class="category">
		<div class="cattitle">
			{$category.title}
		</div>
		<div class="boards">
			<table>
			<thead>
				<tr class="heading">
					<td>&nbsp;</td>
					<td>Boards</td>
					<td>Topics</td>
					<td>Posts</td>
					<td>Last Post</td>
				</tr>
			</thead>
			<tbody>
				{foreach $boards as $board}
				<tr>
					<td class="status">
						{if {boardIsUnread id=$board.id}}
							<div class="sprite newposts">&nbsp;</div>
						{else}
							<div class="sprite nonewposts">&nbsp;</div>
						{/if}
					</td>
					<td class="board">
						<a href="{$SITE_PATH}forum/board{$board.title|cleanForURL}-{$board.id}" class="nonew">{$board.title}</a>
						<p>{$board.description}</p>
					</td>
					<td class="topics">
						{$board.thread_count}
					</td>
					<td class="posts">
						{$board.post_count}
					</td>
					<td class="lastpost">
						{if !empty($board.last_post)}
							{assign var=lastPost value=$board.last_post}
							In <a href="{$SITE_PATH}forum/thead/{$lastPost.thread_title|cleanForURL}-{$lastPost.thread_id}">{$lastPost.thread_title|parseUserText:false:false}<a>
							<div class="sprite goto"><a href="{$SITE_PATH}forum/thead/{$lastPost.thread_title|cleanForURL}-{$lastPost.thread_id}">&nbsp;&nbsp;</div> 
							by <a href="{$SITE_PATH}user/profile/{$lastPost.author}">{$lastPost.author}</a> <br />
							on {$lastPost.date|formatDate:true}
						{else}
							No new posts
						{/if}
					</td>
				</tr>
				{/foreach}
			</tbody>
			</table>	
		</div>
	</div>
{/block}