<!DOCTYPE html>
<html lang="en-US">
	<head>
		<title>Welcome to Blackhole!</title>
		<style>
			body { color: #fff; background-color: #851507; font: 14px/1.5 Helvetica, Arial, sans-serif; }
			#blackhole { margin: 20px auto; width: 700px; }
			pre { padding: 20px; white-space: pre-line; border-radius: 10px; background-color: #b34334; }
			a { color: #fff; }
		</style>
	</head>
	<body>
		<div id="blackhole">
			<h1>You have fallen into a trap!</h1>
			<p>
				This site&rsquo;s <a href="https://www.robotstxt.org/">robots.txt</a> file explicitly forbids your presence at this location.
				The following Whois data will be reviewed carefully. If it is determined that you suck, you will be banned from this site.
				If you think this is a mistake, <em>now</em> is the time to <a href="{$CAT_SITE_URL}/contact/">contact the administrator</a>.
			</p>
			<h3>
				Your IP Address is {$ip}<br />
				Your Host Name is {$host}
			</h3>
            WHOIS Lookup for [{$ip}] on [{$date}]<br />
			<pre>{$whois}</pre>
		</div>
	</body>
</html>