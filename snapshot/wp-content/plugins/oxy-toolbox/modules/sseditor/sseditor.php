<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class Oxy_Toolbox_SSEditor {
	
	static $prefix;
	static $mod = 'sseditor_';
	static $title = 'Stylesheets Editor';
	static $description = 'Global Stylesheets editor in the dashboard.';
	static $link = 'https://oxyplugins.com/doc/sseditor/';
	
	static function init( $prefix ) {
		
		self::$prefix = $prefix;

		$oxy_toolbox_mod = new Oxy_Toolbox_Mod( $prefix, self::$mod, self::$title, self::$description, self::$link );
		
		if ( 0 === intval( get_option( self::$prefix . self::$mod, 0 ) ) || OxyToolboxLicense::is_activated_license() !== true) {
			return;
		}
		
		add_action( 'admin_menu', array( __CLASS__, 'admin_menu' ), 11 );
		
		add_action('wp_ajax_oxy_toolbox_sseditor_update', array( __CLASS__, 'sseditor_update'));
		
	}

	public static function sseditor_update() {
		
		if ( !isset( $_REQUEST['action'] ) || $_REQUEST['action'] != "oxy_toolbox_sseditor_update") {
			return;
		}
		
		$nonce  	= $_REQUEST['nonce'];
		$ss = $_REQUEST['ss'];

		// check nonce
		if ( ! isset( $nonce, $ss) || ! wp_verify_nonce( $nonce, 'edit-sseditor_' . intval($ss) ) ) {
		    // This nonce is not valid.
		    die( 'Security check' );
		}
		
		$newcontent = $_REQUEST['newcontent'];

		$stylesheets = get_option('ct_style_sheets', array());

		$updated = false;
		
		foreach ($stylesheets as $key => $sheet) {
			if(intval($sheet['id']) === intval($ss)) {
				$stylesheets[$key]['css'] = str_replace(' ', '+', $newcontent);
				$updated = true;
			}
		}

		$data = array();

		if($updated === true) {
			update_option('ct_style_sheets', $stylesheets);
			oxygen_vsb_cache_universal_css();
			$data["message"]  = "Stylesheet edited successfully.";
		} else {
			$data['message'] = "Stylesheet could not be edited";
		}

		$data = array("success" => $updated, 'data' => $data);
		
		header('Content-Type: application/json');
	  	echo json_encode( $data );
		die();
	}

	public static function admin_menu() {
		add_submenu_page( 'ct_dashboard_page', self::$title, self::$title, 'manage_options', self::$prefix . self::$mod . 'menu', array( __CLASS__, 'menu_item' ) );
	}

	public static function menu_item() {
		$settings = array(
			'codeEditor' => wp_enqueue_code_editor( array( 'type' => 'css', 'codemirror' => array('viewportMargin' => PHP_FLOAT_MAX) ) ),
		);

		wp_enqueue_script( 'wp-theme-plugin-editor' );
		wp_add_inline_script( 'wp-theme-plugin-editor', sprintf( 'jQuery( function( $ ) { wp.themePluginEditor.initFileBrowser = function() {}; wp.themePluginEditor.init( $( "#template" ), %s ); } )', wp_json_encode( $settings ) ) );
		// wp_add_inline_script( 'wp-theme-plugin-editor', sprintf( 'wp.themePluginEditor.themeOrPlugin = "plugin";' ) );
		?>
		<div class="wrap">
			<h1>Edit Oxygen Stylesheets</h1>
			<?php if(is_oxygen_edit_post_locked()) {
				?>
				<div class="notice notice-error">
			        <p>
			        	<?php echo __("Oxygen is open in another tab or by another user.", "oxygen"); ?><br/>
						<?php echo __("Please close the other instance of the builder and refresh this page to edit.", "oxygen"); ?><br/>
						<?php echo __("Although you can still proceed with editing, if you know what you are doing.", "oxygen"); ?><br/>
			        </p>
			    </div>

				<?php
			}?>
			<style type="text/css">
				.ssendes {
					background: #ededed;
					padding: 6px 10px;
				}

				#templateside a {
					cursor: pointer;
				}

				#templateside li.active {
  					border: 1px dotted
				}

				div.editor-notices {
				  margin-top: 20px;
				}

				div.editor-notices > div.notice {
				  width: 800px !important;
				}

				div.colorpallete {
				  display: flex;
				  flex-wrap: wrap;
				}

				div.colorpallete > div {
				  width: 80px;
				  text-align: center;
				  margin-bottom: 20px;
				}

				div.colorpallete > div > span:first-child {
				  display: block;
				  margin: auto;
				  width: 30px;
				  height: 30px;
				  border-radius: 50%;
				  box-shadow: 1px 1px 3px 1px rgba(0, 0, 0, 0.2);
				  cursor: pointer;
				  margin-bottom: 10px;
				}

				div.colorpallete > div > span:last-child {
				  font-size: 11px;
				}

				.CodeMirror {
				  border: 1px solid #eee;
				  height: auto !important;
				}

			</style>
			<h3>Global Colors</h3>
				
				<div class="colorpallete">
					<?php
					$colors = get_option('oxygen_vsb_global_colors', array());

					if(is_array($colors['colors'])) {
						foreach ($colors['colors'] as $key => $color) {
							echo '<div class="sscolor"><span data-id="'.$color['id'].'" style="background-color:'.$color['value'].'"></span><span>'.$color['name'].'</span></div>';
						}
					}
					?>
				</div>
			<div id="templateside">
				<h2 id="plugin-files-label"><?php _e( 'Oxygen Style Sheets' ); ?></h2>
				<?php 
					$stylesheets = get_option('ct_style_sheets', array());

					$current = isset($_GET['ss'])?intval($_GET['ss']):false;

					foreach ($stylesheets as $sheet) {
						if($sheet['id'] === $current) {
							$current = $sheet;
						}
					}

					$thisUrl = menu_page_url(self::$prefix . self::$mod . 'menu', false);
				?>
				
				<ul role="tree">
					<h4 class="ssendes">Enabled</h4>
					
					<li role="treeitem">
						<ul role="group">
							<?php
							foreach($stylesheets as $sheet) {
								if($sheet['parent'] !== 0 || isset($sheet['folder'])) {
									continue;
								}
								if($current === false) {
									$current = $sheet;
								}
								?>
								<li role="none" class="<?php echo $current['id'] === $sheet['id'] ? 'active' :'';?>">
									<a role="treeitem" href="<?php echo add_query_arg('ss', $sheet['id'], $thisUrl);?>">
										<?php echo $sheet['name']; ?>
									</a>
								</li>
								<?php
							}

							foreach($stylesheets as $folder) {
								if(!isset($folder['folder']) || $folder['status'] !== 1) {
									continue;
								}
								?>
								<li role="treeitem" class="">
									<span class="folder-label"><?php echo $folder['name'];?> <span class="screen-reader-text">folder</span><span class="icon"></span></span>
									<ul role="group" class="tree-folder">
										<?php

										foreach($stylesheets as $child) {
											if(!isset($child['parent']) || $child['parent'] !== $folder['id']) {
												continue;
											}
											if($current === false) {
												$current = $child;
											}
											?>
											<li role="none" class="<?php echo $current['id'] === $child['id'] ? 'active' :'';?>">
												<a role="treeitem" href="<?php echo add_query_arg('ss', $child['id'], $thisUrl);?>">
													<?php echo $child['name'];?>
												</a>
											</li>
										<?php
										}		

										?>
										
									</ul>
								</li>

								<?php
							}
							?>
							
						</ul>
					</li>
					<h4 class="ssendes">Disabled</h4>
					<li role="treeitem" tabindex="-1" aria-expanded="true" aria-level="1" aria-posinset="1" aria-setsize="1">
						<ul role="group">
							<?php
							foreach($stylesheets as $sheet) {
								if($sheet['parent'] !== -1 || isset($sheet['folder'])) {
									continue;
								}
								if($current === false) {
									$current = $sheet;
								}
								?>
								<li role="none" class="<?php echo $current['id'] === $sheet['id'] ? 'active' :'';?>">
									<a role="treeitem" href="<?php echo add_query_arg('ss', $sheet['id'], $thisUrl);?>">
										<?php echo $sheet['name']; ?>
									</a>
								</li>
								<?php
							}

							foreach($stylesheets as $folder) {
								if(!isset($folder['folder']) || $folder['status'] !== 0) {
									continue;
								}
								?>
								<li role="treeitem" class="">
									<span class="folder-label"><?php echo $folder['name'];?> <span class="screen-reader-text">folder</span><span class="icon"></span></span>
									<ul role="group" class="tree-folder">
										<?php

										foreach($stylesheets as $child) {
											if(!isset($child['parent']) || $child['parent'] !== $folder['id']) {
												continue;
											}
											if($current === false) {
												$current = $child;
											}
											?>
											<li role="none" class="<?php echo $current['id'] === $child['id'] ? 'active' :'';?>">
												<a role="treeitem" href="<?php echo add_query_arg('ss', $child['id'], $thisUrl);?>">
													<?php echo $child['name'];?>
												</a>
											</li>
										<?php
										}		

										?>
										
									</ul>
								</li>

								<?php
							}
							?>
						</ul>
					</li>
				</ul>
				
				
			</div>
			<form id="sseditorform" action="<?php echo admin_url('admin-ajax.php');?>" method="post">
				<?php wp_nonce_field( 'edit-sseditor_' . $current['id'], 'nonce' ); ?>
				<div>
					<label for="newcontent" id="theme-plugin-editor-label"><?php _e( 'Editing: ' ); echo $current['name'] ?></label>
					<?php $content = base64_decode(str_replace(' ', '+', $current['css']));?>
					<textarea cols="70" rows="25" name="newcontent" id="newcontent"><?php echo $content; ?></textarea>
					<input type="hidden" name="action" value="oxy_toolbox_sseditor_update" />
					<input type="hidden" name="ss" value="<?php echo $current['id'];?>" />
				</div>
				<div class="editor-notices">
				</div>

				<p class="submit">
					<?php submit_button( __( 'Update Stylesheet' ), 'primary', 'submit', false ); ?>
					<span class="spinner"></span>
				</p>
				
			</form>
			<template id="notice-template">
				<div class="notice inline is-dismissible " style="display: none">
					<p></p>
					<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss</span></button>
				
				</div>
			</template>
			<script type="text/javascript">
				
				(($) => {
					$(document).ready(() => {
						let form = $('#sseditorform');
						let beforeunload = null;
						let editor;


						setTimeout(function() {
							editor = document.querySelector('.CodeMirror').CodeMirror;
							
							editor.on('change', () => {
								
								if(editor.getValue() != document.querySelector('textarea#newcontent').value) {
									if(beforeunload) {
										$(window).on('beforeunload', beforeunload)
									}
								}
							});

							$('.CodeMirror-code').animate({'min-height': $('#templateside').height()-40+'px'});
						}, 500);

						form.on('submit', (e) => {
							e.preventDefault();

							document.querySelector('textarea#newcontent').value = editor.getValue();

							

							let dataArr = form.serializeArray();
							
							let dex = dataArr.findIndex(item => (item.name == 'newcontent'));

							dataArr[dex].value = btoa(dataArr[dex].value);

							let data = dataArr.map(item => (item.name+'='+item.value)).join('&');

							$.post(form.attr('action'), data, (response) => {
								if(response['success'] !== undefined) {
									
									let notice = $($('template#notice-template').html());
									
									notice.addClass(response['success'] === true ? 'notice-success': 'notice-error');
									notice.children('p').html(response['data'] && response['data']['message']?response['data']['message']:'');
									$('div.editor-notices').html('');
									$('div.editor-notices').append(notice);
									notice.show(200);
									notice.children('button').on('click', () => {
										notice.hide(200, () => {
											notice.remove();
										});
									});

									if(response['success'] === true) {

										if($._data( window, "events" )['beforeunload'] && $._data( window, "events" )['beforeunload'][0] !== undefined) {
											beforeunload = $._data( window, "events" )['beforeunload'][0]['handler'];	
										}
										
										$(window).off('beforeunload', beforeunload);
									}

								}
							});
						})

						
						// global colors
						$('.sscolor > span:first-child').on('click', function() {
							let color = parseInt($(this).attr('data-id'));
							var doc = editor.getDoc();
							doc.replaceSelection("color(" + color +")");
						})
						
					})
					
				})(jQuery)
			</script>
		</div>
		<?php
	}
}

Oxy_Toolbox_SSEditor::init( self::PREFIX );