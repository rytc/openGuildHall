{extends file='layout.tpl'}

{block name=head}
    <link href="{$theme_path}/user_controls.css" rel='stylesheet' type='text/css' />
{/block}

{block name=javascript}
	{include file='usercontrols.js'}
{/block}

{block name=body}

<ul class="tabs">
    <li><a href="#usertab" style="font-weight: bold;">{$user->username}</a></li>
    <li><a href="#bio">Bio</a></li>
    <li><a href="#avatar">Avatar</a></li>
    <li><a href="#signature">Signature</a></li>
    <li><a href="#contact">Contact</a></li>
    <li><a href="#password">Password</a></li>
    <li><a href="#settings">Settings</a></li>
</ul>
<div class="tab_container">
    <div id="usertab" class="tab_content">
        <h3>Hello {$user->username}</h3>
        <sub>You were last seen {$user->last_seen|formatDate:true}</sub><br /><br />

		{if $user->avatar == "none" and $user->country == 0}
			<!--<span class="warning"><b>Your profile looks boring.</b><br />
			<sub>Why don't you fix it up by changing your avatar, gender, and country!</sub></span>
			<br />-->
		{/if}
		
		{if empty($user->steam) or empty($userxfire) or empty($user->aim)}
			<!--<span class="warning"><b>We can't get to know you like this!</b><br />
			<sub>We noticed that you haven't set any contact information. How are we supposed to play games together if we have no way of finding you?
			Try setting your Steam, XFire, or AIM contact information!</sub></span>
			<br />-->
		{/if}

    </div>
    <div id="bio" class="tab_content">
        <form action="{$SITE_PATH}user/controls" method="post" accept-charset="utf-8">
            <ol class="form">
				<li>
					<label>Country</label>
					{countryIcon country=$user->country}
					<select name="country" id="country">
						{foreach $countries as $country}
							{if $user->country == $country@key}
								<option selected="selected" value="{$country@key}">{$country}</option>
							{else}
								<option value="{$country@key}">{$country}</option>
							{/if}
						{/foreach}
					</select>
				</li>
				<li>
					<label>Gender</label>
					{if $user->gender == 'male'}
						<input type="radio" name="gender" value="male" checked="checked" />
					{else}
						<input type="radio" name="gender" value="male" />
					{/if}
					<div class="sprite male">&nbsp;</div>
					
					{if $user->gender == 'female'}
						<input type="radio" name="gender" value="female" checked="checked" />
					{else}
						<input type="radio" name="gender" value="female" />
					{/if}
					<div class="sprite female">&nbsp;</div>
					
					{if $user->gender != 'male' and $user->gender != 'female'}
						<input type="radio" name="gender" value="none" checked="checked" /> 
					{else}
						<input type="radio" name="gender" value="none" />
					{/if}
					None
				</li>
                <li>
                    <label>Biography</label>
                    <sub>Tell us about yourself. What do you like? What do you dislike? What is your past? What is in your future? What is your gaming setup? What games do you like?</sub><br />
                    <textarea name="about" cols="60" rows="10">{$user->about}</textarea>
                </li>
                <div class="errors">
                    &nbsp;
                </div>
                <li>
                    <input type="submit" value="Submit" class="submit" />
                </li>
            </ol>
        </form>
    </div>
    <div id="avatar" class="tab_content"> 
        <form action="{$SITE_PATH}user/controls" method="post" accept-charset="utf-8">
            <ol class="form">
                <li>
                    <label>Avatar</label>
                    {displayAvatar url=$user->avatar}
                    <br />
                    <input type="text" size="70" value="{$user->avatar}" name="avatar" id="avatarinput" />
                </li>
                <div class="errors">
                </div>
                <li>
                    <input type="submit" value="Submit" class="submit"  />
                </li>
            </ol>
        </form>
    </div>
    <div id="signature" class="tab_content">
        <form action="{$SITE_PATH}user/controls" method="post" accept-charset="utf-8">
            <ol class="form">
                <li>
                    <label>Signature</label>
                    <textarea name="signature" cols="60" rows="10">{$user->signature}</textarea>
                </li>
                <div class="errors">
                </div>
                <li>
                    <input type="submit" value="Submit" class="submit" />
                </li>
            </ol>
        </form>
    </div>
    <div id="contact" class="tab_content">
        <form action="{$SITE_PATH}user/controls" method="post" accept-charset="utf-8">
            <ol class="form">
                <li>
                    <label><div class="sprite email">&nbsp;</div>&nbsp;Email Address</label>
                    <input type="text" name="email" value="{$user->email}"/>
                </li>
                <li>
                    <label><div class="sprite website">&nbsp;</div>&nbsp;Web Site</label>
                    <input type="text" name="website" value="{$user->website}" />
                </li>
                <li>
                    <label><div class="sprite twitter">&nbsp;</div>&nbsp;Twitter</label>
                    <input type="text" name="twitter" value="{$user->twitter}" />
                </li>
                <li>
                    <label><div class="sprite aim">&nbsp;</div>&nbsp;AIM</label>
                    <input type="text" name="aim" value="{$user->aim}" />
                </li>
                <li>
                    <label><div class="sprite xboxlive">&nbsp;</div>&nbsp;Xbox Live</label>
                    <input type="text" name="xbl" value="{$user->xbl}" />

                </li>
                <li>
                    <label><div class="sprite xfire">&nbsp;</div>&nbsp;XFire</label>
                    <input type="text" name="xfire" value="{$user->xfire}" />
                </li>
                <li>
                    <label><div class="sprite steam">&nbsp;</div>&nbsp;Steam</label>
                    <input type="text" name="steam" value="{$user->steam}" />
                </li>
				<li style="padding-top: 2em;">
					<label>Privacy Settings</label>
					<select name="contact_privacy">
					{if $user->contact_privacy == 0}
						<option value="0" selected="selected">
					{else}
						<option value="0">
					{/if}
						Only visible to members</option>
						
					{if $user->contact_privacy == 1}
						<option value="1" selected="selected">
					{else}
						<option value="1">
					{/if}
						Only visible to logged in users</option>
						
					{if $user->contact_privacy == 2}
						<option value="2" selected="selected">
					{else}
						<option value="2">
					{/if}
						Visible to everyone</option>
					</select>
				</li>
                <div class="errors">
                </div>
                <li>
                    <input type="submit" value="Submit" class="submit" />
                </li>
            </ol>
        </form>
    </div>
    <div id="password" class="tab_content">
        <form action="{$SITE_PATH}user/controls" method="post" accept-charset="utf-8">
            <ol class="form">
                <li>
                    <label>New Password</label>
                    <input type="password" name="password" />
                    {if isset($errors.password)}
                        <ul class="error">
                            {foreach $errors.password as $error}
                                <li>$error</li>
                            {/foreach}
                        </ul>
                    {/if}
                </li>
                <li>
                    <label>New Password (Again)</label>
                    <input type="password" name="password2" />
                </li>
                <div class="errors">
                </div>
                <li>
                    <input type="submit" value="Submit" class="submit" />
                </li>
            </ol>
        </form>
    </div>
    <div id="settings" class="tab_content">
            <form action="{$SITE_PATH}user/controls" method="post" accept-charset="utf-8">
                <ol class="form">
                    <li>

                        <label>Timezone</label>
                        <select name="timezone">
                        	{* Timezones are organized by [Country][City][Subcity] = City/Subcity *}
                            {foreach $timezones as $country => $zones}
                            <optgroup label="{$zones@key}">
                                {if is_array($zones)}
                                    {foreach $zones as $timezone => $city}
                                      {if $user->timezone == $timezone}
                                        <option selected="selected" value="{$timezone}">
                                      {else}
                                        <option value="{$timezone}">
                                      {/if}
                                      {$city}</option>
                                    {/foreach}
                                {else}
                                    <option>{$country}</option>
                                {/if}
                            </optgroup>
                            {/foreach}
                        </select>
                        {if isset($errors.timezone)}
                            <ul class="error">
                                {foreach $errors.timezone as $error}
                                    <li>$error</li>
                                {/foreach}
                            </ul>
                        {/if}
                    </li>
                    <div class="errors">
                    </div>
                    <li>
                        <input type="submit" value="Submit" class="submit" />
                    </li>
                </ol>
            </form>
    </div>
</div>

{/block}