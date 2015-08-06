=== Speed Contact Bar ===
Contributors: Hinjiriyo, allamoda07
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=TPCX6FVZ5NSJ6
Tags: address, bottom, cell phone, contact, contacts, e-mail, email, facebook, flickr, g+, google plus, google+, icons, imdb, instagram, link, linkedin, number, phone, pinterest, position, responsive, slideshare, social media, telephone, top, transparent, tumblr, twitter, url, vimeo, web, xing, yelp, youtube
Requires at least: 3.5
Tested up to: 4.2.4
Stable tag: 4.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Let your website visitors get in touch with you easily with permanent visible contact informations

== Description ==

= Quick contact to you =

Speed Contact Bar enables your visitors to get in contact with you quickly. The plugin shows a colored bar with your contact data and social media URLs on the top of every page of your website. 

The bar design is responsive and thus ready for smartphones and tablets. The social media icons are included already.

Each icon appears if you set its target URL in the option page. The phone icons and numbers are clickable to trigger a phone call. The e-mail is clickable to open a mail client.

= What users said =

* **Number 8** in [Top 9 WordPress Free Contact Forms](http://www.thenightmarketer.com/wordpress/top-9-wordpress-free-contact-forms/) by The Night Marketer on May 29, 2015
* **"Great plugin to add contact info to the top bar."** in [WordPress.org Reviews](https://wordpress.org/support/view/plugin-reviews/speed-contact-bar) by mnbehrens on May 27, 2015
* **Number 23** in [24 Best Free & Premium WordPress Notification Bars Plugin 2015](http://www.frip.in/best-premium-free-wordpress-notification-bars/) by Himanshu on May 8, 2015
* **"Absolutely fantastic. Was exactly what I was looking for and looks brilliant on my website."** in [WordPress.org Reviews](https://wordpress.org/support/view/plugin-reviews/speed-contact-bar) by tydem00 on April 15, 2015
* **"It's one of my favorites, and easily one of the best for social buttons."** in [WordPress.org Support Forum](https://wordpress.org/support/topic/adding-yelp) by aleos97 on April 25, 2015
* **"Easy Peasy Lemon Squeezy!"** in [WordPress.org Reviews](https://wordpress.org/support/view/plugin-reviews/speed-contact-bar) by Beefy on February 10, 2015

= What you can configure =

There are some options you can set to let the contact bar fit to your needs. You can set 

1. your contact data
2. URLs to your social media pages and profiles and
3. many design options like colors

= Configuration options in detail =

You can show these three **personal contact data**:

1. Headline, also used as call to action
2. Phone number
3. Cell phone number
4. E-Mail address
5. any further content you wish by using the filter hook `speed-contact-bar-data`

Up till now Speed Contact Bar supports links to these **social media platforms**, as ordered alphabetically:

1. Facebook
2. Flickr
3. Google Plus
4. IMDb
5. Instagram
6. LinkedIn
7. Pinterest
8. SlideShare
9. Tumblr
10. Twitter
11. Vimeo
12. Xing
12. Yelp
13. Youtube
14. any further content you wish by using the filter hook `speed-contact-bar-icons`

More social media plattforms will come in future.

And of course you can set the **design of the contact bar** to be suitable to the design of your website:

1. **Position of the bar**: You can place the bar at the **top** or **bottom** on every page
2. **Fixed position**: You can set whether it should **scroll** with the content or **stay fixed**
3. **Horizontal padding** of the contact bar
4. **Vertical padding** of the contact bar
5. **Space between bar and page content** if the bar is fixed
6. **Background color** of the contact bar or a **transparent bar**
7. **Text color**
8. **Link color**
9. **Lightness of icons**: You can select between **dark** or **bright** icons
10. **Content alignment** within the bar
11. **Font size** of the texts and links
12. **Icon size**
13. **Headline visibility**: switch the headline on or off
14. **Headline text**
15. **Headline HTML tag**
16. **Headline URL** to make the headline a link
17. **Shadow under or above the bar** for a chic 3D effect
18. **Link target** of all links to open a contact link in the same window or in a new window
19. **Show contact data on small displays** instead of showing icons only
20. any further style you wish by using the filter hook `speed-contact-bar-style`

Do you miss some options? We will add it! Please write your request in the plugin's [support forum at wordpress.org](http://wordpress.org/support/plugin/speed-contact-bar). We will try to take a look and answer as soon as possible.

See [Other Notes](https://wordpress.org/plugins/speed-contact-bar/other_notes/) for examples using hooks.

== Installation ==

= Using The WordPress Dashboard =

1. Navigate to the 'Add New' in the plugins dashboard
2. Search for 'Speed Contact Bar'
3. Click 'Install Now'
4. Activate the plugin on the Plugin dashboard
5. Configure the plugin with the options page at "Settings" =&gt; "Speed Contact Bar".

= Uploading in WordPress Dashboard =

1. Navigate to the 'Add New' in the plugins dashboard
2. Navigate to the 'Upload' area
3. Select `speed-contact-bar.zip` from your computer
4. Click 'Install Now'
5. Activate the plugin in the Plugin dashboard
6. Configure the plugin with the options page at "Settings" =&gt; "Speed Contact Bar".

= Using FTP =

1. Download `speed-contact-bar.zip`
2. Extract the `speed-contact-bar` directory to your computer
3. Upload the `speed-contact-bar` directory to the `/wp-content/plugins/` directory
4. Activate the plugin in the Plugin dashboard
5. Configure the plugin with the options page at "Settings" =&gt; "Speed Contact Bar".

== Other Notes ==
= Add and re-order list entries by using hooks =
Repeatedly users ask for special things to be included in the contact bar. Of course, if you know how to code with PHP, HTML and CSS you can change the plugin's code and insert whatever you want.

But changing the plugin's code is not a good idea. Every upgrade of the plugin will delete your inserted code.

Luckily there is a solution: hooks offered by this plugin. You can change the content and design of the contact bar with your own functions. And you can be sure that they will not be overwritten by future plugin's upgrade when you place your function in the `functions.php` of your theme or in a self-written plugin.

Use the hooks:

1. `speed_contact_bar_data` for altering the personal contact informations list
2. `speed_contact_bar_icons` for altering the social media icons list
3. `speed_contact_bar_style` for altering the style of the contact bar

You can place the code in your `functions.php` of your theme or in your own plugin. Lets look at the following examples.

= Add an item to the personal contact information list =
The following function does two things:

1. it adds a list item with the content 'Hello World' as the first item 
2. and it changes the order of the list items.

At the end the function returns the changed list.

`// describe what the function should do
// passed parameter: an array of personal contact data as list items
function change_speed_contact_bar_data ( $list_items ) {

	// add an item as first item in the list via array_unshift()
	// the content has to be surrounded by the LI element
	array_unshift( $list_items, '<li>Hello World</li>' );

	// re-order the list items
	$new_ordered_items = array(
		$list_items[ 2 ],
		$list_items[ 3 ],
		$list_items[ 0 ],
		$list_items[ 1 ],
	);
	
	// return changed list
	return $new_ordered_items;

}
// let the function work
add_filter( 'speed_contact_bar_data', 'change_speed_contact_bar_data' );`

= Add an item to the icon list =
The following function does two things:

1. it appends a list item with the content 'Foo Bar' at the end of the list,
2. and it inserts a list item with a standard WordPress search form at a desired position in the list.

At the end the function returns the changed list.

`// describe what the function should do
// passed parameter: an array of social media icons as list items
function change_speed_contact_bar_icons ( $list_items ) {

	// add an item as last item in the list, via $array[]
	// the content has to be surrounded by the LI element
	$list_items[] = '<li>Foo Bar</li>';

	// add an item at any position in the list via array_splice()
	// way of thinking: the new item should be the x-th element:
	// so the second parameter value is x - 1
	// if 4th element then parameter value is 4 - 1 = 3.
	// you can find more tipps for array_splice() at 
	// http://php.net/manual/en/function.array-splice.php
	// the content has to be surrounded by the LI element
	// notice: the LI element is extended by an ID attribute to have 
	// an exact selector for CSS
	array_splice(
		$list_items, // array to change
		3, // array index where to insert the new item
		0, // number of items to replace
		// content of new item:
		'<li id="spc-search-form">' . get_search_form( false ) . '</li>' 
	);

	// return changed list
	return $list_items;
	
}
// let the function work
add_filter( 'speed_contact_bar_icons', 'change_speed_contact_bar_icons' );`

= Add style sheets for the search box =
The following function appends some Cascading Style Sheets code for the search form inserted by the previous function.

At the end the function returns the changed CSS code.

`// describe what the function should do
// passed parameter: a string of CSS
function change_speed_contact_bar_style ( $css_code ) {
	
	// add style for search form, append it to existing code with '.='
	// the content has to be surrounded by the STYLE element
	$css_code .= "<style type='text/css'>\n";
	$css_code .= "#spc-search-form form { display: inline; }\n";
	$css_code .= "#spc-search-form input { width: 7em; }\n";
	$css_code .= "</style>\n";

	// return changed CSS
	return $css_code;
	
}
// let the function work
add_filter( 'speed_contact_bar_style', 'change_speed_contact_bar_style' );`

== Frequently Asked Questions ==

= Could there be any conflicts with my theme? =

In most themes the Speed Contact Bar works fine without any conflicts. Only if the theme comes with an own fixed top bar there can be a source of design conflicts with a fixed Speed Contact Bar.

In that case you can try to set the position of the Speed Contact Bar at the bottom of every page. That will avoid the conflict.

= What does the plugin need to work fine? =

Speed Contact bar requires a WordPress installation version equal or higher than 3.5.

= I am logged in as administrator. Why do I not see the contact bar? =

The most likely reason is the **WordPress Admin Bar fixed on top of the page**. In this case it overlaps the Speed Contact Bar.

You have two possibilities to see the bar:

1. Go to your user profile in the backend a deactivate the checkbox at "Show admin bar"
2. Use another browser, as unlogged visitor

= I want to display this and that. How can I add it? =

If you are a developer please read 'Other Notes'. You will find a documentation about the hooks of the Speed Contact Bar. That way you can easily insert your own content into the bar.

If you do not know how to use PHP please write your request in the plugin's [support forum at wordpress.org](http://wordpress.org/support/plugin/speed-contact-bar). We will try to take a look and answer as soon as possible.

= I want to switch off the contact bar for a while without losing all settings. How? =

Just deactivate the plugin. The contact bar does not appear and all your data are kept stored. If you activate the plugin again you see the contact bar with the last stored settings.

If you deactivate and delete the plugin on the plugins page all files and settings of the contact bar will be deleted residue-free.

= How can I get the contact bar in my language? =

Translations are available in English and German. If you want to see another language it would be great if you would provide the translation.

Please write your request in the plugin's [support forum at wordpress.org](http://wordpress.org/support/plugin/speed-contact-bar). We will try to take a look and answer as soon as possible.

== Screenshots ==

1. The contact bar in black with headline, phone number, cell phone number, e-mail address, icons of Facebook icon and Google Plus icons on top of a webpage
2. The options page of the contact bar in the WordPress backend

== Changelog ==

= 4.1 =
* Added option to hide bar in mobile devices
* Fixed icon size for some themes
* Tested successfully with WP 4.2.4
* Updated screenshot of Options Page
* Updated *.pot file and german translation

= 4.0 =
* Introducing hooks to filter the lists and styles of the contact bar with your own functions:
* Added filter hook `speed_contact_bar_data`
* Added filter hook `speed_contact_bar_icons`
* Added filter hook `speed_contact_bar_style`
* Augmented ranges of readjustment and padding pulldowns in the options page

= 3.0.1 =
* Fixed font size of links for some themes
* Changed minimum font size from 8px to 4px
* Changed minimum icon size from 16px to 10px
* Some refactoring

= 3.0 =
* Added Position option: place the bar at the top or at the bottom on every page
* Added Headline URL option: The headline can become a link with a given URL
* Added donation button to make it easier for you to contribute the plugin
* Moved some headline options to the 'Appereance' section
* Cleared up options page: More structure and new order of the options
* Updated screenshot of Options Page
* Updated *.pot file and german translation

= 2.6 =
* Added option to keep the headline in mobile devices
* Tested successfully with WP 4.2.2
* Updated *.pot file and german translation

= 2.5.1 =
* Fixed wrong aspect ratios for the IMDb and Yelp icons

= 2.5 =
* Added Yelp option
* Tested successfully with WP 4.2
* Updated *.pot file and german translation

= 2.4 =
* Added option for a transparent bar
* Updated *.pot file and german translation

= 2.3 =
* Added option to keep phone numbers and mailaddress displayed in small screens
* Added function sanitizing phone numbers to make visual representations callable technically
* Enhanced description at phone numbers on the options page
* Updated *.pot file and german translation

= 2.2 =
* Added Instagram option
* Successfully tested with WordPress 4.1.1
* Updated *.pot file and german translation

= 2.1 =
* Added option to select the horizontal padding within the bar
* Added option to select the vertical padding within the bar
* Added option to select the space between the bar and the page content
* Moved all contact bar CSS code into HTML code to circumvent removing the call of the CSS main file
* Updated *.pot file and german translation

= 2.0 =
* Phone numbers are clickable to trigger phone calls
* Added vimeo option
* Added IMDb option
* Added option to select the font size
* Added option to select the icon size
* Added option to select the headline HTML tag
* Changed visibility of phone and cellphone in mobile design
* Significant improved robustness in case of no or fake settings
* Updated *.pot file and german translation

= 1.9.3 =
* Successfully tested with WordPress 4.1

= 1.9.2 =
* Successfully tested with WordPress 4.0
* Added icons for plugin search
* Changed order of links for this plugin on plugin list

= 1.9.1 =
* Added option for opening links in new windows
* Updated *.pot file and german translation

= 1.9 =
* Improve uninstall routine
* Tested successfully with WordPress 3.9.2

= 1.8 =
* Added Flickr option
* Added SlideShare option
* Added tumblr option
* Fixed undesired occurrence of the contact bar on admin error pages
* Updated *.pot file and german translation

= 1.5 =
* Added LinkedIn option
* Added Xing option
* Updated *.pot file and german translation

= 1.4 =
* Added Youtube option
* Added icons in options page
* Updated *.pot file and german translation

= 1.3 =
* Added Twitter option
* Updated *.pot file and german translation

= 1.2 =
* Added Pinterest option
* Updated *.pot file and german translation

= 1.1 =
* Fixed incorrect call of a non-static method in the admin area
* Added WordPress function antispambot() to prevent spam bots recognizing the email address
* Changed color picker type from Fantastic to modern Iris
* Refactoring the options page
* Fixed a german typo
* Updated *.pot file and german translation

= 1.0 =
First official release

= 0.3 =
* Added dashicons
* Added text color option
* Updated *.pot file and german translation

= 0.2 =
* Added headline in the contact bar
* Better CSS logic for fixed position
* Sanitize input and output of user given data

= 0.1 =
First release for just trying it

== Upgrade Notice ==

= 4.1 =
Added option to hide bar in mobile devices, fixed icon size for some themes, tested with WP 4.2.4

= 4.0 =
Introduced hooks, augmented ranges for padding and readjusment

= 3.0.1 =
Fixed font size of links, refactoring

= 3.0 =
Added and reordered options

= 2.6 =
Added option for the headline in mobile devices, tested successfully with WP 4.2.2

= 2.5.1 =
Fixed wrong aspect ratios for the IMDb and Yelp icons

= 2.5 =
Added Yelp option, tested successfully with WP 4.2

= 2.4 =
Added option for a transparent bar

= 2.3 =
Added option to keep phone numbers and mailaddress displayed in small screens

= 2.2 =
Added Instagram option, tested with WP 4.1.1

= 2.1 =
Added options, please reset the settings of the bar

= 2.0 =
Please set the new Speed Contact Bar settings: IMDb, vimeo, headline HTML tag selection, sizes of texts and icons. Phone numbers are clickable now

= 1.9.3 =
Successfully tested with WordPress 4.1

= 1.9.2 =
Successfully tested with WordPress 4.0, added icons for plugin search

= 1.9.1 =
Added option for opening links in new windows

= 1.9 =
Improved uninstall routine, tested with WordPress 3.9.2

= 1.8 =
Added Flickr, SlideShare and tumblr option

= 1.5 =
Added LinkedIn and Xing options

= 1.4 =
Added Youtube option

= 1.3 =
Added Twitter option

= 1.2 =
Added Pinterest option

= 1.1 =
Fixed errors and changed color picker

= 1.0 =
Added new options and security for data inputs and outputs, removed dashicons

= 0.3 =
Added dashicons and text color option

= 0.2 =
Improvements in sanitizing user data and style

= 0.1 =
First release in beta status
