<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html lang='en'>
<head>
    <title>openGuildHall Install</title>
    <link href="{$theme_path}/install.css" rel='stylesheet' type='text/css' />

    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.js"></script>
    
</head>
<body>
	<div class="content">
		<h1>openGuildHall Installation</h1>
		<hr />
		<p>openGuildHall is currently under heavy development and is currently missing many standard features. This
		current release of openGuildHall is strictly for developers wishing to contribute to the project. openGuldHall
		is distributed under the GNU General Public License v2</p>

		<form action="{$SITE_PATH}install" method="post">
			<h3>1. Setup the database</h3>
			<ol>
				<li>
					<label>MySQL Username</label>
					{if array_key_exists('mysqlusername', $errors)}
						<span class="error">{$errors.mysqlusername}</span>
					{/if}
					<input type="text" name="mysqlusername" value="{$formValues.mysqlusername}" />
				</li>

				<li>
					<label>MySQL Password</label>
					{if array_key_exists('mysqlpassword', $errors)}
						<span class="error">{$errors.mysqlpassword}</span>
					{/if}
					<input type="password" name="mysqlpassword" value=""/>
				</li>

				<li>
					<label>MySQL Database</label>
					{if array_key_exists('mysqldatabase', $errors)}
						<span class="error">{$errors.mysqldatabase}</span>
					{/if}
					<input type="text" name="mysqldatabase" value="{$formValues.mysqldatabase}" />
				</li>

				<li>
					<label>MySQL Hostname</label>
					{if array_key_exists('mysqlhostname', $errors)}
						<span class="error">{$errors.mysqlhostname}</span>
					{/if}
					<input type="text" name="mysqlhostname" value="{$formValues.mysqlhostname}" />
				</li>

				<li>
					<label>Table Prefix</label>
					{if array_key_exists('mysqlprefix', $errors)}
						<span class="error">{$errors.mysqlprefix}</span>
					{/if}
					<input type="text" name="mysqlprefix" value="{$formValues.mysqlprefix}" />
				</li>
			</ol>

			<h3>2. Setup the admin account</h3>
			<ol>
				<li>
					<label>Username</label>
					{if array_key_exists('adminusername', $errors)}
						<span class="error">{$errors.adminusername}</span>
					{/if}
					<input type="text" name="adminusername" value="{$formValues.adminusername}" />
				</li>

				<li>
					<label>Password</label>
					{if array_key_exists('adminpassword', $errors)}
						<span class="error">{$errors.adminpassword}</span>
					{/if}
					<input type="password" name="adminpassword" />
				</li>

				<li>
					<label>Password (again)</label>
					<input type="password" name="adminpassword2" />
				</li>


				<li>
					<label>Email</label>
					{if array_key_exists('adminemail', $errors)}
						<span class="error">{$errors.adminemail}</span>
					{/if}
					<input type="text" name="adminemail" value="{$formValues.adminemail}" />
				</li>
			</ol>
			<h3>3. Settings</h3>
			<ol>
				<li>
					<label>ReCAPTCHA Public Key</label>
					<sub>Get your ReCAPTCHA keys from <a href="http://www.google.com/recaptcha">google.com/recaptcha</a>.
						Leave empty to disable ReCAPTCHA(highly unrecommended).
					{if array_key_exists('recaptcha', $errors)}
						<span class="error">{$errors.recaptcha}</span>
					{/if}
					<input type="text" name="recaptcha_public" value="{$formValues.recaptcha_public}" />
				</li>
				<li>
					<label>ReCAPTCHA Private Key</label>
					<input type="text" name="recaptcha_private" value="{$formValues.recaptcha_private}" />
				</li>
			</ol>
			<h3>4. ...</h3>
			<h3>5. Profit!</h3>
			<input type="submit" name="Install" />
		</form>
		<hr />
		{$smarty.const.VERSION_STRING}<br />
	</div>
</body>
</html>