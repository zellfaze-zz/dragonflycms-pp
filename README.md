#Personal Pages v2.7.1, for DragonflyCMS 9.2.1
4/12/09

by Andy Rink, a.k.a. personman (personman_145@hotmail.com)
with numerous contributions from the DragonflyCMS community

This data is released under the terms of the GNU GPL v2.


Personal Pages is a free social-networking software designed to add functionality similar to MySpace or Facebook.


Features:

	* Each registered user can create their own page.

	* Pages use bbcode or HTML to allow advanced functionality, like embedding images or videos.

	* Visitors can leave comments. Optional anonymous commenting with CAPTCHA.

	* Personal Pages show a user's online status, recent blogs, and image uploads.

	* Personal Pages can be extended further via a blocks system.

	* Multi-language support, with English, German, and Danish included.

	* Optimized for a wide variety of browsers, including Firefox, IE, Konqueror, etc.

	* Free Software released under the GNU GPL v2.


INSTALLATION:

  Place the "modules" and "language" folders in the publicly accessible root of your dragonfly site.
Install and activate it in the modules administration section.

IMPORTANT! If you are upgrading from Personality Pages:

If you want to keep your old data intact, DON'T uninstall Personality Pages!
Instead, disable the module, or delete /modules/Personality manually. 

This will leave the database entries intact.

Upon attempting to install Personal Pages, you may get a SQL error. This is because the tables 
it is trying to create already exist. I've included a hacked copy of cpg_inst.php in "extras" which
will work around this issue. Use it to replace the copy in /modules/Personal

In the future, if you want to uninstall and reinstall the module, you will need to switch
the files back to recreate the initial database entries.


Extras:

Optional Profile Replacement:

    This requires replacing the copy of userinfo.php included with DragonflyCMS.
Using this replacement with versions of DragonflyCMS other than 9.2.1 could cause problems.
Also, upgrading DragonflyCMS will overwrite this replacement, requiring you to reinstall it.

To install the optional profile replacement, rename /modules/Your_Account/userinfo.php
and replace it with the userinfo.php in "extras".


Changes:
    v2.7.1:
	Bugfix: Image block causes profile problems. (Thanks Poldi)
	Feature: Administration Control under Admin Menu/Modules
	Feature: HTML can be enabled in Personal Pages and comments

    v2.7:
	Bugfix: Old references to Personality in the profile personal block. (Thanks heliown)
	Bugfix: Layout bugs in the profile personal block in IE and Konqueror. (Thanks Slang)
	Feature: Anonymous commenting with CAPTCHA. Disabled in /modules/Personal/index.php by default.
	Feature: Display a user's recent image uploads. (Thanks Eastlane, for the initial code)
	Improvement: Links on the list page are more simple and consistent.
	Improvement: Pages are now listed by how recently they are updated, so update often. :)
	Improvement: Improvements to the blocks system.
	Moved optional components to the "extras" folder.

    v2.6.2:
	Bugfix: Under some circumstances, users could create multiple Personal Pages.
	Bugfix: Broken redirection after adding a Personal Page.

    v2.6.1:
	Bugfix: profile replacement shows the Personal Page block, even for users who don't have one.
	There are no core changes, only the file /modules/Personal/blocks/right/personal.php changed.

    v2.6:
	Implemented blocks system
	Preliminary comments bbcode support
	Lots of UI cleanups, fixes and improvements
	Updated Danish language, thanks again Mike Therp Hansen (mike@dragonflycms.dk) of DragonflyCMS.dk
	Updated profile replacement

    v2.0.5: This release is primarily centered around testing bug fixes:
	Broken links on sites that don't use LEO
	Language issues
	Broken install (Thanks bytethegroove)

    v2.0.4:
	Added Danish language, thanks to Mike Therp Hansen (mike@dragonflycms.dk) of DragonflyCMS.dk
	Optional profile replacement that adds Personal Page features to the user profiles

    v2.0.3:
	Added Latest Blogs to Personal Page
	Profile link should always show at the bottom of Personal Pages
	Minor UI cleanup

    v2.0.2:
	Added online status to Personal Pages. (Borrowed code from reDesign theme. Thanks!)
	Added avatars to comments
	Added shortened URL to users' Personal Pages (mysite.com/Personal/u=username/)
	Fixed a bug that would cause the wrong user info to be displayed in the Comment form
	Language changes, improvements and fixes


    v2.0.1 - Initial Release:
	Added profile information to Personal Pages
	Language changes, improvements, and fixes to English version


Credits:

  Mike Therp Hansen (mike@dragonflycms.dk) of DragonflyCMS.dk for the Danish language pack,
  and reporting bugs

  Thanks Eastlane of 000.pri.ee for the initial code for the image blocks

  The DragonflyCMS community, particularly the creators of the original module:
    gtown, DJ Maze, Trevor, and dcollis

People who have contributed valuable testing or feedback:

  Thanks bytethegroove, heliown, and Slang.


DONATE:

If you would like to support my efforts, please consider donating via PayPal to:

personman_145@hotmail.com

Also, send me an e-mail to let me know which project you are supporting, and any suggestions
or comments you have about it. Thanks!


The following is the original ReadMe by the original author.

********************************************
  PERSONALITY - MODULE FOR CPGNUKE 9.0.6.1
********************************************

  author:       gtown
  date:         2006/10/14
  version:      2
  authemail:    admin@germeringer.de
  authurl:      http://www.germeringer.de

  This module is based on the blogs module of
  DJMaze and Trevor from www.cpgnuke.com
  Special thanks to dcollis!

*********************************************


The module is tested on CPGNUKE 9.0.6.1 and works without

any bugs. If you find some anyway please send a message

at admin@germeringer.de or send a private message at gtown at CPGNuke.com!

No warrenty can be given, so making a backup before

installation is always a good idea!


Have Fun!

gtown