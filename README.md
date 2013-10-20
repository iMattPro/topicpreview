![Topic Preview](http://mattfriedman.me/forum/images/search.png "Topic Preview") Topic Preview for phpBB3
========================

This is an extension for phpBB 3.1 that will display a short excerpt of text from the first post of a topic when the user hovers their mouse over the topic title, in the form of a pretty jQuery Tooltip.

## Browser support
![Chrome 4+](http://mattfriedman.me/software/browsericons/chrome.png "Chrome 4+")4+ &nbsp;&nbsp;&nbsp;
![Firefox 3.5+](http://mattfriedman.me/software/browsericons/firefox.png "Firefox 3.5+")3.5+ &nbsp;&nbsp;&nbsp;
![Safari 3+](http://mattfriedman.me/software/browsericons/safari.png "Safari 3+")3+ &nbsp;&nbsp;&nbsp;
![Internet Explorer 6+](http://mattfriedman.me/software/browsericons/ie.png "Internet Explorer 6+")6+ &nbsp;&nbsp;&nbsp;
![Opera 10.5+](http://mattfriedman.me/software/browsericons/opera.png "Opera 10.5+")10.5+

Features
--------

* Let your visitors peek at what's inside a topic before they click on it
* Options to display user avatars and text previews from the first and last posts
* The last word in the topic preview will not be cut off and is followed by an ellipsis
* Smileys are displayed as text :)
* BBcode tags and URL links are removed for a cleaner text-only appearance
* Admin option to set the length of text shown in the preview (or disable the MOD)
* Admin option to define BBcodes whose contents you want removed from the topic preview
* User option allows users to disable the feature
* Does not significantly impact server load (no additional db queries)
* Prosilver and Subsilver2 compatible
* Supports right-to-left languages
* Built-in support for integration with the "Precise Similar Topics II" extension

###Languages supported:
* English
* Czech (translation incomplete)
* Dutch (translation incomplete)
* French
* German
* Italian (translation incomplete)
* Polish
* Romanian (translation incomplete)
* Spanish (translation incomplete)

Awards
------

* Featured MOD of the Week in the phpBB Weekly Podcast, episode #143.

Requirements
------------

* phpBB 3.1-dev or higher
* PHP 5.3 or higher

Installation
------------

You can install this on the latest copy of the develop branch ([phpBB 3.1-dev](https://github.com/phpbb/phpbb3)) by following the steps below:

1. Copy the `topicpreview` folder to `phpBB/ext/vse/`
2. Navigate in the ACP to `Customise -> Extension Management -> Extensions`.
3. Click Enable.

Uninstallation
--------------

Navigate in the ACP to `Customise -> Extension Management -> Extensions` and click `Purge`.

License
-------

[GNU General Public License v2](http://opensource.org/licenses/GPL-2.0)

© 2013 - Matt Friedman (VSE)
