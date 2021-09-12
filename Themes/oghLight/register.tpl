{extends file='layout.tpl'}

{block name=body}

<p>The administrators and moderators of this board cannot be held responsible for any content 
	contained within. Each member is responsible for his own posts.<br /><br />

The rules of this board are as follows:</p>

<ul>
	
	<li>Members who are discovered to be misrepresenting themselves --- i.e. providing false information 
		about themselves in order to deceive others --- will be banned permanently. Similarly, members who 
		harass, stalk, or otherwise act in a threatening manner will be banned, and legal action may be 
		taken.</li>

	<li>Commercial advertising is strictly banned. This includes, but is not limited to, registering 
		accounts for the sole purpose of advertising; posting advertisements on the board; attempting to 
		trick people into visiting commercial sites; and any other actions that are not in keeping with 
		the spirit of this community.</li>
</ul>

<br /><br />
<p>By registering an account, you agree to the above terms and conditions.</p>

{if isset($errors)}
        <ul class="error">
        {foreach $errors as $error}
            <li>{$error}</li>
        {/foreach}
        </ul>
{/if}

<form action="{$SITE_PATH}user/register" method="post">
<ol class="form">
    <li>
        <label>Username</label>
        {$formValues.username=$formValues.username|default:""}
        <input type="text" name="username" value="{$formValues.username}" id='username' />
    </li>
    <li>
        <label>Password</label>
        <input type="password" name="password" />
    </li>
    <li>
        <label>Password Again</label>
        <input type="password" name="password2" />
    </li>
    <li>
        <label>Email</label>
        {$formValues.email=$formValues.email|default:""}
        <input type="text" name="email" value="{$formValues.email}" />
    </li>
    <li>
        <label>Birthday</label>
        <br />
        <select name="birthday_day">
            <option value="0">-</option>
            {for $i = 1 to 32}
    		    
    		        {if $formValues.birthday_day == $i}
    		            <option value="{$i}" selected="selected">{$i}</option>
                    {else}
                        <option value="{$i}">{$i}</option>
    		        {/if}
    		        
    		{/for}
        </select>
        
        <select name="birthday_month">
        {foreach $months as $name}
             
                {if $formValues.birthday_month == $name@key}
                    <option value="{$name@key}" selected="selected">{$name}</option>
                {else}
                    <option value="{$name@key}">{$name}</option>
                {/if}
                
        {/foreach}
        </select>
        
        <select name="birthday_year">
            <option value="0">-</option>
            {for $i=$smarty.now|date_format:"%Y" to 1968 step -1}
                {if isset($formValues.birthday_year) and $formValues.birthday_year == $i}
                    <option value="{$i}" selected="selected">{$i}</option>
                {else}
                    <option value="{$i}">{$i}</option>
                {/if}
            {/for}
        </select>
        
        {if isset($errors.birthday)}
            <ul class="error">
            {foreach $errors.birthday as $error}
                <li>{$errMessages.$error}</li>
            {/foreach}
            </ul>
        {/if}
    </li>
    <li><input type="submit" value="Register" /></li>
</ol>
</form>
{/block}