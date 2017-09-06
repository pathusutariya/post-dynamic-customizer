<?php
// Date and Time picker

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Date and Time output is handled by pdc settings
echo $this->indent . htmlspecialchars("<?php " . $this->the_field_method . "( '" . $this->name . "' ); ?>")."\n";
