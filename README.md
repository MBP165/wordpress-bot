# WordPress Bot

A WordPress plugin scaffolded from the widely-used [WordPress Plugin Boilerplate](https://github.com/DevinVinson/WordPress-Plugin-Boilerplate) pattern: an OOP structure with clear separation between admin, public, and shared (`includes`) code, a central hook loader, i18n support, and activation/deactivation lifecycle hooks.

## Structure

```
wordpress-bot.php               Plugin bootstrap file (headers, activation/deactivation hooks)
uninstall.php                   Cleanup logic run on uninstall
includes/
  class-wordpress-bot.php           Core plugin class wiring everything together
  class-wordpress-bot-loader.php    Registers all actions/filters with WordPress
  class-wordpress-bot-i18n.php      Loads the plugin text domain
  class-wordpress-bot-activator.php Runs on activation
  class-wordpress-bot-deactivator.php Runs on deactivation
admin/
  class-wordpress-bot-admin.php     Admin-facing hooks (enqueues, screens)
  css/, js/, partials/              Admin assets and view partials
public/
  class-wordpress-bot-public.php    Public-facing hooks (enqueues, shortcodes)
  css/, js/, partials/              Public assets and view partials
languages/                          .pot/.po/.mo translation files
```

## Requirements

- PHP 7.4+
- WordPress 5.8+

## Development

Install dev dependencies (PHP_CodeSniffer with WordPress Coding Standards):

```bash
composer install
composer run lint
```

## Getting started

1. Rename `wordpress-bot` occurrences (text domain, class prefixes, function prefixes) to your plugin's own slug if you're using this as a starting point for a new plugin.
2. Update the plugin header in `wordpress-bot.php` (name, URI, author, description).
3. Add functionality by extending `Wordpress_Bot_Admin` and `Wordpress_Bot_Public`, and registering new hooks via `$this->loader->add_action()` / `add_filter()` in `includes/class-wordpress-bot.php`.
