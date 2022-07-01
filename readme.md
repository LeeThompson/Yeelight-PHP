<h1>Yeelight-PHP</h1>

This is a modified fork of a tiny class to facilitate controlling the Yeelight WiFi bulbs by Xiaomi in PHP.<p>
Function names are dynamic and correspond to the API endpoints.<p>
This script has no external dependencies other than some PHP 5.x version and the sockets extension.<br/>
See the <a href="https://github.com/LeeThompson/Yeelight-PHP/wiki">WIKI</a> for more information.
  
You will need to enable wifi "LAN" mode for any bulbs you wish to control via the API from the Yeelight control applet.

NOTE: It currently is not very PHP8 friendly - fixes coming soon.

<h2>New additions:</h2>

<ul>
<li>setDelay(value) sets the delay between commands, default is 100 as in the original.   Delay can be reset with setDelay()
<li>setDebug(true/false) enables/disables debug messages for troubleshooting.
<li>setReuse(true/false) enables/disables using the same connection.  (See <a href="https://github.com/LeeThompson/Yeelight-PHP/wiki/Yeelight-Bugs">Yeelight Bugs</a> for potential issues enabling this)
<li>The constructor now uses the default port of 55443
<li>The constructor now accepts additional parameters (timeout,debug,reuse)
<li>Arrays can now be sent as arguments per Yeelight spec.
<li>Documentation via the repo WIKI.
</ul>
