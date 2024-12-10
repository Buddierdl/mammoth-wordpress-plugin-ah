<?php
/*
Plugin Name: Mammoth .docx converter - forked for Analyst Hub
Plugin URI: https://github.com/mwilliamson/mammoth-wordpress-plugin
Description: Mammoth converts semantically marked up .docx documents to simple and clean HTML.
Version: 1.20.1
Author: Michael Williamson
Author URI: http://mike.zwobble.org/
License: BSD 2-clause
*/

define("MAMMOTH_URI", plugin_dir_url(__FILE__));

add_action( 'add_meta_boxes', 'mammoth_add_post_meta_box' );
add_action( 'admin_footer', 'mammoth_load_javascript' );
add_action( 'admin_enqueue_scripts', 'mammoth_admin_style' );


function mammoth_add_post_meta_box() {
    $post_types = get_post_types();

    foreach ($post_types as $post_type) {
        if (post_type_supports($post_type, 'editor')) {
            add_meta_box(
                'mammoth_add_post',
                __( 'Mammoth .docx converter' ),
                'mammoth_render_editor_box',
                $post_type
            );
        }
    }
}

function mammoth_admin_style() {
    wp_enqueue_style( 'mammoth-style', MAMMOTH_URI . 'mammoth.css', array(), "1.4.0" );
}


function mammoth_render_editor_box( $post ) {
    ?>
    <div id="mammoth-docx-uploader" class="status-empty">
        <div>
            <label>
                Select docx file:
                <input type="file" id="mammoth-docx-upload" />
            </label>
        </div>

        <div id="mammoth-docx-loading">
            Loading...
        </div>

        <div id="mammoth-docx-inserting">
            Inserting...
            <div id="mammoth-progress-bar">
                <div id="mammoth-progress-bar-inner"></div>
            </div>
        </div>

        <p class="mammoth-docx-error">
            Error while attempting to convert file:
            <span id="mammoth-docx-error-message"></span>
        </p>

        <div class="mammoth-docx-preview">
            <input type="hidden"
                id="mammoth-docx-upload-image-nonce"
                value="<?php echo wp_create_nonce( "media-form" ); ?>"
                />
            <input type="hidden"
                id="mammoth-docx-upload-image-href"
                value="<?php echo get_site_url( null, "wp-admin/async-upload.php", "admin" ); ?>"
                />
            <input type="hidden"
                id="mammoth-docx-admin-ajax-href"
                value="<?php echo get_site_url( null, "wp-admin/admin-ajax.php", "admin" ); ?>"
                />

            <p><input type="button" id="mammoth-docx-insert" value="Insert into editor" /></p>
            <div class="mammoth-tabs">
                <div class="tab">
                    <h4>Visual</h4>
                    <iframe
                        id="mammoth-docx-visual-preview"
                        src="about:blank"
                        data-stylesheets="<?php echo mammoth_editor_stylesheets_list(); ?>">
                    </iframe>
                </div>
                <div class="tab">
                    <h4>Raw HTML</h4>
                    <pre id="mammoth-docx-raw-preview">
                    </pre>
                </div>
                <div class="tab">
                    <h4>Messages</h4>
                    <div id="mammoth-docx-messages">
                    </div>
                </div>
            </div>
        </div>

    </div>
<?php
}


function mammoth_load_javascript() {
    mammoth_load_script( 'mammoth-editor' );
    mammoth_load_script( 'tabs' );
}

function mammoth_load_script( $name ) {
    $url = MAMMOTH_URI . $name . '.js';
    echo '<script src="'. $url . '?v=1.20.1c"></script>';
}

function mammoth_editor_stylesheets_list( ) {
    return implode( ',', get_editor_stylesheets() );
}
