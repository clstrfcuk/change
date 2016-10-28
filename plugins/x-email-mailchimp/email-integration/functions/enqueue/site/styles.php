<?php

// =============================================================================
// FUNCTIONS/ENQUEUE/SITE/STYLES.PHP
// -----------------------------------------------------------------------------
// Output site styles for the plugin. This file is included within the
// 'x_head_css' action.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Output Site Styles
// =============================================================================

// Output Site Styles
// =============================================================================

?>

.x-subscribe-form {
  position: relative;
}

.x-subscribe-form h1 {
  font-size: 1.75em;
  margin: 0 0 0.5em;
}

.x-subscribe-form label {
  margin: 0 0 0.375em;
  font-size: 0.85em;
  line-height: 1;
}

.x-subscribe-form label > span {
  position: relative;
}

.x-subscribe-form label .required {
  position: absolute;
  top: -0.1em;
  font-size: 1.5em;
}

.x-subscribe-form input[type="text"],
.x-subscribe-form input[type="email"] {
  width: 100%;
  margin-bottom: 1.25em;
  font-size: inherit;
}

.x-subscribe-form input[type="submit"] {
  display: inline-block;
  width: 100%;
  margin-top: 0.25em;
  font-size: inherit;
}

.x-subscribe-form input[type="submit"]:focus {
  outline: 0;
}

.x-subscribe-form .x-subscribe-form-alert-wrap {
  margin-top: 1.25em;
  font-size: inherit;
}