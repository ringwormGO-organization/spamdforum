<?php
function check_image_url($matches) {
	global $config;
	foreach ($config['allowed_sites'] as $k => $site)
		if (!preg_match("/(https?:\/\/)?$site\/.+/ium", $matches[3]))
			return $matches[0];
	return "<img alt=\"{$matches[2]}\" src=\"{$matches[3]}\">";
}

function format_body($body) {
	/*
	 * Groups are separated extensively so admins can config how they want
	 * links to displays.
	 * \\0 full url.
	 * \\3 for hostname
	 * \\4 for the domain name with a . but without com, net, etc
	 * \\7 for com, net, etc
	 * \\9 for the path (/new, /file/abc.jpg, etc, may be empty)
	 * \\2 for the protocol
	 * \\8 for the port with a : (may be empty)
	 */
	$urlre = "(^| )(https?):\/\/" . "((([[:alnum:]-])+(\.))+" . "([[:alnum:]]{1,6})"
	. "(:[0-9]{2,5})?)" . "(\/[[:alnum:]+=%#&_.:~?@\-\/]*)?( |$)";

	$imgurl = "(^| )\!\[(.*)\]\((.+)\)( |$)";
	$markurl = "(^| )\[(.+)\]\((.+)\)( |$)";

	$body = preg_replace("/$urlre/ium", '<a href="\\0">\\0</a>', $body);
	$body = preg_replace_callback("/$imgurl/ium", "check_image_url", $body);
	$body = preg_replace("/$markurl/ium", '\\1<a href="\\3">\\2</a>\\4', $body);
	$body = preg_replace("/^ *&gt;(.+)$/ium", "<blockquote><p>\\1</p></blockquote>", $body);
	$body = preg_replace("/^ *-{3,}$/ium", "<hr>", $body);
	$body = nl2br($body, false);
	$body = preg_replace("/<br>\n *<br>\n/ium", "</p>\n<p>", $body);
	return $body;
}
?>
