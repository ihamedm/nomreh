<br class="clear">
<h2>ساخت کاربر برای سفارشات مهمان</h2>

<form method="post" class="nomreh-tools-form ajax-form">
    <input type="hidden" name="action" value="assign_users_to_orphan_orders">
    <input type="hidden" name="security" value="<?php echo esc_attr(wp_create_nonce('nomreh_ajax_nonce')); ?>">
    <table class="form-table">
        <tr>
            <th><label for="limit">تعداد سفارش برای پردازش</label></th>
            <td><input type="number" name="limit" value="10" class="regular-text" min="1"></td>
        </tr>

    </table>
    <div>

        <button type="submit" class="spd-button"><span class="text">پردازش کن</span> </button>

    </div>

    <ul class="result-log" style="direction: ltr;text-align: left"></ul>
</form>

<br class="clear">
<h2>مهاجرت از پلاگین Digits</h2>

<?php $stats = \Nomreh\Utilities\DigitsIntegration::get_migration_stats(); ?>
<?php if (\Nomreh\Utilities\DigitsIntegration::has_digits_users()): ?>
    <div class="notice notice-info">
        <p>کاربران با شماره تلفن Digits یافت شدند. می‌توانید آن‌ها را به پلاگین Nomreh مهاجرت دهید.</p>
        <p><strong>آمار مهاجرت:</strong></p>
        <ul style="margin-left: 20px;">
            <li>کل کاربران Digits: <?php echo $stats['total_digits_users']; ?></li>
            <li>کاربران مهاجرت شده: <?php echo $stats['migrated_users']; ?></li>
            <li>کاربران در انتظار مهاجرت: <?php echo $stats['pending_migration']; ?></li>
        </ul>
    </div>
<?php else: ?>
    <div class="notice notice-warning">
        <p>هیچ کاربری با شماره تلفن Digits یافت نشد. اگر قبلاً از پلاگین Digits استفاده کرده‌اید، ابتدا آن را غیرفعال کنید و سپس این ابزار را اجرا کنید.</p>
    </div>
<?php endif; ?>

<form method="post" class="nomreh-tools-form ajax-form">
    <input type="hidden" name="action" value="migrate_digits_users">
    <input type="hidden" name="security" value="<?php echo esc_attr(wp_create_nonce('nomreh_ajax_nonce')); ?>">
    
    <p>این ابزار شماره تلفن‌های ذخیره شده در پلاگین Digits را به پلاگین Nomreh منتقل می‌کند.</p>
    
    <div>
        <button type="submit" class="spd-button"><span class="text">شروع مهاجرت</span></button>
    </div>
    
    <ul class="result-log" style="direction: ltr;text-align: left"></ul>
</form>