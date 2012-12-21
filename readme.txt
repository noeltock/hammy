=== Hammy ===
Contributors: Noel_Tock
Donate link: http://www.noeltock.com
Tags: responsive, adaptive, image, images, picture, pictures, smartphone, mobile, resize, resized, automatic, automated, speed, fast
Requires at least: 3.4
Tested up to: 3.4
Stable tag: 0.4

Hammy speeds up your website by generating and serving resized images for your content area depending on content width.

== Description ==

= Introduction =

Hammy takes your regular content images (within posts and pages) and regenerates a number of smaller sized images. When a person visits your website, it then automatically provides them with the most approriate image (or the smallest one possible). This makes for a better experience, especially on mobile.

= Will it work on my Theme? =

**Yes!**

= How does it work? =

When activated, the `<img>` tag is replaced by the `<picture>` tag, and the alternate image sizes are provided in a way that only jQuery can access/load. If jQuery isn't available, it falls back to the regular image you had there in the first place.

It also takes on any classes or alternate titles that were previously on the image tag. It does not make any changes to your database (i.e. content or images that you already have remain untouched).

= What else do I need to know? =

* Supports Retina
* You need to be willing to **spend a few minutes configuring and playing with the breakpoints and choosing the correct parent container** to get it right.
* Hammy filters the output every single time, so while it's not the end of the world, caching is never a bad idea.

= Updates =

Follow me for updates at [@noeltock](http://www.twitter.com/noeltock)

== Installation ==

Hammy already starts working upon activation, so any configuration is optional:

1. Upload the folder `hammy` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. (Optional) Go to Settings -> Hammy and review the options. Add the id/class of the container that holds your posts (i.e. #content). Then add breakpoints that are relevant to that container ( **see the FAQ for an example** ).
1. (Optional) Edit your theme's CSS to add `picture.hammy-responsive`, in a way that it is identical to any `img` styling used for your content area.

== Frequently Asked Questions ==

= How does the post/page container setting work? =

The width of your browser is quite different than that of the post/page container (i.e. #content). By being able to measure the width of the content container instead of the browser window, you'll ensure far better accuracy at all breakpoints.

= Breakpoints example? =

If your website is 960px wide, but the content (#content) is only 600px, then `600` is your last breakpoint. If the next smaller size is iPhone landscape (where the sidebar gets pushed under) and you have a 10px margin on either side, that breakpoint is `460` (480 minus 20), and so forth.

At the end, your breakpoints may look like `300,460,600`

= It doesn't work with this gallery plugin or something else, what to do? =

I'll add the ability to add classes/ID's to ignore, but please let me know of any edge-cases on the [Hammy forums](http://wordpress.org/support/plugin/hammy)

== Screenshots ==

1. Options Screen

= How is Hammy constructed? =

Hammy is possible through two awesome open source projects:

* [jQuery Picture](http://jquerypicture.com/) by [Abban Dunne](http://abandon.ie/) (this script has been sligthly altered in order to be able to target a particular container).
* [WPThumb](http://hmn.md/introducing-wp-thumb/) by [Human Made Limited](http://hmn.md)

They're both worth checking out and getting a better understanding of (or using in your mega-awesome WordPress client projects).

= What's with the squirrel? =

Hammy, from the movie "Over the Hedge", he's quite fast, like these images. Be sure to check out the movie for full appreciation.

== Changelog ==

= 0.3.1 =
* Slight logic change for the better

= 0.3 =
* Changed tag figure to picture, closer to w3 discussions
* Retina Support

= 0.1.1 =
* Fix for if logic

= 0.1 =
* Initial Release (Hammy Time)

== Upgrade Notice ==

= 0.3.1 =
* Slight logic change for the better

= 0.3 =
* Changed tag figure to picture, closer to w3 discussions
* Retina Support

= 0.1.1 =
* Small but important fix

== Feedback & Bugs ==

As this is a new plugin, there are likely to be a few issues. Kindly post any issues, questions or suggestions on the [Hammy forums](http://wordpress.org/support/plugin/hammy) .