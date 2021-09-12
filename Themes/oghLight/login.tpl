{extends file='layout.tpl'}

{block name=body}
<form action="{$SITE_PATH}user/login" method="post">
{if isset($errors) and is_array($errors)}
	<ol class="error">
		<li>Invalid username or password</li>
	</ol>
{/if}
<ol>
	<li>
		<label>Username</label>
		<input type="text" name="username" value="" style="width: 8em" />

	<? endif; ?>
	</li>
	
	<li>
		<label>Password</label>
		<input type="password" name="password" style="width: 8em" />
	</li>
	
	<li>
		<label>Remember</label>
		<input type="checkbox" name="remember" /><br />
	</li>
	
	<li>
		<input type="submit" value="Login"> or <a href="/user/register">Register</a>
	</li>
</ol>
</form>
{/block}