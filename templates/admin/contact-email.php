<?php
global $wpdb;
$tablename = $wpdb->prefix . 'contact_email';

?>
<div class="wrap">
    <h1 class="wp-heading-inline">ผู้รับเมล</h1>
    <div id="col-container">
        <?php
        
        $results = $wpdb->get_results("SELECT * FROM " . $tablename . " ", OBJECT);
        ?>
        <table class="wp-list-table widefat" id="result-table">
            <thead>
                <tr>
                <td data-style="head1">ชื่อ</td>
                    <td data-style="head1">อีเมล</td>
                    <td data-style="head1"></td>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($results as $key => $value) {
                    ?>
                    <tr>
                    <td><?= $value->name ?></td>  
                        <td><?= $value->email ?></td>  
                        <td><a href="<?php print wp_nonce_url(admin_url('admin.php?page=wp-contact-email&action=delete&id='.$value->id), 'action_delete', 'csrf');?>">ลบ</a></td>                   
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
    </div>

    <form method="post">
        <?php wp_nonce_field('post_add_email', 'add_email'); ?>
        <br/>
        <div class="form-group">
            <input type="text" name="name" class="form-control" id="name" placeholder="Name">
            <input type="email" name="email" class="form-control" id="email" placeholder="Email">
            <button type="submit" class="button">เพิมอีเมล</button>
        </div>

    </form>
</div>

<script type="text/javascript">
    jQuery(document).ready(function ($) {
        var dateFormat = "dd/mm/yy",
                from = $("#from")
                .datepicker({
                    dateFormat: dateFormat,
                    defaultDate: "+1w",
                    changeMonth: true,
                    numberOfMonths: 1
                })
                .on("change", function () {
                    to.datepicker("option", "minDate", getDate(this));
                }),
                to = $("#to").datepicker({
            dateFormat: dateFormat,
            defaultDate: "+1w",
            changeMonth: true,
            numberOfMonths: 1
        })
                .on("change", function () {
                    from.datepicker("option", "maxDate", getDate(this));
                });

        function getDate(element) {
            var date;
            try {

                date = $.datepicker.parseDate(dateFormat, element.value);
            } catch (error) {
                date = null;
            }

            return date;
        }

        $('#export').click(function () {

            tablesToExcel(['result-table'], ['รายชื่อผู้ติดต่อ'], 'รายชื่อผู้ติดต่อ.xls')
            return false
        })
    });
</script>
