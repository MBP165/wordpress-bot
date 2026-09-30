# TODO — Telegram Shop Bot for WooCommerce

Draft plan for review. Edit freely; items marked **❓** need a decision.

---

## 1. Admin settings page

- [ ] Add menu page: **WooCommerce → Telegram Bot** (❓ or a top-level menu item?)
- [ ] Capability: `manage_woocommerce`
- [ ] Show admin notice if WooCommerce is not active

### 1.1 Bot configuration section

- [ ] Field: **Bot token** (password input, show/hide toggle)
- [ ] Button: **Test connection** → calls `getMe`, shows bot name + `@username` on success, error message on failure
- [ ] Save token only if `getMe` succeeds
- [ ] ❓ Default post template (e.g. `{image} {title} {price} {link}`) here or in a later phase?

### 1.2 Channels section

**Help box (top of section):**

> ℹ️ To post to a channel, the bot must be an **admin** of that channel with permission to **post messages**.
> 1. Open your channel → Administrators → Add admin → search `@your_bot`.
> 2. Enable "Post messages".
> 3. Enter the channel here (`@channelname` or numeric ID like `-100123…`) and press **Check**.
>
> 🟢 Green = bot is admin, channel is enabled and saved.
> 🔴 Red = bot is not admin (or can't post), channel is **not** saved.

**Channel list UX:**

```
[ Help box ]

Channel                 Status        Action
@my_shop_channel        🟢 Enabled    [Check] [Remove]
@other_channel          🔴 Not admin  [Check] [Remove]
[ + Add channel ]
```

- [ ] **Add channel** row: text input + **Check** button (no separate save for channels)
- [ ] **Check** button (AJAX, nonce-protected):
  - [ ] Call `getChat` → resolve channel title + numeric ID
  - [ ] Call `getChatMember(chat_id, bot_id)`
  - [ ] Admin with `can_post_messages` → button **green**, channel **saved & enabled**, show channel title
  - [ ] Not admin / no post permission / channel not found → button **red**, inline error reason, channel **not saved**
  - [ ] Loading state (spinner, button disabled) while checking
- [ ] **Remove** button → confirm dialog, delete channel
- [ ] Prevent duplicate channels (compare by numeric chat ID)
- [ ] ❓ Existing saved channel that later loses admin rights: re-check automatically (on page load / before sending) and mark red + disabled, or remove it?
- [ ] ❓ Allow manual enable/disable toggle per channel?

## 2. Sending products

- [ ] ❓ Where does the user send from?
  - Product edit screen metabox: pick channels → **Send to Telegram**
  - Products list bulk action: **Send to Telegram**
  - Both
- [ ] ❓ Auto-post on product publish / update?
- [ ] Message: photo + caption (title, price, short description, link / "Buy" inline button)
- [ ] Only enabled (green) channels are selectable
- [ ] Re-verify admin before sending; skip + report failed channels
- [ ] Show per-channel result (sent / failed + reason)
- [ ] ❓ Keep send history per product (date, channel, message ID)? Allow edit/delete of posted message?

## 3. Technical

- [ ] `Wordpress_Bot_Telegram_Client` — single class for API calls (`wp_remote_post`), error + HTTP 429 `retry_after` handling
- [ ] Storage: token + channels in `wp_options` (channels as array: `chat_id`, `username`, `title`, `status`, `checked_at`) — ❓ or a custom table?
- [ ] Token never exposed to front-end JS or logs
- [ ] AJAX handlers: nonce + `current_user_can()` + sanitization
- [ ] i18n for all strings (text domain `wordpress-bot`)
- [ ] `uninstall.php` removes options
- [ ] Update plugin header (name, description, `Requires Plugins: woocommerce`)
- [ ] Lint passes (`composer run lint`)

## 4. Later / ideas

- [ ] ❓ Ordering inside Telegram (bot chat checkout) vs link to store only
- [ ] Scheduled posts
- [ ] Multiple bots
