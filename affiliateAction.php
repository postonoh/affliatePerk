<?php

function Save()
{
    global $wpdb;
    $table_name = $wpdb->prefix.'affiliatePerks';
    $clientname = $_POST['clientname'];
    $clientcode = $_POST['clientcode'];

 if( isset( $_POST['save'] ) ) {
     $count = $wpdb->get_var("SELECT COUNT(*) FROM wp_affiliateperks WHERE clientCode = '$clientcode'");
     if($count > 0)
     {         
        $message = 'Client Code already exist add another one.';
      }
      else
      {
         $result = $wpdb->insert( 
             $table_name, 
             array( 				 
                 'clientName' => $clientname, 
                 'clientCode' => $clientcode, 
             ), 
             array( 
                 '%s', 
                 '%s' 
             ) 
         );	  
         $message = '<div class="alert alert-success" role="alert">Success</div>';
     }     
  }
}

function Delete()
{
 if( isset( $_POST['delete'] ) ) {
 $clientname = $_POST['clientname'];
 $clientcode = $_POST['clientcode'];

 global $wpdb;
    if(is_user_logged_in())
    {

     $table_name = $wpdb->prefix . 'affiliatePerks';

     $wpdb->insert( 
         $table_name, 
         array( 				 
             'clientName' => $clientname, 
             'clientCode' => $clientcode, 
         ) 
     );
    }
 }
}
?>
