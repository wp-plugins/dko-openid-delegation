<?php
/**
 * Plugin Name: DKO OpenID Delegation
 * Plugin URI:  http://davidosomething.com/
 * Description: Adds OpenID delegation link and meta tags to your HTML head
 * Version:     1.0.1
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

  private $option_group             = 'dkoopenid_option_group';
  private $settings_page_menu_slug  = 'dkoopenid_settings_page';
  private $settings_section_name    = 'dkoopenid_settings_section';
  private $settings_page_title      = 'DKO OpenID Delegation Settings';

  public function __construct() {
    if (!is_admin()) {
      add_action('wp_head', array(&$this, 'add_tags_to_wp_head'));
    }
    else {
      add_action('admin_init', array(&$this, 'register_settings'));
      add_action('admin_menu', array(&$this, 'add_settings_submenu'));

      $plugin = plugin_basename(__FILE__);
      add_filter('plugin_action_links_$plugin', array(&$this, 'add_settings_link_to_plugins_page'));
    }
  }

  public function add_settings_link_to_plugins_page($links) {
    $page               = basename(__FILE__);
    $settings_page_href = admin_url('options-general.php?page = ' . $page);
    $settings_link      = '<a href="' . $settings_page_path . '">Settings</a>';
    array_unshift($links, $settings_link);
    return $links;
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

  /**
   * add_settings_submenu
   *
   * @return void
   */
  public function add_settings_submenu() {
    $menu_title = 'DKO OpenID Delegation';
    $capability = 'manage_options';
    $function   = array(&$this, 'settings_page_html');
    add_options_page($this->settings_page_title, $menu_title, $capability, $this->settings_page_menu_slug, $function);
  }

  public function settings_page_html() {
?><div class="wrap">
    <div id="icon-options-general" class="icon32"><br></div><h2><?php echo $this->settings_page_title; ?></h2>
    <form method="post" action="options.php">
      <p>For information on what OpenID is, check out this site: <a href="http://openid.net/">OpenID.net</a>.</p>
      <?php
        settings_fields($this->option_group);
        do_settings_sections($this->settings_page_menu_slug);
      ?>
      <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php esc_attr_e('Save Changes'); ?>"></p>
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

    $option_key = 'dkoopenid_provider';
    add_settings_field(
      $option_key,
      'OpenID Provider',
      array(&$this, 'text_input_html'),
      $this->settings_page_menu_slug,
      $this->settings_section_name,
      array(
        'option_key'  => $option_key,
        'label_for'   => 'dkoopenid_field_' . $option_key,
        'placeholder' => 'https://www.myopenid.com/server',
        'description' => 'This is the href for the <code>openid.server</code> and <code>openid2.provider</code> link tags.',
        'required'    => true,
      )
    );
    $option_key = 'dkoopenid_local_id';
    add_settings_field(
      $option_key,
      'OpenID Local ID',
      array(&$this, 'text_input_html'),
      $this->settings_page_menu_slug,
      $this->settings_section_name,
      array(
        'option_key'  => $option_key,
        'label_for'   => 'dkoopenid_field_' . $option_key,
        'placeholder' => 'http://USERNAME.myopenid.com',
        'description' => 'This is the href for the <code>openid.delegate</code> and <code>openid2.local_id</code> link tags.',
        'field_type'  => 'url',
        'required'    => true,
      )
    );

    $option_key = 'dkoopenid_xrds_location';
    add_settings_field(
      $option_key,
      'X-XRDS-Location',
      array(&$this, 'text_input_html'),
      $this->settings_page_menu_slug,
      $this->settings_section_name,
      array(
        'option_key'  => $option_key,
        'label_for'   => 'dkoopenid_field_' . $option_key,
        'placeholder' => 'http://www.myopenid.com/xrds?username=USERNAME.myopenid.com',
        'description' => 'This is the content value for the <code>X-XRDS-Location</code> meta tag.',
        'field_type'  => 'url',
        'required'    => false,
      )
    );

    register_setting(
      $this->option_group,
      DKOOPENID_OPTIONS_KEY,
      array(&$this, 'validate_settings')
    );
  }

  public function settings_section_html() {
    echo <<<EOF
<p>Enter in your OpenID server information here. To obtain an OpenID, check out 
<a href="http://www.myopenid.com/">MyOpenID</a> (not affiliated). For help on
how to fill in these fields, check with your OpenID provider, e.g.,
<a href="https://www.myopenid.com/help#own_domain">https://www.myopenid.com/help#own_domain</a>.
EOF;
    // HTML output
  }

  public function text_input_html($args) {
    $options     = get_option(DKOOPENID_OPTIONS_KEY);
    $field_name  = DKOOPENID_OPTIONS_KEY . "[{$args['option_key']}]";
    $field_id    = 'dkoopenid_field_' . $args['option_key'];

    $field_value = $options[$args['option_key']];
    if (!empty($args['field_type']) && $args['field_type'] == 'url') {
      $field_value = esc_url($field_value);
    }

    echo '<input type="text" name="', $field_name, '" id="', $field_id ,'" value="', $field_value , '" placeholder="', $args['placeholder'], '" class="regular-text ltr">';
    if (!empty($args['required']) && $args['required']) {
      ?><abbr class="required">*</abbr><?php
    }
    if (!empty($args['description'])) {
      ?><p class="description"><?php echo $args['description']; ?></p><?php
    }
  }

  /**
   * validate_settings
   * Since this plugin only takes URLs in the settings fields, we just
   * filter all the inputs through WP's esc_url_raw() function if it is a valid
   * URL.
   *
   * @param array $input
   * @return array
   */
  public function validate_settings($input) {
    $output = array();
    foreach ($input as $field => $value) {
      $is_url = filter_var($value, FILTER_VALIDATE_URL);
      if ($is_url) {
        $output[$field] = esc_url_raw($value);
      }
      else {
        $output[$field] = '';
      }
    }
    return $output;
  }

}

new DKOOpenID;
