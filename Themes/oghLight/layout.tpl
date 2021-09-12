<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html lang='en'>
	<head>
		<title>openGuildHall :: {$pagetitle}</title>

		<link href="{$theme_path}/layout.css" rel='stylesheet' type='text/css' />
		<link href="{$theme_path}/sprites.css" rel='stylesheet' type='text/css' />
		{block name=head}{/block}

		<script src='http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.js' type='text/javascript'></script>

	    <script type="text/javascript">
			function checkBrowserVersion() {
				if(navigator.appVersion.indexOf("MSIE 6.")==1 || navigator.appVersion.indexOf("MSIE 7.")==1) {
					$("#browsererror").slideDown();
				}
			}

			{block name=javascript}{/block}
		</script>  
	</head>
<body onload="javascript:checkBrowserVersion();">
	<div id="browsererror" style="display: none;">
		We're sorry, but the browser you are currently using is not supported. Please upgrade to a modern browser such as <a href="http://google.com/chrome">Chrome</a> or <a href="http://firefox.com">Firefox</a>.
	</div>
	
	<div id="bodywrapper">
       <div id="nav">
			<ol>
	            <li><a href="{$SITE_PATH}">Home</a></li>
	            <li><a href="{$SITE_PATH}page/information">Information</a></li>
	            <li><a href="{$SITE_PATH}roster">Roster</a></li>
	            <li><a href="{$SITE_PATH}forum">Forum</a></li>
	       </ol>
       </div>
       <div id="header">
       		<div id="usernav">
       			{if {isLoggedIn}}
       				Welcome, {getUsername}
       				<ol>
       					<li><a href="/user/controls">User Settings</a></li>

       					{if {hasAccess controller=Admin page=Controls action=Edit}}
       						<li><a href="/admin">Admin Controls</a></li>
       					{/if}
       					<li><a href="/user/profile/{getUsername}">Profile</a></li>
       					<li><a href="/user/logout">Logout</a>
       				</ol>
       			{else}
       				Welcome, Guest
       				<ol>
       					<li><a href="/user/login">Login</a></li>
       					<li><a href="/user/register">Register</a></li>
       				</ol>
       			{/if}
       		</div>   
       </div>

       <div id="content">
       		<h2 id="pagetitle">{$pagetitle}</h2>
    	   	{block name=body}{/block}

    	   	<div style="clear:both;">&nbsp;</div>
    	   	<div id="footer">
	    	   	<div id="credit">
	    	   		openGuildHall<br />
	    	   	</div>
    	   	</div>
       </div>

	</div>
	
</body>
</html>