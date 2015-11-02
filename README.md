# MLA-filterable-gallery
An extension to the wonderful Media Library Assistant plugin for WordPress, extending the gallery to add front-end filtration.

This started life as functionality within a custom theme for a nonprofit who wanted a filterable photo gallery. 
The existing plugins I found each left something to be desired - no filtration, no responsiveness, awkward interface, etc - 
so I decided to piggyback onto Media Library Assistant's taxonomies and gallery functionality with a gallery that could also be
maintained in the same location as a photo slider featured elsewhere on the site. That's all explained on my blog: 
http://www.rweber.net/tag/wordpress-gallery/

This project is a way for me to learn about making WordPress plugins, while separating this functionality from the theme 
for easier reuse (it really doesn't belong in a theme anyway). I'm going to add options to the shortcode for default albums 
(i.e. attachment categories) and whatever else seems appropriate, and options to the back end for style, or at
least color scheme.

Changelog:

2-11-15: Shortcode allows top level categories to be sorted in ascending or descending order in menu.

Initial commit: Shortcode allows setting category to show when page is loaded; page in admin giving syntax.
