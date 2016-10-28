<?php if ( false ) : ?>

  <p class="cs-validated"><?php _e('<strong>Congrats! Cornerstone is validated</strong>. Automatic updates are up and running.', csl18n() ); ?><br><input type="text" name="api_key"></p>

<?php else : ?>

  <p class="cs-not-validated"><?php _e('<strong>Uh oh! It looks like Cornerstone isn&apos;t validated</strong>. Product validation enables automatic updates.', csl18n() ); ?><br><input type="text" name="api_key"></p>

<?php endif;