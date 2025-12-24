<?php
/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       www.casheer.com
 * @since      1.0.0
 *
 * @package    casheer
 * @subpackage casheer/public/partials
 */

?>
<!-- This file should primarily consist of HTML with a little bit of PHP. -->

  <div class="wpbody-content box">
   <h1 align="left">Casheer Checkout Payment Methods</h1>
   <br />
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" id="form-casheer" class="table-box-main">	  
		   <fieldset>	
			   <h2>Customize Your Payment Form</h2>
			   <div class="table-responsive">
				<div id="alert_message"></div>

				<!-- Nonce field here. -->
				<?php wp_nonce_field( 'casheer_setting_nonce', 'casheer_nonce' ); ?>

				<input type="hidden" name="action" value="save_casheer_payment_details_form" />
					<div class="table-responsive">
					<table class="table table-bordered" id="dynamic_field">
					<?php
					$all_methods = ! empty( get_option( 'customize_payment_data', '' ) ) ? json_decode( get_option( 'customize_payment_data', '' ) , true ) : array();
					$i = count( $all_methods );
					?>
						
					<thead data-key="<?php echo esc_html( $i ); ?>"><td><h3>Payment Method <span class="required">*</span></h3></td><td><h3>Channel Code <span class="required">*</span></h3></td><td><h3>Currency Code <span class="required">*</span></h3></td><td><h3>Tunnel <span class="required">*</span></h3></td><td></td></thead>	
					<?php
					$k = 0; if ( ! empty( $all_methods ) ) {
						foreach ( $all_methods as $method ) {
							?>
					<tr id="row<?php echo esc_html( $k ); ?>">
					<td><input type="text" name="customize_payment[<?php echo esc_html( $k ); ?>][name]" placeholder="" value="<?php echo esc_html( $method['name'] ); ?>" class="form-control name_list" /></td>
					<td><input type="text" name="customize_payment[<?php echo esc_html( $k ); ?>][code]" placeholder="" value="<?php echo esc_html( $method['code'] ); ?>" class="form-control name_list" /></td>
					<td><input type="text" name="customize_payment[<?php echo esc_html( $k ); ?>][currency]" placeholder="" value="<?php echo esc_html( $method['currency'] ); ?>" class="form-control name_list" /></td>		
					<td><input type="text" name="customize_payment[<?php echo esc_html( $k ); ?>][tunnel]" placeholder="" value="<?php echo esc_html( $method['tunnel'] ); ?>" class="form-control name_list tunnelid" /></td>		

					<td>
							<?php
							if ( 0 == $k ) {
								?>
						<button type="button" name="add" id="add" class="wp-core-ui button button-secondary">Add More</button><?php } else { ?>
						<button type="button" name="remove" id="<?php echo esc_html( $k ); ?>" class="wp-core-ui button button-secondary btn_remove">Remove</button></td> <?php } ?></td>
					</tr>
							<?php
											$k++; }
					} else {
						?>
					<tr id="row<?php echo esc_html( $k ); ?>">
					<td><input type="text" name="customize_payment[<?php echo esc_html( $k ); ?>][name]" placeholder="" class="form-control name_list" /></td>
					<td><input type="text" name="customize_payment[<?php echo esc_html( $k ); ?>][code]" placeholder="" class="form-control name_list" /></td>
					<td><input type="text" name="customize_payment[<?php echo esc_html( $k ); ?>][currency]" placeholder="" class="form-control name_list" /></td>		
					<td><input type="text" name="customize_payment[<?php echo esc_html( $k ); ?>][tunnel]" placeholder="" class="form-control name_list" /></td>
					<td><button type="button" name="add" id="add" class="wp-core-ui button button-secondary">Add More</button></td>
					</tr>
					<?php } ?>	
	
					</table>
						<div class="action-button" style="background:#fff;min-height: 30px;margin-top: 15px;">
						<a href="admin.php?page=wc-settings&tab=checkout&section=casheer" target="blank" class="button button-secondary button-large">Go To Settings</a>
						<input type="submit" name="submit" id="submit" class="button button-primary button-large" value="Save" />	

						</div>			   
					</div>			
			   </div>
		   </fieldset>	 	

	  </form>
  </div>
