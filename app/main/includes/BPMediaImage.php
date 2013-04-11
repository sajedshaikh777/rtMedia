<?php

/**
 * Description of BPMediaImage
 *
 * @author Joshua Abenazer <joshua.abenazer@rtcamp.com>
 */
class BPMediaImage {

    public function __construct() {
        add_action('bp_media_add_media_fields', array($this, 'edit'), 99);
        add_action('wp_enqueue_scripts', array($this, 'editor_ui'));
    }

    public function edit($media_type) {
        if ($media_type == 'image') {
            global $bp_media_current_entry;
            $id = $bp_media_current_entry->get_id();
            $editor = wp_get_image_editor(get_attached_file($id));
            include_once( ABSPATH . 'wp-admin/includes/image-edit.php' );
            echo '<div class="image-editor" id="image-editor-' . $id . '">';
            wp_image_editor($id);
            $thumb_url = wp_get_attachment_image_src($id, array(900, 450), true);
            $nonce = wp_create_nonce("image_editor-$id");
            echo '</div>';

            echo '<div id="imgedit-response-' . $id . '"></div>';
            echo '<div class="wp_attachment_image" id="media-head-' . $id . '" style="display: none;">
			<p><input type="button" id="imgedit-open-btn-' . $id . '" onclick="imageEdit.open( \'' . $id . '\', \'' . $nonce . '\' )" class="button" value="Edit Image"> <span class="spinner" style="display: none;"></span></p>
		</div>';
            echo '<div class="image-editor" id="imgedit-response-' . $id . '"></div>';
        }
    }

    public function editor_ui() {
        if (( 'edit' == bp_action_variable(0)) && is_numeric(bp_action_variable(1))) {
            if ('attachment' == get_post_type(bp_action_variable(1)) && substr_compare(get_post_field('post_mime_type', bp_action_variable(1)), 'image', 0, 6)) {
                $suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
                wp_enqueue_script('wp-ajax-response');
                wp_enqueue_script('bp-media-image-edit', admin_url("js/image-edit$suffix.js"), array('jquery', 'json2', 'imgareaselect'), false, 1);
                wp_enqueue_style('bp-media-image-edit', BP_MEDIA_URL . 'app/assets/css/image-edit.css');
                wp_enqueue_style('imgareaselect');
            }
        }
    }

}

?>
