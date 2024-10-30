
<div id="tab_container">

<?php 
	
	/*
	echo '<pre>';
	print_r($ctlggi_general_options);
	echo '</pre>';
	*/
	
$categories_drop_down_nav  = isset( $ctlggi_general_options['categories_drop_down_nav'] ) ? sanitize_text_field( $ctlggi_general_options['categories_drop_down_nav'] ) : '';

$product_view_featured_image  = isset( $ctlggi_general_options['product_view_featured_image'] ) ? sanitize_text_field( $ctlggi_general_options['product_view_featured_image'] ) : '';

$display_cat_boxes  = isset( $ctlggi_general_options['display_cat_boxes'] ) ? sanitize_text_field( $ctlggi_general_options['display_cat_boxes'] ) : '0';

?>
    
<form method="post" action="" id="ctlggi-general-options-form">

<input type="hidden" name="ctlggi-general-options-form-nonce" value="<?php echo wp_create_nonce('ctlggi_general_options_form_nonce'); ?>"/>
    
<table class="form-table">
<h2 class="padding-top-15"><?php _e('Product Options', 'cataloggi'); ?></h2>
<p><?php _e('The following options affect how the products are displayed on the catalog frontend.', 'cataloggi'); ?></p>
    <tbody>
            <tr>
                <th scope="row"><?php _e('Products View', 'cataloggi'); ?></th>
                <td>
                    <select class="small-text" name="default_items_view" id="default_items_view">
                        <option selected="selected" value="<?php echo esc_attr( $ctlggi_general_options['default_items_view'] ); ?>"><?php echo esc_attr( $ctlggi_general_options['default_items_view'] ); ?></option>
                        <option value="Normal"><?php _e('Normal', 'cataloggi'); ?></option>
                        <option value="Large"><?php _e('Large', 'cataloggi'); ?></option>
                        <option value="List"><?php _e('List', 'cataloggi'); ?></option>
                    </select>
                    <p class="description"><?php _e('Select default product view option, Normal, Large or List view.', 'cataloggi'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Display Product Thumb Image', 'cataloggi'); ?></th>
                <td>
                <input type="checkbox" value="1" name="display_item_thumb_img" id="display_item_thumb_img" <?php echo ($ctlggi_general_options['display_item_thumb_img'] == '1') ? 'checked' : '' ?>>
                    <p class="description"><?php _e('Display the product thumb image on the product listing pages. (Product Thumb Image)', 'cataloggi'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Display Product Price', 'cataloggi'); ?></th>
                <td>
                <input type="checkbox" value="1" name="display_item_price" id="display_item_price" <?php echo ($ctlggi_general_options['display_item_price'] == '1') ? 'checked' : '' ?>>
                    <p class="description"><?php _e('Display the product price on the product listing page.', 'cataloggi'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Display Short Description', 'cataloggi'); ?></th>
                <td>
                <input type="checkbox" value="1" name="display_item_short_desc" id="display_item_short_desc" <?php echo ($ctlggi_general_options['display_item_short_desc'] == '1') ? 'checked' : '' ?>>
                    <p class="description"><?php _e('Display the product short description on the product listing page.', 'cataloggi'); ?></p>
                </td>
            </tr>
        <tr>
            <th scope="row"><?php _e('Products Per Page', 'cataloggi'); ?></th>
            <td>
             <input type="number" value="<?php echo esc_attr( $ctlggi_general_options['number_of_items_per_page'] ); ?>" name="number_of_items_per_page" class="small-text" id="number_of_items_per_page" step="1">
            </td>
        </tr>
        <tr>
            <th scope="row"><?php _e('Products Order By', 'cataloggi'); ?></th>
            <td>
            
<?php 

    // format items order by
    if ( $ctlggi_general_options['items_order_by'] == 'modified' ) {
        $items_order_by = 'last modified';
    } elseif ( $ctlggi_general_options['items_order_by'] == 'rand' ) {
        $items_order_by = 'random';
    } else {
        $items_order_by = $ctlggi_general_options['items_order_by'];
    }

?>
            
                <select class="small-text" name="items_order_by" id="items_order_by">
                    <option selected="selected" value="<?php echo esc_attr( $ctlggi_general_options['items_order_by'] ); ?>"><?php echo esc_attr( $items_order_by ); ?></option>
                    <option value="ID"><?php _e('ID', 'cataloggi'); ?></option>
                    <option value="author"><?php _e('author', 'cataloggi'); ?></option>
                    <option value="title"><?php _e('title', 'cataloggi'); ?></option>
                    <option value="name"><?php _e('name', 'cataloggi'); ?></option>
                    <option value="date"><?php _e('date', 'cataloggi'); ?></option>
                    <option value="modified"><?php _e('last modified', 'cataloggi'); ?></option>
                    <option value="rand"><?php _e('random', 'cataloggi'); ?></option>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row"><?php _e('Products Order', 'cataloggi'); ?></th>
            <td>
                <p>
                    <input type="radio" value="<?php echo esc_attr( 'ASC' ); ?>" name="items_order" id="items_order_asc" <?php echo ($ctlggi_general_options['items_order'] == 'ASC') ? 'checked' : '' ?>><?php _e('ASC', 'cataloggi'); ?>
                </p>
                <p>
                    <input type="radio" value="<?php echo esc_attr( 'DESC' ); ?>" name="items_order" id="items_order_decs" <?php echo ($ctlggi_general_options['items_order'] == 'DESC') ? 'checked' : '' ?>><?php _e('DESC', 'cataloggi'); ?>
                </p>
            </td>
        </tr>
            <tr>
                <th scope="row"><?php _e('Product View Featured Image', 'cataloggi'); ?></th>
                <td>
                <input type="checkbox" value="1" name="product_view_featured_image" id="product_view_featured_image" <?php echo ($product_view_featured_image == '1') ? 'checked' : '' ?>>
                    <p class="description"><?php _e('Display the product featured image on the product view page.', 'cataloggi'); ?></p>
                </td>
            </tr>
    </tbody>
</table>

<table class="form-table">
<h2><?php _e('Category Options', 'cataloggi'); ?></h2>
<p><?php _e('The following options affect how categories are displayed on the catalog frontend.', 'cataloggi'); ?></p>
    <tbody>
            <tr>
                <th scope="row"><?php _e('Display Category Boxes', 'cataloggi'); ?></th>
                <td>
                <input type="checkbox" value="1" name="display_cat_boxes" id="display_cat_boxes" <?php echo ($display_cat_boxes == '1') ? 'checked' : '' ?>>
                    <p class="description"><?php _e('Display the category boxes on the catalog pages.', 'cataloggi'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Display Sub Category Boxes', 'cataloggi'); ?></th>
                <td>
                <input type="checkbox" value="1" name="display_category_boxes" id="display_category_boxes" <?php echo ($ctlggi_general_options['display_category_boxes'] == '1') ? 'checked' : '' ?>>
                    <p class="description"><?php _e('Display the sub category boxes on the catalog pages.', 'cataloggi'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Categories Navigation', 'cataloggi'); ?></th>
                <td>
                <input type="checkbox" value="1" name="categories_drop_down_nav" id="categories_drop_down_nav" <?php echo ($categories_drop_down_nav == '1') ? 'checked' : '' ?>>
                    <p class="description"><?php _e('Display the categories drop down navigation menu on top of the products list.', 'cataloggi'); ?></p>
                </td>
            </tr>
        <tr>
            <th scope="row"><?php _e('Category Order By', 'cataloggi'); ?></th>
            <td>
            
<?php 

    // format category order by
    if ( $ctlggi_general_options['category_order_by'] == 'modified' ) {
        $category_order_by = 'last modified';
    } elseif ( $ctlggi_general_options['category_order_by'] == 'rand' ) {
        $category_order_by = 'random';
    } else {
        $category_order_by = $ctlggi_general_options['category_order_by'];
    }

?>
            
                <select class="small-text" name="category_order_by" id="category_order_by">
                    <option selected="selected" value="<?php echo esc_attr( $ctlggi_general_options['category_order_by'] ); ?>"><?php echo esc_attr( $category_order_by ); ?></option>
                    <option value="ID"><?php _e('ID', 'cataloggi'); ?></option>
                    <option value="author"><?php _e('author', 'cataloggi'); ?></option>
                    <option value="title"><?php _e('title', 'cataloggi'); ?></option>
                    <option value="name"><?php _e('name', 'cataloggi'); ?></option>
                    <option value="date"><?php _e('date', 'cataloggi'); ?></option>
                    <option value="modified"><?php _e('last modified', 'cataloggi'); ?></option>
                    <option value="rand"><?php _e('random', 'cataloggi'); ?></option>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row"><?php _e('Category Order', 'cataloggi'); ?></th>
            <td>
                <p>
                    <input type="radio" value="<?php echo esc_attr( 'ASC' ); ?>" name="category_order" id="items_order_asc" <?php echo ($ctlggi_general_options['category_order'] == 'ASC') ? 'checked' : '' ?>><?php _e('ASC', 'cataloggi'); ?>
                </p>
                <p>
                    <input type="radio" value="<?php echo esc_attr( 'DESC' ); ?>" name="category_order" id="items_order_decs" <?php echo ($ctlggi_general_options['category_order'] == 'DESC') ? 'checked' : '' ?>><?php _e('DESC', 'cataloggi'); ?>
                </p>
            </td>
        </tr>
    </tbody>
</table>

<table class="form-table">
<h2><?php _e('Sidebar Parent Category Menu Options', 'cataloggi'); ?></h2>
<p><?php _e('The following options affect how parent menu(s) are displayed on the catalog frontend.', 'cataloggi'); ?></p>
    <tbody>
        <tr>
            <th scope="row"><?php _e('Parent Menu Order By', 'cataloggi'); ?></th>
            <td>
            
<?php 

    // format parent menu order by
    if ( $ctlggi_general_options['parent_menu_order_by'] == 'modified' ) {
        $parent_menu_order_by = 'last modified';
    } elseif ( $ctlggi_general_options['parent_menu_order_by'] == 'rand' ) {
        $parent_menu_order_by = 'random';
    } else {
        $parent_menu_order_by = $ctlggi_general_options['parent_menu_order_by'];
    }

?>
            
                <select class="small-text" name="parent_menu_order_by" id="parent_menu_order_by">
                    <option selected="selected" value="<?php echo esc_attr( $ctlggi_general_options['parent_menu_order_by'] ); ?>"><?php echo esc_attr( $parent_menu_order_by ); ?></option>
                    <option value="ID"><?php _e('ID', 'cataloggi'); ?></option>
                    <option value="author"><?php _e('author', 'cataloggi'); ?></option>
                    <option value="title"><?php _e('title', 'cataloggi'); ?></option>
                    <option value="name"><?php _e('name', 'cataloggi'); ?></option>
                    <option value="date"><?php _e('date', 'cataloggi'); ?></option>
                    <option value="modified"><?php _e('last modified', 'cataloggi'); ?></option>
                    <option value="rand"><?php _e('random', 'cataloggi'); ?></option>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row"><?php _e('Parent Menu Order', 'cataloggi'); ?></th>
            <td>
                <p>
                    <input type="radio" value="<?php echo esc_attr( 'ASC' ); ?>" name="parent_menu_order" id="parent_menu_order_asc" <?php echo ($ctlggi_general_options['parent_menu_order'] == 'ASC') ? 'checked' : '' ?>> <?php _e('ASC', 'cataloggi'); ?>
                </p>
                <p>
                    <input type="radio" value="<?php echo esc_attr( 'DESC' ); ?>" name="parent_menu_order" id="parent_menu_order_decs" <?php echo ($ctlggi_general_options['parent_menu_order'] == 'DESC') ? 'checked' : '' ?>> <?php _e('DESC', 'cataloggi'); ?>
                </p>
            </td>
        </tr>
    </tbody>
</table>

<table class="form-table">
<h2><?php _e('Sidebar Sub Category Menu Options', 'cataloggi'); ?></h2>
<p><?php _e('The following options affect how sub menu(s) are displayed on the frontend.', 'cataloggi'); ?></p>
    <tbody>
        <tr>
            <th scope="row"><?php _e('Sub Menu Order By', 'cataloggi'); ?></th>
            <td>
            
<?php 

    // format sub menu order by
    if ( $ctlggi_general_options['sub_menu_order_by'] == 'modified' ) {
        $sub_menu_order_by = 'last modified';
    } elseif ( $ctlggi_general_options['sub_menu_order_by'] == 'rand' ) {
        $sub_menu_order_by = 'random';
    } else {
        $sub_menu_order_by = $ctlggi_general_options['sub_menu_order_by'];
    }

?>
            
                <select class="small-text" name="sub_menu_order_by" id="sub_menu_order_by">
                    <option selected="selected" value="<?php echo esc_attr( $ctlggi_general_options['sub_menu_order_by'] ); ?>"><?php echo esc_attr( $sub_menu_order_by ); ?></option>
                    <option value="ID"><?php _e('ID', 'cataloggi'); ?></option>
                    <option value="author"><?php _e('author', 'cataloggi'); ?></option>
                    <option value="title"><?php _e('title', 'cataloggi'); ?></option>
                    <option value="name"><?php _e('name', 'cataloggi'); ?></option>
                    <option value="date"><?php _e('date', 'cataloggi'); ?></option>
                    <option value="modified"><?php _e('last modified', 'cataloggi'); ?></option>
                    <option value="rand"><?php _e('random', 'cataloggi'); ?></option>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row"><?php _e('Sub Menu Order', 'cataloggi'); ?></th>
            <td>
                <p>
                    <input type="radio" value="<?php echo esc_attr( 'ASC' ); ?>" name="sub_menu_order" id="sub_menu_order_asc" <?php echo ($ctlggi_general_options['sub_menu_order'] == 'ASC') ? 'checked' : '' ?>> <?php _e('ASC', 'cataloggi'); ?>
                </p>
                <p>
                    <input type="radio" value="<?php echo esc_attr( 'DESC' ); ?>" name="sub_menu_order" id="sub_menu_order_decs" <?php echo ($ctlggi_general_options['sub_menu_order'] == 'DESC') ? 'checked' : '' ?>> <?php _e('DESC', 'cataloggi'); ?>
                </p>
            </td>
        </tr>
    </tbody>
</table>
    
    
    <?php submit_button(); ?>
</form>

</div><!--/ #tab_container-->