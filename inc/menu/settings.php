<?php
$active_section = $_GET['section'] ?? 'general';

?>

<div id="wpma-settings" class="tab-content">

    <ul class="subsubsub">
        <li><a href="?page=sepid-login&tab=settings&section=general" class="<?php echo $active_section === 'general' ? 'current' : ''; ?>">عمومی</a></li> |
        <li><a href="?page=sepid-login&tab=settings&section=kavenegar" class="<?php echo $active_section === 'kavenegar' ? 'current' : ''; ?>">کاوه نگار</a></li>
    </ul>

    <?php include_once dirname(__FILE__) . '/settings/'.$active_section.'.php';?>
</div>

