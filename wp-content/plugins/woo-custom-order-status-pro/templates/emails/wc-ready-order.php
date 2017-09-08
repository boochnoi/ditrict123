<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
//print __FILE__;
$recipient = $ops['woocommerce_woo_advance_order_status_recipient']; 
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<body>

                 
<!-- Wrapper -->
<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0" align="center">
	<tr>
		<td width="100%" height="100%" valign="top">	

		<!-- Main wrapper -->
		<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" bgcolor="#eeeeee">
			<tr>
				<td>
				
					<!-- Space -->
					<table width="652" border="0" cellpadding="0" cellspacing="0" align="center">
						<tr>
							<td width="652" height="0" style="line-height: 1px;">									
							</td>
						</tr>
					</table>

				
					<!-- 1px Wrapper -->
					<table width="652" border="0" cellpadding="0" cellspacing="0" align="center">
						<tr>
							<td width="1" bgcolor="#cecece"></td>
						  <td width="710">
					
								<!-- Wrapper -->
								<table width="710" bgcolor="#2d2d2d" border="0" cellpadding="0" cellspacing="0" align="center">
									<tr>
										<td width="20"></td>
										<td width="590" bgcolor="#2d2d2d">

							
											<!-- Logo + Social Icons -->
											<table width="590" bgcolor="#2d2d2d" border="0" cellpadding="0" cellspacing="0" align="center">
												<tr>
													<!-- Logo -->
													<td width="240" height="52"><img src="http://orderstatus.appzab.com/images/logo.png" alt="MyStore.com" longdesc="http://www.mystore.com"></td>
													<td width="326"></td>
													<!-- Icon 1 -->
													
													<td width="8"></td>
													<!-- Icon 2 -->
													<td width="16" style="text-align: center">
														<a href="http://www.facebook.com/Google"><img mc:edit="icon_2" src="http://orderstatus.appzab.com/images/icon_2.jpg" alt="" border="0" /></a>								
													</td>
													<td width="8"></td>
													<!-- Icon 3 -->
													<td width="16" style="text-align: center">
														<a href="http://twitter.com/Google"><img mc:edit="icon_3" src="http://orderstatus.appzab.com/images/icon_3.jpg" alt="" border="0" /></a>								
													</td>
												</tr>
											</table><!-- End Logo + Social Icons -->
										
										</td>
										<td width="60"></td>
									</tr>
								</table><!-- End Wrapper -->
							
							
								
							
								<!-- MC Repeatable -->
								<table width="710" border="0" cellpadding="0" cellspacing="0" align="center">
								<tr mc:repeatable="">
								<td>
							
								<div mc:hideable=""><!-- MC Hideable -->
								<table width="710" border="0" cellpadding="0" cellspacing="0" align="center">
								<tr mc:repeatable="">
								<td>
							
								<!-- Wrapper -->
								<table width="710" bgcolor="#f2f2f2" border="0" cellpadding="0" cellspacing="0" align="center">
									<tr>
										
										<td width="590">
							
											
											
									  </td>
										
									</tr>
								</table><!-- End Wrapper -->
								
								</td>
								</tr>
								</table></div><!-- End MC Hideable -->
							
								</td>
								</tr>
								</table><!-- End MC Repeatable -->
							
								<!-- Wrapper -->
						    <table width="710" bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" align="center">
									<tr>
										
										<td width="650">
											
											<!-- MC Repeatable -->
											<table width="650" border="0" cellpadding="0" cellspacing="0" align="center">
											<tr mc:repeatable="">
											<td>
										
											<div mc:hideable=""><!-- MC Hideable -->
											<table width="650" border="0" cellpadding="0" cellspacing="0" align="center">
											<tr mc:repeatable="">
											<td>
							
											<!-- Space -->
											<table width="650" border="0" cellpadding="0" cellspacing="0" align="center">
												<tr>
													<td width="650" height="30">									
													</td>
												</tr>
											</table>
											
											<!-- Text Left, Image Right 1st -->
											<table width="452" border="0" cellpadding="0" cellspacing="0" align="center">
												<tr>
													<td width="380" valign="top">
														
														<!-- Headline -->
														<table width="650" border="0" cellpadding="0" cellspacing="0" align="center" >
															<tr>
																<td width="600" style="font-family: Helvetica, Arial, sans-serif;">	
																
																<span style="font-size:18px;float:right;"><?php echo $email_heading; ?></span>
<br />
<div style="clear:both;"></div>
<span style="font-size:12px;float:right;color:#333;">Order <?php echo __( '', 'woocommerce' ) . ' ' . $order->get_order_number(); ?></span>
<br />
<span style="font-size:12px;float:right;color:#b0b0b0;">Placed on <?php printf( '<time datetime="%s">%s</time>', date_i18n( 'c', strtotime( $order->order_date ) ), date_i18n( woocommerce_date_format(), strtotime( $order->order_date ) ) ); ?>
</span>
																		
															  </td>
															</tr>
															<!-- Space -->
															<tr>
																<td width="650" height="8" style="line-height: 1px;"></td>
															</tr>
															<!-- Text -->
															<tr>
																<td mc:edit="text_left_1" width="650" style="font-size: 12px; color: #000; text-align: left; font-family: Helvetica, Arial, sans-serif; line-height: 21px; vertical-align: top;">	
																	<table bgcolor="#FFFFFF" cellspacing="0" cellpadding="10" border="0">
																		<tr>
																			<td>
                   
																				<span style="font-size:18px;color:#f8c300;">Hello <?php printf( __( '%s', 'woocommerce' ), $order->billing_first_name . ' ' . $order->billing_last_name ); ?></span> 
																				<?php if( isset($ops['woocommerce_woo_advance_order_status_email_display_custom_msg']) 
			&& (int)$ops['woocommerce_woo_advance_order_status_email_display_custom_msg'] == 1 ): ?>
<div style="color:#333;"><p align="justify"><?php print $ops['woocommerce_woo_advance_order_status_email_custom_msg']; ?></p></div>
<?php endif; ?>	
<br />

<table style="width:100%;border-top:3px solid #e2e2e2;border-collapse:collapse">
<tbody>
<tr>
<td style="font-size:14px;padding:11px 18px 18px 18px;background-color:#f4f3f3;width:50%;vertical-align:top;line-height:18px;font-family:Arial,sans-serif">
<?php if( isset($ops['woocommerce_woo_advance_order_status_send_payment_method']) 
			&& (int)$ops['woocommerce_woo_advance_order_status_send_payment_method'] == 1 ): ?>
<p style="margin:2px 0 9px 0;font:14px Arial,sans-serif">
<span style="font-size:14px;color:rgb(102,102,102)">
Your Payment method is 
</span><br />
<b><?php print get_post_meta( $order->id, '_payment_method_title', 1); ?></b>
</p>
<?php endif; ?>
<p style="margin:2px 0 9px 0;font:14px Arial,sans-serif">
<span style="font-size:14px;color:rgb(102,102,102)">
Email Address
</span><br />
<b><?php echo $order->billing_email; ?></b>
</p>
<p style="margin:2px 0 9px 0;font:14px Arial,sans-serif">
<span style="font-size:14px;color:rgb(102,102,102)">
Contact Number
</span><br />
<b><?php echo $order->billing_phone; ?></b>
</p>

<a target="_blank" href="http://{site_url}/my-account"><img src="http://orderstatus.appzab.com/images/appzab-order-details.png" style="border:0px;" border=0></img></a>
</td>
<td style="font-size:14px;padding:11px 18px 18px 18px;background-color:#f4f3f3;width:50%;vertical-align:top;line-height:18px;font-family:Arial,sans-serif">
<?php if( isset($ops['woocommerce_woo_advance_order_status_send_shipping_method']) 
			&& (int)$ops['woocommerce_woo_advance_order_status_send_shipping_method'] == 1 ): ?>
<p style="margin:2px 0 9px 0;font:14px Arial,sans-serif">
<span style="font-size:14px;color:rgb(102,102,102)">
Your Shipping preference is
</span><br />
<b><?php print $order->get_shipping_method(); ?></b>
</p>
<?php endif; ?>

<p style="margin:2px 0 9px 0;font:14px Arial,sans-serif">
<span style="font-size:14px;color:rgb(102,102,102)">
Your order will be shipped to
</span><br />
<b><?php echo $order->get_formatted_billing_address(); ?></b>

</p>


</td>
</tr>
</tbody>
</table>


																			</td>
																		</tr>
																		
																		<tr>
																			<td>
                    
<span style="font-size:18px;color:#f8c300;">Order Details</span> <br />
<hr style=" border: 0px; height: 1px; background: #b7b7b7; ">
<span style="font-size:11px;color:#dadada;font-style: italic;">Order <?php echo __( '', 'woocommerce' ) . ' ' . $order->get_order_number(); ?></span>
<br />																				<table cellspacing="0" cellpadding="6" style="padding:20px;width: 100%; border: 0px;border-collapse:collapse;color:#333;" >
	
																					<tbody>
																						<?php echo $order->email_order_items_table( $order->is_download_permitted(), true, ($order->status=='processing') ? true : false, true,array( 64,64 ) ); ?>
																					</tbody>
																					<tfoot>
																					<?php
																						if ( $totals = $order->get_order_item_totals() ) {
																						$i = 0;
																						foreach ( $totals as $total ) {
																						$i++;
																					?>
																					<tr>
																					<th scope="row" colspan="2" style="text-align:left; border: 1px solid #eee; <?php if ( $i == 1 ) echo 'border-top-width: 4px;'; ?>"><?php echo $total['label']; ?></th>
																					<td style="text-align:left; border: 1px solid #eee; <?php if ( $i == 1 ) echo 'border-top-width: 4px;'; ?>"><?php echo $total['value']; ?></td>
																					</tr>
																					<?php
																							}
																								}
																					?>
																					</tfoot>
																				</table>                   
																			</td>
																		</tr>
																	</table>		
																			
								

																</td>
															</tr>
															<!-- Space -->
															<tr>
																<td width="650" height="25" style="line-height: 1px;"></td>
															</tr>
														</table>
														
														
													</td>
												</tr>
											</table><!-- End Text Left, Image Right 1st -->
											
											
											<!-- Coded Button -->

											
											</td>
											</tr>
											</table></div><!-- End MC Hideable -->
										
											</td>
											</tr>
											</table><!-- End MC Repeatable -->
											
											<!-- MC Repeatable -->
											<table width="650" border="0" cellpadding="0" cellspacing="0" align="center">
											<tr mc:repeatable="">
											<td>
										
											<div mc:hideable=""><!-- MC Hideable -->
											<table width="650" border="0" cellpadding="0" cellspacing="0" align="center">
											<tr mc:repeatable="">
											<td>
											
											<!-- Space -->
											
											
																
				
											</td>
											</tr>
											</table></div><!-- End MC Hideable -->
										
											</td>
											</tr>
											</table><!-- End MC Repeatable -->
											
											<!-- MC Repeatable -->
											<table width="590" border="0" cellpadding="0" cellspacing="0" align="center">
											<tr mc:repeatable="">
											<td>
										
											<div mc:hideable=""><!-- MC Hideable -->
											<table width="590" border="0" cellpadding="0" cellspacing="0" align="center">
											<tr mc:repeatable="">
											<td>
											
											<!-- Space -->
																			
											<!-- Text Left, Image Right 1st -->
											<table width="650" border="0" cellpadding="0" cellspacing="0" align="center">
												<tr>
													<td width="650" valign="top">
														
														<!-- Headline -->
														<table width="650" border="0" cellpadding="0" cellspacing="0" align="center">
															<tr>
																<td mc:edit="headline_left_2" style="font-size: 20px; color: #2d2d2d; font-weight: bold; text-align: left; font-family: Helvetica, Arial, sans-serif; line-height: 24px; vertical-align: top;">	
																	What Next?</td>
															</tr>
															<!-- Space -->
															<tr>
																<td height="8" style="line-height: 1px;"></td>
															</tr>
															<!-- Text -->
															<tr>
																<td mc:edit="text_left_2" style="font-size: 12px; color: #000; font-weight: bold; text-align: left; font-family: Helvetica, Arial, sans-serif; line-height: 21px; vertical-align: top;">Our Packaging Honks will prepare your order and put it into the delivery truck shortly. Once this is done, we will send you the dispatch confirmation with a link to track the package. <br>
															    Usually, it takes upto 7 days for the package to get delivered.</td>
															</tr>
															<!-- Space -->
															<tr>
																<td height="25" style="line-height: 1px;"></td>
															</tr>
														</table>
														
														
													</td>
																																						
												</tr>
											</table><!-- End Text Left, Image Right 1st -->
											
											
											<!-- Coded Button -->
											<table width="590" border="0" cellpadding="0" cellspacing="0" align="center">
												<tr>
													
													
													<td width="472" bgcolor="#ffffff"></td>
												</tr>
											</table><!-- End Coded Buttons -->
											
											</td>
											</tr>
											</table></div><!-- End MC Hideable -->
										
											</td>
											</tr>
											</table><!-- End MC Repeatable -->
											
											<!-- Space -->
										 					  </td>
										
									</tr>
							  </table><!-- End Wrapper -->
								
								<!-- Wrapper -->
								
											
										  <!-- Space -->
										
											
						  </td>
										
									</tr>
								</table><!-- End Wrapper -->
							
								<!-- iPhone Wrapper -->
								<table width="710" bgcolor="#2d2d2d" border="0" cellpadding="0" cellspacing="0" align="center">
									<tr>
										<td width="30"></td>
										<td width="590" height="102">
							
											<!-- Logo + Social Icons -->
											<table width="590" border="0" cellpadding="0" cellspacing="0" align="center">
												<tr>
													<!-- Logo -->
													<td width="200">
														<a href="#"><img src="http://orderstatus.appzab.com/images/logo.png" alt="MySite" width="117" height="25" longdesc="http://www.mysiteurl.com"></a>								
													</td>
												 <td mc:edit="subscribe" width="140" style="font-size: 12px; color: #cac9ca; font-weight: bold; text-align: center; font-family: Helvetica, Arial, sans-serif; line-height: 20px; vertical-align: top;"><a href="#" style="text-decoration: none; color: #cac9ca;">Call us 1800.111.111<br>
												   Email Us<br>
												   support@mysiteurl.com
                                                 </a></td>
		<td width="30"></td>											<!-- Unsubscribe -->
													<td mc:edit="subscribe" width="220" style="font-size: 12px; color: #cac9ca; font-weight: bold; text-align: center; font-family: Helvetica, Arial, sans-serif; line-height: 20px; vertical-align: top;">
														<a href="#" style="text-decoration: none; color: #cac9ca;">Address														<br>
														MyCompany, 645, 6th St Broadway, NY, New York - 10010 </a></td>
												</tr>
											</table><!-- End Logo + Social Icons -->
								
										</td>
										<td width="30"></td>
									</tr>
								</table><!-- End iPhone Wrapper -->
								
							</td>
							<td width="1" bgcolor="#cecece"></td>
						</tr>
					</table><!-- End 1px Wrapper -->
					
					<!-- Space -->
					
		
				</td>
			</tr>
		</table><!-- End Main wrapper -->

		</td>
	</tr>
</table><!-- End Wrapper -->

<!-- Done -->



<!-- Done -->

</body>
</html>