<?php
$active_section = $_GET['section'] ?? 'general';

// Get available SMS providers
$sms_providers = \Nomreh\Sms::get_providers();
?>

<div id="wpma-settings" class="tab-content">

    <ul class="subsubsub">
        <li><a href="?page=nomreh&tab=settings&section=general" class="<?php echo $active_section === 'general' ? 'current' : ''; ?>">عمومی</a></li>
        <?php foreach ($sms_providers as $provider_name => $provider): ?>
            | <li><a href="?page=nomreh&tab=settings&section=<?php echo esc_attr($provider_name); ?>" class="<?php echo $active_section === $provider_name ? 'current' : ''; ?>"><?php echo esc_html($provider->get_display_name()); ?></a></li>
        <?php endforeach; ?>
    </ul>

    <?php 
    $settings_file = dirname(__FILE__) . '/settings/' . $active_section . '.php';
    if (file_exists($settings_file)) {
        include_once $settings_file;
    } else {
        echo '<div class="error"><p>تنظیمات مورد نظر یافت نشد.</p></div>';
    }
    ?>
</div>

