<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Oxy_Toolbox_Mod {

	private $prefix      = '';
	private $mod         = '';
	private $title       = '';
	private $description = '';
	private $link        = '';

	function __construct( $prefix, $mod, $title, $description, $link ) {
		$this->prefix      = $prefix;
		$this->mod         = $mod;
		$this->title       = $title;
		$this->description = $description;
		$this->link        = $link;

		add_action( $this->prefix . 'register_options', array( $this, 'register_options' ) );
		add_action( $this->prefix . 'form_options', array( $this, 'options_form' ) );

	}

	function register_options() {
		$inital = 0;
		add_option( $this->prefix . $this->mod, $inital );
		register_setting( $this->prefix . 'settings', $this->prefix . $this->mod, array( $this, 'sanitize_enable' ) );
	}

	function sanitize_enable( $enable ) {

		if ( is_numeric( $enable ) && intval( $enable ) === 1 && true === OxyToolboxLicense::is_activated_license() ) {
			return 1;
		}

		return 0; // default
	}

	function options_form() {
		?>

		
				<tr valign="top"<?php echo get_option( $this->prefix . $this->mod ) === '1' ? ' class="active"' : ' class="inactive"'; ?>>
					<th class="check-column">
						<input id="<?php echo $this->prefix . $this->mod; ?>" name="<?php echo $this->prefix . $this->mod; ?>" type="checkbox" value="1" <?php checked( get_option( $this->prefix . $this->mod ), 1 ); ?> />
					</th>
					<td class="plugin-title column-primary">
						<?php echo '<strong>' . $this->title . '</strong>'; ?>
						<?php echo '<p>' . $this->description . '</p>'; ?>
						<?php do_action( $this->prefix . $this->mod . 'form_options' ); ?>
					</td>
	<th class="doc-link-th">
		<?php
		if ( '' !== $this->link ) {
			?>
 <a href="<?php echo $this->link; ?>" target="_blank" class="doc-link">Doc</a><?php } ?></th>
				</tr>
			

		<?php

	}
}
