<?php
$footerWhatsapp = company_whatsapp_href();
$footerCompanyName = company_setting('company_name');
$footerLogo = company_asset_href('company_logo');
$footerSocialChannels = company_social_links();
$footerPhone = company_phone_display();
$footerEmail = company_setting('company_email');
$footerAddress = company_setting('company_address');
$footerBusinessHours = company_setting('company_business_hours');
?>
<footer class="site-footer">
    <div class="site-footer__main">
        <div class="footer-column footer-brand">
            <a class="brand brand--footer" href="/" aria-label="<?= e($footerCompanyName) ?> home">
                <img class="brand__mark" src="<?= e($footerLogo) ?>" alt="" width="40" height="40">
                <span class="brand__name"><?= e($footerCompanyName) ?></span>
            </a>
            <p><?= e(company_setting('company_tagline')) ?></p>
            <div class="footer-social" aria-label="Social media channels">
                <?php foreach ($footerSocialChannels as $key => $social): ?>
                    <a href="<?= e($social['href']) ?>"
                       class="footer-social__link footer-social__link--<?= e($key) ?>"
                       target="_blank"
                       rel="noopener noreferrer"
                       aria-label="<?= e($social['label']) ?>"
                       title="<?= e($social['label']) ?>">
                        <?= social_icon_svg($social['icon'], 20) ?>
                        <span class="visually-hidden"><?= e($social['label']) ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="footer-column footer-links-col">
            <h4>Quick Links</h4>
            <ul class="footer-links">
                <li><a href="/about.php">About</a></li>
                <li><a href="/services.php">Services</a></li>
                <li><a href="/projects.php">Projects</a></li>
                <li><a href="/blog.php">Insights</a></li>
                <li><a href="/contact.php">Contact</a></li>
            </ul>
        </div>
        <div class="footer-column footer-services-col">
            <h4>Core Services</h4>
            <ul class="footer-links">
                <li><a href="/service.php?slug=building-construction">Building Construction</a></li>
                <li><a href="/service.php?slug=civil-works">Civil Works</a></li>
                <li><a href="/service.php?slug=renovation-remodeling">Renovation</a></li>
                <li><a href="/service.php?slug=road-works">Road Works</a></li>
                <li><a href="/service.php?slug=roofing">Roofing</a></li>
            </ul>
        </div>
        <div class="footer-column footer-contact-col">
            <h4>Contact</h4>
            <ul class="footer-contact">
                <?php if ($footerPhone !== ''): ?>
                    <li class="footer-contact-item">
                        <strong>Phone</strong><br>
                        <a href="<?= e($footerWhatsapp ?: company_phone_href()) ?>"><?= e($footerPhone) ?></a>
                    </li>
                <?php endif; ?>
                <?php if ($footerWhatsapp !== ''): ?>
                    <li class="footer-contact-item">
                        <strong>WhatsApp</strong><br>
                        <a href="<?= e($footerWhatsapp) ?>" target="_blank" rel="noopener noreferrer">WhatsApp us</a>
                    </li>
                <?php endif; ?>
                <?php if ($footerEmail !== ''): ?>
                    <li class="footer-contact-item">
                        <strong>Email</strong><br>
                        <a href="mailto:<?= e($footerEmail) ?>"><?= e($footerEmail) ?></a>
                    </li>
                <?php endif; ?>
                <li class="footer-contact-item">
                    <strong>Location</strong><br>
                    <span><?= e($footerAddress) ?></span>
                </li>
                <?php if ($footerBusinessHours !== ''): ?>
                    <li class="footer-contact-item">
                        <strong>Hours</strong><br>
                        <span><?= e($footerBusinessHours) ?></span>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
        <div class="footer-column footer-newsletter-col">
            <h4>Newsletter</h4>
            <p class="footer-newsletter-text">
                Subscribe to get quarterly market insights, building cost tips, and new project reveals.
            </p>
            <form action="/subscribe.php" method="POST" class="footer-newsletter-form" data-newsletter-form>
                <div style="position: absolute; left: -9999px; opacity: 0;" aria-hidden="true">
                    <input type="text" name="website_hp" tabindex="-1" autocomplete="off">
                </div>
                <input type="hidden" name="source" value="footer">
                <div class="footer-newsletter-wrap">
                    <input type="email" name="email" placeholder="Your email..." required class="footer-newsletter-input" autocomplete="email">
                    <button type="submit" class="footer-newsletter-btn" aria-label="Subscribe to newsletter" data-newsletter-submit>
                        <span aria-hidden="true">→</span>
                    </button>
                </div>
                <div class="newsletter-form__status" data-newsletter-status aria-live="polite"></div>
            </form>
        </div>
    </div>
    <div class="site-footer__bottom">
        <span>&copy; <?= date('Y') ?> <?= e($footerCompanyName) ?>. All rights reserved.</span>
        <span><?= e($footerAddress) ?> <span aria-hidden="true">·</span> <?= e($footerCompanyName) ?></span>
    </div>
</footer>
<?php $whatsappLink = company_whatsapp_href() ?: 'https://wa.me/256700000000'; ?>
<a class="whatsapp-float" href="<?= e($whatsappLink) ?>" target="_blank" rel="noopener noreferrer" aria-label="Chat with ICON Construction on WhatsApp">
    <span class="whatsapp-float__icon" aria-hidden="true"><?= social_icon_svg('whatsapp', 24) ?></span>
    <span class="whatsapp-float__label">WhatsApp</span>
</a>
</body>
</html>
