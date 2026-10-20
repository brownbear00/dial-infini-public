DIAL INFINI  -  dialinfini.com
===================================

UPLOAD (cPanel / any PHP hosting, PHP 7.4+ / 8.x)
1. File Manager -> public_html -> Upload dialinfini.zip -> Extract.
   Make sure index.php and .htaccess sit directly inside public_html
   (move the files out of the "dialinfini" folder if the zip created one).
2. Folders /data and /uploads must be writable (permission 755, or 775 if saving fails).
3. Open https://dialinfini.com  -> website is live.
4. Open https://dialinfini.com/admin/  -> log in -> you must set a new password.
5. Dashboard -> SMTP / Email -> enter your mailbox details -> "Send test".
6. Enable the free SSL certificate in cPanel (AutoSSL / Let's Encrypt).
7. Dashboard -> SEO: submit https://dialinfini.com/sitemap.xml in Google Search Console.

DASHBOARD
- Pages & Content : every heading, text, button, link, image and list item on every page
- SEO             : title, description, keywords, social image, indexing - per page
- Media Library   : upload / delete images (WebP/JPG keep the site fast)
- Messages        : contact-form submissions (also emailed to you), CSV export
- SMTP / Email    : host, port, SSL/TLS, user, password, from-address + test button
- Site Settings   : logo, favicon, colours, menu, footer, contact info, social links,
                    app-store links, announcement bar, analytics/head/body code, custom CSS

NOTES
- No database needed: content lives in /data (blocked from the web).
- Backup: download /data and /uploads occasionally.
- If pages show 404 (host without mod_rewrite): Settings -> General -> turn OFF "Clean URLs".
- Pricing figures and the Privacy/Terms pages are editable templates - review before launch.
