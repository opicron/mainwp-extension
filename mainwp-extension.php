<?php
/*
Plugin Name: MainWP Exension
Plugin URI: https://mainwp.com
Description: MainWP Hello World! Extension is a MainWP example extension
Version: 1.0
Author: MainWP
Author URI: https://mainwp.com
Icon URI: 
*/
class MainWPExtension
{
  
    public function __construct()
	{		
		add_filter('mainwp_getsubpages_sites', array(&$this, 'managesites_subpage' ), 10, 1 );
	}

	/**
	 * Adds metabox (widget) on the MainWP Dashboard overview page via the 'mainwp_getmetaboxes' filter.
	 *
	 * @param array $metaboxes Array containing metaboxes data.
	 *
	 * @return array $metaboxes Updated array that contains metaboxes data.
	 */
	public function get_metaboxes( $metaboxes ) {
		if ( ! is_array( $metaboxes ) ) {
			$metaboxes = array();
		}
		if ( isset( $_GET['page'] ) && 'managesites' == $_GET['page'] ) {
			$metaboxes[] = array(
				'plugin'        => $this->childFile,
				'key'           => $this->childKey,
				'metabox_title' => MainWP_WooCommerce_Shortcuts::get_name(),
				'callback'      => array( 'MainWPExtension', 'render_woocommerce_shortcuts_widget' ),
			);
		}

		return $metaboxes;
	}
	
	/**
	 * Render the extension metabox (widget) HTML content.
	 */
	public static function render_woocommerce_shortcuts_widget() {
		$website_id = isset( $_GET['dashboard'] ) ? $_GET['dashboard'] : 0;
		if ( empty( $website_id ) ) {
			return;
		}
		?>
		<div class="ui grid">
			<div class="twelve wide column">
				<h3 class="ui header handle-drag">
					Test
					<div class="sub header">Woocommerce</div>
				</h3>
			</div>
			<div class="four wide column right aligned"></div>
		</div>
		<?php
	}
	
	function managesites_subpage( $subPage ) {		

		$subPage[] = array(
			'title'                 => 'Example Extension',
			'slug'                     => 'ExampleExtension',
			'sitetab'             => true,
			'menu_hidden'     => true,
			'callback'             => array( 'MainWPExtension', 'renderPage' ),
		);

		return $subPage;
	}

	/*
	public static function render_header( $shownPage = '' ) {
		MainWP_Deprecated_Hooks::maybe_handle_deprecated_hook();
		MainWP_Manage_Sites_View::render_header( $shownPage, self::$subPages );
	}
	*/
	
    /*
    * Create your extension page
    */

    public static function renderPage() {
        global $mainWPExampleExtensionActivator;

	//add_action( 'mainwp-pageheader-sites', array( self::get_class_name(), 'render_header' ) ); // @deprecated Use 'mainwp_pageheader_sites' instead.
	//add_action( 'mainwp_pageheader_sites', array( self::get_class_name(), 'render_header' ) );

	//render_header();
	//render_footer()
        // Fetch all child-sites 
        $websites = apply_filters('mainwp-getsites', $mainWPExampleExtensionActivator->getChildFile(), $mainWPExampleExtensionActivator->getChildKey(), null);              
        
        // Location to open on child site
        $location = "admin.php?page=mainwp_child_tab";                 
        
        if (is_array($websites)) {
            ?>      
            <div class="postbox">
                <div class="inside">
                    <p><?php _e('MainWP Hello World! Extension is an example extension. This extension provides two examples for calling MainWP Actions and Hooks. Purpose of this extension is to give you a start point in developing your first custom extension for the MainWP Plugin.'); ?></p>
                    <p><?php echo __('For more details, please visit ') .'<a href="http://codex.mainwp.com" target="_blank">'. __('MainWP Codex') . '</a>' . ' website.'; ?></p>
                </div>
            </div>
            <div class="postbox">
                <h3 class="mainwp_box_title"><?php _e('Get Child Sites'); ?></h3>
                <div class="inside">
                    <em><?php _e('List all child sites and link to open the child site'); ?></em>
                    <?php
                    //Display number of your child sites
                    ?>
                    <p><?php echo __('Number of Child Sites: ') . count($websites); ?></p>
                    <div id="mainwp_example_links">
                        <?php
                        //Display a list of your child site with a secure link to child site WP Admin (login not required)
                        foreach($websites as $site) { 
                            // Create link to open $location on child site
                            $open_url = "admin.php?page=SiteOpen&newWindow=yes&websiteid=". $site['id'] . "&location=" . base64_encode($location) ;
                            echo $site['name'] .": ";
                            ?>
                            <a href="<?php echo $open_url; ?>" class="queue" target="_blank"><?php _e("Open location", "mainwp");?></a>.                        
                            <br/>              
                        <?php            
                        }
                        ?>
                    </div>  
                </div>
            </div>
            
            <div class="postbox">
                <h3 class="mainwp_box_title"><?php _e('Get Posts From Child Sites'); ?></h3>
                <div class="inside">
                    <em><?php _e('List all posts from your child site'); ?></em>           
                        <?php $site = $websites[0]; ?>
                        <p><?php _e('Get posts from child site:'); ?> <?php echo $site['url'] ?> <a href="admin.php?page=Extensions-Mainwp-Example-Extension&siteid=<?php echo $site['id']; ?>" class="button button-primary"><?php _e('Click Here!'); ?></a></p>
                        <?php            
                        if (isset($_GET['siteid']) && !empty($_GET['siteid'])) {
                            $websiteId = $_GET['siteid'];         
                            //fetch information of one child-site                 
                            $website = apply_filters('mainwp-getsites', $mainWPExampleExtensionActivator->getChildFile(), $mainWPExampleExtensionActivator->getChildKey(), $websiteId);            
                            if ($website && is_array($website)) {
                                $website = current($website);
                            }  

                            if (!$website) {
                                echo "<p><strong>ERROR</strong>: ". __('Child Site Not Found') ."</p>";
                            } else {

                                // Example to call function get_all_posts on child-plugin to get posts on child site
                                $post_data = array(               
                                    'status' => 'publish',
                                    'maxRecords' => 10
                                );

                                // hook to call the function get_all_posts
                                $information = apply_filters('mainwp_fetchurlauthed', $mainWPExampleExtensionActivator->getChildFile(), $mainWPExampleExtensionActivator->getChildKey(), $websiteId, 'get_all_posts', $post_data);			                            
                                
                                if (is_array($information)) {
                                    if (isset($information['error'])) {
                                        echo "<p><strong>ERROR</strong>: " . $information['error'] . "</p>";
                                    } else {                                                 
                                        echo "<h3>"._('List of posts: ')."</h3>";
                                        echo "<ol>";
                                        foreach($information as $post) {
                                            echo "<li>";
                                            echo $post['title'];
                                            ?>                        
                                            <a href="<?php echo $website['url'] . (substr($website['url'], -1) != '/' ? '/' : '') . '?p=' . $post['id']; ?>"
                                               target="_blank" 
                                               title="View '<?php echo $post['title']; ?>'" 
                                               rel="permalink"><?php _e('View Post'); ?></a>
                                            <?php 
                                            echo "</li>";
                                        }
                                        echo "</ol>";
                                    }
                                }                  
                            }
                        } 
                    
                    } else {
                            echo "Child Sites Not Found";
                    }
                    ?>
                </div>
            </div>
        <?php
    }    
}


/*
* Activator Class is used for extension activation and deactivation
*/

class MainWPExtensionActivator
{
    protected $mainwpMainActivated = false;
    protected $childEnabled = false;
    protected $childKey = false;
    protected $childFile;
	protected $plugin_handle = 'mainwp-example-extension';
	

    public function __construct()
    {
        $this->childFile = __FILE__;
        add_filter('mainwp-getextensions', array(&$this, 'get_this_extension'));
        
        // This filter will return true if the main plugin is activated
        $this->mainwpMainActivated = apply_filters('mainwp-activated-check', false);

        if ($this->mainwpMainActivated !== false)
        {
            $this->activate_this_plugin();
        }
        else
        {
            //Because sometimes our main plugin is activated after the extension plugin is activated we also have a second step, 
            //listening to the 'mainwp-activated' action. This action is triggered by MainWP after initialisation. 
            add_action('mainwp-activated', array(&$this, 'activate_this_plugin'));
        }        
        add_action('admin_notices', array(&$this, 'mainwp_error_notice'));
    }

    function get_this_extension($pArray)
    {
        $pArray[] = array('plugin' => __FILE__,  'api' => $this->plugin_handle, 'mainwp' => false, 'callback' => array(&$this, 'settings'));
        return $pArray;
    }

    function settings()
    {
        //The "mainwp-pageheader-extensions" action is used to render the tabs on the Extensions screen. 
        //It's used together with mainwp-pagefooter-extensions and mainwp-getextensions
        do_action('mainwp-pageheader-extensions', __FILE__);
        if ($this->childEnabled)
        {
            MainWPExtension::renderPage();
        }
        else
        {
            ?><div class="mainwp_info-box-yellow"><?php _e("The Extension has to be enabled to change the settings."); ?></div><?php
        }
        do_action('mainwp-pagefooter-extensions', __FILE__);
    }
    
    //The function "activate_this_plugin" is called when the main is initialized. 
    function activate_this_plugin()
    {
        //Checking if the MainWP plugin is enabled. This filter will return true if the main plugin is activated.
        $this->mainwpMainActivated = apply_filters('mainwp-activated-check', $this->mainwpMainActivated);
        
        // The 'mainwp-extension-enabled-check' hook. If the plugin is not enabled this will return false, 
        // if the plugin is enabled, an array will be returned containing a key. 
        // This key is used for some data requests to our main
        $this->childEnabled = apply_filters('mainwp-extension-enabled-check', __FILE__);       

        $this->childKey = $this->childEnabled['key'];
		
		new MainWPExtension();

    }

    function mainwp_error_notice()
    {
        global $current_screen;
        if ($current_screen->parent_base == 'plugins' && $this->mainwpMainActivated == false)
        {
            echo '<div class="error"><p>MainWP Hello World! Extension ' . __('requires '). '<a href="http://mainwp.com/" target="_blank">MainWP</a>'. __(' Plugin to be activated in order to work. Please install and activate') . '<a href="http://mainwp.com/" target="_blank">MainWP</a> '.__('first.') . '</p></div>';
        }
    }

    public function getChildKey()
    {
        return $this->childKey;
    }

    public function getChildFile()
    {
        return $this->childFile;
    }
}

global $mainWPExampleExtensionActivator;
$mainWPExampleExtensionActivator = new MainWPExtensionActivator();
