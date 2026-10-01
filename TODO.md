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
- [ ] Field: **Buy button text** (default: `خرید`)

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
- [ ] Saved channel that later loses admin rights → re-check (on settings page load and before sending) and mark **red + disabled** (kept in the list, not removed; hidden from product checkboxes until re-checked green)
- [ ] No manual on/off switch; channel status comes only from the admin check

## 2. Sending products

Channels are **global** (managed on the settings page). Products pick which of them to post to.

### 2.1 Product create / edit screen

- [ ] Metabox **Telegram channels** with a checkbox per **enabled (green)** channel
- [ ] Checked channels are saved as product meta
- [ ] Sending is triggered when the product is **published**:
  - [ ] Published now → send immediately
  - [ ] Scheduled (`future`) → send when WordPress publishes it (`transition_post_status` future → publish)
- [ ] Re-verify bot admin status before sending; skip and report failed channels
- [ ] Show per-channel result in the metabox (sent / failed + reason, date)
- [ ] Editing an already-published product with channels checked → **send again** to those channels
- [ ] Next to each channel checkbox, if already sent, show a note: *"Already sent to this channel"* (with last send date)
- [ ] Store send history in product meta (`chat_id`, date, message IDs)
- [ ] After a successful send, **uncheck** that channel's checkbox (failed channels stay checked)
- [ ] Each successful send is stored, so the "Already sent" note + date stays visible beside the checkbox

### 2.2 Telegram message format

- [ ] Caption/text uses a **fixed** template (not editable): title, price, short description
- [ ] Choose the API method by product images (featured image + gallery):
  - [ ] **Multiple images** → `sendMediaGroup` (album, max 10 images; caption on the first image)
  - [ ] **One image** → `sendPhoto`
  - [ ] **No image** → `sendMessage`
- [ ] Inline URL button that opens the product page
  - [ ] Default label: **خرید**
  - [ ] Label editable on the settings page (field: *Buy button text*)
- [ ] Albums: Telegram does not allow inline buttons on `sendMediaGroup`, so:
  - [ ] Send the album (images only, caption on first image)
  - [ ] Right after, `sendMessage` with a short text + **خرید** button
- [ ] Caption limit is 1024 chars for photos/albums (4096 for `sendMessage`) → truncate the description
- [ ] Only posts link to the store; no ordering inside Telegram

## 3. Technical

- [ ] `Wordpress_Bot_Telegram_Client` — single class for API calls (`wp_remote_post`), error + HTTP 429 `retry_after` handling
- [ ] Storage: token + channels in `wp_options` (channels as array: `chat_id`, `username`, `title`, `status`, `checked_at`)
- [ ] Token never exposed to front-end JS or logs
- [ ] AJAX handlers: nonce + `current_user_can()` + sanitization
- [ ] i18n for all strings (text domain `wordpress-bot`)
- [ ] `uninstall.php` removes options
- [ ] Update plugin header (name, description, `Requires Plugins: woocommerce`)
- [ ] Lint passes (`composer run lint`)

## 4. Later / ideas

- [ ] Multiple bots
