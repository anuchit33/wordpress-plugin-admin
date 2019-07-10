<?php
global $wpdb;

wp_enqueue_script('script', '/wp-content/plugins/wordpress-plugin-admin/inc/js/export-table2excel.js', array('jquery'), 1.1, true);

$from = empty($_GET['from']) ? date('Y-m-d'): sanitize_text_field($_GET['from']);
$to = empty($_GET['to']) ? date('Y-m-d') : sanitize_text_field($_GET['to']);

?>
<div class="wrap">
    <h1 class="wp-heading-inline">รายชื่อผู้ติดต่อ</h1>
    <a href="#" class="page-title-action" id="export">
        Export
    </a>
    <form method="get">
        <input type="hidden" name="page" value="wp-contact-us"/>
        <p class="search-box">
            <label for="from">From</label>
            <input type="date" id="from" name="from" value="<?= $from ?>">
            <label for="to">to</label>
            <input type="date" id="to" name="to" value="<?= $to ?>">
            <input type="submit" id="search-submit" class="button" value="Search"></p>
        <div id="col-container">
            <?php
            $tablename = $wpdb->prefix . 'contact_message';
            $results = $wpdb->get_results("SELECT * FROM " . $tablename . " Where created_datetime >= '" . $from . " 00:00:00' and created_datetime <= '" . $to . " 23:00:00'", OBJECT);
            ?>
            <table class="wp-list-table widefat" id="result-table">
                <thead>
                    <tr>
                        <td data-style="head1">วันที่</td>
                        <td data-style="head1">อีเมล</td>
                        <td data-style="head1">ข้อความ</td>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($results as $key => $value) {
                        ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($value->created_datetime)) ?> <?= date('H:i', strtotime($value->created_datetime)) ?></td>
                            <td><?= $value->email ?></td>
                            <td><?= $value->message ?></td>                        
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </form>
</div>



<script type="text/javascript">
    jQuery(document).ready(function ($) {
        
        $('#export').click(function () {

            tablesToExcel(['result-table'], ['รายชื่อผู้ติดต่อ'], 'รายชื่อผู้ติดต่อ.xls')
            return false
        })
    });
</script>