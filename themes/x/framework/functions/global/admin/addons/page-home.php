<?php

// =============================================================================
// FUNCTIONS/GLOBAL/ADMIN/ADDONS/PAGE-HOME.PHP
// -----------------------------------------------------------------------------
// Addons home page output.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Page Output
// =============================================================================

// Page Output
// =============================================================================

function x_addons_page_home() { ?>

  <div class="wrap about-wrap x-addons-home">

    <?php if ( ! x_plugin_cornerstone_exists() ) : ?>

      <div class="updated x-notice cornerstone">
        <p><strong>Only one final step!</strong> Please head over to the <a href="<?php echo x_addons_get_link_extensions(); ?>" target="_blank">Extensions</a> page and install Cornerstone to finalize the theme setup process. While you're there, you can optionally choose to manage any of our additional included plugins as well.</p>
      </div>

    <?php endif; ?>

    <header class="x-addons-home-header">
      <div class="x-addons-home-header-text">
        <h1><?php echo apply_filters( 'x_addons_home_heading', 'Congratulations!' ); ?></h1>
        <div class="about-text"><?php echo apply_filters( 'x_addons_home_subheading', ( x_is_validated() ) ? '<strong>X is now installed (woo-hoo).</strong> Read on to learn more about various member center resources and other helpful tools. There are many important parts to each update, so be sure to <a href="https://theme.co/changelog/" target="_blank">check out the release notes and changelog</a> for this latest version.' : '<strong>X is now installed (woo-hoo).</strong> Follow the steps below to validate your product, which unlocks automatic updates, additional Extensions, and more.' ); ?></div>
      </div>
      <div class="x-badge">
        <div class="text">Version</div>
        <div class="version"><?php echo X_VERSION; ?></div>
      </div>
    </header>

    <?php ob_start(); ?>

    <?php if ( x_is_validated() ) : ?>

      <div class="changelog">
        <div class="feature-section three-col">
          <div class="col">
            <h3><span class="dashicons dashicons-welcome-learn-more"></span> Knowledge Base</h3>
            <p>The <a href="https://community.theme.co/kb/" target="_blank">Knowledge Base</a> is where we include all of our in-depth documentation for the theme, including dozens of high definition videos.</p>
            <p>Here you'll find detailed information on various theme features including the Customizer, post formats, shortcodes, and much more!</p>
          </div>
          <div class="col">
            <h3><span class="dashicons dashicons-megaphone"></span> Marketing Training</h3>
            <p>X is so much more than a theme. It's also a collection of resources from some of the world's top <a href="https://community.theme.co/experts/" target="_blank">Internet marketers</a>.</p>
            <p>While we've built many of the items discussed by these experts directly into the theme, we're also able to bring you <a href="https://community.theme.co/training/" target="_blank">incredible training</a> to help take your website to the next level.</p>
          </div>
          <div class="col">
            <h3><span class="dashicons dashicons-tickets"></span> WordPress Tutorials</h3>
            <p>New to WordPress? Don't worry, we've all been there. If you're still working your way through the foundational elements of WordPress, you will be pleased to know that we have <a href="https://community.theme.co/kb/" target="_blank">dozens of high definition videos</a> available for free in the member center to help you with many of the most commonly asked questions that new users have.</p>
          </div>
        </div>
        <div class="feature-section three-col">
          <div class="col">
            <h3><span class="dashicons dashicons-admin-page"></span> Child Themes</h3>
            <p>If you're planning on doing any amount of customization outside of the settings afforded in the Customizer, <a href="https://community.theme.co/child-theme/" target="_blank">child themes</a> are absolutely the way to go.</p>
            <p>We provide a child theme for X in our community center, which comes bootstrapped with all of the necessary code so that it can be used right out of the box!</p>
          </div>
          <div class="col">
            <h3><span class="dashicons dashicons-media-archive"></span> Demo Content</h3>
            <p>Recreate your favorite looks from our demos with a few simple clicks. <a href="<?php echo x_addons_get_link_demo_content(); ?>" target="_blank">Demo content</a> is available to help you achieve this in no time at all. This includes WordPress import data, markup, Customizer import data, and more! You can read about demo content <a href="https://community.theme.co/kb/demo-content/" target="_blank">here</a> in the Knowledge Base.</p>
          </div>
          <div class="col">
            <h3><span class="dashicons dashicons-update"></span> Superior Support</h3>
            <p>Got a question? We're here to help! The member center is where we handle all official support via our <a href="https://community.theme.co/forums/group/support-center/" target="_blank">forums</a>.</p>
            <p>If you haven't done so already, make sure to <a href="https://theme.co/go/register.php" target="_blank">register for access</a> to the member center to gain access to all of our great resources.</p>
          </div>
        </div>
      </div>

    <?php else : ?>

      <div class="changelog">
        <div class="feature-section three-col">
          <div class="col">
            <h3><span class="dashicons dashicons-external"></span> Step 1: Register</h3>
            <p>To get started, <a href="https://theme.co/go/register.php" target="_blank">register for access</a> to the member center. This is where you'll have access to all of our resources including the Knowledge Base, support forums, and much more.</p>
          </div>
          <div class="col">
            <h3><span class="dashicons dashicons-admin-network"></span> Step 2: Get an API Key</h3>
            <p>Once you have registered for access to the member center, go generate your <a href="https://community.theme.co/my-account/" target="_blank">product API key</a> under the "Licenses" section. This key is used for automatic updates (<a href="https://community.theme.co/kb/product-validation/" target="_blank">tutorial here</a>).</p>
          </div>
          <div class="col">
            <h3><span class="dashicons dashicons-awards"></span> Step 3: Validate</h3>
            <p>After acquiring your product API key from the member center, enter it on the <a href="<?php echo x_addons_get_link_product_validation(); ?>">Product Validation</a> page to start receiving automatic updates once they're available.</p>
          </div>
        </div>
        <a href="https://theme.co/x/go/register.php" class="button button-primary x-addons-home-register-button" target="_blank">Register Now</a>
      </div>

    <?php endif; ?>

    <?php $output = ob_get_contents(); ob_end_clean(); ?>

    <?php echo apply_filters( 'x_addons_home_content', $output ); ?>

  </div>

<?php }