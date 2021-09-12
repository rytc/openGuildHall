{extends file='layout.tpl'}

{block name=head}
	<link href="{$theme_path}/profile.css" rel='stylesheet' type='text/css' />
{/block}

{block name=body}
<table class="userprofile">
	<thead>
		<tr>
			<td colspan="2">{$user->username}'s Profile</td>
		</tr>
	</thead>
	<tr>
		<td class="bioleft">
			{displayAvatar url=$user->avatar}
			<div class="statusicons">
				{onlineFlag lastseen=$user->last_seen}
				{countryIcon country=$user->country}
				{genderIcon gender=$user->gender}
			</div>
			
			<dl>
				<dt>Group:</dt>
				<dd>{$user->group_name}</dd>
				
				<dt>Last Seen:</dt>
				<dd>{$user->last_seen|formatDate:true}</dd>
				
				<dt>Registered:</dt>
				<dd>{$user->register_date|formatDate}</dd>
				
				<dt>Posts:</dt>
				<dd>{$user->posts}</dd>
				
			</dl>
			
			<div class="header">
				Contact
			</div>
			{if $user->contact_privacy == 2 or isLoggedIn}
				<ol class="contact">
				
				{if !empty($user->email)}
	            	<li><div class="sprite email">&nbsp;</div>
	            	&nbsp;{$user->email}</li>
	            {/if}
	
	           {if !empty($user->website)}
	            	<li><div class="sprite website">&nbsp;</div>
	            	&nbsp;<a href="{$user->website}">{$user->website|cleanURLForDisplay}</a></li>
	            {/if}
	
	           {if !empty($user->twitter)}
	            	<li><div class="sprite twitter">&nbsp;</div>
	            	&nbsp;<a href="http://www.twitter.com/{$user->twitter}">{$user->twitter}</a></li>
	            {/if}
	
	            {if !empty($user->aim)}
	            	<li><div class="sprite aim">&nbsp;</div>
	            	&nbsp;{$user->aim}</li>
	            {/if}
	
	            {if !empty($user->xbl)}
	            	<li><div class="sprite xboxlive">&nbsp;</div>
	            	&nbsp;{$user->xbl}</li>
	            {/if}
	
	            {if !empty($user->xfire)}
	            	<li><div class="sprite xfire">&nbsp;</div>
	            	&nbsp;{$user->xfire}</li>
	            {/if}
	            
	            {if !empty($user->steam)}
	            	<li><div class="sprite steam">&nbsp;</div>
	            	&nbsp;{$user->steam}</li>
	            {/if}
	            				
				</ol>
			{else}
				<sub>User has hidden their contact information.</sub>
			{/if}
			
			<br />
		</td>
		<td class="bioright">
			{if empty($user->about)}
				<p>This user has not writen a bio.</p>
			{else}
				{$user->about|parseUserText}
			{/if}
		<hr />
		{$user->signature|parseUserText}
		</td>
	</tr>
</table>

{/block}