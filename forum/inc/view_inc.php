<?php
/* PLEASE NOTE THAT $body ALREADY GONE THROUGH htmlspecialchars() */
function check_image_url($matches) {
	global $config;
	foreach ($config['allowed_sites'] as $k => $site)
		if (preg_match("/(https?:\/\/)?$site\/.+/ium", $matches[3]))
			return "<img alt=\"{$matches[2]}\" src=\"{$matches[3]}\">";
	return $matches[0];
}
/* save_p: save current content in $p_arr to $html */
function save_p(&$html, &$arr, $strstart) {
	if ($strstart == '! ')
		$html .= "<pre>" . implode("\n", $arr) . "</pre>\n";
	else if ($strstart == '&gt;')
		$html .= "<blockquote><p>" . nl2br(real_format(implode("\n", $arr))) . "</p></blockquote>\n";
	else {
		$para = "<p>" . nl2br(real_format(implode("\n", $arr))) . "</p>\n";
		$html .= $para;
	}
	$arr = array();
}

function format_body($body) {
	$html = '';
	$arr = array();
	$strstarts = array('! ', '&gt;');
	$cutlen = 0;
	$searchfor = '';

	$lines = explode("\n", $body);
	foreach ($lines as $line) {
		$line = ltrim($line);
		if ($cutlen == 0) { /* because $searchfor='' also match <pre> and others */
			/* newstrstart: look for new $searchfor */
			newstrstart:
			foreach($strstarts as $search) {
				if (str_starts_with($line, $search)) {
					if ($searchfor != $search)
						/* if e.g '' != '! ' commit the paragraph and
						 * start looking for preformatted text */
						save_p($html, $arr, $searchfor);
					$searchfor = $search;
					$cutlen = strlen($search);
					break;
				}
				$searchfor = '';
				$cutlen = 0;
			}
		}
		if (str_starts_with($line, $searchfor)) {
			$arr[] = substr($line, $cutlen);
		} else {
			save_p($html, $arr, $searchfor);
			goto newstrstart;
		}
	}
	save_p($html, $arr, $searchfor);
	return $html;
}

function real_format($body) {
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
	$urlre = "(https?):\/\/" . "((([[:alnum:]-])+(\.))+" . "([[:alnum:]]{1,6})"
	. "(:[0-9]{2,5})?)" . "(\/[[:alnum:]+=%#&_.:~?@\-\/]*)?";

	$imgurl = "(^| )\!\[(.*)\]\((.+)\)( |$)";
	$markurl = "(^| )\[(.+?)\]\(($urlre)?\)( |$)";
	$bbcolor = "\[color=([[:alnum:]#]+?)\](.+?)\[\/color\]";

	$body = preg_replace("/$bbcolor/ius", "<span style=\"color:\\1;\">\\2</span>", $body);
	$body = preg_replace("/(^| )$urlre( |$)/ium", '<a href="\\0">\\0</a>', $body);
	$body = preg_replace_callback("/$imgurl/ium", "check_image_url", $body);
	/*
	 * replace markdown links after image because two patterns just look the same
	 * I deliberately do not support image to link/video
	 */
	$body = preg_replace("/$markurl/ium", '\\1<a href="\\3">\\2</a>\\12', $body);
	$body = preg_replace("/^ *-{3,}$/ium", "<hr>", $body); /* 3 or more - forms a <hr> on duolingo */
	return $body;
}
?>
