<?php
/**
 * Content schema = every editable field on the website + its default value.
 * The admin dashboard builds its forms from this file automatically.
 */
function T($label, $default = '', $hint = '')   { return ['type' => 'text', 'label' => $label, 'default' => $default, 'hint' => $hint]; }
function TA($label, $default = '', $hint = '')  { return ['type' => 'textarea', 'label' => $label, 'default' => $default, 'hint' => $hint]; }
function IMG($label, $default = '', $hint = '') { return ['type' => 'image', 'label' => $label, 'default' => $default, 'hint' => $hint]; }
function LNK($label, $default = '', $hint = '') { return ['type' => 'link', 'label' => $label, 'default' => $default, 'hint' => $hint ?: 'Internal page: /pricing   •   External: https://…   •   Section: #how']; }
function HTMLF($label, $default = '', $hint = '') { return ['type' => 'html', 'label' => $label, 'default' => $default, 'hint' => $hint ?: 'HTML allowed (h2, h3, p, ul, li, a, strong)']; }
function TOG($label, $default = 0, $hint = '')  { return ['type' => 'toggle', 'label' => $label, 'default' => $default, 'hint' => $hint]; }
function PW($label, $default = '', $hint = '')  { return ['type' => 'password', 'label' => $label, 'default' => $default, 'hint' => $hint]; }
function SEL($label, $opts, $default = '', $hint = '') { return ['type' => 'select', 'label' => $label, 'options' => $opts, 'default' => $default, 'hint' => $hint]; }
function LST($label, $item, $default = [], $hint = '') { return ['type' => 'list', 'label' => $label, 'item' => $item, 'default' => $default, 'hint' => $hint]; }

function seo_fields() {
    return [
        'title'       => T('SEO title', '', 'Ideal: 50–60 characters'),
        'description' => TA('Meta description', '', 'Ideal: 140–160 characters'),
        'keywords'    => T('Meta keywords', '', 'Comma separated (optional)'),
        'og_image'    => IMG('Social share image (Open Graph)', '', 'Ideal: 1200×630. Empty = default image from Settings → SEO'),
        'canonical'   => T('Canonical URL', '', 'Leave empty to auto-generate'),
        'robots'      => SEL('Search engines', ['index,follow' => 'Index + follow (recommended)', 'noindex,follow' => 'Do not index this page', 'noindex,nofollow' => 'Do not index, do not follow'], 'index,follow'),
    ];
}

function settings_schema() {
    return [
        'general' => ['label' => 'General', 'icon' => 'bi-gear', 'fields' => [
            'site_name' => T('Site / brand name', 'Dial Infini'),
            'tagline'   => T('Tagline', 'Call anyone, anywhere'),
            'site_url'  => T('Website URL', 'https://dialinfini.com', 'Used for canonical links, sitemap & social tags. No trailing slash.'),
            'clean_urls' => TOG('Clean URLs (/pricing instead of /pricing.php)', 1, 'Needs Apache mod_rewrite (standard on cPanel hosting). Turn OFF if pages show 404.'),
            'topbar_on'   => TOG('Show announcement bar', 1),
            'topbar_text' => T('Announcement text', 'New: get a real phone number from any country in minutes.'),
            'topbar_link_text' => T('Announcement link text', 'See pricing'),
            'topbar_link' => LNK('Announcement link', '/pricing'),
        ]],
        'branding' => ['label' => 'Logo & Colours', 'icon' => 'bi-palette', 'fields' => [
            'logo'    => IMG('Logo (icon)', 'assets/img/logo.svg', 'SVG or PNG, shown next to the brand name'),
            'favicon' => IMG('Favicon', 'assets/img/favicon.svg', 'SVG / PNG / ICO'),
            'footer_logo' => IMG('Footer logo (optional)', '', 'Separate logo for the dark footer — use a light / white version. Leave empty to use the main logo on a white badge.'),
            'footer_logo_height' => T('Footer logo height (px)', '56', 'Between 24 and 120'),
            'color_primary' => ['type' => 'color', 'label' => 'Primary colour', 'default' => '#0287BF'],
            'color_bg'      => ['type' => 'color', 'label' => 'Light background', 'default' => '#F5F7FB'],
            'color_dark'    => ['type' => 'color', 'label' => 'Dark / text colour', 'default' => '#101828'],
        ]],
        'contact' => ['label' => 'Contact Details', 'icon' => 'bi-telephone', 'fields' => [
            'email'   => T('Main email', 'hello@dialinfini.com', 'Shown in the footer and on the Contact page'),
            'phone'   => T('Main phone', '', 'Shown in the footer and on the Contact page'),
            'address' => TA('Main address', '', 'Shown in the footer and on the Contact page'),
            'more_emails' => LST('More emails (optional)', ['label' => T('Label', '', 'e.g. Support, Sales'), 'value' => T('Email address')], [], 'Click "Add" for as many as you like — they appear in the footer and on the Contact page'),
            'more_phones' => LST('More phone numbers (optional)', ['label' => T('Label', '', 'e.g. Sales, WhatsApp'), 'value' => T('Phone number')], [], 'Click "Add" for as many as you like — they appear in the footer and on the Contact page'),
            'more_addresses' => LST('More addresses (optional)', ['label' => T('Label', '', 'e.g. Dubai office'), 'value' => TA('Address')], [], 'Click "Add" for as many as you like — they appear in the footer and on the Contact page'),
            'hours'   => T('Support hours', 'Mon – Fri, 9:00 – 18:00'),
            'appstore_url'  => T('App Store link', '', 'Shows an "App Store" button when filled'),
            'playstore_url' => T('Google Play link', '', 'Shows a "Google Play" button when filled'),
            'facebook'  => T('Facebook URL', ''),
            'instagram' => T('Instagram URL', ''),
            'x_twitter' => T('X (Twitter) URL', ''),
            'linkedin'  => T('LinkedIn URL', ''),
            'youtube'   => T('YouTube URL', ''),
        ]],
        'navigation' => ['label' => 'Menu & Footer', 'icon' => 'bi-list', 'fields' => [
            'header_link_text' => T('Header small link text (optional)', '', 'e.g. Log in  /  My account — shown before the button'),
            'header_link' => LNK('Header small link URL', ''),
            'header_cta_text' => T('Header button text', 'Get started'),
            'header_cta_link' => LNK('Header button link', '/contact'),
            'nav' => LST('Main menu', ['label' => T('Label'), 'link' => LNK('Link')], [
                ['label' => 'Home', 'link' => '/'], ['label' => 'Features', 'link' => '/features'],
                ['label' => 'Pricing', 'link' => '/pricing'], ['label' => 'About', 'link' => '/about'], ['label' => 'Contact', 'link' => '/contact'],
            ]),
            'footer_about' => TA('Footer description', 'Dial Infini gives you a real phone number from any country, HD calls, messaging and voicemail — all in one simple app.'),
            'footer_links_1_title' => T('Footer column 1 title', 'Product'),
            'footer_links_1' => LST('Footer column 1 links', ['label' => T('Label'), 'link' => LNK('Link')], [
                ['label' => 'Features', 'link' => '/features'], ['label' => 'Pricing', 'link' => '/pricing'], ['label' => 'Get a number', 'link' => '/pricing#numbers'],
            ]),
            'footer_links_2_title' => T('Footer column 2 title', 'Company'),
            'footer_links_2' => LST('Footer column 2 links', ['label' => T('Label'), 'link' => LNK('Link')], [
                ['label' => 'About us', 'link' => '/about'], ['label' => 'Contact', 'link' => '/contact'],
            ]),
            'footer_links_3_title' => T('Footer column 3 title', 'Legal'),
            'footer_links_3' => LST('Footer column 3 links', ['label' => T('Label'), 'link' => LNK('Link')], [
                ['label' => 'Privacy Policy', 'link' => '/privacy'], ['label' => 'Terms of Service', 'link' => '/terms'],
            ]),
            'copyright' => T('Copyright line', '© {year} Dial Infini. All rights reserved.', '{year} is replaced automatically'),
        ]],
        'seo' => ['label' => 'Global SEO', 'icon' => 'bi-search', 'fields' => [
            'title_suffix' => T('Title suffix', ' | Dial Infini', 'Added after every page title'),
            'default_og'   => IMG('Default social share image', 'uploads/og-default.jpg'),
            'twitter_handle' => T('Twitter / X handle', '', 'e.g. @dialinfini'),
            'google_verification' => T('Google Search Console verification code', '', 'Only the content="…" value'),
            'bing_verification'   => T('Bing verification code', ''),
            'schema_on' => TOG('Add Organization structured data (JSON-LD)', 1),
            'robots_extra' => TA('Extra robots.txt lines', '', 'One per line, e.g. Disallow: /private/'),
        ]],
        'code' => ['label' => 'Scripts & Custom Code', 'icon' => 'bi-code-slash', 'fields' => [
            'head_code' => ['type' => 'html', 'label' => 'Code in <head>', 'default' => '', 'hint' => 'Google Analytics, Meta Pixel, Search Console tags…'],
            'body_code' => ['type' => 'html', 'label' => 'Code before </body>', 'default' => '', 'hint' => 'Chat widgets, tracking scripts…'],
            'custom_css' => ['type' => 'html', 'label' => 'Custom CSS', 'default' => '', 'hint' => 'Plain CSS, no <style> tags'],
        ]],
        'forms' => ['label' => 'Contact Form', 'icon' => 'bi-envelope-paper', 'fields' => [
            'notify_email' => T('Send new messages to', 'hello@dialinfini.com', 'Comma separate for several addresses'),
            'auto_reply_on' => TOG('Send auto-reply to the visitor', 1),
            'auto_reply_subject' => T('Auto-reply subject', 'We received your message'),
            'auto_reply_body' => TA('Auto-reply message', "Hi {name},\n\nThanks for contacting Dial Infini. We've received your message and will get back to you within one business day.\n\n— The Dial Infini team"),
        ]],
    ];
}

function smtp_fields() {
    return [
        'smtp_on'   => TOG('Send emails via SMTP', 0, 'When off, PHP mail() is used (often goes to spam)'),
        'smtp_host' => T('SMTP host', '', 'e.g. mail.dialinfini.com or smtp.gmail.com'),
        'smtp_port' => T('Port', '465', '465 (SSL) • 587 (TLS) • 25 (none)'),
        'smtp_secure' => SEL('Encryption', ['ssl' => 'SSL (port 465)', 'tls' => 'STARTTLS (port 587)', 'none' => 'None'], 'ssl'),
        'smtp_user' => T('SMTP username', '', 'Usually the full email address'),
        'smtp_pass' => PW('SMTP password', '', 'Leave blank to keep the saved password'),
        'smtp_from' => T('From email', '', 'Should match your SMTP account, e.g. no-reply@dialinfini.com'),
        'smtp_from_name' => T('From name', 'Dial Infini'),
    ];
}

function pages_schema() {
    static $s = null;
    if ($s) return $s;
    $item_icon = ['icon' => T('Icon (Bootstrap Icons name)', 'bi-stars'), 'title' => T('Title'), 'text' => TA('Text')];
    $s = [
    /* ================= HOME ================= */
    'home' => ['label' => 'Home', 'slug' => '', 'icon' => 'bi-house',
      'seo' => ['title' => 'Dial Infini – Call anyone, anywhere with your own number', 'description' => 'Get a real phone number from any country. Crystal-clear HD calls, unlimited messaging and voicemail in one app — with zero hidden fees.', 'keywords' => 'virtual phone number, international calls, VoIP app, SMS, voicemail, Dial Infini'],
      'sections' => [
        'hero' => ['label' => 'Hero', 'fields' => [
            'eyebrow' => T('Small label above title', 'Calls • Messages • Voicemail'),
            'title'   => T('Headline', 'Call anyone, anywhere with *your own number*', 'Put *words* between stars to colour them'),
            'text'    => TA('Sub-text', 'Get a real phone number from any country, enjoy HD-quality voice calls and send messages without limits — with zero hidden fees.'),
            'cta1_text' => T('Primary button', 'Get started'), 'cta1_link' => LNK('Primary link', '/contact'),
            'cta2_text' => T('Secondary button', 'See how it works'), 'cta2_link' => LNK('Secondary link', '#how'),
            'note'    => T('Small note under buttons', 'No contracts. Top up only what you use.'),
            'img_main'   => IMG('Main app screenshot', 'uploads/app-messages.webp'),
            'img_phone1' => IMG('Phone screenshot (left)', 'uploads/phone-dialpad.webp'),
            'img_phone2' => IMG('Phone screenshot (right)', 'uploads/phone-call-anyone.webp'),
        ]],
        'stats' => ['label' => 'Statement + Stats', 'fields' => [
            'kicker' => T('Small label', 'Why Dial Infini'),
            'title' => T('Heading', 'Staying connected shouldn’t cost a *fortune* — or a SIM card.', 'Words reveal one by one as visitors scroll. *Stars* = coloured'),
            'items' => LST('Stats', ['icon' => T('Icon (Bootstrap Icons name)', 'bi-stars'), 'value' => T('Big value'), 'label' => T('Description')], [
                ['icon' => 'bi-cash-coin', 'value' => '0', 'label' => 'hidden fees. You only pay for the calls, texts and numbers you use.'],
                ['icon' => 'bi-soundwave', 'value' => 'HD', 'label' => 'crystal-clear voice and video calls to anyone, worldwide.'],
                ['icon' => 'bi-phone', 'value' => '1', 'label' => 'app for your numbers, calls, messages and voicemail.'],
            ]),
        ]],
        'products' => ['label' => 'Product cards', 'fields' => [
            'title' => T('Heading', 'One app that turns your phone into a *global line*'),
            'text'  => TA('Sub-text', 'Everything you need to talk to the world, beautifully simple.'),
            'items' => LST('Cards', ['tag' => T('Small tag'), 'title' => T('Title'), 'text' => TA('Text'), 'image' => IMG('Image'), 'link' => LNK('Link'), 'link_text' => T('Link text')], [
                ['tag' => 'Numbers', 'title' => 'Your own number, any country', 'text' => 'Pick a real local number in dozens of countries and stay reachable everywhere — monthly, quarterly or yearly.', 'image' => 'uploads/phone-get-number.webp', 'link' => '/features', 'link_text' => 'Explore numbers'],
                ['tag' => 'Calls', 'title' => 'HD calls at honest rates', 'text' => 'Crystal-clear voice calls with a built-in dial pad, recents and call history. See the per-minute price before you call.', 'image' => 'uploads/phone-dialpad.webp', 'link' => '/features', 'link_text' => 'Explore calls'],
                ['tag' => 'Messages', 'title' => 'Message without limits', 'text' => 'Send and receive text messages from your own number. Keep every conversation tidy in one inbox.', 'image' => 'uploads/phone-messages.webp', 'link' => '/features', 'link_text' => 'Explore messaging'],
                ['tag' => 'Voicemail', 'title' => 'Voicemail you can see', 'text' => 'Play, call back or delete missed messages straight from a clean waveform inbox — unlimited, included.', 'image' => 'uploads/phone-voicemail.webp', 'link' => '/features', 'link_text' => 'Explore voicemail'],
            ]),
        ]],
        'showcase' => ['label' => 'Feature showcase (alternating)', 'fields' => [
            'items' => LST('Showcase blocks', ['tag' => T('Small tag'), 'title' => T('Title'), 'text' => TA('Text'), 'bullets' => TA('Bullet points', '', 'One per line'), 'image' => IMG('Screenshot')], [
                ['tag' => 'Calling', 'title' => 'A dial pad that feels at home', 'text' => 'Choose which of your numbers to call from, pick the country, and dial. Rates and balance are always one tap away.', 'bullets' => "Switch between your numbers instantly\nRecents and contacts in one place\nTop up credit in seconds", 'image' => 'uploads/app-dialpad-crop.webp'],
                ['tag' => 'Messaging', 'title' => 'Conversations, beautifully organised', 'text' => 'A familiar chat inbox with search, unread filters and favourites — so nothing important gets lost.', 'bullets' => "Per-number conversations\nUnread and favourite filters\nClear per-message pricing", 'image' => 'uploads/app-messages.webp'],
                ['tag' => 'Voicemail', 'title' => 'Never miss what matters', 'text' => 'Every voicemail shows up with a waveform, timestamp and one-tap call back or delete.', 'bullets' => "Play right in the app\nOne-tap call back\nUnlimited voicemail included", 'image' => 'uploads/app-voicemail.webp'],
            ]),
        ]],
        'how' => ['label' => 'How it works', 'fields' => [
            'title' => T('Heading', 'Up and running in *four simple steps*'),
            'text'  => TA('Sub-text', 'From download to your first call in just a few minutes.'),
            'items' => LST('Steps', ['title' => T('Title'), 'text' => TA('Text'), 'image' => IMG('Screen')], [
                ['title' => 'Enter your number', 'text' => 'Choose your country and enter your mobile number to get a one-time code.', 'image' => 'uploads/phone-step-1.webp'],
                ['title' => 'Verify the code', 'text' => 'Type the code we send you to confirm it’s really you.', 'image' => 'uploads/phone-step-2.webp'],
                ['title' => 'Create your account', 'text' => 'Add your name and email — that’s all we need to set you up.', 'image' => 'uploads/phone-step-3.webp'],
                ['title' => 'Confirm & call', 'text' => 'Verify your email, pick a number and start calling anyone, anywhere.', 'image' => 'uploads/phone-step-4.webp'],
            ]),
        ]],
        'usecases' => ['label' => 'Who it’s for', 'fields' => [
            'title' => T('Heading', 'Built for people who live *across borders*'),
            'items' => LST('Cards', $item_icon, [
                ['icon' => 'bi-airplane', 'title' => 'Travellers', 'text' => 'Skip roaming fees. Keep one number and call home from anywhere.'],
                ['icon' => 'bi-laptop', 'title' => 'Freelancers & remote teams', 'text' => 'Give clients a local number in their country, wherever you work from.'],
                ['icon' => 'bi-shop', 'title' => 'Small businesses', 'text' => 'Separate business calls from personal ones and never miss a customer.'],
                ['icon' => 'bi-people', 'title' => 'Families & expats', 'text' => 'Stay close to loved ones overseas with clear, affordable calls and texts.'],
            ]),
        ]],
        'pricing' => ['label' => 'Pricing teaser', 'fields' => [
            'title' => T('Heading', 'Simple, honest *pay-as-you-go* pricing'),
            'text'  => TA('Sub-text', 'Top up credit and use it for calls and messages. Numbers are billed on a plan that suits you.'),
            'items' => LST('Cards', ['value' => T('Big value'), 'unit' => T('Unit'), 'title' => T('Title'), 'text' => T('Text')], [
                ['value' => '$4.15', 'unit' => '/month', 'title' => 'Your own number', 'text' => 'Real numbers from 1 month to 1 year'],
                ['value' => '3.6¢', 'unit' => '/min', 'title' => 'Landline calls', 'text' => 'Mobile calls from 7.2¢/min'],
                ['value' => '8.4¢', 'unit' => '/SMS', 'title' => 'National SMS', 'text' => 'Inbound SMS & calls are unlimited'],
            ]),
            'cta_text' => T('Button text', 'See full pricing'), 'cta_link' => LNK('Button link', '/pricing'),
        ]],
        'cta' => ['label' => 'Final call-to-action', 'fields' => [
            'title' => T('Heading', 'Get your global number today'),
            'text'  => TA('Text', 'Start with one number. Add more whenever you need them.'),
            'cta1_text' => T('Primary button', 'Get started'), 'cta1_link' => LNK('Primary link', '/contact'),
            'cta2_text' => T('Secondary button', 'View pricing'), 'cta2_link' => LNK('Secondary link', '/pricing'),
            'image' => IMG('Image', 'uploads/app-login.webp'),
        ]],
      ]],

    /* ================= FEATURES ================= */
    'features' => ['label' => 'Features', 'slug' => 'features', 'icon' => 'bi-grid',
      'seo' => ['title' => 'Features – Calls, Messages, Voicemail & Numbers', 'description' => 'Explore Dial Infini: HD calling, messaging from your own number, visual voicemail, call history and real phone numbers in dozens of countries.', 'keywords' => 'virtual number features, VoIP calling, visual voicemail, international SMS'],
      'sections' => [
        'hero' => ['label' => 'Page header', 'fields' => [
            'eyebrow' => T('Label', 'Features'),
            'title' => T('Title', 'Everything you need to *talk to the world*'),
            'text' => TA('Text', 'A complete phone experience in one app: numbers, calls, messages and voicemail, designed to be effortless.'),
        ]],
        'blocks' => ['label' => 'Feature blocks', 'fields' => [
            'items' => LST('Blocks (alternate left/right)', ['tag' => T('Small tag'), 'title' => T('Title'), 'text' => TA('Text'), 'bullets' => TA('Bullet points', '', 'One per line'), 'image' => IMG('Screenshot')], [
                ['tag' => 'Dial pad', 'title' => 'Call anyone with a clean, fast dial pad', 'text' => 'Pick the number you want to call from, choose the destination country and dial. Your balance is always in view.', 'bullets' => "Multiple numbers, one tap to switch\nCountry selector with flags\nHD voice quality", 'image' => 'uploads/app-dialpad-crop.webp'],
                ['tag' => 'Recents & contacts', 'title' => 'Every call and contact, at a glance', 'text' => 'See incoming, outgoing and missed calls with duration and the number used. Open a contact to call, message, block or delete.', 'bullets' => "Full call history per contact\nMissed calls highlighted\nBlock unwanted callers", 'image' => 'uploads/app-calls.webp'],
                ['tag' => 'Messaging', 'title' => 'Message from your own number', 'text' => 'Send and receive texts in a modern chat inbox with unread filters, favourites and search.', 'bullets' => "Per-message pricing shown upfront\nUnread & favourites filters\nEmoji and attachments", 'image' => 'uploads/app-messages.webp'],
                ['tag' => 'Voicemail', 'title' => 'Visual voicemail that saves time', 'text' => 'Listen with a waveform player, call back instantly or delete with one tap. Unlimited voicemail is included.', 'bullets' => "Waveform playback\nNew-message highlighting\nCall back in one tap", 'image' => 'uploads/app-voicemail.webp'],
                ['tag' => 'Numbers', 'title' => 'Real numbers in countries around the world', 'text' => 'Browse popular countries or search for any destination. See the monthly price and expiry date for each number you own.', 'bullets' => "Monthly, quarterly, 6‑month & yearly plans\nSet a default number\nTravel-date numbers", 'image' => 'uploads/app-numbers.webp'],
            ]),
        ]],
        'grid' => ['label' => 'More features grid', 'fields' => [
            'title' => T('Heading', 'And a whole lot more'),
            'items' => LST('Cards', $item_icon, [
                ['icon' => 'bi-wallet2', 'title' => 'Credit wallet', 'text' => 'Top up your balance and watch recent activity and costs in real time.'],
                ['icon' => 'bi-telephone-inbound', 'title' => 'Unlimited inbound', 'text' => 'Receiving calls, SMS and voicemail on your number is unlimited.'],
                ['icon' => 'bi-globe2', 'title' => 'Global coverage', 'text' => 'Numbers and calling to a growing list of countries.'],
                ['icon' => 'bi-shield-check', 'title' => 'Secure sign-up', 'text' => 'One-time code and email verification protect every account.'],
                ['icon' => 'bi-slash-circle', 'title' => 'Block & manage', 'text' => 'Block nuisance callers and keep your contacts organised.'],
                ['icon' => 'bi-calendar-event', 'title' => 'Travel numbers', 'text' => 'Get a local number just for the dates you’re travelling.'],
            ]),
        ]],
        'cta' => ['label' => 'Call-to-action', 'fields' => [
            'title' => T('Heading', 'Ready to get your number?'),
            'text' => TA('Text', 'Create your account in minutes and make your first call today.'),
            'cta_text' => T('Button', 'Get started'), 'cta_link' => LNK('Link', '/contact'),
        ]],
      ]],

    /* ================= PRICING ================= */
    'pricing' => ['label' => 'Pricing', 'slug' => 'pricing', 'icon' => 'bi-tag',
      'seo' => ['title' => 'Pricing – Phone numbers from $4.15/month', 'description' => 'Transparent pricing for virtual phone numbers, calls and SMS. Pay-as-you-go credit with unlimited inbound calls, SMS and voicemail.', 'keywords' => 'virtual number pricing, international call rates, SMS rates'],
      'sections' => [
        'hero' => ['label' => 'Page header', 'fields' => [
            'eyebrow' => T('Label', 'Pricing'),
            'title' => T('Title', 'Simple pricing, *no surprises*'),
            'text' => TA('Text', 'Choose a number plan that fits you, then top up credit for outgoing calls and messages. Rates are shown before you ever press call.'),
        ]],
        'calc' => ['label' => 'Price calculator', 'fields' => [
            'title' => T('Heading', 'Pick a country, see the price'),
            'text' => TA('Text', 'Choose where you want your number and where you want to call. Prices update instantly.'),
            'phones' => TA('Phone number prices', "us|USA|6.99|9.99|24.99|Inbound calls: 1.2¢/min, inbound SMS are free\ngb|United Kingdom|7.99|11.99|29.99|Inbound calls and inbound SMS are free\nca|Canada|6.49|9.49|23.99|Inbound calls and inbound SMS are free\nau|Australia|4.15|12.45|49.80|Inbound calls and inbound SMS are free", 'One country per line:  code|Name|1-month price|3-month price|12-month price|Inbound note.   code = 2-letter country code (used for the flag).   Prices in USD. Savings % are calculated automatically.'),
            'rates' => TA('Calling & SMS rates', "pk|Pakistan|7.2|7.2|22.8\nus|USA|1.2|1.2|0.8\ngb|United Kingdom|2.4|2.4|4.5\nin|India|3.1|3.1|3.9\nae|United Arab Emirates|9.6|9.6|11.2", 'One country per line:  code|Name|Mobile ¢/min|Landline ¢/min|SMS ¢/msg.   Values in US cents.'),
            'eur' => T('EUR per 1 USD', '0.92'), 'gbp' => T('GBP per 1 USD', '0.79'),
            'phone_title' => T('Left card title', 'Phone Number'), 'phone_sub' => T('Left card subtitle', 'Subscribe to a local number'),
            'rates_title' => T('Right card title', 'Calling & SMS Rates'), 'rates_sub' => T('Right card subtitle', 'Outgoing call & SMS prices by destination'),
            'phone_link_text' => T('Left link text', 'Get a {country} number'), 'rates_link_text' => T('Right link text', 'Need bulk rates for {country}? Talk to us'),
            'cta_link' => LNK('Link target', '/contact'),
            'footnote' => T('Footnote', 'Prices are indicative and may change. Taxes may apply.'),
        ]],
        'included' => ['label' => 'What’s included', 'fields' => [
            'title' => T('Heading', 'Included with every number'),
            'items' => LST('Items', ['icon' => T('Icon', 'bi-check2-circle'), 'title' => T('Title'), 'text' => T('Text')], [
                ['icon' => 'bi-chat-dots', 'title' => 'Unlimited inbound SMS', 'text' => 'Receive as many texts as you like'],
                ['icon' => 'bi-telephone-inbound', 'title' => 'Unlimited inbound calls', 'text' => 'Anyone can call you, free'],
                ['icon' => 'bi-voicemail', 'title' => 'Unlimited voicemail', 'text' => 'Included on every number'],
                ['icon' => 'bi-send', 'title' => '8.4¢ per SMS', 'text' => 'National text messages'],
                ['icon' => 'bi-telephone', 'title' => '3.6¢ per minute', 'text' => 'Landline calls'],
                ['icon' => 'bi-phone', 'title' => '7.2¢ per minute', 'text' => 'Mobile calls'],
            ]),
        ]],
        'credit' => ['label' => 'Credit section', 'fields' => [
            'title' => T('Heading', 'Top up once, use it anywhere'),
            'text' => TA('Text', 'Add credit to your wallet and it covers outgoing calls and messages to any destination. Track every charge in your recent activity — no hidden fees, ever.'),
            'bullets' => TA('Bullets', "Instant top-ups\nTransparent per-minute and per-SMS pricing\nFull activity history", 'One per line'),
            'image' => IMG('Screenshot', 'uploads/phone-credit.webp'),
        ]],
        'faq' => ['label' => 'FAQ', 'fields' => [
            'title' => T('Heading', 'Frequently asked questions'),
            'items' => LST('Questions', ['q' => T('Question'), 'a' => TA('Answer')], [
                ['q' => 'How does Dial Infini work?', 'a' => 'Create an account, choose a phone number from the country you like, add credit and start calling and messaging from the app.'],
                ['q' => 'Are there any hidden fees?', 'a' => 'No. Every call and message rate is shown in the app before you use it, and inbound calls, SMS and voicemail are unlimited.'],
                ['q' => 'Can I have more than one number?', 'a' => 'Yes. You can own several numbers from different countries and switch between them from the dial pad.'],
                ['q' => 'What happens when my number plan ends?', 'a' => 'You’ll see the expiry date for every number in the app, so you can renew whenever you’re ready.'],
                ['q' => 'How do I top up credit?', 'a' => 'Open the Credit tab, choose Top Up Credit and add funds. The balance is available immediately.'],
            ]),
        ]],
      ]],

    /* ================= ABOUT ================= */
    'about' => ['label' => 'About', 'slug' => 'about', 'icon' => 'bi-info-circle',
      'seo' => ['title' => 'About Dial Infini', 'description' => 'Dial Infini makes global communication simple: real phone numbers, HD calls and messaging with zero hidden fees.', 'keywords' => 'about Dial Infini'],
      'sections' => [
        'hero' => ['label' => 'Page header', 'fields' => [
            'eyebrow' => T('Label', 'About us'),
            'title' => T('Title', 'Communication that *has no borders*'),
            'text' => TA('Text', 'We build tools that let people stay connected to the ones who matter, wherever life takes them.'),
        ]],
        'story' => ['label' => 'Our story', 'fields' => [
            'title' => T('Heading', 'Why we built Dial Infini'),
            'text' => TA('Text', "Staying in touch across countries shouldn’t mean juggling SIM cards, confusing plans or surprise bills.\n\nDial Infini puts a real phone number from any country in your pocket, with clear rates, HD calls, messaging and voicemail in one beautifully simple app.\n\nEdit this text from your dashboard to tell your own story."),
            'image' => IMG('Image', 'uploads/app-login.webp'),
        ]],
        'values' => ['label' => 'Our values', 'fields' => [
            'title' => T('Heading', 'What we stand for'),
            'items' => LST('Values', $item_icon, [
                ['icon' => 'bi-eye', 'title' => 'Transparency', 'text' => 'Clear prices, shown before you spend. No hidden fees.'],
                ['icon' => 'bi-lightning-charge', 'title' => 'Simplicity', 'text' => 'Powerful features wrapped in an interface anyone can use.'],
                ['icon' => 'bi-shield-lock', 'title' => 'Trust', 'text' => 'Verified accounts and secure sign-in keep you protected.'],
                ['icon' => 'bi-globe-americas', 'title' => 'Global by design', 'text' => 'Built for people and businesses who connect across borders.'],
            ]),
        ]],
        'cta' => ['label' => 'Call-to-action', 'fields' => [
            'title' => T('Heading', 'Join the conversation'),
            'text' => TA('Text', 'Get your first number and see how simple global calling can be.'),
            'cta_text' => T('Button', 'Get started'), 'cta_link' => LNK('Link', '/contact'),
        ]],
      ]],

    /* ================= CONTACT ================= */
    'contact' => ['label' => 'Contact', 'slug' => 'contact', 'icon' => 'bi-envelope',
      'seo' => ['title' => 'Contact Us', 'description' => 'Questions about Dial Infini? Send us a message and our team will get back to you within one business day.', 'keywords' => 'contact Dial Infini, support'],
      'sections' => [
        'hero' => ['label' => 'Page header', 'fields' => [
            'eyebrow' => T('Label', 'Contact'),
            'title' => T('Title', 'Let’s *talk*'),
            'text' => TA('Text', 'Questions about numbers, pricing or your account? Send us a message and we’ll reply within one business day.'),
        ]],
        'form' => ['label' => 'Form texts', 'fields' => [
            'title' => T('Form heading', 'Send us a message'),
            'topics' => TA('Topic choices', "General question\nPricing\nNumbers & countries\nTechnical support\nPartnerships", 'One per line'),
            'button' => T('Submit button', 'Send message'),
            'success' => T('Success message', 'Thank you! Your message has been sent — we’ll be in touch shortly.'),
            'side_title' => T('Side box heading', 'Prefer to reach us directly?'),
            'side_text' => TA('Side box text', 'Our team is happy to help with anything you need.'),
        ]],
      ]],

    /* ================= PRIVACY ================= */
    'privacy' => ['label' => 'Privacy Policy', 'slug' => 'privacy', 'icon' => 'bi-shield-lock',
      'seo' => ['title' => 'Privacy Policy', 'description' => 'How Dial Infini collects, uses and protects your personal information.', 'keywords' => ''],
      'sections' => [
        'content' => ['label' => 'Page content', 'fields' => [
            'title' => T('Title', 'Privacy Policy'),
            'updated' => T('Last updated', 'October 2026'),
            'body' => HTMLF('Content', "<p>This Privacy Policy explains how Dial Infini (“we”, “us”) collects, uses and protects information when you use our website and app.</p>\n<h2>Information we collect</h2>\n<ul><li><strong>Account information:</strong> your name, email address and phone number.</li><li><strong>Usage information:</strong> call and message metadata needed to provide and bill the service.</li><li><strong>Technical information:</strong> device type, IP address and cookies used to keep the site secure and working.</li></ul>\n<h2>How we use information</h2>\n<p>We use your information to provide and improve the service, process payments, prevent fraud, provide support and comply with legal obligations.</p>\n<h2>Sharing</h2>\n<p>We do not sell your personal information. We share data only with service providers who help us operate the service, or when required by law.</p>\n<h2>Your rights</h2>\n<p>You may request access to, correction of or deletion of your personal information by contacting us.</p>\n<h2>Contact</h2>\n<p>Questions about this policy? Reach out via our contact page.</p>\n<p><em>This is a general template — please have it reviewed to match your business and local law.</em></p>"),
        ]],
      ]],

    /* ================= TERMS ================= */
    'terms' => ['label' => 'Terms of Service', 'slug' => 'terms', 'icon' => 'bi-file-text',
      'seo' => ['title' => 'Terms of Service', 'description' => 'The terms that apply when you use Dial Infini’s website, app and services.', 'keywords' => ''],
      'sections' => [
        'content' => ['label' => 'Page content', 'fields' => [
            'title' => T('Title', 'Terms of Service'),
            'updated' => T('Last updated', 'October 2026'),
            'body' => HTMLF('Content', "<p>By using Dial Infini you agree to these Terms. Please read them carefully.</p>\n<h2>The service</h2>\n<p>Dial Infini provides phone numbers, calling, messaging and voicemail over the internet. Availability and features may vary by country.</p>\n<h2>Accounts</h2>\n<p>You must provide accurate information and keep your login secure. You are responsible for activity under your account.</p>\n<h2>Payments and credit</h2>\n<p>Number plans and credit are billed as shown in the app. Rates are displayed before you use the service. Unused credit and plan fees may be non-refundable except where required by law.</p>\n<h2>Acceptable use</h2>\n<p>You may not use the service for spam, fraud, harassment or any unlawful activity. We may suspend accounts that breach these rules.</p>\n<h2>Liability</h2>\n<p>The service is provided “as is”. To the extent permitted by law, we are not liable for indirect or consequential losses.</p>\n<h2>Changes</h2>\n<p>We may update these Terms from time to time. Continued use means you accept the updated Terms.</p>\n<p><em>This is a general template — please have it reviewed to match your business and local law.</em></p>"),
        ]],
      ]],
    ];
    return $s;
}
