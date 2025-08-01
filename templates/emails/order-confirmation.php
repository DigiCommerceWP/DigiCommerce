<?php
/**
 * Order confirmation email template
 *
 * This template can be overridden by copying it to yourtheme/digicommerce/emails/order-confirmation.php
 *
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

$order = DigiCommerce_Orders::instance()->get_order( $order_id ); // phpcs:ignore
if ( ! $order ) {
	return;
}

// Get product instance for price formatting
$product = DigiCommerce_Product::instance();

// Get countries for proper country name display
$countries = DigiCommerce()->get_countries();

// Billing details
$data                 = $order['billing_details'] ?? array();
$company              = ! empty( $data ) ? $data['company'] : '';
$first_name           = ! empty( $data ) ? $data['first_name'] : '';
$last_name            = ! empty( $data ) ? $data['last_name'] : '';
$billing_address      = ! empty( $data ) ? $data['address'] : '';
$billing_city         = ! empty( $data ) ? $data['city'] : '';
$billing_postcode     = ! empty( $data ) ? $data['postcode'] : '';
$billing_state        = ! empty( $data ) ? $data['state'] : '';
$vat_number           = ! empty( $data ) ? $data['vat_number'] : '';
$billing_country      = ! empty( $data ) ? $data['country'] : '';
$billing_country_name = isset( $countries[ $billing_country ] ) ? $countries[ $billing_country ]['name'] : $billing_country;

// Payment method
$payment_method = $order['payment_method'];
if ( 'stripe' === $payment_method ) {
	$payment_method = esc_html__( 'Credit Card', 'digicommerce' );
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>
		<?php
		printf(
			// translators: %s: order number
			esc_html__( 'Order Confirmation %s', 'digicommerce' ),
			esc_attr( $order['order_number'] )
		);
		?>
	</title>
	<style type="text/css">
		<?php echo wp_strip_all_tags( DigiCommerce_Emails::instance()->get_styles() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS content needs to remain unescaped for email styling to work properly ?>
	</style>
</head>
<body>
	<div class="container">
		<?php echo wp_kses_post( DigiCommerce_Emails::instance()->get_header() ); ?>

		<div class="content">
			<h2><?php esc_html_e( 'Thank you for your order!', 'digicommerce' ); ?></h2>
			
			<p>
			<?php
			printf(
				/* translators: %s: customer first name */
				esc_html__( 'Hi %s,', 'digicommerce' ),
				esc_html( $first_name )
			);
			?>
			</p>
			
			<p><?php esc_html_e( 'Your order has been completed successfully. Here are the details of your purchase:', 'digicommerce' ); ?></p>

			<div class="order-info">
				<h3><?php esc_html_e( 'Order Details', 'digicommerce' ); ?></h3>
				<p><strong><?php esc_html_e( 'Order Number:', 'digicommerce' ); ?></strong> <?php echo esc_html( $order['order_number'] ); ?></p>
				<p><strong><?php esc_html_e( 'Date:', 'digicommerce' ); ?></strong> <?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $order['date_created'] ) ) ); ?></p>
				<p><strong><?php esc_html_e( 'Email:', 'digicommerce' ); ?></strong> <?php echo esc_html( $data['email'] ); ?></p>
				<p><strong><?php esc_html_e( 'Payment Method:', 'digicommerce' ); ?></strong> <?php echo esc_html( $payment_method ); ?></p>

				<?php
				// Check if DigiCommerce Pro is active and if we have license information
				if ( class_exists( 'DigiCommerce_Pro' ) ) :
					$licenses = DigiCommerce_Pro_License::instance()->get_user_licenses(
						$order['user_id'],
						array(
							'status'  => array( 'active', 'expired' ),
							'orderby' => 'date_created',
							'order'   => 'DESC',
						)
					);

					// Filter licenses for this specific order
					$order_licenses = array_filter(
						$licenses,
						function ( $license ) use ( $order_id ) {
							return $license['order_id'] == $order_id;
						}
					);

					if ( ! empty( $order_licenses ) ) :
						foreach ( $order_licenses as $license ) :
							?>
							<div class="license-info">
								<p><strong><?php echo esc_html( $license['product_name'] ); ?></strong></p>
								<p>
									<strong><?php esc_html_e( 'License Key:', 'digicommerce' ); ?></strong> 
									<span class="license-key"><?php echo esc_html( $license['license_key'] ); ?></span>
								</p>
								<?php
								$account_page_id = DigiCommerce()->get_option( 'account_page_id' );
								if ( $account_page_id ) :
									$license_url = add_query_arg(
										array(
											'section' => 'licenses',
										),
										get_permalink( $account_page_id )
									);
									?>
									<p>
										<a href="<?php echo esc_url( $license_url ); ?>" style="color: #4f46e5; text-decoration: none;">
											<?php esc_html_e( 'Manage Your Licenses', 'digicommerce' ); ?> →
										</a>
									</p>
								<?php endif; ?>
							</div>
							<?php
						endforeach;
						?>
						<div style="margin-top: 15px;">
							<p style="color: #4b5563; font-size: 14px;">
								<?php esc_html_e( 'You can view and manage all your licenses from your account dashboard.', 'digicommerce' ); ?>
							</p>
						</div>
						<?php
					endif;
				endif;
				?>
			</div>

			<table class="order-items">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Product', 'digicommerce' ); ?></th>
						<th><?php esc_html_e( 'Price', 'digicommerce' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $order['items'] as $item ) : ?>
						<tr>
							<td>
								<div class="inline-flex flex-col gap-2">
									<?php
									echo esc_html( $item['name'] );
									if ( ! empty( $item['variation_name'] ) ) {
										echo ' - ' . esc_html( $item['variation_name'] );
									}

									// Handle downloadable files
									$product_id = isset( $item['product_id'] ) ? absint( $item['product_id'] ) : 0;

									if ( $product_id && $order_id ) {
										// Check if this is a bundle product by checking order item data first
										$is_bundle_item = !empty($item['is_bundle']) && !empty($item['bundle_products']);

										// Fallback: check product meta if order item doesn't have bundle flag
										if (!$is_bundle_item) {
											$bundle_products_meta = get_post_meta( $product_id, 'digi_bundle_products', true );
											$is_bundle_from_meta = !empty($bundle_products_meta) && is_array($bundle_products_meta) && count(array_filter($bundle_products_meta)) > 0;
											
											// If it's a bundle from meta but doesn't have bundle_products in item, reconstruct the data
											if ($is_bundle_from_meta) {
												$item['is_bundle'] = true;
												$item['bundle_products'] = array();
												
												foreach ($bundle_products_meta as $bundle_product_id) {
													if (empty($bundle_product_id)) continue;
													
													$bundle_product_id = intval($bundle_product_id);
													$bundle_product = get_post($bundle_product_id);
													if ($bundle_product) {
														$bundle_files = get_post_meta($bundle_product_id, 'digi_files', true);
														$item['bundle_products'][] = array(
															'product_id' => $bundle_product_id,
															'name' => $bundle_product->post_title,
															'files' => $bundle_files ?: array(),
														);
													}
												}
												$is_bundle_item = true;
											}
										}

										if ( $is_bundle_item ) {
											// Display bundle products in email
											?>
											<div style="margin-top: 10px; padding: 10px; background-color: #f8f9fa; border-radius: 4px;">
												<div style="font-weight: 600; font-size: 14px; margin-bottom: 10px; color: #374151;">
													<?php esc_html_e( 'Bundle includes:', 'digicommerce' ); ?>
												</div>
												<?php 
												// Ensure bundle_products exists and is an array
												$bundle_products = isset($item['bundle_products']) && is_array($item['bundle_products']) ? $item['bundle_products'] : array();
												
												foreach ( $bundle_products as $bundle_product ) : 
													$bundle_product_id = isset($bundle_product['product_id']) ? intval($bundle_product['product_id']) : 0;
													$bundle_product_name = isset($bundle_product['name']) ? $bundle_product['name'] : '';
													
													if (!$bundle_product_id || !$bundle_product_name) continue;
												?>
													<div style="margin: 8px 0; padding: 8px; border-left: 3px solid #e5e7eb;">
														<div style="font-weight: 500; font-size: 14px; color: #374151; margin-bottom: 5px;">
															<?php echo esc_html( $bundle_product_name ); ?>
														</div>
														<?php
														$bundle_files = isset($bundle_product['files']) && is_array($bundle_product['files']) ? $bundle_product['files'] : array();
														
														if ( !empty( $bundle_files ) ) :
															// For email, show all available files as individual download links
															$downloadable_files = array();
															
															foreach ( $bundle_files as $file ) {
																if ( ! empty( $file['id'] ) ) {
																	$downloadable_files[] = $file;
																}
															}

															if ( count( $downloadable_files ) > 1 ) :
																// Multiple files - show all as individual links
																?>
																<div style="margin: 5px 0;">
																	<?php foreach ( $downloadable_files as $file ) : 
																		$file_name = $file['itemName'] ?? $file['name'] ?? esc_html__( 'Download', 'digicommerce' );
																	?>
																		<div style="margin: 3px 0;">
																			<a href="<?php echo esc_url( DigiCommerce_Files::instance()->generate_secure_download_url( $file['id'], $order_id, true ) ); ?>" 
																			style="display: inline-block; padding: 6px 12px; background-color: #e5e7eb; color: #374151; text-decoration: none; border-radius: 4px; font-size: 13px; margin-right: 5px;">
																				<?php echo esc_html( $file_name ); ?>
																			</a>
																		</div>
																	<?php endforeach; ?>
																</div>
																<?php
															elseif ( count( $downloadable_files ) === 1 ) :
																// Single file - show as single link
																$file = reset( $downloadable_files );
																$file_name = $file['itemName'] ?? $file['name'] ?? esc_html__( 'Download', 'digicommerce' );
																?>
																<div style="margin: 5px 0;">
																	<a href="<?php echo esc_url( DigiCommerce_Files::instance()->generate_secure_download_url( $file['id'], $order_id, true ) ); ?>" 
																	style="display: inline-block; padding: 6px 12px; background-color: #e5e7eb; color: #374151; text-decoration: none; border-radius: 4px; font-size: 13px;">
																		<?php echo esc_html( $file_name ); ?>
																	</a>
																</div>
																<?php
															endif;
														else :
															// No files available for this bundle product
															?>
															<div style="font-size: 12px; color: #9ca3af; font-style: italic;">
																<?php esc_html_e( 'No downloadable files', 'digicommerce' ); ?>
															</div>
															<?php
														endif;
														?>
													</div>
												<?php endforeach; ?>
											</div>
											<?php
										} else {
											$price_mode           = get_post_meta( $product_id, 'digi_price_mode', true );
											$variation_name       = isset( $item['variation_name'] ) ? $item['variation_name'] : '';
											$show_variation_files = false;
											$variation_files      = array();
											$regular_files        = array();

											// First check for variation files if it's a variable product
											if ( 'variations' === $price_mode && ! empty( $variation_name ) ) {
												$variations = get_post_meta( $product_id, 'digi_price_variations', true );

												if ( ! empty( $variations ) && is_array( $variations ) ) {
													foreach ( $variations as $variation ) {
														if ( isset( $variation['name'] ) && $variation['name'] === $variation_name ) {
															if ( ! empty( $variation['files'] ) && is_array( $variation['files'] ) ) {
																$variation_files      = $variation['files'];
																$show_variation_files = true;
																break;
															}
														}
													}
												}
											}

											// Only get regular files if no variation files were found
											if ( ! $show_variation_files ) {
												$cache_key     = 'product_files_' . $product_id;
												$regular_files = wp_cache_get( $cache_key, 'digicommerce_files' );

												if ( false === $regular_files ) {
													$regular_files = get_post_meta( $product_id, 'digi_files', true );

													if ( ! empty( $regular_files ) && is_array( $regular_files ) ) {
														wp_cache_set( $cache_key, $regular_files, 'digicommerce_files', HOUR_IN_SECONDS );
													}
												}
											}

											// Use variation files if available, otherwise fall back to regular files
											$files_to_show = $show_variation_files ? $variation_files : $regular_files;

											if ( ! empty( $files_to_show ) && is_array( $files_to_show ) ) :
												// Get only the latest file (last item in the array)
												$latest_file = end( $files_to_show );

												if ( ! empty( $latest_file['id'] ) ) :
													?>
													<div style="margin-top: 10px;">
														<div style="margin: 5px 0;">
															<a href="<?php echo esc_url( DigiCommerce_Files::instance()->generate_secure_download_url( $latest_file['id'], $order_id, true ) ); ?>" style="display: inline-block; padding: 8px 15px; background-color: #e5e7eb; color: #374151; text-decoration: none; border-radius: 4px; font-size: 14px;">
																<?php esc_html_e( 'Download', 'digicommerce' ); ?>
															</a>
														</div>
													</div>
													<?php
												endif;
											endif;
										}
									}
									?>
								</div>
							</td>
							<td><?php echo wp_kses_post( $product->format_price( $item['price'] ) ); ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>

			<div class="order-total">
				<p>
					<strong><?php esc_html_e( 'Subtotal:', 'digicommerce' ); ?></strong>
					<?php echo wp_kses_post( $product->format_price( $order['subtotal'] ) ); ?>
				</p>
				<p>
					<strong>
						<?php
						printf(
							'%s (%s%%):',
							esc_html__( 'VAT', 'digicommerce' ),
							esc_html( rtrim( rtrim( number_format( $order['vat_rate'] * 100, 3 ), '0' ), '.' ) )
						);
						?>
					</strong>
					<?php echo wp_kses_post( $product->format_price( $order['vat'] ) ); ?>
				</p>
				<?php if ( ! empty( $order['discount_code'] ) ) : ?>
					<p>
						<strong><?php esc_html_e( 'Discount:', 'digicommerce' ); ?></strong>
						-<?php echo wp_kses_post( $product->format_price( $order['discount_amount'] ) ); ?>
					</p>
				<?php endif; ?>
				<p>
					<strong><?php esc_html_e( 'Total:', 'digicommerce' ); ?></strong>
					<?php echo wp_kses_post( $product->format_price( $order['total'] ) ); ?>
				</p>
			</div>

			<div class="billing-info">
				<h3><?php esc_html_e( 'Billing Information', 'digicommerce' ); ?></h3>
				<p>
					<?php
					if ( ! empty( $company ) ) :
						echo esc_html( $company );
						?>
						<br>
						<?php
					endif;

					echo esc_html( $first_name . ' ' . $last_name );
					?>
					<br>
					
					<?php
					if ( ! empty( $address ) ) :
						echo esc_html( $address );
						?>
						<br>
						<?php
					endif;

					if ( ! empty( $city ) && ! empty( $postcode ) ) {
						echo esc_html(
							DigiCommerce_Orders::instance()->format_city_postal(
								$city,
								$postcode,
								$country,
								$countries
							)
						);
						echo '<br>';
					}

					if ( ! empty( $billing_state ) ) {
						echo esc_html( $billing_state ) . '<br>';
					}

					if ( ! empty( $billing_country_name ) ) {
						echo esc_html( $billing_country_name ) . '<br>';
					}

					if ( ! empty( $vat_number ) ) :
						esc_html_e( 'VAT: ', 'digicommerce' );
						?>
						<?php echo esc_html( $vat_number ); ?><br>
						<?php
					endif;

					if ( ! empty( $phone ) ) :
						echo esc_html( $phone );
					endif;
					?>
				</p>
			</div>

			<div style="text-align: center;">
				<a href="<?php echo esc_url( get_permalink( DigiCommerce()->get_option( 'account_page_id' ) ) ); ?>" class="button">
					<?php esc_html_e( 'View Order Details', 'digicommerce' ); ?>
				</a>
			</div>
		</div>

		<?php echo wp_kses_post( DigiCommerce_Emails::instance()->get_footer() ); ?>
	</div>
</body>
</html>