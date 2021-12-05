**v1.3.4** (13 Aug 2021)  
[new] Add compatibility with WooCommerce Currency Switcher (WOOCS) by realmag777  
[update] `Linked Variations` plugin can use attribute groups now  
[update] Update dependencies  
[fix] Compatibility with WooCommerce Product Bundles by Somewherewarm  
[fix] Fix attribute fees not updating issue  
[fix] Fix non-numeric value encountered warning  
[fix] Escape quotes in JS when changing selected label  
[fix] Fix tooltip issues on iOS devices  
[fix] Fix invalid characters message when no color assigned to swatch  
[fix] Disable overflow effects on product page for radio buttons  
[fix] Ensure visual attributes aren't given default values (attribute name)  


**v1.3.3** (20 Apr 2021)  
[update] Remove group label in single line view  
[update] Compatibility with OceanWP theme product filters  
[update] Added grouped attributes to filter widgets  
[update] Compatibility with GeneratePress theme  
[update] Loco translate compatibility  
[update] Update dependencies  
[fix] Warning in_array warning in archive page  
[fix] Fix issue with similar attribute names  
[fix] Compatibility with 3rd-party quickview plugins  
[fix] Expand self-closing jQuery elements, e.g. <div/>  
[fix] Update tooltips to work if swatch is loaded dynamically  
[fix] Prevent error when default attribute value contains spaces  

**v1.3.2** (23 Nov 2020)  
[fix] Fixed fees not working bug  

**v1.3.1** (18 Nov 2020)  
[update] Update dependencies  
[fix] Ensure fcms_was_disable_fees runs at the right time  
[fix] Ensure attribute labels are only modified in the variations table  
[fix] Fix tooltip image alignment issue  
[fix] Only add "From:" price label if product has fees  

**v1.3.0** (28 Oct 2020)  
[new] `fcms_was_catalog_swatches` shortcode for displaying the shop/catalog swatches  
[new] Accordion Feature  
[new] Overflow feature - slider, "more", and stacked  
[new] Add global fee field for attributes  
[update] Offset tooltips when on the screen edge  
[update] Add `fcms_was_disable_fees` filter to disable fee hooks  
[update] Allow fees to be set to 0 for product attribute  
[update] Improved swatch tooltips - no layering issues  
[update] Allow float values in fee field (i.e. 2.50)  
[update] Compatibility with WooCommerce Currency Switcher (WOOCS)  
[update] Added a new class on single product page suggesting swatches status  
[update] Compatibility with Product Bundles by SomewhereWarm  
[update] Hide group label when no terms are available for it  
[update] Update POT file  
[update] Update dependencies  
[fix] Don't show backorder products as out of stock  
[fix] Add fees to dropdowns for product attributes  
[fix] Firefox page reload swatch state issue  
[fix] Swatch fees showing "0" when no fee set  
[fix] Fix price with fees in mini-cart  

**v1.2.7** (23 Apr 2020)  
[update] Compatibility with "Smart Variation Images"  
[update] Compatibility with FCMS Sales Booster  
[update] Update dependencies  
[update] Change default swatch style to "border"  
[fix] Handle custom attributes (fixes array_diff() error)  
[fix] Swatches showing square rather than round  
[fix] Rounding precision issue when using attribute fees  
[fix] Persistent Object Cache issue  

**v1.2.6** (18 Mar 2020)  
[update] Version compatibility  

**v1.2.5** (28 Oct 2019)  
[update] Add out of stock class to swatch list item (so it can be hidden or styled)  
[fix] Ensure attributes set to "any" are counted as available  

**v1.2.4** (23 Oct 2019)  
[update] Improve performance when loading swatches in the catalog  
[update] Changed tick icon to embedded fontawesome svg (https://fontawesome.com/license)  
[update] Compatibility with OceanWP Theme  
[update] Update dependencies  
[fix] Ensure unavailable swatches are not shown in the catalog  
[fix] Mark out of stock variations as disabled in the catalog  
[fix] Check for product before adding fee to attribute label (fixes issue with Jilt)  
[fix] Deselect unavailable attribute automatically  

**v1.2.3** (30 July 2019)  
[update] Change fee calculation method for better compatibility  
[update] Ensure variation sale price is formatted correctly  
[fix] Prevent double tap on ios  
[fix] Slow query when fetching first variation for an attribute value (archives)  
[fix] When adding fee to swatch label, check product is not a string (issue with Jilt compatibility)  

**v1.2.2** (1 July 2019)  
[fix] Freemius fix  

**v1.2.1** (23 Apr 2019)  
[update] Don't show attribute fees for admin labels  
[update] Allow swatches to be positioned in ajax based results  
[update] Deselect unavailable attributes on page load  
[fix] Attributes not showing in Swatches tab  
[fix] Issue where new product page was not loading  

**v1.2.0** (18 Apr 2019)  
[new] Ability to add fees to each attribute option  
[update] Compatibility with Woo 3.6.1  
[update] Update dependencies  
[update] Use CRUD for product meta  
[fix] Stop layered nav swatches being replaced by single product settings  

**v1.1.4** (2 Mar 2019)  
[fix] Security Fix  

**v1.1.3** (10 Jan 2019)  
[new] Show swatches in layered nav filters  
[update] Update dependencies  
[update] Allow swatches to be hidden in loop per product via `fcms_was_hide_loop_swatches` filter  
[fix] Sometimes the product image is not updated when clicking on swatches in the loop  
[fix] SSV compatibility. The context switch wasn't restored at the right place on the product loop when product had multiple attributes  
[fix] Sometimes attributes didn't appear in the loop  
[fix] The swatches in the loop didn't follow the custom terms order set in the backend  

**v1.1.2** (10 Sep 2018)  
[update] implement FCMS core classes  
[update] Allow swatches to be displayed under variation products on the catalog  
[fix] Issue with product specific visual swatches saving  
[fix] Use `jQuery` instead of `$` when editing attributes  
[fix] Change method of selecting value in select field  

**v1.1.1** (15 Jun 2018)  
[fix] Attributes not selected when over AJAX variation threshold  

**v1.1.0** (14 Jun 2018)  
[update] Add WPML config settings  
[update] Flatsome compatibility helpers  
[update] Add POT file  
[update] Better conditional fields in admin  
[update] Add Woocommerce Variations Table - Grid compatibility  
[update] Freemius  
[update] Optimize catalog swatches  
[update] Hide unavailable catalog swatches  
[update] New style options for visual swatches  
[update] Add ability to group swatches by label  
[update] Add filters to modify default swatch sizes `fcms_was_default_swatch_size`  
[fix] Product specific attribute swatches not showing in catalog  
[fix] Double tap selection issue on touch devices  
[fix] Attribute order for composite products  
[fix] Image switch size  
[fix] Some styling issues in the product edit panel  
[fix] Don't add swatches in catalog if $product is false  
[fix] Output of swatch options when creating a global attribute  
[fix] Don't remove current values when changing swatch type  
[fix] Ensure "disabled" options are greyed out  

**v1.0.10** (07/10/2017)  
[update] Freemius

**v1.0.9** (13/09/2017)  
[update] Re-enable product-level swatch catalog settings  
[update] Allow custom swatch sizes  
[fix] Escape double quotes in swatch data attributes  
[fix] Some performance updates

**v1.0.8** (06/08/2017)  
[fix] wp_mail issue

**v1.0.7** (02/08/2017)  
[update] Code tidy  
[update] Add filter for loop position priority  
[update] Add new licensing system  
[fix] Make sure chosen attribute span is more specific  
[fix] Product tab formatting  
[fix] Deprecated action  
[fix] Make sure swatches are positioned on 'init'


**v1.0.6** (02/04/2017)  
[update] WooCommerce 3.0.0 compatibility  
[update] Made "disabled" swatch styling more apparent  
[update] Add attribute data to swatch data for filters  
[update] Improve load time in archive  
[update] Allow large preview without enabling tooltips  
[fix] Undefined index error

**v1.0.5** (21/12/2016)  
[update] Show available options on hover  
[update] Remove slug_alt variable  
[update] Atelier compatibility  
[update] Added filters to the modify_attribute_html function  
[update] Remove dashboard  
[update] Add option to change image when swatch is clicked in catalog view  
[fix] Remove max-width on preview image

**v1.0.4** (18/07/2016)  
[update] Add large preview option to tooltip  
[fix] Compatibility with Shop the Look plugin

**v1.0.2** (21/06/2016)  
[fix] Fix admin product tab when attributes do not exist  
[fix] Fix label in admin variations tab  
[update] Ability to show swatches in the catalog listing, they also click through to select that option.

**v1.0.1** (19/06/2016)  
[update] Add swatch options title to term edit page
[fix] Remove license key page
[update] Add check for WooCommerce plugin
[update] Allow disabled swatches to be clicked - refresh selection
[fix] Stop tooltip text wrapping
[fix] Correct attribute term ordering
[update] Compatibility with WooCommerce Quickview by FCMS
[update] modify labels for normal select fields

**v1.0.0** (16/05/2016)  
Initial release
