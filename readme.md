<h1>Yeelight-PHP</h1>

This is a modified fork of a tiny class to facilitate controlling the Yeelight WiFi bulbs by Xiaomi in PHP.<p>
Function names are dynamic and respond to the API endpoints.<p>
This script has no external dependencies other than some PHP 5.x version and the sockets extension.<br/>
See the WIKI for more information.

<h2>New additions:</h2>

<ul>
<li>setDelay(value) sets the delay between commands, default is 100 as in the original.   Delay can be reset with setDelay()
<li>setDebug(true/false) enables/disables debug messages for troubleshooting.
<li>The constructor now uses the default port of 55443
<li>The constructor now accepts timeout as the third parameter (default is 30)
<li>Arrays can now be sent as arguments per Yeelight spec.
<li>Documentation via the repo WIKI.
</ul>
