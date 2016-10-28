<?php

// =============================================================================
// FUNCTIONS/FRAMEWORK/INIT.PHP
// -----------------------------------------------------------------------------
// The view-handler.php file is so the plugin and providers can easily load
// view files that are injected with shared data from the plugin.
//
// The options-handler.php file is for saving and validating options. It allows
// the providers to simply "declare" what they want to use, and the plugin will
// handle their storage and initialization.
//
// The plugin-base.php file performs essential bootstrap like hooks that will
// always be used (i.e. loading the config file, instantiating any declared
// widgets, instantiating any declared shortcodes, et cetera). It is what
// powers the plugin's config file.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Require Files
// =============================================================================

// Require Files
// =============================================================================

require_once( 'view-handler.php' );
require_once( 'options-handler.php' );
require_once( 'plugin-base.php' );