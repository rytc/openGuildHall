{extends file='layout.tpl'}

{block name=head}
<link href="{$theme_path}/index.css" rel="stylesheet" type="text/css" />
{/block}

{block name=body}
<div class="community">
	<div class="header">
		<h2>General Information</h2>
	</div>

	<p>Welcome to your new installation of Open guild Hall! Some general information
	about your guild should go here, like what kind of games you like to play, who can
	join, how new members can join and anything else you think should go here. The default
	login is admin:admin.</p>

	<p>Please note that this is a pre-alpha version and is not ready for real use.</p>

	<br />
	<div class="header">
		<h2>Recent Forum Activity</h2>
	</div>
		{if empty($latestThreads)}
			<p style="font-weight: bold">There are no forum posts</p>
		{else}
			<ol id="latest">
				{foreach $latestThreads as $thread}
					<li>
						<a href="forum/thread/{$thread.title|cleanForURL}-{$thread.id}">{$thread.title}</a> by
						<a href="user/profile/{$thread.post.author_username|cleanForURL}">{$thread.post.author_username}</a> <br />
						<span class="date">{$thread.post.date|formatDate:true}</span>
					</li>
				{/foreach}
			</ol>
		{/if}
</div>
{if empty($news)}
	<p style="font-weight: bold;">There is no news.</p>
{else}
	<ol id="news">
	{foreach $news as $newsItem}
		<li>
			<div class="news_item">
				<div class="header">
					<h2><a href='forum/thread/{$newsItem.title|cleanForURL}-{$newsItem.thread_id}'>{$newsItem@title}</h2></h2>		
				</div>
				<div class="content">
					{$newsItem.copy|parseUserText:!$newsItem.disable_bbcode:!$newsItem.disable_smileys}
				</div>
				<div class="footer">
					<sub>Posted By <a href="user/profile/{$newsItem.author|cleanForURL}">{$newsItem.author}</a></sub>
					<span class="date">{$newsItem.post.date|formatDate:true}</span> 
					under <a href"forum/board/{$newsItem.board_id}">{$newsItem.board_title}</a>
				</div>
		</li>
	{/foreach}
	</ol>
{/if}
{/block}