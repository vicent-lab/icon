<?php

declare(strict_types=1);

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function asset(string $path): string
{
    return '/assets/' . ltrim($path, '/');
}

function project_image_thumbnail_path(string $path): string
{
    if (
        preg_match('~^uploads/([a-f0-9]{32})\.webp$~D', $path, $matches)
        && is_file(PROJECT_ROOT . '/assets/uploads/thumbs/' . $matches[1] . '.webp')
    ) {
        return 'uploads/thumbs/' . $matches[1] . '.webp';
    }

    foreach (['images/demo-gallery', 'images/site', 'images/services'] as $dir) {
        $pattern = '~^' . preg_quote($dir, '~') . '/([a-z0-9-]+)\.webp$~D';
        if (
            preg_match($pattern, $path, $matches)
            && is_file(PROJECT_ROOT . '/assets/' . $dir . '/thumbs/' . $matches[1] . '.webp')
        ) {
            return $dir . '/thumbs/' . $matches[1] . '.webp';
        }
    }

    return $path;
}

function project_image_categories(): array
{
    return [
        'building-construction' => 'Building Construction',
        'residential' => 'Residential',
        'commercial' => 'Commercial',
        'roads' => 'Roads',
        'civil-works' => 'Civil Works',
        'roofing' => 'Roofing',
        'concrete' => 'Concrete',
        'electrical' => 'Electrical',
        'plumbing' => 'Plumbing',
        'renovation' => 'Renovation',
        'equipment' => 'Equipment',
        'workers' => 'Workers',
        'safety' => 'Safety',
        'completed-projects' => 'Completed Projects',
    ];
}

function redirect(string $location): void
{
    header('Location: ' . $location, true, 303);
    exit;
}

function public_content_version(): string
{
    $tables = [
        'company_settings',
        'services',
        'projects',
        'project_images',
        'testimonials',
        'team_members',
        'blog_categories',
        'blog_posts',
    ];
    $queries = [];

    foreach ($tables as $table) {
        $queries[] = sprintf(
            "SELECT '%1\$s' AS table_name, COUNT(*) AS row_count, COALESCE(MAX(updated_at), '') AS last_modified FROM `%1\$s`",
            $table
        );
    }

    $statement = db()->query(implode(' UNION ALL ', $queries));
    $fingerprints = $statement->fetchAll();

    return hash('sha256', json_encode($fingerprints, JSON_THROW_ON_ERROR));
}

function company_settings(): array
{
    static $settings = null;

    if ($settings !== null) {
        return $settings;
    }

    $environmentPhone = getenv('COMPANY_PHONE') ?: '';
    $companyName = getenv('COMPANY_NAME') ?: 'ICON Construction Company';
    $settings = [
        'company_name' => $companyName,
        'company_tagline' => getenv('COMPANY_TAGLINE') ?: 'Sample Ugandan construction business',
        'company_description' => getenv('COMPANY_DESCRIPTION') ?: $companyName . ' is a sample Ugandan construction business focused on delivering reliable building, renovation, civil works and project solutions. The company emphasizes quality workmanship, safety, responsible project management and customer satisfaction.',
        'company_demo_notice' => '',
        'company_hero_heading' => "Building Uganda's Future, One Project at a Time.",
        'company_hero_subheading' => 'ICON Construction Company is a sample Ugandan construction business focused on reliable building, renovation, civil works and project solutions.',
        'company_service_area' => 'Kampala, Uganda',
        'company_phone' => $environmentPhone,
        'company_whatsapp' => getenv('COMPANY_WHATSAPP') ?: $environmentPhone,
        'company_email' => getenv('COMPANY_EMAIL') ?: '',
        'company_address' => getenv('COMPANY_ADDRESS') ?: 'Kampala, Uganda',
        'company_google_maps_location' => '',
        'company_business_hours' => '',
        'company_facebook_url' => '',
        'company_tiktok_url' => '',
        'company_instagram_url' => '',
        'company_linkedin_url' => '',
        'company_logo' => 'images/icon-mark.svg',
        'company_favicon' => 'images/icon-mark.svg',
        'company_hero_image' => 'images/hero-construction.jpg',
        'company_hero_image_1' => 'images/hero/hero-1.webp',
        'company_hero_image_1_alt' => 'Building construction and site work with workers and steel reinforcement',
        'company_hero_image_2' => 'images/hero/hero-2.webp',
        'company_hero_image_2_alt' => 'Construction workers and engineers coordinating on site',
        'company_hero_image_3' => 'images/hero/hero-3.webp',
        'company_hero_image_3_alt' => 'Heavy construction equipment and civil works on site',
        'company_hero_image_4' => 'images/hero/hero-4.webp',
        'company_hero_image_4_alt' => 'Completed modern commercial building project',
        'company_safety_image' => 'images/site/safety-ppe.webp',
    ];

    $processSteps = [
        ['Consultation', 'Every project starts with a conversation about what you want to build, improve or repair. We discuss how the space will be used, your priorities, preferred timeline and budget range, then record the key requirements and questions to explore. This gives both sides a clear starting brief before site visits, designs or estimates are prepared.'],
        ['Site Assessment', 'We review the site and the conditions that may affect the work, including access, existing structures, ground levels, drainage, utilities and nearby activity. We discuss any visible constraints with you and identify information that may need specialist investigation. Findings help shape a practical scope; technical surveys or engineering checks are arranged where the project requires them.'],
        ['Planning & Design', 'Using the agreed brief and site information, the project team develops or reviews the drawings, specifications and sequence of work needed to define the project. We discuss material options, finish expectations, approvals and any decisions still required, so changes can be considered before construction. Design work, permits and statutory approvals are confirmed with the appropriate qualified professionals and authorities.'],
        ['Cost Estimation', 'Once the scope is clear enough to price, we prepare an itemised estimate showing the proposed work, materials, labour and other agreed cost items in Ugandan shillings. We explain assumptions, exclusions, allowances and any items that could change if site conditions or selections differ. You can review the estimate, ask questions and agree on revisions before deciding whether to proceed.'],
        ['Contract & Mobilization', 'Before work begins, the parties review and agree the scope, drawings, price, payment stages, schedule, responsibilities and process for handling changes. After the agreement is documented, the team plans site access, safety arrangements, material deliveries, staffing and the first work sequence. Mobilization proceeds when required approvals, access and agreed start conditions are in place.'],
        ['Construction', 'The agreed work is carried out in planned stages, with coordination between the site team, client and relevant suppliers or specialists. Progress, upcoming decisions and any issue affecting the scope or schedule are discussed as the work advances. Materials and workmanship are checked at suitable stages, and changes are reviewed and agreed before additional work is undertaken.'],
        ['Quality Inspection', 'As work reaches its agreed milestones and completion, the team checks finished items against the drawings, specifications and scope. We walk through the relevant areas with you, record outstanding or corrective items and agree how they will be addressed. Inspections required from engineers, consultants or authorities are coordinated separately where applicable to the project.'],
        ['Handover', 'At handover, we review the completed work with you, confirm the status of agreed outstanding items and share relevant keys, operating guidance, warranties or product information available for the project. We clarify any agreed follow-up arrangements and record completion of the handover. The aim is to leave you with a clear understanding of the finished space and its documentation.'],
    ];
    foreach ($processSteps as $index => [$title, $description]) {
        $number = $index + 1;
        $settings['company_process_' . $number . '_title'] = $title;
        $settings['company_process_' . $number . '_description'] = $description;
    }

    if (
        getenv('DB_HOST') === false
        || getenv('DB_NAME') === false
        || getenv('DB_USER') === false
        || getenv('DB_PASSWORD') === false
    ) {
        return $settings;
    }

    try {
        $statement = db()->prepare(
            'SELECT setting_key, setting_value
            FROM company_settings
            WHERE is_public = :is_public'
        );
        $statement->execute(['is_public' => 1]);

        foreach ($statement->fetchAll() as $setting) {
            $value = trim((string) $setting['setting_value']);

            if (
                array_key_exists($setting['setting_key'], $settings)
                && ($value !== '' || $setting['setting_key'] === 'company_demo_notice')
            ) {
                $settings[$setting['setting_key']] = $value;
            }
        }
    } catch (PDOException | RuntimeException $error) {
        error_log('ICON company settings query failed: ' . $error->getMessage());
    }

    return $settings;
}

function company_setting(string $key): string
{
    $settings = company_settings();

    return $settings[$key] ?? '';
}

function company_demo_notice(): string
{
    $notice = trim((string) company_setting('company_demo_notice'));
    $defaultDemoNotice = 'SAMPLE / DEMO INFORMATION: Company details, service information and imagery are illustrative, not verified business facts. Replace them with approved company information in Admin → Company Settings before publishing.';

    return $notice === $defaultDemoNotice ? '' : $notice;
}

function company_process_steps(): array
{
    $steps = [];
    $fallbacks = [
        1 => 'images/site/process-consultation.webp',
        2 => 'images/site/process-site-assessment.webp',
        3 => 'images/site/process-planning.webp',
        4 => 'images/site/process-cost-estimation.webp',
        5 => 'images/site/process-construction.webp',
        6 => 'images/site/process-quality-inspection.webp',
        7 => 'images/site/process-handover.webp',
        8 => 'images/site/process-handover.webp',
    ];

    for ($number = 1; $number <= 8; $number++) {
        $title = company_setting('company_process_' . $number . '_title');
        $description = company_setting('company_process_' . $number . '_description');
        $image = company_setting('company_process_' . $number . '_image');
        if ($image === '' && isset($fallbacks[$number])) {
            $image = $fallbacks[$number];
        }
        if ($title !== '' && $description !== '') {
            $steps[] = ['title' => $title, 'description' => $description, 'image' => $image];
        }
    }

    return $steps;
}

function company_phone_display(): string
{
    $phone = company_setting('company_phone');
    $digits = preg_replace('/\D/', '', $phone);

    if (preg_match('/^256([237]\d{8})$/', $digits, $matches)) {
        return '+256 ' . substr($matches[1], 0, 3) . ' ' . substr($matches[1], 3, 3) . ' ' . substr($matches[1], 6);
    }

    if (preg_match('/^0([237]\d{8})$/', $digits, $matches)) {
        return '0' . substr($matches[1], 0, 3) . ' ' . substr($matches[1], 3, 3) . ' ' . substr($matches[1], 6);
    }

    return $phone;
}

function format_ugx(int|float|string|null $amount): string
{
    if ($amount === null || !is_numeric($amount)) {
        return 'Not specified';
    }

    return 'UGX ' . number_format((float) $amount, 0, '.', ',');
}

function company_asset_href(string $settingKey, ?string $fallback = null): string
{
    $path = company_setting($settingKey);

    if ($path === '' && $fallback !== null) {
        $path = $fallback;
    }

    if (
        str_starts_with($path, '/')
        || str_contains($path, '\\')
        || preg_match('/[\x00-\x1F]/', $path)
        || in_array('..', explode('/', $path), true)
        || !preg_match('~^(?:uploads|images)/[a-zA-Z0-9._/-]+$~D', $path)
    ) {
        return $fallback === null ? '' : asset($fallback);
    }

    return asset($path);
}

function company_phone_href(): string
{
    $phone = preg_replace('/[^0-9+]/', '', company_setting('company_phone'));

    return $phone === '' ? '' : 'tel:' . $phone;
}

function company_maps_href(): string
{
    $location = trim(company_setting('company_google_maps_location'));

    if ($location === '') {
        $location = company_setting('company_address');
    }

    function company_maps_embed_href(): string
    {
        $location = trim(company_setting('company_google_maps_location'));

        if ($location === '') {
            $location = company_setting('company_address');
        }

        if ($location === '' || strtolower($location) === 'uganda' || filter_var($location, FILTER_VALIDATE_URL) !== false) {
            return '';
        }

        return 'https://www.google.com/maps?q=' . rawurlencode($location) . '&output=embed';
    }

    if ($location === '' || strtolower($location) === 'uganda') {
        return '';
    }

    if (filter_var($location, FILTER_VALIDATE_URL) !== false) {
        $host = strtolower((string) parse_url($location, PHP_URL_HOST));
        $allowedHosts = ['google.com', 'www.google.com', 'maps.google.com', 'maps.app.goo.gl', 'goo.gl'];

        if (strtolower((string) parse_url($location, PHP_URL_SCHEME)) === 'https' && in_array($host, $allowedHosts, true)) {
            return $location;
        }
    }

    return 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($location);
}

function company_social_href(string $settingKey): string
{
    $url = trim(company_setting($settingKey));

    if (filter_var($url, FILTER_VALIDATE_URL) === false) {
        return '';
    }

    $host = strtolower((string) parse_url($url, PHP_URL_HOST));
    $allowedHosts = match ($settingKey) {
        'company_facebook_url' => ['facebook.com', 'www.facebook.com', 'm.facebook.com'],
        'company_tiktok_url' => ['tiktok.com', 'www.tiktok.com', 'vm.tiktok.com', 'm.tiktok.com'],
        'company_instagram_url' => ['instagram.com', 'www.instagram.com'],
        'company_linkedin_url' => ['linkedin.com', 'www.linkedin.com'],
        default => [],
    };

    return strtolower((string) parse_url($url, PHP_URL_SCHEME)) === 'https'
        && in_array($host, $allowedHosts, true)
        ? $url
        : '';
}

function company_whatsapp_href(): string
{
    $number = company_setting('company_whatsapp') ?: company_setting('company_phone');
    $number = preg_replace('/\D/', '', $number);

    if ($number === '') {
        return '';
    }

    if (str_starts_with($number, '0')) {
        $number = '256' . substr($number, 1);
    } elseif (!str_starts_with($number, '256')) {
        $number = '256' . $number;
    }

    if (strlen($number) < 11 || strlen($number) > 15) {
        return '';
    }

    return 'https://wa.me/' . $number;
}

/**
 * Return accessible SVG markup for social network icons.
 */
function social_icon_svg(string $platform, int $size = 20): string
{
    $platform = strtolower(trim($platform));

    return match ($platform) {
        'tiktok' => '<svg width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64c.298-.002.595.042.88.13V9.4a6.33 6.33 0 0 0-1-.08A6.34 6.34 0 0 0 3 15.66a6.34 6.34 0 0 0 10.86 4.43c.12-.11.23-.23.33-.35a6.28 6.28 0 0 0 1.58-4.32V8.92a8.28 8.28 0 0 0 4.82 1.52V7c-.34-.01-.68-.11-1-.31z"/></svg>',
        'whatsapp' => '<svg width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0 0 12.04 2m.01 1.67c2.2 0 4.26.86 5.82 2.42a8.225 8.225 0 0 1 2.41 5.83c0 4.54-3.7 8.24-8.24 8.24-1.48 0-2.93-.4-4.2-1.15l-.3-.18-3.12.82.83-3.04-.2-.31a8.196 8.196 0 0 1-1.26-4.38c0-4.54 3.7-8.24 8.24-8.24m-3.53 4.26c-.19 0-.5.07-.76.36-.26.29-1 1-1 2.44s1.02 2.83 1.16 3.02c.14.19 2 3.06 4.85 4.29.68.29 1.21.46 1.62.6.68.22 1.3.19 1.79.12.55-.08 1.68-.69 1.92-1.36.24-.67.24-1.25.17-1.36-.07-.11-.26-.17-.55-.32-.29-.14-1.68-.83-1.94-.93-.26-.1-.45-.14-.64.14-.19.29-.74.93-.91 1.12-.17.19-.34.22-.63.07-.29-.15-1.22-.45-2.33-1.44-.86-.77-1.44-1.72-1.61-2.01-.17-.29-.02-.45.13-.59.13-.13.29-.34.43-.51.14-.17.19-.29.29-.48.1-.19.05-.36-.02-.5-.07-.15-.64-1.55-.88-2.12-.23-.56-.47-.48-.64-.49-.17-.01-.36-.01-.55-.01z"/></svg>',
        'facebook' => '<svg width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>',
        'instagram' => '<svg width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>',
        'linkedin' => '<svg width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.28 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.75M6.46 8.76a1.64 1.64 0 0 0 1.64-1.64 1.64 1.64 0 0 0-1.64-1.64 1.64 1.64 0 0 0-1.64 1.64c0 .91.73 1.64 1.64 1.64m1.39 9.74v-8.37H5.07v8.37h2.78z"/></svg>',
        default => '<span aria-hidden="true">↗</span>',
    };
}

/**
 * Return list of active or fallback social channels with their labels and icons.
 */
function company_social_links(): array
{
    $tiktok = company_social_href('company_tiktok_url') ?: 'https://www.tiktok.com/@iconconstructionug';
    $whatsapp = company_whatsapp_href() ?: 'https://wa.me/256700000000';
    $facebook = company_social_href('company_facebook_url') ?: 'https://www.facebook.com/iconconstructionug';
    $instagram = company_social_href('company_instagram_url');
    $linkedin = company_social_href('company_linkedin_url');

    $links = [
        'tiktok' => [
            'label' => 'TikTok',
            'href' => $tiktok,
            'icon' => 'tiktok',
            'handle' => '@iconconstructionug',
        ],
        'whatsapp' => [
            'label' => 'WhatsApp',
            'href' => $whatsapp,
            'icon' => 'whatsapp',
            'handle' => 'Chat on WhatsApp',
        ],
        'facebook' => [
            'label' => 'Facebook',
            'href' => $facebook,
            'icon' => 'facebook',
            'handle' => 'facebook.com/iconconstructionug',
        ],
    ];

    if ($instagram !== '') {
        $links['instagram'] = [
            'label' => 'Instagram',
            'href' => $instagram,
            'icon' => 'instagram',
            'handle' => 'Instagram',
        ];
    }

    if ($linkedin !== '') {
        $links['linkedin'] = [
            'label' => 'LinkedIn',
            'href' => $linkedin,
            'icon' => 'linkedin',
            'handle' => 'LinkedIn',
        ];
    }

    return $links;
}
