<?php

global $psp;
echo json_encode(
		$psp->loadRichSnippets('options')
);

?>