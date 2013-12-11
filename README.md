# ![Topic Preview](http://mattfriedman.me/forum/images/search.png "Topic Preview") Topic Preview for phpBB3

This is an extension for phpBB 3.1 that will display a short excerpt of text from the first post in a tooltip while the mouse hovers over a topic’s title.

## Browser support
![Chrome 4+](http://mattfriedman.me/software/browsericons/chrome.png "Chrome 4+")4+ &nbsp;&nbsp;&nbsp;
![Firefox 3.5+](http://mattfriedman.me/software/browsericons/firefox.png "Firefox 3.5+")3.5+ &nbsp;&nbsp;&nbsp;
![Safari 3+](http://mattfriedman.me/software/browsericons/safari.png "Safari 3+")3+ &nbsp;&nbsp;&nbsp;
![Internet Explorer 6+](http://mattfriedman.me/software/browsericons/ie.png "Internet Explorer 6+")6+ &nbsp;&nbsp;&nbsp;
![Opera 10.5+](http://mattfriedman.me/software/browsericons/opera.png "Opera 10.5+")10.5+

## Features
* Let your visitors peek at what's inside a topic before they click on it
* Admin options to display user avatars and text previews from the first and last posts
* Admin option to set the length of text shown in the preview
* Admin option to define BBCodes whose contents you want removed from the topic preview
* Admin options to customize the appearance and assign different themes to board styles
* Ships with two themes. Additional custom themes can be added using css files.
* User option allows users to disable topic previews
* Previews are smart: They are aware of browser window edges and they are responsive
* The last word in the topic preview will be followed by an ellipsis and is not cut off
* BBCode tags and URL links are removed for a cleaner text-only appearance
* Smileys are displayed as text :)
* Supports right-to-left languages
* Does not significantly impact server load (no additional db queries)
* Built-in support for integration with the "Precise Similar Topics II" extension

### Languages supported:
* English
* Czech (translation incomplete)
* Dutch (translation incomplete)
* French
* German
* Italian (translation incomplete)
* Persian (translation incomplete)
* Polish
* Romanian (translation incomplete)
* Spanish

## Awards
* Featured MOD of the Week in the phpBB Weekly Podcast, episode #143.

## Requirements
* phpBB 3.1-dev or higher
* PHP 5.3.3 or higher

## Installation
You can install this on the latest copy of the develop branch ([phpBB 3.1-dev](https://github.com/phpbb/phpbb3)) by following the steps below:

**Manual:**

1. Copy the entire contents of this repo to to `phpBB/ext/vse/topicpreview/`
2. Navigate in the ACP to `Customise -> Extension Management -> Extensions`.
3. Click `Enable`.

**Git CLI:**

1. From the board root run the following git command:
`git clone -b extension https://github.com/VSEphpbb/topic_preview.git phpBB/ext/vse/topicpreview`
2. Navigate in the ACP to `Customise -> Extension Management -> Extensions`.
3. Click `Enable`.

Note: This extension is in development. Installation is only recommended for testing purposes and is not supported on live boards. This extension will be officially released following phpBB 3.1.0.

## Uninstallation
Navigate in the ACP to `Customise -> Extension Management -> Extensions` and click `Disable`.

To permanently uninstall, click `Delete Data` and then you can safely delete the `/ext/vse/topicpreview` folder.

## License
[GNU General Public License v2](http://opensource.org/licenses/GPL-2.0)

© 2013 - Matt Friedman (VSE)
