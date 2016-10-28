=== WooCommerce German Market ===
Contributors: @inpsyde, @apeiffer, @Chrico, @glueckpress, @nullbytes, @danwit, @jjoeris, @seville76, @bueltge, @dasllama, @eteubert
Tags: online shop, german, language, legal disclaimer, ecommerce, e-commerce, commerce, woothemes, wordpress ecommerce, affiliate, store, sales, sell, shop, shopping, cart, checkout, configurable, variable,  widgets, reports, download, downloadable, digital, inventory, stock, reports, shipping, tax
Requires at least: 3.8
Tested up to: 4.1.1
Stable tag: 2.4.11

WooCommerce Plugin für Features und Rechtsicherheit für den deutschsprachigen Raum

== Description ==

Dieses Plugin erweitert das beliebte Shop Plugin WooCommerce ( http://www.woothemes.com/woocommerce/ ) derart, dass es rechtssicher in Deutschland und Österreich eingesetzt werden kann.
Es enthält Templates für AGB, Bestellvorgang, Datenschutz, Impressum, Versandkosten, Widerrufsbelehrung, Widerrufsbelehrung Österreich, Zahlungsarten, welche bei der Installation direkt angelegt werden können.

= Features =

* Templates für vorgeschriebene Seiten für einen Webshop
* Zweite Kassenseite, welche alle Daten vor der endgültigen Bestellung noch ein mal anzeigt.
* Verandkostenhinweis beim Produkt
* Lieferzeiten für Produkte
* Widerrufsfrist konfigurierbar
* Standardeinstellungen für Deutschland (Sprache, Mehrwertsteuersätze, Maße und Gewichte, Dezimaltrennzeichen, etc. ...)
* alternative Installation der österreichischen Widerrufsbelehrung
* definieren von Preis Pro Einheit für jedes Produkt
* Lieferzeiten Global oder pro Produkt, bzw. keine Lieferzeit bei Downloads, etc.
* Versandkostenhinweis für Produkte unterdrücken (z.B. Downloads )
* Spezielles Per Nachnahme Plugin, welches auch Nachnahmegebühren in die Kalkulation aufnimmt (ersetzt das Mitgelieferte Per Nachnahme Plugin von Wooocommerce )


= Hinweise =

*WICHTIG - HAFTUNGSAUSSCHLUSS*
Dieses Plugin trägt wesentlich dazu bei, ihren WooCommerce Shop rechtssicher betreiben zu können. Es ist durch einen Rechtsanwalt geprüft worden, der ebenfalls die rechtlichen Mustertexte wie AGB und die Widerrufsbelehrung erstellt hat. Sie entsprechen den rechtlichen Anforderungen eines typischen, an Verbraucher gerichteten Onlineshops. Jedoch kann die rechtliche Sicherheit eines Onlineshops nur im Einzelfall geprüft und bestätigt werden. Daher sind die rechtlichen Muster nur als Vorlagen zu verstehen, deren abschließende rechtliche Prüfung und ggf. Anpassung Ihnen obliegt. Falls Sie eine Prüfung Ihres Shops wünschen, können Sie sich an Rechtsanwalt Thomas Schwenke, LL.M. <http://rechtsanwalt-schwenke.de/kontakt> wenden.

Bitte beachten Sie, dass Preise im Backend immer mit `.` als Dezimaltrennzeichen eingegeben werden müssen.
Siehe: https://github.com/woothemes/woocommerce/issues/733
`The catalog options are for display only - prices need to be entered into the backend as decimals. i.e. 10.23`

== Installation ==
= Requirements =
* WordPress 3.8*
* PHP 5.2*
* WooCommerce 2.3*

= Installation =

 * Installieren Sie zuerst WooCommerce
 * Installieren Sie die Standardseiten für WooCommerce (Folgen Sie dazu der Installationsroutine von Wooocommerce)
 * Benutzen Sie den installer im Backend, oder

1. Entpacken sie das zip-Archiv
2. Laden sie es in das `/wp-content/plugins/` Verzeichnis ihrer WordPress Installation auf ihrem Webserver
3. Aktivieren Sie das Plugin über das 'Plugins' Menü in WordPress und drücken Sie aktivieren
4. Folgen Sie den Anweisungen des Installationsbildschirms


== Screenshots ==
1. Legen Sie die Grundeinstellungen fest, und klicken Sie auf Installieren
2. Wenn die Installation erfolgreich war sehen Sie folgenden Meldung
3. Hier können Sie alle Einstellungen Vornehmen, welche die deutschsprachige Version betreffen
4. Hier können Sie weitere Maßeinheiten definieren, und bearbeiten, welche bei Preis Pro Einheit ausgewählt werden können
5. Hier können Sie definieren, welche Lieferzeit das aktuelle Produkt haben soll, oder ob der in 4. definierte genommen werden soll. Sollte es für das Produkt keine Lieferzeit geben, so können Sie hier einen Text hinterlegen, der dem Benutzer angezeigt werden soll.
6. Hier können Sie definieren, ob der Versandkostenhinweis angezeigt werden soll oder nicht (z.B.: bei Downloads, die sofort ausgeliefert werden).
7. Hier können Sie den Preis Pro Einheit definieren. Dieser wird nicht in die Berechnung einbezogen und dient lediglich der Anzeige auf den jeweiligen Produktseiten.


== Other Notes ==
= Acknowledgements =

Thanks Mike Jolley (http://mikejolley.com/) for supporting us with the WooCommerce core.

= Licence =

 GPL Version 3

= Translations =


== Changelog ==

= 2.4.11 =

- Fixed a visual bug with duplicated items in cart
- Removed CSS class `second-checkout-button-container` in favor of `wc-proceed-to-checkout wgm-proceed-to-checkout`
- Removed no longer needed CSS declarations in frontend.css
- Removed no longer needed JS function call (left function in place for legacy)
- Updated button CSS classes to match default WooCommerce CSS classes
- Updated cart-totals.php template to make use of `wc_cart_totals_coupon_label()` and `wc_cart_totals_taxes_total_html()`
- Added body class `woocommerce` to be applied to second checkout page in order to match default WC button styles
- Added CSS classes `wgm-place-order`, `wgm-go-back-button` and `checkout-button` to second-checkout2.php template

= 2.4.10 =
- WGM now uses the Woocommerce Geolocate feature to determine the displayed tax rates if the option is enabled
- Added "Cash on delivery" for virtual products to the cash on delivery gateway
- Updated Autoupdater
- Changed from WC_Customer to WC_Order to retrive customer location in orders
- Removed selectable shipping methods in second checkout and only display chonsen one
- Added second checkout to woocommerce exclude from caching transient
- Removed shipping methods from checkout when the cart only contains digital products.
- Updated translation
- Fixed typos

= 2.4.9 =
- Fixed deprecated function calls from cart->tax to WC_Tax
- Fixed a typo in WGM_Settings
- Removed old and no longer needed review-order.php template

= 2.4.8 =
 - Fixed an issue with the Amazon Payments gateway
 - Fixed an issue where tax was worngly calculated when using a coupon
 - Fixed an issue where shipping costs for variable products in small business regulation shops would not be displayed
 - Fixed an issue where with excluding tax option active the price would not be displayed
 - Fixed an issue where taxes would be worngly calculated when a reduced digital products was in cart
 - Fixed an issue where no further text could be inserted before or after the woocommerce_de_check shortcode
 - Fixed a division by zero
 - Fixed typos
 - Changed button text on first checkout page when using paypal to "next" from "pay with paypal"
 - Added one decimal place to tax rate display
 - Updated translation
 - Added a wraping div to mails

= 2.4.7 =
- added new filters `wgm_get_split_tax_html` and `wgm_get_excl_incl_tax_string`
- improved support for coupons during split tax calculations for shipping costs and fees
- fixed a bug during in cart view where shipping costs would display net values before switching to gross values when checked
- fixed a bug during in cart view where taxes for shipping costs would be duplicated when added to total amount
- fixed a bug during order editing in the back-end where WGM’s calculations for split taxes on shipping costs and fees would not be applied

= 2.4.6 =
- added a missing argument to WGM_Template::checkout_readonly_field()
- added displaying of fee taxes for COD on thank-you page
- added legal notice for store owners to text templates
- improved formating of text templates, fixed corrupted HTML tag
- improved tax calculation for shipping fees and COD
- fixed a PHP notice at order-pay endpoint
- fixed a bug that resulted in zero values for taxes in invoice e-mails
- fixed obsolete displaying of fee taxes when small business option was enabled
- fixed a bug that resulted in falsified tax calculations for checkout totals
- fixed a bug that resulted in missing tax columns for orders in the back-end

= 2.4.5 =
- Fixed an fatal error when sending invoices from the backend
- Fixed wrong tax display for cash on delivery
- Fixed undefined offset notice for additional services tax calculation

= 2.4.4 =
- fixed wrong use of woo translation string
- fixed cod gateway and fee display
- show variation price in oderlisting
- added paypal return URL fix
- added new calculation for shipping and COD costs
- removed unnecessary expression
- replaced all , with . for wc_price in price per unit output
- updated translation

= 2.4.3 =
- Fixed notice in cart
- Fixed wording and punctuation
- Added Compability for Woo 2.2
- Removed strict warnings
- Updated updater
- Removed notice in mail
- Added target _blank to disclaimer line links

= 2.4.2 =
- Added filter for checkout checbox lables and texts
- Remmoved double shipping cost notice for variations with the same price
- Fixed digital notice in outgoing mails and paypal

= 2.4.1 =
- Added a Filter for HTML digital notice and the digital keyword itself respectively
- Added prerequists for virtual variable products
- Added missing CSS class prefix
- Replaced old "woocommerce_order_item_title" with new hook
- Changed COD Gateway to use the Woocommerce fees api
- Fixed worngly used hook "woocommerce_order_actions" to "woocommerce_order_actions_start"
- Fixed digital notice HTML output for all completed order tasks (e.g. Paypal payments etc)
- Fixed doubled displayed shipping costs notice and missing tax notice on variable products
- Fixed various typos
- Fixed some internal filters
- Fixed an issue where the updater would show an update notice for an old version of the plugin
- Fixed an Issue where the wrong cancelation policy notice would be shown at checkout
- Fixed an display issue with virtual product prerequists
- Fixed multiple display of shipping costs on product page
- Fixed estimate cart option
- Fixed an undefined variable error
- Fixed strict standard notices
- Fixed missing digital notice in cart widget
- Fixed various php notices
- Fixed diliverytime display with virutal products
- Fixed false prositive digital products in cart
- Fixed dislay for digital variation prerequists in checkout
- Fixed supress shipping product option
- Cleand up old code
- Replaced old deprecated functions with new ones
- Updated translation

= 2.4 =
- made software compliant with new german online sales rights (13.6.2014)
- added new text templates
- updated old text templates
- upadted translation
- removed some options
- updated mail
- added new product field for requirements for digital products
- tax and delivery notice is now mandatory
- new customer acknowledgement for digital products
- some cleanup

= 2.3.6 =
- info release, no fixes or features

= 2.3.5 =
- added a body class for second checkout
- fixed doubled ids in second checkout
- added css class for place order button in second checkout
- minor cleanups
- fixed wrongly displayed deliveryadress in second checkout
- translated licence key messgae
- minor html fixes

= 2.3.4 =
- fixed an serious javascript issue

= 2.3.3 =
- fixed various issues with WPML and WGM ajax calls

= 2.3.2 =
- Added support for WPML
- Added compability for WooCommerce 2.1.6
- added css class for variation price infos
- added various filter and actions for manipulating wgm's markup output
- added css class for loding animation
- fixed variation price display with identical prices
- fixed various typos
- fixed various translations
- fixed broken markup
- hide default price on variation single product page when variation price is loaded
- fixed static asset loading
- fixed free shipping label display to use user entered instead of default label
- fixed tax display with empty values in conjunction with small business regulation
- fixed doulbe tax loading with variation products
- fixed cash on delivery display on certain pages
- fixed missing row in cart
- fixed various issues with coupons
- fixed a typo in a setting id
- fixed various issues with checkout validation
- fixed wgm's default tax rates
- fixed missing shortcode in english installations
- updated documentation
- fixed wgm cash on delivery gateway
- added html markup to checkout readonly fields
- removed default woo tax notices
- added english translation for tax rate import

= 2.3.1 =
- fixed variation frontend javascript
- added plugin version function
- fixed body classes
- updated woocommerce templates
- fixed typo in cart template
- updated deprecrated parameter in checkout template
- fixed filter for WGM_Template::add_mwst_rate_to_product_item
- fixed typos ins translation
- fixed price per unit currency smybol usage
- fixed wrong option for price per unit
- changed additional vat string for better context
- fixed tax display for shipping with no costs
- various minor compatibility fixes

= 2.3 =
- updated hooks and functions for WooCommerce 2.1
- added filter for unexpected extra EU costs
- added filter for product short desc
- added filter for payment method in mails
- added filter for disclaimer
- added filter for small business regulation text
- added filter for small business regulation review text
- added filter for for after cart totals
- fixed typos
- implemented new template loadding system (templates can now be overwritten like woo templates)
- fixed check if woocommerce is active
- cleaned up css
- updated woocommerce templates
- fixed shipping calculation in second checkout
- fixed some installation errors
- added option for dual shipping methods
- fixed a warning in checkout with some particular vat options
- adjusted html output
- fixed an issue with taxes in product variations
- fixed some minor stability issues
- fixed some translation errors

= 2.2.6 =
- Added warning message

= 2.2.5 =
- Added Filter for small buissiness regulation texts
- Remove hardcoded 'approx' from delivery times
- Removed no deliverytime text field
- Added filter for default deliverytimes
- Extends Checkout SSL to all WGM Checkout sites
- Removed Deliverytimes from quickedit
- Changed deliverytimes from indecies to term id
- fixed price per unit display
- updated translation
- fixed some woocommrece incompabilities
- updated settings desctiptions
- some minor display fixes
- fixed some PHP5.4 warnings
- fixed some issues with wrong delivery times
- fixed an error which would not display price per unit attributs in dropdown menus
- fixed a missing currency symbol in mails
- updated translation
- fixed an superfluous whitespace in small bussiness regulations text filter
- removed wrong post count form deliverytime editor
- moved price per unit display under actual price
- added price per unit filter and before and after actions

= 2.2.4 =
- tax does not anymore depend on country when sending a mail manually through the backend

= 2.2.3 =
- fixed a bug where delivery times would not be set correctly after update
- product short desc on second checkout now hides properly
- fixed translation
- replaced a depricated jquery function .live with .on
- fixed deliverytimes quick edit
- fixed strict error message
- fixed wrongly stacked html
- tax rates are matching the country you selected on checkout
- removed debug output
- added css class for product option panales
- "approx" deos not display before "not available at the moment"
- fixed wrong tax calculation when using a coupon

= 2.2.1 =
- fixed wrong delivery times after upgrade
- fixed translation
- removed deliverytimes from quickedit

= 2.2 =
- Added small business regulation according to §19 UStG
- Fixed a bug where VAT didn't show up in cart
- Added compability for new woocommerce releases
- added dliverytime editor
- fixed some style issues
- fixed plugin activation notice
- removed style recommendation box on settings page
- CSS and JS only now only loading when needed
- fixed colspan in some tables
- fixed free shipping when free shipping amount is reached
- updated deliverytime wording
- updated leagal templates
- updated translation
- fixed wrongly rounded VAT displays
- fixed some issues with WPML
- fixed mails to not allways include legal stuff in the footer
- VAT can now be entered with . or ,
- fixed VAT percent displayed
- fixed typos
- fixed some minor display issues
- added support for a wider range of themes
- fixed a warning occurring when doing a search

= 2.1.6 =
- sperated price per unit html from data
- added some html elements for styling to second checkout
- updated updater

= 2.1.5 =
- templates clean up
- added function Woocommerce_German_Market::is_wgm_checkout() to check if the called site is the second checkout page
- fixed some minor issues
- moved inline css to css file
- fixed some css issues

= 2.1.4 =
- Fixed VAT Display in cart

= 2.1.3 =
- Added filter into cart template
- Fixed sorting order of delivery time terms

= 2.1.2 =
- changed array_map function for translated terms into static function

= 2.1.1 =
- Fixed fatal error in PHP 5.2

= 2.1 =
- Fixed a conflict with WPML
- Added product short descriptions to the product in checkout and order listing
- Fixed an issue where the VAT dind't display properly
- Fixed a conflict with Role Based Prices
- Added an option to display a hint text for extra costs when shipping in non eu contries
- Shippingcosts no longer displayed when the limit for free shipping is reached
- Removed activation.css from Frontend
- Added option for 'Free Shipping'
- Show terms in default selection
- Fixed various translation issues
- Added a Deliverytime Editor
- Fixed some typos
- Fixed various display issues
- Fixed various behaviour issues
- Fixed various javascript issues

= 2.0.2 =
- Fixed array to string conversion
- Added filter for colspan in cart
- Removed superfluous td tag
- added filter for additional tax strings
- changed prices tag method in additional tax display
- Fixed COD Payment gateway

= 2.0.1 =
- Fixed array to string conversion
- Fixed js payment gateway selection
- Fixed bodyclasses for new checkoutpages

= 2.0 =
- Added Support for Woocommerce 2.0+
- Refactoring, splitted codebase into seperate classes
- Removed no longer needed functions
- Added documentation to all methods
- Added various hooks for thrid party developers
- Added Class Autoloaded
- Changed Textdomain loading
- Updated Templates
- Improved Template loading
- Improved Cash on delivery Gateway
- Improved second Checkout page
- Fixed verious loading issues
- Replaced Emailtemplates with a hook
- Updated Translation
- Updated all woocommerce API calls
- Fixed various issues regarding the english version
- Fixed an issue where the terms page was not assined correctly
- Fixed an issue where the error notice when woocommerce is not installed didn't disapear after a reload
- Fixed an issue with the shipping costs
- Fixed an issue where the revoction page had no content
- Fixed an issue where the tax didn't got correctly assigned by the installtion routine
- Fixed an issue where taxes dindt got displayed properly
- Fixes various display issues
- Fixed an issue where paged coulnd't be saved in the en_US version
- Fixed an issue where the plugin didnt deactivated it self when the wrong version of woocommerce was instlled
- Fixed various corrupted or broken options
- Fixed price per unit handling and display
- Fixed various typos
- Fixed various minor things

= 1.1.4 =
- JS only listens on radio buttons on first checkout page
- All numbers are now formatted properly
- Exclusive tax in cart are now displayed properly
- Removed tax from items order table for cleaner view
- Custom delivery strings now obey the "delivery time in overview" option
- Fixed variation prices in mails

= 1.1.3 =
- Fixed PHP 5.4 Warnings
- Checkout Page dropdown now gets dispayed and saved into the database
- Fixed Shipping text position
- Added a new distinctive css class for the buy button
- Added a hint fild in the options which gets displayed on the last checkout page
- Some CSS fixes
- Some rearangements on the last checkout page
- Updated translation

= 1.1.2 =
- Added English localization
- Added missing english translations
- Conditions can now be turned off
- Updated depricated functions
- Add fresh styled Setup-Message
- Add english versions of page templates like imprint, terms, Method of payment ...
- Support english for sample page localizazion and Messages
- Updated Mailtemplates
- Fixed wrong Shippingcost calculations
- Notices displayed on the cart page can now be turned off
- Fixed Spelling mistakes
- Fixed some display errors on the checkout page

= 1.1.1 =
- Fixed several issues in autoupdater
- Fixed issue with facebook plugins
- separate wording for "Widerruf" checkbox
- delivery time: rename "day" to "business day"
- new template loading mechanism
- new default mail templates
- enhanced cart
- changed some translations
- fixed numberformarts in various areas
- some internal changes
- taxes now displayed properly on every listing
- various typo fixes

= 1.1 =
- placed next button in the right order
- fixed baseprice display
- fixed tax display
- fixed autoupdater

= 1.0.10 =
- include shipping in second checkout
- extended price per unit meassure
- new custom review order template with taxes
- license code dosn't show up in frontend anymore when defined in the config
- added filter for second checkout buy button text
- fixed mail bug
- fixed notice

= 1.0.9 =
- Terms page has now the correct content
- Cash on delivery is now properly displayed in mails
- delivery cost link is now longer splitted into 2 parts

= 1.0.8 =
- New option for displaying delivery costs seperatly in product overview and detailview
- Removed install notice if german marekt was previously installed

= 1.0.7 =
- CSS Fixes
- TOS and Widerrufsbelehrung on first checkout are now links only
- Deliverytime and Shipping costs are now displayed properly
- Removed no longer needed files
- Refactored roduct display
- Base prices are now properly displayed in the product overview
- Autoupdater: use site_option instead of get_option
- Move shippingcosts and tax rate closer to pricetag

= 1.0.6 =
- Updated Auto Updater (pro-only)

= 1.0.5 =
- Moved Paymentmethod above the product list on the checkout page

= 1.0.4 =
- Fixed update process

= 1.0.3 =
- Fixed delivery adress on checkout page
- price incl tax on single product page is now unter the title
- Fixed bug which caused problems for the plugin update mechanism
- small changes on the checkout page for the "Button-Lösung"
- added changelog

= 1.0.2 =
- Changes for the "Button-Lösung"
- Short description under the product on the checkout page
- various CSS fixes
- spelling fixes

= 1.0.1 =
- compatibility changes for Woocommerce 1.6.1
- localization
- various CSS fixes

= 1.0  =
- Inital Release