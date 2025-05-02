# ![Topic Preview](https://imattpro.github.io/logo/search.png "Topic Preview") Topic Preview for phpBB3

A phpBB extension that displays a short excerpt of text from the first post in a tooltip while the mouse hovers over a topic’s title.

[![Build Status](https://github.com/iMattPro/topicpreview/actions/workflows/tests.yml/badge.svg)](https://github.com/iMattPro/topicpreview/actions)
[![codecov](https://codecov.io/gh/iMattPro/topicpreview/branch/master/graph/badge.svg?token=XCFCZ4NZCX)](https://codecov.io/gh/iMattPro/topicpreview)
[![Maintainability](https://api.codeclimate.com/v1/badges/f1d45031bd20ef8804e6/maintainability)](https://codeclimate.com/github/iMattPro/topicpreview/maintainability)
[![Latest Stable Version](https://poser.pugx.org/vse/topicpreview/v/stable)](https://www.phpbb.com/customise/db/extension/topicpreview/)

## Features
* Let your visitors peek at what's inside a topic before they click on it
* Admin options to display user avatars and text previews from the first and last posts
* Admin option to set the amount of text shown in the preview
* Admin option to define BBCodes whose contents you want removed from the topic preview
* Admin options to customize the appearance and assign different themes to board styles
* Ships with four themes, additional custom themes can be added using CSS files
* No theme option, i.e.: native browser tooltip (does not support avatars or last posts)
* User option allows users to disable topic previews
* Previews are aware of browser window top/bottom edges and are responsive
* The last word in the topic preview is not cut off and will be followed by an ellipsis
* BBCode tags and URL links are removed for a cleaner text-only appearance
* Smileys are displayed as text :)
* Does not significantly impact server load (no additional db queries)
* Multiple languages are supported. View the pre-installed [localizations](https://github.com/iMattPro/topicpreview/tree/master/language).
* Built-in support for "[Precise Similar Topics](https://www.phpbb.com/customise/db/extension/precise_similar_topics/)", "[Recent Topics](https://www.phpbb.com/customise/db/extension/recent_topics_2/)" and "[Top Five](https://www.phpbb.com/customise/db/extension/topfiveext/)" extensions

## Awards
* Featured MOD of the Week in the phpBB Weekly Podcast, episode #143.

## Minimum Requirements
* phpBB 3.2.0 or higher
* PHP 5.4 or higher

## Installation
1. [Download the latest validated release](https://www.phpbb.com/customise/db/extension/topicpreview/).
2. Unzip the downloaded release and copy it to the `ext` directory of your phpBB board.
3. Navigate in the ACP to `Customise -> Manage extensions`.
4. Look for `Topic Preview` under the Disabled Extensions list and click its `Enable` link.

## Uninstallation
1. Navigate in the ACP to `Customise -> Manage extensions`.
2. Click the `Disable` link for Topic Preview.
3. To permanently uninstall, click `Delete Data`, then delete the `topicpreview` folder from `phpBB/ext/vse/`.

## Support
Support is available for Topic Preview in the [phpBB Extension Database](https://www.phpbb.com/customise/db/extension/topicpreview/support).

## License
[GNU General Public License v2](https://opensource.org/licenses/GPL-2.0)

© 2013 - Matt Friedman
