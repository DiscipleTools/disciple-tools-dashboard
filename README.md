[![Build Status](https://travis-ci.com/DiscipleTools/disciple-tools-dashboard-plugin.svg?branch=master)](https://travis-ci.com/DiscipleTools/disciple-tools-dashboard-plugin)

# Disciple Tools Dashboard Plugin
The Disciple Tools Dashboard Plugin is intended to accelerate integrations and extensions to the Disciple Tools system.
This basic plugin dashboard has some of the basic elements to quickly launch and extension project in the pattern of
the Disciple Tools system.


### The dashboard plugin is equipped with:
1. Wordpress style requirements
1. Travis Continueous Integration
1. Disciple Tools Theme presence check
1. Remote upgrade system for ongoing updates outside the Wordpress Directory
1. Multilingual ready
1. PHP Code Sniffer support (composer) @use /vendor/bin/phpcs and /vendor/bin/phpcbf
1. Dashboard Admin menu and options page with tabs.

### Refactoring this plugin as your own:
1. Refactor all occurences of the name `Dashboard_Plugin`, `dashboard_plugin`, `dashboard-plugin`, and `Dashboard Plugin` with you're own plugin
name for the `disciple-tools-dashboard-plugin.php and admin-menu-and-tabs.php files.
1. Update the README.md and LICENSE
1. Update the translation strings inside `default.pot` file with a multilingual sofware like POEdit, if you intend to make your plugin multilingual.
