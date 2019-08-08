# WordPress Plugin Admin
ขั้นตอนสร้าง WordPress Plugin สำหรับแสดงผลส่วน Admin

### 1.Header Requirements
สร้างไฟล์ wp-content/plugins/wordpress-plugin-admin/`wordpress-plugin-admin.php`
```
<?php
/*
Plugin Name: WordPress Admin
Plugin URI: https://github.com/anuchit33/wordpress-plugin-admin
Description: wordpress-plugin-admin
Author: Anuchit Yai-in
Version: 0.0.1
*/
```

### 2. สร้าง Plugin Class(OOP)
```
class WordPressPluginAdmin {
    function __construct() {
    }
}
new WordPressPluginAdmin();
```

### 3. Activation / Deactivation Hooks
เพิ่มเข้าไปในส่วนของ __construct()
```
    function __construct() {
        # Activation / Deactivation Hooks
        register_activation_hook(__FILE__, array($this, 'wp_activation'));
        register_deactivation_hook(__FILE__, array($this, 'wp_deactivation'));
    }

    function wp_activation(){
    }

    function wp_deactivation(){
    }
```

### 4. สร้าง admin menu
```
    function __construct() {
        ...

        # add_action admin_menu
        add_action('admin_menu', array($this, 'wp_add_menu'));
    }
```
เพิ่ม wp_add_menu
- wordpress dashicons - https://developer.wordpress.org/resource/dashicons/#admin-home
- doc add_menu_page - https://developer.wordpress.org/reference/functions/add_menu_page/
- doc add_submenu_page - https://developer.wordpress.org/reference/functions/add_submenu_page/
```
function wp_add_menu(){
        // admin menu
        $page_title = 'Contact US';
        $menu_title = 'Contact US';
        $capability = 'read'; // manage_options , read
        $menu_slug = 'wp-contact-list';
        $function = '';
        $icon_url = 'dashicons-email-alt';
        $position = '2';

        add_menu_page($page_title , $menu_title, $capability, $menu_slug ,$function , $icon_url, $position);

        // add sub menu 1
        $sub_parent_slug = $menu_slug;
        $sub_page_title =  $menu_title.'- รายชื่อผู้ติดต่อ';
        $sub_menu_title = 'รายชื่อผู้ติดต่อ';
        $sub_menu_slug = 'wp-contact-list';
        $sub_capability = 'read';

        add_submenu_page($sub_parent_slug, $sub_page_title, $sub_menu_title , $sub_capability, $sub_menu_slug , array($this, 'wp_page_contact_list'));

        // add sub menu 2
        $sub_parent_slug = $menu_slug;
        $sub_page_title =  $menu_title.' - รายชื่อผู้รับเมล';
        $sub_menu_title = 'รายชื่อผู้รับเมล';
        $sub_menu_slug = 'wp-contact-email';
        $sub_capability = 'read';

        add_submenu_page($sub_parent_slug, $sub_page_title, $sub_menu_title , $sub_capability, $sub_menu_slug , array($this, 'wp_page_contact_email'));

    }
```

### 5. Handle display
- wp_page_contact_list
```
    function wp_page_contact_list(){
        include( dirname(__FILE__) . '/templates/admin/contact-list.php' );
    }
```
- wp_page_contact_list
```
    function wp_page_contact_email(){
        include( dirname(__FILE__) . '/templates/admin/contact-email.php' );
    }
```

### 6. สร้าง templates admin
- สร้างไฟล์ `wordpress-plugin-admin/templates/admin/contact-list.php`
```
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
```

- สร้างไฟล์ `wordpress-plugin-admin/templates/admin/contact-email.php`
```
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

```

### 7.Handle Submit
1. บันทึกรายชื่อผู้รับเมล
2. ลบรายชื่อผู้รับเมล
```
function wp_page_contact_email(){

        # add email
        if (isset($_POST['email'])) {
            if (!isset($_POST['add_email']) || !wp_verify_nonce($_POST['add_email'], 'post_add_email')) {
               echo 'Sorry, your nonce did not verify.';
               exit();
            }
            global $wpdb;
            $tablename = $wpdb->prefix . 'contact_email';
            $wpdb->insert($tablename, array(
                'email' => sanitize_text_field($_POST['email']),
                'name' => sanitize_text_field($_POST['email'])
                    )
            );
        }

        # delete email
        if (isset($_GET['action']) && $_GET['action'] == 'delete') {
            if (!isset($_GET['csrf']) || !wp_verify_nonce($_GET['csrf'], 'action_delete')) {
                echo 'Sorry, your nonce did not verify.';
                exit();
             }
             
            global $wpdb;
            $tablename = $wpdb->prefix . 'contact_email';
            $wpdb->delete($tablename, array('id' => intval($_GET['id'])));
        }
        
        include( dirname(__FILE__) . '/templates/admin/contact-email.php' );
    }
```
