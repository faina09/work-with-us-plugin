<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://stefanocotterli.it
 * @since      1.0.0
 *
 * @package    Showcta
 * @subpackage Showcta/includes
 */

class Showcta {

	/**
	 * The identifiers of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to short descfribe this plugin.
	 * @var      string    $plugin_code    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;
	protected $plugin_code;
	

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'SHOWCTA_VERSION' ) ) {
			$this->version = SHOWCTA_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		if ( defined( 'SHOWCTA_NAME' ) ) {
			$this->plugin_name = SHOWCTA_NAME;
		} else {
			$this->plugin_name = 'undefined';
		}
		if ( defined( 'SHOWCTA_CODE' ) ) {
			$this->plugin_code = SHOWCTA_CODE;
		} else {
			$this->plugin_code = 'undefined';
		}
		$this->define_public_hooks();
	}


	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {
		add_shortcode( 'showcta', array( $this, 'add_fshortcode' ) ); //shortcode - JUST FOR TEST
		
		// filtra per ogni content caricato dal main_loop
		add_filter( 'the_content', array( $this, 'add_cta' ), 0, 1 );

		// aggiunta voce al menù Impostazioni e pagina gestione opzioni CTA
		add_action( 'admin_init', array( $this, 'add_foption_init' ) );	
		add_action( 'admin_menu', array( $this, 'add_foption_page' ) );
	}
	
	
	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}
	
	/**
	 * visualizzazione shortcode - JUST FOR TEST
	 * @since     1.0.0
	 * @return    string	The html fragment shown in the placeholder
	 */
	public function add_fshortcode( $atts ) {
		// solo per test per verificare se il plugin è attivo e la sua versione 
		return '<b>SHORTCODE per plugin: ' . $this->plugin_name . ' VER. ' . $this->version . '</b><br/>';
	}


	/**
	 * visualizzazione Call To Action
	 */
	public function add_cta( $content ){
		// cerca in tutti i post (articoli)
		if ( is_singular('post') &&  in_the_loop() && is_main_query() ) {
			$post = get_post( $content );
			// TODO possibile sviluppo: leggi dalle options il tag o i tags da cercare; inserimenti di più CTA diversi dopo l'n, m, ... -esimo paragrafo
			$searchtag = 'governo';
 			if ( has_tag($searchtag, $post) ) { 
				// TODO get the CTA position from options
				$position = 4; // fisso dopo il 4th paragrafo
				$options = get_option( $this->plugin_code );
				if ( $options['active'] ) {
					// recupera il frammento di codice html per il CTA dalle options
					$paragraphAfter[$position] = $options['htmlcode'];
					$paragraphs = explode("</p>", $content);
					$count = count($paragraphs);
					// inizia riscrittura del content includendo il CTA
					$content = '';
					for ($i = 0; $i < $count; $i++ ) {
						if ( array_key_exists($i, $paragraphAfter) ) {
							$content = $content . $paragraphAfter[$i];// add CTA html code after the n-th paragraph
						}
						$content = $content . $paragraphs[$i] . "</p>";
					}
				}
			}
		}
		return $content;
	}
	
	/*************************************************************
	* Codice per amministrazione del CTA
	*************************************************************/

	/**
	 * options stored in DB entry '$this->plugin_code'
	 */
	function add_foption_init()
	{
		register_setting( $this->plugin_code . '_options', $this->plugin_code );
	}
	
	/**
	 * Add the "Settings" level menu page.
	 */
	public function add_foption_page()	{
		add_options_page( $this->plugin_name . ' settings', $this->plugin_name, 'manage_options', $this->plugin_code, array( $this, 'foption_configure' ) );
	}

	/**
	 * Pagina amministratore per settare il valore da attribuire alla CTA
	 */
	public function foption_configure() {
		// verifica che l'utente sia un admin con possibilità di modifica opzioni
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		
		// TODO si potrebbe aggiungere la possibilità di modificare la posizione (dopo n paragrafi)
		// TODO si potrebbe aggiungere la possibilità di modificare il tag (o i tag) per i quali il CTA si attiva
		// TODO da verificare e sanitizzare il codice html inserito qui dall'utente!
		// queste sono le opzioni attualmente settabili:
		$options = array(
			'active' => true,
			'log_level' => 0,
			'htmlcode' => '<p><button onclick="window.location.href=\'https://www.ilpost.it/\';">Salta qui!</button></p>'
		);
		?>
		<div class="wrap">
			<h2><?php printf( __( '%1$s - Configurazione Opzioni', $this->plugin_name ), $this->plugin_name ); ?></h2>
			<form method="post" action="options.php">
				<?php
				settings_fields( $this->plugin_code . '_options' );
				$options_read = stripslashes_deep( get_option( $this->plugin_code ) );
				if ( is_array( $options_read ) ) {
					$options = array_merge( $options, $options_read );
				} 
				?>
				<table class="form-table">
					<tr style="vertical-align: top"><th scope="row"><?php _e( 'CTA Attivo (dopo il quarto paragrafo degli articoli taggati \'governo\')', $this->plugin_code ); ?></th>
						<td><input type="checkbox" name="<?php echo $this->plugin_code; ?>[active]" value="1" <?php checked( '1', $options['active'] ); ?> /></td>
					</tr>
					<tr style="vertical-align: top"><th scope="row"><?php _e( 'Livello log (non implementato)', $this->plugin_code ); ?></th>
						<td><select name="<?php echo $this->plugin_code; ?>[log_level]">
								<option value="0" <?php selected( $options['log_level'], 0 ); ?>>DISABLED</option>
								<option value="1" <?php selected( $options['log_level'], 1 ); ?>>ERROR</option>
								<option value="2" <?php selected( $options['log_level'], 2 ); ?>>INFO</option>
								<option value="3" <?php selected( $options['log_level'], 3 ); ?>>DEBUG</option>
							</select>
						</td>
					</tr>										
					<tr style="background-color:#558c9a; vertical-align: top"><th scope="row" style="color:#FFF; padding-left:10px"><?php _e( 'Codice html della CTA', $this->plugin_code ); ?></th>
						<td>
							<textarea rows="3" cols="80" style="height: 100px; width: 60%;" name="<?php echo $this->plugin_code; ?>[htmlcode]"><?php echo $options['htmlcode']; ?></textarea>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<p class="submit">
								<input type="submit" class="button-primary" value="<?php _e( 'Salva modifiche', $this->plugin_code ) ?>" />
							</p>
						</td>
					</tr>
				</table>
			</form>
		</div>
		<?php
	}
	
}
