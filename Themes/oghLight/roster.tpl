{extends file="layout.tpl"}

{block name=head}
	<link href="{$theme_path}/roster.css" rel='stylesheet' type='text/css' />
{/block}

{block name=body}


<div class="roster">
	<table>
		<thead>
			<tr>
				<td>&nbsp;</td>
				<td>Username</td>
				<td>Registered</td>
			</tr>
		</thead>
		{foreach $users as $user}
			<tr>
				<td class="avatar">
					{displayAvatar url=$user.avatar}
				</td>
				<td>
					{countryIcon country=$user.country}&nbsp;<a href="/user/profile/{$user.username}">{$user.username}</a>
					<sub>Last seen {$user.last_seen|formatDate:true}</sub>
				</td>
				<td>{$user.register_date|formatDate}</td>
			</tr>
		{/foreach}
	</table>
</div>
{$pagination}


{/block}