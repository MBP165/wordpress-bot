# CLAUDE.md

Guidance for Claude Code when working in this repository.

## Project goal

A WordPress/WooCommerce plugin that turns a Telegram bot into a shop channel publisher:

- Connect a Telegram bot (bot token) to a WooCommerce store.
- Publish WooCommerce products (image, title, price, description, link / buy button) to **multiple Telegram channels**.
- The bot must be an **admin** of each target channel to post. Verify admin status (e.g. `getChatMember` / `getChatAdministrators`) before saving a channel or sending, and surface a clear error if it isn't.

Decisions:
- Channels are global (settings page); each product picks channels via checkboxes on create/edit.
- Sending happens on publish, including scheduled products when they go live.
- Message: multiple images → `sendMediaGroup`, one image → `sendPhoto`, none → `sendMessage`.
- Inline URL button to the product page, label default `خرید`, editable in admin. Albums can't carry buttons, so after `sendMediaGroup` send a short `sendMessage` with the button.
- No ordering inside Telegram; posts link to the store.

See `TODO.md` for the full plan and open questions.

## Stack

- PHP 7.4+, WordPress 5.8+, WooCommerce (required dependency).
- Structure follows the WordPress Plugin Boilerplate (see `README.md`).
- Telegram Bot API over HTTPS via `wp_remote_post()` — no external SDK unless agreed.

## Layout

- `wordpress-bot.php` — bootstrap, activation/deactivation hooks.
- `includes/` — core class, hook loader, i18n, activator/deactivator. Register hooks via `$this->loader->add_action()` / `add_filter()` in `includes/class-wordpress-bot.php`.
- `admin/` — settings screens, product metaboxes, admin assets.
- `public/` — front-end hooks (likely minimal).
- `uninstall.php` — remove plugin options/data.

## Conventions

- WordPress Coding Standards (`phpcs.xml.dist`). Run `composer install && composer run lint`.
- Class prefix `Wordpress_Bot_`, text domain `wordpress-bot`, files `class-wordpress-bot-*.php`.
- Sanitize input, escape output, use nonces + `current_user_can()` on every admin action.
- Store the bot token securely (options, never logged or exposed to front-end JS).
- Keep Telegram API calls in a single client class; handle rate limits (HTTP 429 `retry_after`) and errors.

## Working rules

- Ask before assuming unclear requirements.
- Keep changes small; don't add dependencies without asking.
