<?php
/**
 * Apply CSS Admin Dashboard
 *
 * @package       APPLYCSSAD
 * @author        Khairul Imran
 * @license       gplv3-or-later
 * @version       1.0.1
 *
 * @wordpress-plugin
 * Plugin Name:   Apply CSS Admin Dashboard
 * Plugin URI:    https://khairulimran.com/
 * Description:   Customize the look of your WordPress site with the Custom CSS Admin plugin. Add custom CSS code from the dashboard to make style tweaks or overhaul the design of your site. Coding knowledge is required.
 * Version:       1.0.1
 * Author:        Khairul Imran
 * Author URI:    https://khairulimran.com/
 * Text Domain:   apply-css-admin-dashboard
 * Domain Path:   /languages
 * License:       GPLv3 or later
 * License URI:   https://www.gnu.org/licenses/gpl-3.0.html
 *
 * You should have received a copy of the GNU General Public License
 * along with Apply CSS Admin Dashboard. If not, see <https://www.gnu.org/licenses/gpl-3.0.html/>.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

function custom_admin_css_enqueue_scripts() {
    wp_enqueue_style( 'codemirror', 'https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/codemirror.css' );
    wp_enqueue_style( 'codemirror-theme', 'https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/theme/night.css' );
    wp_enqueue_script( 'codemirror', 'https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/codemirror.js', array( 'jquery' ) );
    wp_enqueue_script( 'codemirror-css', 'https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/mode/css/css.js', array( 'codemirror' ) );
    wp_enqueue_script( 'codemirror-closebrackets', 'https://codemirror.net/5/addon/edit/closebrackets.js', array( 'codemirror' ) );
  }
  add_action( 'admin_enqueue_scripts', 'custom_admin_css_enqueue_scripts' );
  
  
  function custom_admin_css_menu() {
    add_submenu_page( 'options-general.php', 'Custom Admin CSS', 'Custom Admin CSS', 'manage_options', 'custom-admin-css', 'custom_admin_css_options' );
  }
  add_action( 'admin_menu', 'custom_admin_css_menu' );
  
  function custom_admin_css_options() {
    if ( !current_user_can( 'manage_options' ) )  {
      wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }
    ?>
  <div class="wrap">
    <h1>Custom Admin CSS</h1>
    <form method="post" action="options.php">
      <?php settings_fields( 'custom_admin_css_options' ); ?>
      <?php do_settings_sections( 'custom-admin-css' ); ?>
      <table class="form-table">
        <tr valign="top">
          <th scope="row">Custom CSS</th>
          <td>
            <textarea name="custom_admin_css" id="custom_admin_css" class="codemirror" style="width:100%;height:200px;"><?php echo get_option( 'custom_admin_css' ); ?></textarea>
          </td>
        </tr>
        <tr valign="top">
          <th scope="row">Enable Custom CSS</th>
          <td>
            <label class="switch">
              <input type="checkbox" name="custom_admin_css_enabled" id="custom_admin_css_enabled" value="1" <?php checked(1, get_option('custom_admin_css_enabled'), true); ?> />
              <span class="slider"></span>
            </label>
          </td>
        </tr>
      </table>
      <?php submit_button(); ?>
    </form>
  </div>
    <script>
    CodeMirror.defineMode("css-extended", function(config, parserConfig) {
      var cssMode = CodeMirror.getMode(config, "css");
      var cssExtendedMode = {};
      for (var prop in cssMode) {
        cssExtendedMode[prop] = cssMode[prop];
      }
      cssExtendedMode.autoCloseBrackets = true;
      return cssExtendedMode;
    });
  
    jQuery(document).ready(function($){
      var editor = CodeMirror.fromTextArea(document.getElementById("custom_admin_css"), {
        lineNumbers: true,
        mode: 'css-extended',
        autoCloseBrackets: true,
        theme: 'night',
        readOnly: false,
      });
  
      $('#custom_admin_css_enabled').change(function() {
        var value = $(this).is(':checked') ? 1 : 0;
        $.ajax({
      type: 'POST',
      url: ajaxurl,
      data: {
        action: 'custom_admin_css_save_option',
        option_name: 'custom_admin_css_enabled',
        option_value: value
      },
      success: function(response) {
        console.log(response);
        location.reload();
      }});
      });
  
  });
    </script>
  
  <style>
  .switch {
    position: relative;
    display: inline-block;
    width: 60px;
    height: 34px;
  }
  
  .switch input { 
    opacity: 0;
    width: 0;
    height: 0;
  }
  
  .slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    -webkit-transition: .4s;
    transition: .4s;
  }
  
  .slider:before {
    position: absolute;
    content: "";
    height: 26px;
    width: 26px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    -webkit-transition: .4s;
    transition: .4s;
  }
  
  input:checked + .slider {
    background-color: #2196F3;
  }
  
  input:focus + .slider {
    box-shadow: 0 0 1px #2196F3;
  }
  
  input:checked + .slider:before {
    -webkit-transform: translateX(26px);
    -ms-transform: translateX(26px);
    transform: translateX(26px);
  }
  
  /* Rounded sliders */
  .slider.round {
    border-radius: 34px;
  }
  
  .slider.round:before {
    border-radius: 50%;
  }
  
  </style>
  
  <?php
  
  }
  
  
  // handle AJAX request to save custom_admin_css_enabled option
  function custom_admin_css_save_option() {
    $option_name = $_POST['option_name'];
    $option_value = $_POST['option_value'];
    update_option( $option_name, $option_value );
    wp_die();
  }
  add_action( 'wp_ajax_custom_admin_css_save_option', 'custom_admin_css_save_option' );
  
  
  // register plugin settings
  function custom_admin_css_register_settings() {
    register_setting( 'custom_admin_css_options', 'custom_admin_css' );
    register_setting( 'custom_admin_css_options', 'custom_admin_css_enabled', 'intval' );
  }
  add_action( 'admin_init', 'custom_admin_css_register_settings' );
  
  
  
  function custom_admin_css_wp_head() {
    // only output custom CSS if it is enabled
    if ( get_option( 'custom_admin_css_enabled' ) ) {
      echo '<style type="text/css">' . get_option( 'custom_admin_css' ) . '</style>';
    }
  }
  add_action( 'admin_head', 'custom_admin_css_wp_head' );
  
  ?>