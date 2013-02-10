<?php
/**
 * Plugin Name: DKO OpenID Delegate
 * Plugin URI:  http://davidosomething.com/
 * Description: Adds OpenID delegate link tags to your HTML head
 * Version:     1.0.3
 * Author:      David O'Trakoun (@davidosomething)
 * Author URI:  http://davidosomething.com/
 */

define('DKOOPENID_OPTIONS_KEY', 'dkoopenid_settings');

register_activation_hook(__FILE__, 'dkoopenid_activation');

function dkoopenid_activation() {
  if (get_option(DKOOPENID_OPTIONS_KEY)) {
    return;
  }

  $defaults = array(
    'dkoopenid_xrds_location' => '',
    'dkoopenid_provider'      => '',
    'dkoopenid_local_id'      => ''
  );
  update_option(DKOOPENID_OPTIONS_KEY, $defaults);
}

class DKOOpenID {

  private $option_group            = 'dkoopenid_option_group';
  private $settings_page_menu_slug  = 'dkoopenid_settings_page';
  private $settings_section_name    = 'dkoopenid_settings_section';

  public function __construct() {
    if (!is_admin()) {
      add_action('wp_head', array(&$this, 'add_tags_to_wp_head'));
    }
    else {
      add_action('admin_menu', array(&$this, 'add_options_page'));
      add_action('admin_init', array(&$this, 'register_settings'));
    }
  }

  public function add_options_page() {
    $page_title = 'DKO OpenID Settings Page';
    $menu_title = 'DKO OpenID Settings';
    $capability = 'manage_options';
    $menu_slug  = $this->settings_page_menu_slug;
    $function   = array(&$this, 'settings_page_html');
    add_options_page($page_title, $menu_title, $capability, $menu_slug, $function);
  }

  /**
   * add_tags_to_wp_head
   *
   * @return void
   */
  public function add_tags_to_wp_head() {
    $options = get_option(DKOOPENID_OPTIONS_KEY);
    if (!empty($options['dkoopenid_xrds_location'])) {
      ?><meta http-equiv="X-XRDS-Location" content="<?php echo $options['dkoopenid_xrds_location']; ?>"><?php
    }
    if (!empty($options['dkoopenid_provider'])) {
      ?><link rel="openid.server" href="<?php echo $options['dkoopenid_provider']; ?>"><link rel="openid2.provider" href="<?php echo $options['dkoopenid_provider']; ?>"><?php
    }
    if (!empty($options['dkoopenid_local_id'])) {
      ?><link rel="openid.delegate" href="<?php echo $options['dkoopenid_local_id']; ?>"><link rel="openid2.local_id" href="<?php echo $options['dkoopenid_local_id']; ?>"><?php
    }
  }

  public function settings_page_html() {
?>
  <div class="wrap">
    <h2>DKO OpenID Settings</h2>
    <form method="post" action="options.php">
      <?php settings_fields($this->option_group); ?>
      <?php do_settings_sections($this->settings_page_menu_slug); ?>
      <input name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" />
    </form>
  </div>
<?php
  }

  public function register_settings() {
    add_settings_section(
      $this->settings_section_name,
      'OpenID server settings',
      array(&$this, 'settings_section_html'),
      $this->settings_page_menu_slug
    );
    add_settings_field(
      'dkoopenid_provider',
      'OpenID server (openid.server and openid2.provider)',
      array(&$this, 'text_input_html'),
      $this->settings_page_menu_slug,
      $this->settings_section_name
    );
    add_settings_field(
      'dkoopenid_local_id',
      'OpenID delegate (openid.delegate and openid2.local_id)',
      array(&$this, 'text_input_html'),
      $this->settings_page_menu_slug,
      $this->settings_section_name
    );
    add_settings_field(
      'dkoopenid_xrds_location',
      'X-XRDS-Location',
      array(&$this, 'text_input_html'),
      $this->settings_page_menu_slug,
      $this->settings_section_name
    );

    register_setting(
      $this->option_group,
      DKOOPENID_OPTIONS_KEY,
      array(&$this, 'validate_settings')
    );
  }

  public function settings_section_html() {
    // HTML output
  }

  public function text_input_html() {
    $options = get_option(DKOOPENID_OPTIONS_KEY);
    echo '<input type="text" name="" id="" value="', $options['dkoopenid_'], '">';
  }

  public function validate_settings($input) {
    return $input;
  }

}

new DKOOpenID;
