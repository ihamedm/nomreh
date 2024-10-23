<br class="clear">
<h2>Orphan Orders</h2>

<form method="post" class="sepid-tools-form ajax-form">
    <input type="hidden" name="action" value="assign_users_to_orphan_orders">
    <input type="hidden" name="security" value="<?php echo esc_attr(wp_create_nonce('sepid_ajax_nonce')); ?>">
    <table class="form-table">
        <tr>
            <th><label for="limit">تعداد سفارش برای پردازش</label></th>
            <td><input type="number" name="limit" value="10" class="regular-text" min="1"></td>
        </tr>

    </table>
    <div>

        <button type="submit" class="sepid-button"><span class="text">پردازش کن</span> </button>

    </div>

    <ul class="result-log" style="direction: ltr;text-align: left"></ul>
</form>