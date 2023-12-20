<?php
/** 
* Plugin Name: data-table
* Description: Creating Plugin for showing the data table by Zehntech.
* Version: 1.0.0
* Author: Zehntech Technologies Pvt. Ltd.
* Author URI: https://www.zehntech.com/
* License: GPL2
* License URI: https://www.gnu.org/licenses/gpl-2.0.html
* TextDomain: zt-data-table

* @package zt-data-table

*/



defined('ABSPATH') || die('you do not have access to this page!');




function my_plugin_styles()
{
    wp_enqueue_script('jquery');
  //  wp_enqueue_script('my-plugin-table-script', plugins_url('assets/js/table.js', __FILE__), array('jquery'), '1.0', true);
    wp_enqueue_style('my-plugin-custom-style', plugins_url('assets/css/custom.css', __FILE__), array(), '1.0', 'all');
    // wp_enqueue_style('my-plugin-table-style', plugins_url('assets/css/table.css', __FILE__), array(), '1.0', 'all');


    wp_enqueue_script('my-plugin-custom-script', plugins_url('assets/js/custom.js', __FILE__), array('jquery'), '1.0', true);
 
    wp_enqueue_script('datatables-script', 'https://cdn.datatables.net/v/dt/dt-1.10.20/datatables.min.js', array('jquery') );

    wp_enqueue_style('datatables-style', 'https://cdn.datatables.net/v/dt/dt-1.10.20/datatables.min.css' );
    
    wp_localize_script( 'my-plugin-custom-script', 'myajax', array('ajaxurl' => admin_url( 'admin-ajax.php' )) );
}

add_action('init', 'my_plugin_styles');






add_action('admin_menu', 'wpdocs_register_my_custom_menu_page', 9);

function wpdocs_register_my_custom_menu_page()
{

    add_menu_page(
        __('data_table', 'zt-data-table'),
        'Data Table',
        'manage_options',
        'datatable',
        'my_custom_menu_page'
    );
}


/**
 * Display a custom menu page
 */
function my_custom_menu_page()
{
    ?>
  
<table id='empTable' class='display dataTable' width="100%">

   <thead>
       <tr role="row">
                 <th>CERT ID</th>
                <th>CERT SERIAL</th>
                <th>CERT EK SERIAL</th>
                <th>CERT OWNERS</th>
                <th>CERT STATUS</th>
                <th>CERT PATH</th>
                <th>CERT ZIP</th>
                <th>CERT DATE</th>
                <th>CERT MODIFIED</th>
       </tr>
   </thead>
   <tbody></tbody>
</table>

<?php
}


add_action( 'wp_ajax_getpostsfordatatables', 'getpostsfordatatables' );
add_action( 'wp_ajax_nopriv_getpostsfordatatables', 'getpostsfordatatables' );

function getpostsfordatatables() {

  include 'db_connection.php';
  global $wpdb;
 

## Read value
$draw = $_POST['draw'];
$row = $_POST['start'];
$rowperpage = $_POST['length']; 
$columnIndex = $_POST['order'][0]['column']; 
$columnName = $_POST['columns'][$columnIndex]['data']; 
$columnSortOrder = $_POST['order'][0]['dir']; 
$searchValue = mysqli_real_escape_string($conn,$_POST['search']['value']); 


## Search 
$searchQuery = " ";
if($searchValue != ''){
   $searchQuery = " and (cert_id like '%".$searchValue."%' or 
   cert_serial like '%".$searchValue."%' or 
   cert_ek_serial like'%".$searchValue."%' or cert_owners like '%".$searchValue."%'  or cert_status like '%".$searchValue."%'  or cert_path like '%".$searchValue."%' or cert_zip like '%".$searchValue."%'  or cert_date like '%".$searchValue."%' or cert_modified like '%".$searchValue."%' ) ";
}


## Total number of records without filtering
$sel = mysqli_query($conn,"select count(*) as allcount from wp_certs");
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];


## Total number of record with filtering
$sel = mysqli_query($conn,"select count(*) as allcount from wp_certs WHERE 1 ".$searchQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

## Fetch records
$empQuery = "select * from wp_certs WHERE 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
$result = mysqli_query($conn, $empQuery);
$return_json = array();

   if ($result->num_rows > 0) {
       while ($row = $result->fetch_assoc()) {

        $row_array = array(
          'cert_id' => $row['cert_id'],
          'cert_serial' => $row['cert_serial'],
          'cert_ek_serial' => $row['cert_ek_serial'],
          'cert_owners' => $row['cert_owners'],
          'cert_status' => $row['cert_status'],
          'cert_path' => $row['cert_path'],
          'cert_zip' => $row['cert_zip'],
          'cert_date' => $row['cert_date'],
          'cert_modified' => $row['cert_modified']
     
        );
           $return_json[] = $row_array;
       }

      }
   


      $json_data = array(
         
  
        "recordsTotal"  => intval( $totalRecords ),  // total number of records
        "recordsFiltered" => intval( $totalRecordwithFilter ), 
        "aaData" => $return_json  // total data array
        );
echo json_encode($json_data); 


   $conn->close();

  
   wp_die();

 
}