=== WP Testimonials with rotator widget ===
Contributors: wponlinesupport, anoopranawat 
Tags: testimonial, Testimonial, testimonials, Testimonials, widget,  Best testimonial slider, Responsive testimonial slider, client testimonial slider, easy testimonial slider, testimonials with widget, wordpress testimonial with widget, testimonial rotator, testimonial slider, Testimonial slider , testimonial with shortcode, client testimonial, client, customer, quote, shortcodes
Requires at least: 3.1
Tested up to: 4.5
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A quick, easy way to add and display responsive, clean client's testimonial on your website using a shortcode or a widget.

== Description ==
Many CMS site needs to display client's testimonial on their website. WP Testimonial Plugin with Widget allow you to add testimonial
from wp-admin side same like you add post, which allows you to display testimonials on your website the easy way.
You can quickly add your testimonials with their authors, jobs, pictures, pictures size, Website URL and Position.

**This testimonial plugin contain two shortcode**
<code>[sp_testimonials] and [sp_testimonials_slider]</code>
Where you can display testimonial in list view, in grid view and slider testimonial with responsive. You can also select design theme from "WP Testimonials -> Designs".

View [DEMO](http://wponlinesupport.com/wp-plugin/wp-testimonial-with-widget/) for additional information.

View [PRO DEMO](http://wponlinesupport.com/wp-plugin/wp-testimonial-with-widget/) for additional information.

This plugin creates a testimonial and a testimonial rotator/testimonial slider custom post type, complete with WordPress admin fields for adding testimonials. It includes a Widget and Shortcode to display the testimonials.

= Here is the Testimonial shortcode example =

<code>[sp_testimonials]</code>

If you want to display Testimonial by category then use this short code 
<code>[sp_testimonials  category="category_ID"]</code>

If you want to display Testimonial using slider then use this short code 
<code>[sp_testimonials_slider limit="2" slides_column="2"
 slides_scroll="2" dots="false" arrows="false" autoplay="true"
 autoplay_interval="100" speed="5000" ]</code>

= Shortcode Examples =

<code>
1. Simple list/Grid view
[sp_testimonials] OR [sp_testimonials per_row="2"]

2. Slider (per row one/per row two)
[sp_testimonials_slider] OR [sp_testimonials_slider slides_column="2"]</code>

= Use Following Testimonial parameters with shortcode =
<code>[sp_testimonials]</code>
* **limit:**
[sp_testimonials limit="5"] ( ie Display 5 testimonials on your website )
* **design:**
[sp_testimonials design="design-1"] ( ie Select the design for testimonial. Values are design-1, design-2, design-3, design-4 )
* **Grid:**
[sp_testimonials per_row="2"]( ie Display your testimonials by Grid view )
* **orderby:**
[sp_testimonials orderby="title"] ( ie Order your testimonials by "title" OR "post_date" OR "none" OR "name" OR "rand" OR "ID" )
* **order:**
[sp_testimonials order="ASC"] ( ie Order your testimonials by "ASC" OR "DESC" )
* **id:**
[sp_testimonials id="testimonail_id"] ( ie Display testimonials by their ID )
* **Display by category**
[sp_testimonials  category="category_ID"] ( ie Display testimonials by their category ID )
* **Display client:**
[sp_testimonials display_client="false"] ( Display Client name OR: You can use "true" OR "false")
* **Display job title:**
[sp_testimonials display_job="false"] ( Display Client job title : You can use "true" OR "false")
* **Display company name:**
[sp_testimonials display_company="false"] ( Display Client company name : You can use "true" OR "false")
* **Display avatar:**
[sp_testimonials display_avatar="false"] ( Display Client avatar : You can use "true" OR "false")
* **Avatar size and style:**
[sp_testimonials size="150" image_style="square"] (Set size of Client avatar and style - square, circle )
* **Display Quotes:**
[sp_testimonials display_quotes="false"] ( Display Quotes: You can use "true" OR "false")


= Use Following Testimonial Slider parameters with shortcode =
<code>[sp_testimonials_slider]</code>
* **Slide columns for testimonial rotator:**
[sp_testimonials_slider slides_column="2"] (Display no of columns in testimonial rotator )
* **design:**
[sp_testimonials_slider design="design-1"] ( ie Select the design for testimonial. Values are design-1, design-2, design-3, design-4 )
* **Number of testimonial slides at a time:**
[sp_testimonials_slider slides_scroll="2"] (Controls number of testimonial rotate at a time)
* **Pagination and arrows:**
[sp_testimonials_slider dots="false" arrows="false"]
* **Autoplay and Autoplay Interval:**
[sp_testimonials_slider autoplay="true" autoplay_interval="100"]
* **Testimonials Slide Speed:**
[sp_testimonials_slider speed="3000"]
* **limit:**
[sp_testimonials_slider limit="5"] ( ie Display 5 testimonials on your website )
* **orderby:**
[sp_testimonials_slider orderby="title"] (ie Order your testimonials by "title" OR "post_date" OR "none" OR "name" OR "rand" OR "ID" )
* **order:**
[sp_testimonials_slider order="ASC"] ( ie Order your testimonials by "ASC" OR "DESC" )
* **id:**
[sp_testimonials_slider id="testimonail_id"] ( ie Display testimonials by their ID )
* **Display  by category**
[sp_testimonials_slider  category="category_ID"] ( ie Display testimonials by their category ID )
* **Display client:**
[sp_testimonials_slider display_client="false"] ( Display Client name OR: You can use "true" OR "false")
* **Display job title:**
[sp_testimonials_slider display_job="false"] ( Display Client job title : You can use "true" OR "false")
* **Display company name:**
[sp_testimonials_slider display_company="false"] ( Display Client company name : You can use "true" OR "false")
* **Display avatar:**
[sp_testimonials_slider display_avatar="false"] ( Display Client avatar : You can use "true" OR "false")
* **Avatar size and style:**
[sp_testimonials_slider size="150" image_style="square"] (Set size of Client avatar and style - square, circle )
* **Display Quotes:**
[sp_testimonials display_quotes="false"] ( Display Quotes: You can use "true" OR "false")

= Here is Template code =
<code><?php echo do_shortcode('[sp_testimonials]'); ?> </code>
<code><?php echo do_shortcode('[sp_testimonials_slider]'); ?> </code>

= Available fields : =
* Title
* Testimonials Content
* Job Title
* Company
* Website URL
* Picture

= New Features include: =
* Added 4 New Designs.
* Display Testimonial categories wise.
* Display Testimonial on home page with limit <code>[sp_testimonials limit="1" ]</code> 
* Adding a Random Testimonial to Your Page.
* Responsive.
* Display testimonials using an easy testimonial widget.
* Add Client image.

= Pro Features include: =
> <strong>Premium Version</strong><br>
>
> * Added 15 New Designs.
> * Testimonial front-end form.
> * Star rating
> * Display testimonials using 15 testimonial widget designs.
> * Display Testimonial categories wise.
>
> View [PRO DEMO](http://wponlinesupport.com/wp-plugin/wp-testimonial-with-widget/) for additional information.
>

= Why Use Testimonials? =
* The web has made it easier for consumers to get recommendations not only from friends, but to see secure, verified Testimonial from people all over the world.
* Testimonials help potential customers get to know that you are a credible business.
* Testimonials, when used effectively, are a great tool to increase conversions rates on your website!




== Installation ==

1. Upload the 'WP Testimonial Plugin with Widget' folder to the '/wp-content/plugins/' directory.
2. Activate the "WP Testimonial Plugin with Widget" list plugin through the 'Plugins' menu in WordPress.
3. Add a new page and add this short code 
<code>[sp_testimonials]</code>
4. If you want to display Testimonial using slider then use this short code 
<code>[sp_testimonials_slider]</code>
5. Here is Template code 
<code><?php echo do_shortcode('[sp_testimonials]'); ?> </code>
6. If you want to display Testimonial using slider then use this template code
<code><?php echo do_shortcode('[sp_testimonials_slider]'); ?> </code>

= How to install : =
[youtube https://www.youtube.com/watch?v=gUIp0rCNsHg]  

== Screenshots ==

1. Simple list view
2. Grid view
3. Slider (per row one)
4. Slider (per row two)
5. all Testimonials
6. Creating testimonials (admin view)
7. Widget Setting


== Changelog ==

= 2.2.4 =
* Fixed some css issues.

= 2.2.3 =
* Fixed some css issues.
* Resolved multiple slider jquery conflict issue.

= 2.2.2 =
* Added 'display_quotes' parameter in shortcode to show and hide emphasized tag.

= 2.2.1 =
* Fixed some bug
* Removed 'display_url' and added as a href link.
* Added 15 Pro Designs

= 2.2 =
* Fixed some bug

= 2.1 =
* Fixed some css bug and designs
* Display more then 1 testimonial post in widget


= 2.0.1 =
* Fixed some css bug and designs
* Display more then 1 testimonial post in widget


= 2.0 =
* Added 4 themes designs
* Removed radio button to select the theme.
* Just use the shortcode with design parameters ie design="design-1"

= 1.1.1 =
* Added 2 themes designs

= 1.1 =
* Added new Testimonial slider parameters ie autoplay, autoplayInterval, speed.

= 1.0 =
* Initial release.
* Adds custom post type.


== Upgrade Notice ==

= 2.2.4 =
* Fixed some css issues.

= 2.2.3 =
* Fixed some css issues.
* Resolved multiple slider jquery conflict issue.

= 2.2.2 =
* Added 'display_quotes' parameter in shortcode to show and hide emphasized tag.

= 2.2.1 =
* Fixed some bug
* Removed 'display_url' and added as a href link.
* Added 15 Pro Designs

= 2.2 =
* Fixed some bug

= 2.1 =
* Fixed some css bug and designs
* Display more then 1 testimonial post in widget


= 2.0.1 =
* Fixed some css bug and designs
* Display more then 1 testimonial post in widget

= 2.0 =
* Added 4 themes designs
* Removed radio button to select the theme.
* Just use the shortcode with design parameters ie design="design-1"

= 1.1.1 =
* Added 2 themes designs

= 1.1 =
* Added new Testimonial slider parameters ie autoplay, autoplayInterval, speed.

= 1.0 =
* Initial release
