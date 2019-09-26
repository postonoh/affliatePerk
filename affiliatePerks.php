<?php
/**
 * Plugin Name: Affiliate Perks
 * Plugin URI: http://www.interspersesoftware.com/
 * Description: The plugin is set to allow affiliates to access survey forms.
 * Version: 1.0
 * Author: Shelby Poston
 * Author URI: http://www.interspersesoftware.com/
 */

/*	Copyright 2019 Shelby Poston

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Prohibit direct script loading.


defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

if(!defined('AFFILIATEPERKS_URL'))
	define('AFFILIATEPERKS_URL', plugin_dir_url( __FILE__ ));
if(!defined('AFFILIATEPERKS_PATH'))
	define('AFFILIATEPERKS_PATH', plugin_dir_path( __FILE__ ));


	//Include database insert delete code
	include_once('affiliateAction.php');
	Save();
	register_activation_hook( __FILE__, 'poston_install' );
	register_activation_hook( __FILE__, 'poston_install_data' );

	global $poston_install_db_version;
	$poston_install_db_version = '1.0';

	
	function poston_install() {
		global $wpdb;
		global $poston_install_db_version;
	
		$table_name = $wpdb->prefix . 'affiliatePerks';
		
		$charset_collate = $wpdb->get_charset_collate();
	
		$sql = "CREATE TABLE $table_name (
			affiliateperks_id mediumint(9) NOT NULL AUTO_INCREMENT,
			clientName text NOT NULL,
			clientCode text NOT NULL,
			PRIMARY KEY  (affiliateperks_id)
		) $charset_collate;";
	
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	
		add_option( 'poston_db_version', $poston_db_version );
	}
	
	function poston_install_data() {
		global $wpdb;
		
		$clientname = 'Sheby Poston';
		$clientcode = 'SPCode';
		
		$table_name = $wpdb->prefix . 'affiliatePerks';
		
		$wpdb->insert( 
			$table_name, 
			array( 				 
				'clientName' => $clientname, 
				'clientCode' => $clientcode, 
			) 
		);
	}
	wp_enqueue_script('affiliatemain', plugins_url('/js/jquery.js', __FILE__), array('jquery'), '', true);
	wp_enqueue_script('datatables', plugins_url('/js/jquery.dataTables.js', __FILE__), array('jquery'), '', true);
	wp_enqueue_script('datatablesbuttons', plugins_url('/js/dataTables.buttons.min.js', __FILE__), array('jquery'), '', true);
	wp_enqueue_script('myaffiliatePerks', plugins_url('/js/PerkAffiliate.js', __FILE__), array('jquery'), '', true); //buttons.html5.min.js
	wp_enqueue_script('html5Buttons', plugins_url('/js/buttons.html5.min.js', __FILE__), array('jquery'), '', true);
	wp_register_style('datatablecss', plugins_url('/css/jquery.dataTables.css', __FILE__));
	wp_register_style('affiliatePerksCss', plugins_url('/css/styleAffiliate.css', __FILE__)); 
	wp_register_style('datatablesbuttonsCss', plugins_url('/css/buttons.dataTables.min.css', __FILE__)); 
	wp_localize_script('myaffiliatePerks', 'myAffiliateAjax', array('ajaxurl' => admin_url('admin-ajax.php')));

	 //add_action( 'admin_footer', 'Testing' );
	 add_action( 'wp_ajax_myAffiliateAjax', 'GetAffiliateList' );

	

	


class AffiliatePerks
{
 
	private $_nonce = 'affiliateperks_admin';

	/**
	 * The option name
	 *
	 * @var string
	 */
	private $option_name = 'affiliateperks_data';

    /**
     * AffiliatePerks constructor.
     *
     * The main plugin actions registered for WordPress
     */
    public function __construct()
    {
		//Admin page calls:

		add_action( 'admin_menu',  array( $this, 'addAdminMenu' ) ); 
		   

	} 

	private function getData()
    {
	    return get_option($this->option_name, array());
	}
	
	
   public function addAdminMenu()
   {

	if($user_level=='administrator')
		$nf_user_level = 'activate_plugins';

    add_menu_page(
	__( 'Affiliate Perks', 'affiliateperks',  $nf_user_level ),
	__( 'Affiliate Perks', 'affiliateperks',$nf_user_level ),
	    'manage_options',
	    'affiliateperks',
	    array($this, 'adminLayout'),
	    'dashicons-testimonial'
     );
   }

   

   public function adminLayout()
   {
	
	
	if (is_admin() ) {

	global $data;
	
	$data = $this->getData();  
	

	    ?>

    <style type="text/css">
	.hide{
		display:none;
	}
	#tblAffiliate{
		width:200px !important;		
	}

	#tblAffiliate th{
		width:150px !important;
		text-align:left;
	}
	#tblAffiliate td{
		width:150px !important;

		text-align:left;
	}
	</style>
		<div class="wrap">

            <h1><?php _e('Affiliate Perks Settings - Enter Affiliate Perk Code', 'affiliateperks'); ?></h1>
			
            <form id="affiliateperks-admin-form" class="postbox" method="post" action="admin.php?page=affiliateperks">

                <div class="form-group inside">
                   <div><label><?php _e( 'Affiliate Perk Codes', 'affiliateperks' ); ?></label> </div>	               
                    <table class="form-table">
						<thead>
							<tr>
								<th><label><?php _e( 'Client Name', 'affiliateperks' ); ?></label></th>
								<th><label><?php _e( 'Client Code', 'affiliateperks' ); ?></label></th>
							</tr>
						</thead>
                        <tbody>
                            <tr>
                                <td scope="row">
                                    <input name="clientname"
                                           id="clientname"
                                           class="regular-text"
                                           type="text"
                                           value="<?php echo (isset($data['clientName'])) ? $data['clientName'] : ''; ?>"/>
                                </td>
                                <td>
                                    <input name="clientcode"
                                           id="clientcode"
                                           class="regular-text"
                                           type="text"
                                           value="<?php echo (isset($data['clientCode'])) ? $data['clientCode'] : ''; ?>"/>
                                </td>
							    <td scope="row"></td>
                            </tr>    
						</tbody>
                    </table>
                 </div>  
                <hr>
				<strong><div class="alert alert-success" role="alert"></div></strong>
				<hr>
                <div class="inside">
                    <button class="button button-primary" id="affiliateperks-admin-save" type="submit" name="save">
                        <?php _e( 'Save', 'affiliateperks' ); ?>
                    </button>   
					<button class="button button-primary" id="affiliateperks-admin-delete" type="submit" name="delete">
                        <?php _e( 'Delete', 'affiliateperks' ); ?>
                    </button>                       
                </div>
            </form>

			<div class="affiliateWrapper">
			 
			</div>

		</div>
		
		<?php 
		   global $wpdb;
		   $sql = "Select * from wp_affiliatePerks";
		   $getTheRows = $wpdb->get_results($sql);
	   
			echo '<table id="tblAffiliate">';
			echo '<thead><tr><th>Client Name</th><th>Client Code</th></tr></thead>';
			foreach((array)$getTheRows as $gotRow){
			$clientName = $gotRow->clientName;
			$clientCode = $gotRow->clientCode;
			echo '<tbody><tr><td>' . $clientName . '</td><td>' . $clientCode . '</td></tr></tbody>';
		   }  
		   echo '</table>';
		} else {
			echo "ACCESS DENIED";
		}
   } 
}

new AffiliatePerks();
