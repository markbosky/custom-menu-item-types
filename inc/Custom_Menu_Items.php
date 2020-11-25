<?php
/**
 * Main plugin file.
 *
 * @package Menu_Item_Types
 */

namespace Required\CustomMenuItemTypes;

use Required\CustomMenuItemTypes\Walker\NavMenuEditWithCustomItemTypes;

/**
 * Menu_Item_Types class.
 */
class Custom_Menu_Items {

	// Add menu meta box
	public function add_meta_box() {
		add_meta_box(
			'r_custom_item_types',
			__( 'Custom Menu Objects', 'custom-menu-item-types' ),
			array( $this, 'r_custom_item_types' ),
			'nav-menus',
			'side',
			'high'
		);
	}

	// Change item label and object depending on the type
	public function customize_menu_item_label( $menu_item ) {
		
		switch( $menu_item->type ) {
				
			case 'row':
				$menu_item->type_label = __( 'Row', 'custom-menu-item-types');
				$menu_item->object = 'row';
				break;
				
			case 'column':
				$menu_item->type_label = __( 'Column', 'custom-menu-item-types');
				$menu_item->object = 'column';
				$menu_item->column  = $menu_item->column ?? get_post_meta( $menu_item->ID, '_menu_item_column', true );
				break;
				
			case 'heading':
				$menu_item->type_label = __( 'Heading', 'custom-menu-item-types');
				$menu_item->object = 'heading';
				break;
			
			case 'button':
				$menu_item->type_label = __( 'Button', 'custom-menu-item-types');
				$menu_item->object = 'button';
				$menu_item->btn_color = $menu_item->btn_color ?? get_post_meta( $menu_item->ID, '_menu_item_btn_color', true );
				$menu_item->btn_style = $menu_item->btn_style ?? get_post_meta( $menu_item->ID, '_menu_item_btn_style', true );
				break;
			
			case 'spacer':
				$menu_item->type_label = __( 'Spacer', 'custom-menu-item-types');
				$menu_item->object = 'spacer';
				$menu_item->spacer_height = $menu_item->spacer_height ?? get_post_meta( $menu_item->ID, '_menu_item_spacer_height', true );
				break;
				
			default:
				return $menu_item;
		}

		return $menu_item;

	}

	public function nav_menu_start_el( $item_output, $item, $depth, $args ){
		
		switch( $menu_item->type ) {
				
			case 'row':
			case 'column':
			case 'heading':
			case 'button':
				$item_output = '';
				break;
				
			default:
				return $item_output;
		}

		/** This filter is documented in wp-includes/post-template.php */
		$title = apply_filters( 'the_title', $item->title, $item->ID );
		/** This filter is documented in wp-includes\nav-menu-template.php */
		$title = apply_filters( 'nav_menu_item_title', $title, $item, $args, $depth );

		$item->rcmit_type = $item->rcmit_type ?? get_post_meta( $item->ID, '_menu_item_rcmit_type', true );

		return $item_output;

	}

	public function wp_edit_nav_menu_walker() {
		return NavMenuEditWithCustomItemTypes::class;
	}

	/**
	 * Filters list of settings fields of a menu item.
	 *
	 * @param array $nav_menu_item_fields Mapping of ID to the field paragraph HTML.
	 * @param array $context {
	 *     Context for applied filter.
	 *
	 *     @type \Walker_Nav_Menu_Edit $walker Nav menu walker.
	 *     @type object                $item   Menu item data object.
	 *     @type int                   $depth  Current depth.
	 * }
	 * @return array Mapping of ID to the field paragraph HTML.
	 */
	public function nav_menu_item_fields( $nav_menu_item_fields, $context ) {
		
		switch( $context['item']->type ) {
				
			case 'row':
				unset(
					$nav_menu_item_fields['url'],
					$nav_menu_item_fields['css-classes'],
					$nav_menu_item_fields['description'],
					$nav_menu_item_fields['link-target'],
					$nav_menu_item_fields['xfn']
				);
				break;
				
			case 'column':
				unset(
					$nav_menu_item_fields['url'],
					$nav_menu_item_fields['css-classes'],
					$nav_menu_item_fields['description'],
					$nav_menu_item_fields['link-target'],
					$nav_menu_item_fields['xfn']
				);

				ob_start(); ?>
					<p class="field-column description description-wide">
						<label for="edit-menu-item-column-<?php echo $context['item']->ID; ?>">
							<?php _e( 'Width of column', 'custom-menu-item-types' ); ?><br />
							<select name="menu-item-column[<?php echo $context['item']->ID; ?>]">
								<option value="col-lg-12" <?php selected( $context['item']->column, 'col-lg-12' ); ?>><?php _e( 'Full', 'custom-menu-item-types' ); ?></option>
								<option value="col-lg-2" <?php selected( $context['item']->column, 'col-lg-2' ); ?>><?php _e( '1/6', 'custom-menu-item-types' ); ?></option>
								<option value="col-lg-3" <?php selected( $context['item']->column, 'col-lg-3' ); ?>><?php _e( '1/4', 'custom-menu-item-types' ); ?></option>
								<option value="col-lg-4" <?php selected( $context['item']->column, 'col-lg-4' ); ?>><?php _e( '1/3', 'custom-menu-item-types' ); ?></option>
								<option value="col-lg-6" <?php selected( $context['item']->column, 'col-lg-6' ); ?>><?php _e( '1/2', 'custom-menu-item-types' ); ?></option>
							</select>
						</label>
					</p>
				<?php $nav_menu_item_fields['column_width'] = ob_get_clean();
				break;
				
			case 'heading':
				unset(
					$nav_menu_item_fields['url'],
					$nav_menu_item_fields['css-classes'],
					$nav_menu_item_fields['description'],
					$nav_menu_item_fields['link-target'],
					$nav_menu_item_fields['xfn']
				);
				break;
			
			case 'button':
				
				unset(
					$nav_menu_item_fields['title'],
					$nav_menu_item_fields['css-classes'],
					$nav_menu_item_fields['description'],
					$nav_menu_item_fields['link-target'],
					$nav_menu_item_fields['xfn']
				);
				
				ob_start(); ?>
					<p class="field-url description description-wide">
						<label for="edit-menu-item-url-<?php echo $context['item']->ID; ?>">
							URL<br>
							<input type="text" id="edit-menu-item-url-<?php echo $context['item']->ID; ?>" class="widefat code edit-menu-item-url" name="menu-item-url[<?php echo $context['item']->ID; ?>]" value="<?php echo $context['item']->url; ?>" placeholder="https://">
						</label>
					</p>
				<?php $nav_menu_item_fields['url'] = ob_get_clean();
				
				ob_start(); ?>
					<p class="field-title description description-wide">
						<label for="edit-menu-item-title-<?php echo $context['item']->ID; ?>">
							Button Text<br>
							<input type="text" id="edit-menu-item-title-<?php echo $context['item']->ID; ?>" class="widefat edit-menu-item-title" name="menu-item-title[<?php echo $context['item']->ID; ?>]" value="<?php echo $context['item']->title; ?>">
						</label>
					</p>
				<?php $nav_menu_item_fields['title'] = ob_get_clean();

				ob_start(); ?>
					<p class="field-btn-color description description-wide">
						<label for="edit-menu-item-btn-color-<?php echo $context['item']->ID; ?>">
							<?php _e( 'Button Color', 'custom-menu-item-types' ); ?><br />
							<select name="menu-item-btn-color[<?php echo $context['item']->ID; ?>]">
								<option value="btn-dark" <?php selected( $context['item']->btn_color, 'btn-dark' ); ?>><?php _e( 'Grey', 'custom-menu-item-types' ); ?></option>
								<option value="btn-gold" <?php selected( $context['item']->btn_color, 'btn-gold' ); ?>><?php _e( 'Gold', 'custom-menu-item-types' ); ?></option>
								<option value="btn-blue" <?php selected( $context['item']->btn_color, 'btn-blue' ); ?>><?php _e( 'Blue', 'custom-menu-item-types' ); ?></option>
								<option value="btn-teal" <?php selected( $context['item']->btn_color, 'btn-teal' ); ?>><?php _e( 'Teal', 'custom-menu-item-types' ); ?></option>
								<option value="btn-purple" <?php selected( $context['item']->btn_color, 'btn-purple' ); ?>><?php _e( 'Purple', 'custom-menu-item-types' ); ?></option>
								<option value="btn-white" <?php selected( $context['item']->btn_color, 'btn-white' ); ?>><?php _e( 'White', 'custom-menu-item-types' ); ?></option>
							</select>
						</label>
					</p>
				<?php $nav_menu_item_fields['btn_color'] = ob_get_clean();
				
				ob_start(); ?>
					<p class="field-btn-style description description-wide">
						<label for="edit-menu-item-btn-style-<?php echo $context['item']->ID; ?>">
							<?php _e( 'Button Style', 'custom-menu-item-types' ); ?><br />
							<select name="menu-item-btn-style[<?php echo $context['item']->ID; ?>]">
								<option value="btn-outline" <?php selected( $context['item']->btn_style, 'btn-outline' ); ?>><?php _e( 'Outline', 'custom-menu-item-types' ); ?></option>
								<option value="btn-solid" <?php selected( $context['item']->btn_style, 'btn-solid' ); ?>><?php _e( 'Solid', 'custom-menu-item-types' ); ?></option>
							</select>
						</label>
					</p>
				<?php $nav_menu_item_fields['btn_style'] = ob_get_clean();
				break;
			case 'spacer':
				unset(
					$nav_menu_item_fields['url'],
					$nav_menu_item_fields['css-classes'],
					$nav_menu_item_fields['description'],
					$nav_menu_item_fields['link-target'],
					$nav_menu_item_fields['xfn']
				);

				ob_start(); ?>
					<p class="field-spacer-height description description-wide">
						<label for="edit-menu-item-spacer-height-<?php echo $context['item']->ID; ?>">
							Spacer Height<br>
							<input type="number" id="edit-menu-item-spacer-height-<?php echo $context['item']->ID; ?>" class="edit-menu-item-spacer-height" name="menu-item-spacer-height[<?php echo $context['item']->ID; ?>]" value="<?php echo $context['item']->spacer_height; ?>"> px
						</label>
					</p>
				<?php $nav_menu_item_fields['spacer_height'] = ob_get_clean();
				break;
				
			default:
				 return $nav_menu_item_fields;
		}

		return $nav_menu_item_fields;

	}

	public function customize_nav_menu_available_item_types( $item_types ) {
		// This would work if could query the custom items from somewhere.
		return $item_types;
	}

	/**
	 * Displays a metabox for the custom links menu item.
	 *
	 * @global int        $_nav_menu_placeholder
	 * @global int|string $nav_menu_selected_id
	 */
	public function r_custom_item_types() {

		global $_nav_menu_placeholder, $nav_menu_selected_id;
		$_nav_menu_placeholder = 0 > $_nav_menu_placeholder ? $_nav_menu_placeholder - 1 : -1; ?>
		<div class="posttypediv" id="custom-item-types">
			<div id="tabs-panel-custom-item-types" class="tabs-panel tabs-panel-active">
				<ul id ="custom-item-types-checklist" class="categorychecklist form-no-clear">
					<li>
						<label class="menu-item-title">
							<input type="radio" class="menu-item-checkbox" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-object-id]" value="-1"> <?php _e( 'Row Break', 'custom-menu-item-types' ); ?>
						</label>
						<input type="hidden" class="menu-item-type" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-type]" value="row">
						<input type="hidden" class="menu-item-title" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-title]" value="<?php _e( 'Row Break', 'custom-menu-item-types' ); ?>">
						<input type="hidden" class="menu-item-url" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-url]" value="">
					</li>
					<li>
						<label class="menu-item-title">
							<input type="radio" class="menu-item-checkbox" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-object-id]" value="-1"> <?php _e( 'Column', 'custom-menu-item-types' ); ?>
						</label>
						<input type="hidden" class="menu-item-type" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-type]" value="column">
						<input type="hidden" class="menu-item-title" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-title]" value="<?php _e( 'Column', 'custom-menu-item-types' ); ?>">
						<input type="hidden" class="menu-item-url" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-url]" value="">
					</li>
					<li>
						<label class="menu-item-title">
							<input type="radio" class="menu-item-checkbox" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-object-id]" value="-1"> <?php _e( 'Heading', 'custom-menu-item-types' ); ?>
						</label>
						<input type="hidden" class="menu-item-type" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-type]" value="heading">
						<input type="hidden" class="menu-item-title" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-title]" value="<?php _e( 'Heading', 'custom-menu-item-types' ); ?>">
						<input type="hidden" class="menu-item-url" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-url]" value="">
					</li>
					<li>
						<label class="menu-item-title">
							<input type="radio" class="menu-item-checkbox" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-object-id]" value="-1"> <?php _e( 'Button', 'custom-menu-item-types' ); ?>
						</label>
						<input type="hidden" class="menu-item-type" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-type]" value="button">
						<input type="hidden" class="menu-item-title" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-title]" value="<?php _e( 'Button', 'custom-menu-item-types' ); ?>">
						<input type="hidden" class="menu-item-url" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-url]" value="#">
					</li>
					<li>
						<label class="menu-item-title">
							<input type="radio" class="menu-item-checkbox" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-object-id]" value="-1"> <?php _e( 'Spacer', 'custom-menu-item-types' ); ?>
						</label>
						<input type="hidden" class="menu-item-type" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-type]" value="spacer">
						<input type="hidden" class="menu-item-title" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-title]" value="<?php _e( 'Spacer', 'custom-menu-item-types' ); ?>">
						<input type="hidden" class="menu-item-url" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-url]" value="">
						<input type="hidden" class="menu-item-spacer-height" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-spacer-height]" value="20">
					</li>
				</ul>
			</div>
			<input type="hidden" value="custom" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-type]" />
			<p class="button-controls wp-clearfix">
				<span class="add-to-menu">
					<input type="submit"<?php wp_nav_menu_disabled_check( $nav_menu_selected_id ); ?> class="button-secondary submit-add-to-menu right" value="<?php esc_attr_e( 'Add to Menu', 'custom-menu-item-types' ); ?>" name="add-custom-menu-item" id="submit-custom-item-types" />
					<span class="spinner"></span>
				</span>
			</p>
		</div><!-- /.custom-item-types -->
	<?php }

	public function wp_update_nav_menu_item( $menu_id = 0, $menu_item_db_id = 0, $args ) {

		if( ! current_user_can( 'edit_theme_options' ) ) return;

		// Add new menu item via ajax.
		if( isset( $_REQUEST['menu-settings-column-nonce'] ) && wp_verify_nonce( $_REQUEST['menu-settings-column-nonce'], 'add-menu_item' ) ) {

			if( ! empty( $_POST['menu-item']['-1']['menu-item-url'] ) && in_array( $_POST['menu-item']['-1']['menu-item-url'] ) ) {

				update_post_meta(
					$menu_item_db_id,
					'_menu_item_rcmit_type',
					sanitize_text_field( $_POST['menu-item']['-1']['menu-item-url'] )
				);

				update_post_meta(
					$menu_item_db_id,
					'_menu_item_url',
					''
				);

			}

		}

		// Update settings for existing menu items.
		if( isset( $_REQUEST['update-nav-menu-nonce'] ) && wp_verify_nonce( $_REQUEST['update-nav-menu-nonce'], 'update-nav_menu' ) ) {

			// Save column width
			if( ! empty( $_POST['menu-item-column'][ $menu_item_db_id ] ) ) {
				update_post_meta(
					$menu_item_db_id,
					'_menu_item_column',
					sanitize_text_field( $_POST['menu-item-column'][ $menu_item_db_id ] )
				);
			}
			
			// Save spacer height
			if( ! empty( $_POST['menu-item-spacer-height'][ $menu_item_db_id ] ) ) {
				update_post_meta(
					$menu_item_db_id,
					'_menu_item_spacer_height',
					sanitize_text_field( $_POST['menu-item-spacer-height'][ $menu_item_db_id ] )
				);
			}
			
			// Save button url, color, and style
			if( ! empty( $_POST['menu-item-btn-color'][ $menu_item_db_id ] ) ) {
				update_post_meta(
					$menu_item_db_id,
					'_menu_item_url',
					sanitize_text_field( $_POST['menu-item-url'][ $menu_item_db_id ] )
				);
				update_post_meta(
					$menu_item_db_id,
					'_menu_item_btn_color',
					sanitize_text_field( $_POST['menu-item-btn-color'][ $menu_item_db_id ] )
				);
				update_post_meta(
					$menu_item_db_id,
					'_menu_item_btn_style',
					sanitize_text_field( $_POST['menu-item-btn-style'][ $menu_item_db_id ] )
				);
			}

		}

	}

}
