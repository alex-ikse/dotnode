<?php
if ($test=$_REQUEST["text"]) {
    print "";
}
else if ($lines = file($text)) {
	foreach ($lines as $line) {
	// To avoid loop:ignore inclusion of other raw link
		if (!(preg_match("/^\[\[\|(\S*)(\s+(.+))?\]\]$/", $line, $matches)))
			print $line;
	}
}
?>
