<?php
/*
  Plugin Name: UBC FOA Website
  Plugin URI:
  Description: Transforms the UBC Collab Theme into a specific faculty website | Note: This plugin will only work on wp-hybrid-clf theme
  Version: 2.0
  Author: Amir Entezaralmahdi | Arts ISIT
  Licence: GPLv2
  Author URI: http://isit.arts.ubc.ca
 */
Class UBC_FOA_Theme_Options {
    static $prefix;
    static $faculty_main_homepage;
    static $add_script;
    /**
     * init function.
     * 
     * @access public
     * @return void
     */
    function init() {
        self::$prefix = 'wp-hybrid-clf'; // function hybrid_get_prefix() is not available within the plugin
        
        self::$faculty_main_homepage = 'http://www.arts.ubc.ca';
        // include Arts specific css file
        wp_register_style('arts-theme-option-style', plugins_url('foa-site') . '/css/style.css');
        // include Arts specific javascript file
        wp_register_script('arts-theme-option-script', plugins_url('foa-site') . '/js/script.js');
        
        add_action( 'init', array(__CLASS__, 'register_scripts' ), 12 );
        add_action( 'wp_footer', array(__CLASS__, 'print_script' ) );
        
        add_action('ubc_collab_theme_options_ui', array(__CLASS__, 'arts_ui'));
        
        add_action( 'admin_init',array(__CLASS__, 'admin' ) );
        
        add_filter( 'ubc_collab_default_theme_options', array(__CLASS__, 'default_values'), 10,1 );
        add_filter( 'ubc_collab_theme_options_validate', array(__CLASS__, 'validate'), 10, 2 );
      	
        add_action( 'wp_head', array( __CLASS__,'wp_head' ) );
        
        add_action( 'wp_footer', array( __CLASS__,'wp_footer' ) );
        
        /************ Arts specifics *************/    
        //Add News Ticker
        add_action( 'init', array( __CLASS__,'add_news_ticker' ));
        //Add event Carousel
        add_action( 'init', array( __CLASS__,'add_event_carousel' ));
        //Add Social Row
        add_action( 'init', array( __CLASS__,'add_social_row' ));
        //Add Arts Logo
        add_filter('wp_nav_menu_items', array(__CLASS__,'add_arts_logo_to_menu'), 10, 2);
        //Add Apply Now button to Menu if selected
        add_filter('wp_nav_menu_items', array(__CLASS__,'add_apply_now_to_menu'), 10, 2);
        //Add Arts frontpage layout
        add_action( 'init', array(__CLASS__, 'arts_frontpage_layout' ) );
        //remove slider margin
        add_action( 'init', array(__CLASS__, 'remove_slider_margin'));
        //Select Transparent Slider
        add_action( 'init', array(__CLASS__, 'select_transparent_slider'));
    }
    
    /**
     * register_scripts function.
     * 
     * @access public
     * @return void
     */
    function register_scripts() {
    	self::$add_script = true;
		// register the spotlight functions
        if( !is_admin() ):
        	//wp_register_script( 'ubc-collab-arts', plugins_url('foa-site').'/js/foa-site.js', array( 'jquery' ), '0.1', true );
        	//wp_enqueue_style('ubc-collab-arts', plugins_url('foa-site').'/css/foa-site.css');
        endif;
	
	}   
	/**
	 * print_script function.
	 * 
	 * @access public
	 * @static
	 * @return void
	 */
	static function print_script() {
		if ( ! self::$add_script )
			return;
                
		wp_print_scripts( 'ubc-collab-arts' );
	}    
        
    /*
     * This function includes the css and js for this specifc admin option
     *
     * @access public
     * @return void
     */
     function arts_ui(){
        wp_enqueue_style('arts-theme-option-style');
        wp_enqueue_script('arts-theme-option-script', array('jquery'));
     }
     
    /**
     * admin function.
     * 
     * @access public
     * @return void
     */
    function admin(){
        
        //Add Arts Options tab in the theme options
        add_settings_section(
                'foa-options', // Unique identifier for the settings section
                'FOA options', // Section title
                '__return_false', // Section callback (we don't want anything)
                'theme_options' // Menu slug, used to uniquely identify the page; see ubc_collab_theme_options_add_page()
        );
        //Add Colour options
        add_settings_field(
                'arts-colours', // Unique identifier for the field for this section
                'Colour Options', // Setting field label
                array(__CLASS__,'arts_colour_options'), // Function that renders the settings field
                'theme_options', // Menu slug, used to uniquely identify the page; see ubc_collab_theme_options_add_page()
                'foa-options' // Settings section. Same as the first argument in the add_settings_section() above
        );
        //Add Colour options
        add_settings_field(
                'arts-logo', // Unique identifier for the field for this section
                'Logo Options', // Setting field label
                array(__CLASS__,'arts_logo_options'), // Function that renders the settings field
                'theme_options', // Menu slug, used to uniquely identify the page; see ubc_collab_theme_options_add_page()
                'foa-options' // Settings section. Same as the first argument in the add_settings_section() above
        );        
         //Add Why-Unit options
        add_settings_field(
                'arts-why-unit', // Unique identifier for the field for this section
                'Why Unit?', // Setting field label
                array(__CLASS__,'arts_why_unit_options'), // Function that renders the settings field
                'theme_options', // Menu slug, used to uniquely identify the page; see ubc_collab_theme_options_add_page()
                'arts-options' // Settings section. Same as the first argument in the add_settings_section() above
        );       
        
        //Add Why-Unit options
        add_settings_field(
                'arts-apply-now', // Unique identifier for the field for this section
                'Apply Now', // Setting field label
                array(__CLASS__,'arts_apply_now_options'), // Function that renders the settings field
                'theme_options', // Menu slug, used to uniquely identify the page; see ubc_collab_theme_options_add_page()
                'foa-options' // Settings section. Same as the first argument in the add_settings_section() above
        );
        
        //Add Slider options
        add_settings_field(
                'arts-slider', // Unique identifier for the field for this section
                'Slider Options', // Setting field label
                array(__CLASS__,'arts_slider_options'), // Function that renders the settings field
                'theme_options', // Menu slug, used to uniquely identify the page; see ubc_collab_theme_options_add_page()
                'foa-options' // Settings section. Same as the first argument in the add_settings_section() above
        );  
         //Add News Ticker options
        add_settings_field(
                'arts-news-ticker', // Unique identifier for the field for this section
                'News Ticker', // Setting field label
                array(__CLASS__,'arts_news_ticker_theme_options'), // Function that renders the settings field
                'theme_options', // Menu slug, used to uniquely identify the page; see ubc_collab_theme_options_add_page()
                'foa-options' // Settings section. Same as the first argument in the add_settings_section() above
        );
        
         //Add Events options
        add_settings_field(
                'arts-event-carousel', // Unique identifier for the field for this section
                'Events Carousel', // Setting field label
                array(__CLASS__,'arts_event_carousel_options'), // Function that renders the settings field
                'theme_options', // Menu slug, used to uniquely identify the page; see ubc_collab_theme_options_add_page()
                'foa-options' // Settings section. Same as the first argument in the add_settings_section() above
        );     
        
         //Add Social Row options
        add_settings_field(
                'arts-social-row', // Unique identifier for the field for this section
                'Social Row', // Setting field label
                array(__CLASS__,'arts_social_row_options'), // Function that renders the settings field
                'theme_options', // Menu slug, used to uniquely identify the page; see ubc_collab_theme_options_add_page()
                'foa-options' // Settings section. Same as the first argument in the add_settings_section() above
        );                 
    }     
    
    /**
     * arts_colour_options.
     * Display colour options for Arts specific template
     * @access public
     * @return void
     */   
    function arts_colour_options(){ ?>
        
    
		<div class="explanation"><a href="#" class="explanation-help">Info</a>
			
			<div> These colours are specific to each unit and represent the colour of Arts logo, and pieces of the items throughout the site.</div>
		</div>
		<div id="arts-unit-colour-box">
			<label><b>Unit/Website Main Colour:</b></label>
			<div class="arts-colour-item"><span>(A) Main colour: </span><?php  UBC_Collab_Theme_Options::text( 'arts-main-colour' ); ?></div><br/>
                        <div class="arts-colour-item"><span>(B) Gradient colour: </span><?php  UBC_Collab_Theme_Options::text( 'arts-gradient-colour' ); ?></div><br/>
                        <div class="arts-colour-item"><span>(C) Hover colour: </span><?php  UBC_Collab_Theme_Options::text( 'arts-hover-colour' ); ?></div><br/>
                        <div class="arts-colour-item"><span>(D) Reverse colour: </span></div>
                        <ul>                        
                        <?php	
                            foreach ( UBC_FOA_Theme_Options::arts_reverse_colour() as $option ) {
                                ?>
                                <li class="layout">
                                <?php UBC_Collab_Theme_Options::radio( 'arts-reverse-colour', $option['value'], $option['label']); ?>
                                </li>
                      <?php } ?>
                        </ul>
		</div>   <?php     
    }

    /**
     * arts_colour_options.
     * Display colour options for Arts specific template
     * @access public
     * @return void
     */   
    function arts_logo_options(){ ?>
        
    
		<div class="explanation"><a href="#" class="explanation-help">Info</a>
			
			<div> This section allows you to enable/disable and select the logo image to be added to the site menu.</div>
		</div>
		<div id="arts-logo-box">
			<label><b>Add Arts Logo to menu:</b></label>
                            <div><?php UBC_Collab_Theme_Options::checkbox( 'arts-enable-logo', 1, 'Enable Arts Logo' ); ?></div>
                            <div class="arts-logo-inputs"><?php UBC_Collab_Theme_Options::text('arts-logo-url', 'URL'); ?></div>
		</div>   <?php     
    }    
    
    /**
     * arts_apply_now_options.
     * Display Apply Now options for Arts specific template
     * @access public
     * @return void
     */      
    function arts_apply_now_options(){ ?>
            <div class="explanation"><a href="#" class="explanation-help">Info</a>

                    <div> An optional button to be appended to the main navigation menu that will link to the specified application page</div>
            </div>
            <div id="arts-apply-now-box">
                <label><b>Apply Now Options:</b></label>
                <div><?php UBC_Collab_Theme_Options::checkbox( 'arts-enable-apply-now', 1, 'Enable Apply-now botton' ); ?></div>
                <div class="half arts-apply-inputs"><?php UBC_Collab_Theme_Options::text('arts-apply-now-text', 'Botton text'); ?></div>
                <div class="half arts-apply-inputs"><?php UBC_Collab_Theme_Options::text('arts-apply-now-url', 'URL'); ?></div>
            </div>
        
    <?php
    }
    
    
    /**
     * arts_apply_now_options.
     * Display Apply Now options for Arts specific template
     * @access public
     * @return void
     */      
    function arts_slider_options(){ ?>
            <div class="explanation"><a href="#" class="explanation-help">Info</a>

                    <div> This option allows admin to insert the unit logo to the slider, as well as deciding the caption location on the slider</div>
            </div>
            <div id="arts-slider-logo-box">
                <label><b>Slider Logo Position:</b></label>
                <ul>
                        <?php	
                            foreach ( UBC_FOA_Theme_Options::arts_slider_logo_position() as $option ) {
                                ?>
                                <li class="layout">
                                <?php UBC_Collab_Theme_Options::radio( 'arts-slider-logo-position', $option['value'], $option['label']); ?>
                                </li>
                      <?php } ?>
                </ul>
                <div class="half arts-logo-url-inputs"><?php UBC_Collab_Theme_Options::text('arts-slider-logo-url', 'URL'); ?></div>
            </div>
        
    <?php
    }    
    
    /**
     * arts_apply_now_options.
     * Display Apply Now options for Arts specific template
     * @access public
     * @return void
     */      
    function arts_news_ticker_theme_options(){ ?>
            <div class="explanation"><a href="#" class="explanation-help">Info</a>

                    <div> This option allows admin to enable or disable a ticker feature on the website that displays the post title as a sliding feature.</div>
            </div>
            <div id="arts-slider-logo-box">
                <label><b>News Ticker Options</b></label>
                <ul>
                        <?php	
                            foreach ( UBC_FOA_Theme_Options::arts_news_ticker_options() as $option ) {
                                ?>
                                <li class="layout">
                                <?php UBC_Collab_Theme_Options::radio( 'arts-enable-news-ticker', $option['value'], $option['label']); ?>
                                </li>
                      <?php } ?>
                </ul>
            </div>
            <div class="half"> 
                <label><b>Category to be displayed:</b></label><br><br>
                <div><?php UBC_Collab_Theme_Options::select_categories('arts-news-ticker-category'); ?>  <a href="<?php echo admin_url('edit-tags.php?taxonomy=category'); ?>">add category</a></div>
            </div>
        
    <?php
    }     
    
    function arts_event_carousel_options(){ ?>
            <div class="explanation"><a href="#" class="explanation-help">Info</a>

                    <div> This option allows admin to feature posts of an event categories to be available on the site homepage</div>
            </div>
            <div id="arts-slider-logo-box">
                <label><b>Event Carousel Options</b></label>
                <div><?php UBC_Collab_Theme_Options::checkbox( 'arts-enable-event-carousel', 1, 'Enable Events Carousel Section' ); ?></div>
            </div>
            <div class="half"> 
                <label><b>Category to be displayed:</b></label><br><br>
                <div><?php UBC_Collab_Theme_Options::select_categories('arts-event-carousel-category'); ?>  <a href="<?php echo admin_url('edit-tags.php?taxonomy=category'); ?>">add category</a></div>
            </div>
        
    <?php
    }  
    
    function arts_social_row_options(){?>
            <div class="explanation"><a href="#" class="explanation-help">Info</a>

                    <div> This allows admin to define 3 social media sources to be fed automatically on the website</div>
            </div>
            <div id="arts-social-box">
                <label><b>Social Row Options</b></label>
                <div><?php UBC_Collab_Theme_Options::checkbox( 'arts-enable-social-row', 1, 'Enable Social Row Section' ); ?></div>
            </div><br>
            <div class="third"> 
                <label><b>Column 1:</b></label><br>
                <select id="ubc-collab-theme-options-arts-social-column1-type" name="ubc-collab-theme-options[arts-social-column1-type]">
                    <?php	
                    foreach ( UBC_FOA_Theme_Options::arts_social1_options() as $option ) {
                             UBC_Collab_Theme_Options::option( 'arts-social-column1-type', $option['value'], $option['label']); ?>    
                  <?php } ?>
                </select><br>
                <div><?php UBC_Collab_Theme_Options::text('arts-social-column1-title', 'Title'); ?></div>
                <div><?php UBC_Collab_Theme_Options::text('arts-social-column1-logo', 'Logo'); ?></div>
                <div id='sr1-max-number-of-items'>
                    <label>Max number of items: </label>
                    <select name="ubc-collab-theme-options[arts-social-column1-num-items]">
                        <?php foreach ( UBC_FOA_Theme_Options::number_of_social_items() as $option ) {
                                UBC_Collab_Theme_Options::option( 'arts-social-column1-num-items', $option['value'], $option['label'] );
                        } ?>     
                    </select>
                </div>
                <div id="sr1-posts" class="sr1-element"><label>Choose a category</label><br><?php UBC_Collab_Theme_Options::select_categories('arts-social-column1-post-category'); ?></div>
                <div id="sr1-twitter" class="sr1-element"><?php UBC_Collab_Theme_Options::text('arts-social-column1-twitter-widget-id', 'Twitter Widget ID'); ?><br><?php UBC_Collab_Theme_Options::text('arts-social-column1-twitter-user', 'Twitter User'); ?><br><?php UBC_Collab_Theme_Options::text('arts-social-column1-twitter-url', 'Twitter Page URL'); ?></div>
                <div id="sr1-flickr" class="sr1-element"><?php UBC_Collab_Theme_Options::text('arts-social-column1-flickr', 'Flickr User ID'); ?><br><span><a href="http://idgettr.com/" target="_blank">Find Flickr Id</a></span></div>
                <div id="sr1-rss" class="sr1-element"><?php UBC_Collab_Theme_Options::text('arts-social-column1-rss', 'RSS URL'); ?></div>
                <div id="sr1-text" class="sr1-element"><?php UBC_Collab_Theme_Options::textarea('arts-social-column1-content', 'Content'); ?></div>
            </div>
            <div class="third"> 
                <label><b>Column 2:</b></label><br>
                <select id="ubc-collab-theme-options-arts-social-column2-type" name="ubc-collab-theme-options[arts-social-column2-type]">
                    <?php	
                    foreach ( UBC_FOA_Theme_Options::arts_social2_options() as $option ) {
                             UBC_Collab_Theme_Options::option( 'arts-social-column2-type', $option['value'], $option['label']); ?>    
                  <?php } ?>
                </select><br>
                  <div><?php UBC_Collab_Theme_Options::text('arts-social-column2-title', 'Title'); ?></div>
                <div><?php UBC_Collab_Theme_Options::text('arts-social-column2-logo', 'Logo'); ?></div>
                <div id='sr2-max-number-of-items'>
                    <label>Max number of items: </label>
                    <select name="ubc-collab-theme-options[arts-social-column2-num-items]">
                        <?php foreach ( UBC_FOA_Theme_Options::number_of_social_items() as $option ) {
                                UBC_Collab_Theme_Options::option( 'arts-social-column2-num-items', $option['value'], $option['label'] );
                        } ?>     
                    </select>
                </div>
                <div id="sr2-posts" class="sr2-element"><label>Choose a category</label><br><?php UBC_Collab_Theme_Options::select_categories('arts-social-column2-post-category'); ?></div>
                <div id="sr2-twitter" class="sr2-element"><?php UBC_Collab_Theme_Options::text('arts-social-column2-twitter-widget-id', 'Twitter Widget ID'); ?><br><?php UBC_Collab_Theme_Options::text('arts-social-column2-twitter-user', 'Twitter User'); ?><br><?php UBC_Collab_Theme_Options::text('arts-social-column2-twitter-url', 'Twitter Page URL'); ?></div>
                <div id="sr2-flickr" class="sr2-element"><?php UBC_Collab_Theme_Options::text('arts-social-column2-flickr', 'Flickr User ID'); ?><br><span><a href="http://idgettr.com/" target="_blank">Find Flickr Id</a></span></div>
                <div id="sr2-rss" class="sr2-element"><?php UBC_Collab_Theme_Options::text('arts-social-column2-rss', 'RSS URL'); ?></div>
                <div id="sr2-text" class="sr2-element"><?php UBC_Collab_Theme_Options::textarea('arts-social-column2-content', 'Content'); ?></div>
            </div>
            <div class="third"> 
                <label><b>Column 3:</b></label><br>
                <select id="ubc-collab-theme-options-arts-social-column3-type" name="ubc-collab-theme-options[arts-social-column3-type]">
                    <?php	
                    foreach ( UBC_FOA_Theme_Options::arts_social3_options() as $option ) {
                             UBC_Collab_Theme_Options::option( 'arts-social-column3-type', $option['value'], $option['label']); ?>    
                  <?php } ?>
                </select><br>
                  <div><?php UBC_Collab_Theme_Options::text('arts-social-column3-title', 'Title'); ?></div>
                <div><?php UBC_Collab_Theme_Options::text('arts-social-column3-logo', 'Logo'); ?></div>
                <div id='sr3-max-number-of-items'>
                    <label>Max number of items: </label>
                    <select name="ubc-collab-theme-options[arts-social-column3-num-items]">
                        <?php foreach ( UBC_FOA_Theme_Options::number_of_social_items() as $option ) {
                                UBC_Collab_Theme_Options::option( 'arts-social-column3-num-items', $option['value'], $option['label'] );
                        } ?>     
                    </select>
                </div>
                <div id="sr3-posts" class="sr3-element"><label>Choose a category</label><br><?php UBC_Collab_Theme_Options::select_categories('arts-social-column3-post-category'); ?></div>
                <div id="sr3-twitter" class="sr3-element"><?php UBC_Collab_Theme_Options::text('arts-social-column3-twitter-widget-id', 'Twitter Widget ID'); ?><br><?php UBC_Collab_Theme_Options::text('arts-social-column3-twitter-user', 'Twitter User'); ?><br><?php UBC_Collab_Theme_Options::text('arts-social-column3-twitter-url', 'Twitter Page URL'); ?></div>
                <div id="sr3-flickr" class="sr3-element"><?php UBC_Collab_Theme_Options::text('arts-social-column3-flickr', 'Flickr User ID'); ?><br><span><a href="http://idgettr.com/" target="_blank">Find Flickr Id</a></span></div>
                <div id="sr3-rss" class="sr3-element"><?php UBC_Collab_Theme_Options::text('arts-social-column3-rss', 'RSS URL'); ?></div>
                <div id="sr3-text" class="sr3-element"><?php UBC_Collab_Theme_Options::textarea('arts-social-column3-content', 'Content'); ?></div>
            </div>
        
    <?php
        
    }
    
    /*********** 
     * Default Options
     * 
     * Returns the options array for arts.
     *
     * @since ubc-clf 1.0
     */
    function default_values( $options ) {
            if (!is_array($options)) { 
                    $options = array();
            }
            $defaults = array(
                'arts-main-colour'		=> '#5E869F',
                'arts-gradient-colour'		=> '#71a1bf',
                'arts-hover-colour'		=> '#002145',
                'arts-reverse-colour'		=> 'white',
                'arts-enable-why-unit'  => true,
                'arts-why-unit-text'    => 'Why Unit/Department?',
                'arts-why-unit-url'     => '#',
                'arts-enable-apply-now' => true,
                'arts-apply-now-text'   => 'Apply Now',
                'arts-apply-now-url'    => '#',
                'arts-enable-logo' => false,
                'arts-logo-url'    => '#',
                'arts-slider-logo-url' => '/',
                'arts-slider-logo-position'    => 'auto',
                'arts-enable-news-ticker' => 'after_content',
                'arts-news-ticker-category' => 0,
                'arts-enable-event-carousel' => false,
                'arts-event-carousel-category' => 0,
                'arts-enable-social-row' => false,
                /* Social Column 1*/
                'arts-social-column1-type' => 'sr1-text',
                'arts-social-column1-title' => '',
                'arts-social-column1-logo' => '/',
                'arts-social-column1-num-items' => 10,
                'arts-social-column1-content' => '',
                'arts-social-column1-post-category'  => '',
                'arts-social-column1-twitter-widget-id'  => '582656531818147841',
                'arts-social-column1-twitter-user' => '@UBC_Arts',
                'arts-social-column1-twitter-url' => 'https://twitter.com/UBC_Arts',
                'arts-social-column1-instagram'  => 'UBC_Arts',
                'arts-social-column1-facebook'  => 'https://www.facebook.com/ubc.foa',
                'arts-social-column1-flickr'  => '47412247@N00',
                'arts-social-column1-rss'  => 'http://wire.arts.ubc.ca/feed/',
                /* Social Column 2*/
                'arts-social-column2-type' => 'sr2-text',
                'arts-social-column2-title' => '',
                'arts-social-column2-logo' => '/',
                'arts-social-column2-num-items' => 10,
                'arts-social-column2-content' => '',
                'arts-social-column2-post-category'  => '',
                'arts-social-column2-twitter-widget-id'  => '582656531818147841',
                'arts-social-column2-twitter-user' => '@UBC_Arts',
                'arts-social-column2-twitter-url' => 'https://twitter.com/UBC_Arts',
                'arts-social-column2-instagram'  => 'UBC_Arts',
                'arts-social-column2-facebook'  => 'https://www.facebook.com/ubc.foa',
                'arts-social-column2-flickr'  => '47412247@N00',
                'arts-social-column2-rss'  => 'http://wire.arts.ubc.ca/feed/',
                /* Social Column 3*/
                'arts-social-column3-type' => 'sr3-text',
                'arts-social-column3-title' => '',
                'arts-social-column3-logo' => '/',
                'arts-social-column3-num-items' => 10,
                'arts-social-column3-content' => '',
                'arts-social-column3-post-category'  => '',
                'arts-social-column3-twitter-widget-id'  => '582656531818147841',
                'arts-social-column3-twitter-user' => '@UBC_Arts',
                'arts-social-column3-twitter-url' => 'https://twitter.com/UBC_Arts',
                'arts-social-column3-instagram'  => 'UBC_Arts',
                'arts-social-column3-facebook'  => 'https://www.facebook.com/ubc.foa',
                'arts-social-column3-flickr'  => '47412247@N00',
                'arts-social-column3-rss'  => 'http://wire.arts.ubc.ca/feed/',
                
                
            );
            $options = array_merge( $options, $defaults );
            return $options;
    }  
	/**
	 * Sanitize and validate form input. Accepts an array, return a sanitized array.
	 *
	 *
	 * @todo set up Reset Options action
	 *
	 * @param array $input Unknown values.
	 * @return array Sanitized theme options ready to be stored in the database.
	 *
	 */
	function validate( $output, $input ) {
		
		// Grab default values as base
		$starter = UBC_FOA_Theme_Options::default_values( array() );
		
	    // Validate Unit Colour Options A, B, and C
            $starter['arts-main-colour'] = UBC_Collab_Theme_Options::validate_text($input['arts-main-colour'], $starter['arts-main-colour'] );
            $starter['arts-gradient-colour'] = UBC_Collab_Theme_Options::validate_text($input['arts-gradient-colour'], $starter['arts-gradient-colour'] );
            $starter['arts-hover-colour'] = UBC_Collab_Theme_Options::validate_text($input['arts-hover-colour'], $starter['arts-hover-colour'] );
            
            // Validate Unit Colour Options D
            if ( isset( $input['arts-reverse-colour'] ) && array_key_exists( $input['arts-reverse-colour'], UBC_FOA_Theme_Options::arts_reverse_colour() ) ) {
	        $starter['arts-reverse-colour'] = $input['arts-reverse-colour'];
	    }
            
            //Validate Why-unit options
            $starter['arts-enable-why-unit'] = (bool)$input['arts-enable-why-unit'];
            $starter['arts-why-unit-text']   = UBC_Collab_Theme_Options::validate_text($input['arts-why-unit-text'], $starter['arts-why-unit-text'] );
            $starter['arts-why-unit-url']     = UBC_Collab_Theme_Options::validate_text($input['arts-why-unit-url'], $starter['arts-why-unit-url'] );
 
            //Validate Why-unit options
            $starter['arts-enable-apply-now'] = (bool)$input['arts-enable-apply-now'];
            $starter['arts-apply-now-text']   = UBC_Collab_Theme_Options::validate_text($input['arts-apply-now-text'], $starter['arts-apply-now-text'] );
            $starter['arts-apply-now-url']     = UBC_Collab_Theme_Options::validate_text($input['arts-apply-now-url'], $starter['arts-apply-now-url'] );
            
            $starter['arts-enable-logo'] = (bool)$input['arts-enable-logo'];
            $starter['arts-logo-url']     = UBC_Collab_Theme_Options::validate_text($input['arts-logo-url'], $starter['arts-logo-url'] );
            
            // Validate Slider Logo Direction
            if ( isset( $input['arts-slider-logo-position'] ) && array_key_exists( $input['arts-slider-logo-position'], UBC_FOA_Theme_Options::arts_slider_logo_position() ) ) {
	        $starter['arts-slider-logo-position'] = $input['arts-slider-logo-position'];
	    }
            $starter['arts-slider-logo-url']     = UBC_Collab_Theme_Options::validate_text($input['arts-slider-logo-url'], $starter['arts-slider-logo-url'] );
            
            // Validate News Ticker Options
            if ( isset( $input['arts-enable-news-ticker'] ) && array_key_exists( $input['arts-enable-news-ticker'], UBC_FOA_Theme_Options::arts_news_ticker_options() ) ) {
	        $starter['arts-enable-news-ticker'] = $input['arts-enable-news-ticker'];
	    }
            
            // what category is selected
            $starter['arts-news-ticker-category'] = ( is_numeric( $input['arts-news-ticker-category'] )  ?  (int)$input['arts-news-ticker-category'] : 'all' );
            
            $starter['arts-enable-event-carousel'] = (bool)$input['arts-enable-event-carousel'];
            
            // what Events category is selected
            $starter['arts-event-carousel-category'] = ( is_numeric( $input['arts-event-carousel-category'] )  ?  (int)$input['arts-event-carousel-category'] : 'all' );
            
            $starter['arts-enable-social-row'] = (bool)$input['arts-enable-social-row'];
            
            /* Social Media Row Validation */
            
            // Column 1
            $starter['arts-social-column1-title']     = UBC_Collab_Theme_Options::validate_text($input['arts-social-column1-title'], $starter['arts-social-column1-title'] );
            $starter['arts-social-column1-logo']     = UBC_Collab_Theme_Options::validate_text($input['arts-social-column1-logo'], $starter['arts-social-column1-logo'] );
            $starter['arts-social-column1-num-items'] = ( is_numeric( $input['arts-social-column1-num-items'] )  ?  (int)$input['arts-social-column1-num-items'] : $starter['arts-social-column1-num-items'] );
            // Validate Social Options
            if ( isset( $input['arts-social-column1-type'] ) && array_key_exists( $input['arts-social-column1-type'], UBC_FOA_Theme_Options::arts_social1_options() ) ) {
	        $starter['arts-social-column1-type'] = $input['arts-social-column1-type'];
	    }
            $starter['arts-social-column1-content']     = UBC_Collab_Theme_Options::validate_text($input['arts-social-column1-content'], $starter['arts-social-column1-content'] );
            $starter['arts-social-column1-post-category'] = ( is_numeric( $input['arts-social-column1-post-category'] )  ?  (int)$input['arts-social-column1-post-category'] : 'all' );
            
            $starter['arts-social-column1-twitter-widget-id']     = UBC_Collab_Theme_Options::validate_text($input['arts-social-column1-twitter-widget-id'], $starter['arts-social-column1-twitter-widget-id'] );
            $starter['arts-social-column1-twitter-user']     = UBC_Collab_Theme_Options::validate_text($input['arts-social-column1-twitter-user'], $starter['arts-social-column1-twitter-user'] );
            $starter['arts-social-column1-twitter-url']     = UBC_Collab_Theme_Options::validate_text($input['arts-social-column1-twitter-url'], $starter['arts-social-column1-twitter-url'] );
            $starter['arts-social-column1-instagram']     = UBC_Collab_Theme_Options::validate_text($input['arts-social-column1-instagram'], $starter['arts-social-column1-instagram'] );
            $starter['arts-social-column1-facebook']     = UBC_Collab_Theme_Options::validate_text($input['arts-social-column1-facebook'], $starter['arts-social-column1-facebook'] );
            $starter['arts-social-column1-flickr']     = UBC_Collab_Theme_Options::validate_text($input['arts-social-column1-flickr'], $starter['arts-social-column1-flickr'] );
            $starter['arts-social-column1-rss']     = UBC_Collab_Theme_Options::validate_text($input['arts-social-column1-rss'], $starter['arts-social-column1-rss'] );
            
            // Column 2
            $starter['arts-social-column2-title']     = UBC_Collab_Theme_Options::validate_text($input['arts-social-column2-title'], $starter['arts-social-column2-title'] );
            $starter['arts-social-column2-logo']     = UBC_Collab_Theme_Options::validate_text($input['arts-social-column2-logo'], $starter['arts-social-column2-logo'] );
            $starter['arts-social-column2-num-items'] = ( is_numeric( $input['arts-social-column2-num-items'] )  ?  (int)$input['arts-social-column2-num-items'] : $starter['arts-social-column2-num-items'] );
            // Validate Social Options
            if ( isset( $input['arts-social-column2-type'] ) && array_key_exists( $input['arts-social-column2-type'], UBC_FOA_Theme_Options::arts_social2_options() ) ) {
	        $starter['arts-social-column2-type'] = $input['arts-social-column2-type'];
	    }
            $starter['arts-social-column2-content']     = UBC_Collab_Theme_Options::validate_text($input['arts-social-column2-content'], $starter['arts-social-column2-content'] );
            $starter['arts-social-column2-post-category'] = ( is_numeric( $input['arts-social-column2-post-category'] )  ?  (int)$input['arts-social-column2-post-category'] : 'all' );
            
            $starter['arts-social-column2-twitter-widget-id']     = UBC_Collab_Theme_Options::validate_text($input['arts-social-column2-twitter-widget-id'], $starter['arts-social-column2-twitter-widget-id'] );
            $starter['arts-social-column2-twitter-user']     = UBC_Collab_Theme_Options::validate_text($input['arts-social-column2-twitter-user'], $starter['arts-social-column2-twitter-user'] );
            $starter['arts-social-column2-twitter-url']     = UBC_Collab_Theme_Options::validate_text($input['arts-social-column2-twitter-url'], $starter['arts-social-column2-twitter-url'] );
            $starter['arts-social-column2-instagram']     = UBC_Collab_Theme_Options::validate_text($input['arts-social-column2-instagram'], $starter['arts-social-column2-instagram'] );
            $starter['arts-social-column2-facebook']     = UBC_Collab_Theme_Options::validate_text($input['arts-social-column2-facebook'], $starter['arts-social-column2-facebook'] );
            $starter['arts-social-column2-flickr']     = UBC_Collab_Theme_Options::validate_text($input['arts-social-column2-flickr'], $starter['arts-social-column2-flickr'] );
            $starter['arts-social-column2-rss']     = UBC_Collab_Theme_Options::validate_text($input['arts-social-column2-rss'], $starter['arts-social-column2-rss'] );
            
            // Column 3
            $starter['arts-social-column3-title']     = UBC_Collab_Theme_Options::validate_text($input['arts-social-column3-title'], $starter['arts-social-column3-title'] );
            $starter['arts-social-column3-logo']     = UBC_Collab_Theme_Options::validate_text($input['arts-social-column3-logo'], $starter['arts-social-column3-logo'] );
            $starter['arts-social-column3-num-items'] = ( is_numeric( $input['arts-social-column3-num-items'] )  ?  (int)$input['arts-social-column3-num-items'] : $starter['arts-social-column3-num-items'] );
            // Validate Social Options
            if ( isset( $input['arts-social-column3-type'] ) && array_key_exists( $input['arts-social-column3-type'], UBC_FOA_Theme_Options::arts_social3_options() ) ) {
	        $starter['arts-social-column3-type'] = $input['arts-social-column3-type'];
	    }
            $starter['arts-social-column3-content']     = UBC_Collab_Theme_Options::validate_text($input['arts-social-column3-content'], $starter['arts-social-column3-content'] );
            $starter['arts-social-column3-post-category'] = ( is_numeric( $input['arts-social-column3-post-category'] )  ?  (int)$input['arts-social-column3-post-category'] : 'all' );
            
            $starter['arts-social-column3-twitter-widget-id']     = UBC_Collab_Theme_Options::validate_text($input['arts-social-column3-twitter-widget-id'], $starter['arts-social-column3-twitter-widget-id'] );
            $starter['arts-social-column3-twitter-user']     = UBC_Collab_Theme_Options::validate_text($input['arts-social-column3-twitter-user'], $starter['arts-social-column3-twitter-user'] );
            $starter['arts-social-column3-twitter-url']     = UBC_Collab_Theme_Options::validate_text($input['arts-social-column3-twitter-url'], $starter['arts-social-column3-twitter-url'] );
            $starter['arts-social-column3-instagram']     = UBC_Collab_Theme_Options::validate_text($input['arts-social-column3-instagram'], $starter['arts-social-column3-instagram'] );
            $starter['arts-social-column3-facebook']     = UBC_Collab_Theme_Options::validate_text($input['arts-social-column3-facebook'], $starter['arts-social-column3-facebook'] );
            $starter['arts-social-column3-flickr']     = UBC_Collab_Theme_Options::validate_text($input['arts-social-column3-flickr'], $starter['arts-social-column3-flickr'] );
            $starter['arts-social-column3-rss']     = UBC_Collab_Theme_Options::validate_text($input['arts-social-column3-rss'], $starter['arts-social-column3-rss'] );
            
            
            $output = array_merge($output, $starter);
            return $output;            
        }
        
         /**
	 * Returns and array of news-ticker options
	 */
	function arts_news_ticker_options() {
		$slider_direction = array(
	        'disabled' => array(
	            'value' => 'disabled',
	            'label' => __( 'Disabled', 'arts-clf' )
	        ),
	        'before_content' => array(
	            'value' => 'before_content',
	            'label' => __( 'Before Content', 'arts-clf' )
	        ),
	        'after_content' => array(
	            'value' => 'after_content',
	            'label' => __( 'After Content', 'arts-clf' )
	        )
	    );
	   return $slider_direction;
	}
    	/**
	 * Returns and array of options for slider logo position
	 */
	function arts_slider_logo_position() {
		$slider_direction = array(
	        '0' => array(
	            'value' => '0',
	            'label' => __( 'Left', 'arts-clf' )
	        ),
	        'auto' => array(
	            'value' => 'auto',
	            'label' => __( 'Right', 'arts-clf' )
	        )
	    );
	   return $slider_direction;
	}
        
    	/**
	 * Returns and array of reverse colours
	 */
	function arts_reverse_colour() {
		$reverse_colour = array(
	        'white' => array(
	            'value' => 'white',
	            'label' => __( 'White', 'arts-clf' )
	        ),
	        'black' => array(
	            'value' => 'black',
	            'label' => __( 'Black', 'arts-clf' )
	        )
	    );
	   return $reverse_colour;
	}
        
     	/**
	 * Returns and array of Social Columns for column 1
	 */
	function arts_social1_options() {
		$social_columns = array(
	        '0' => array(
	            'value' => '0',
	            'label' => __( 'Select', 'arts-clf' )
	        ),'sr1-posts' => array(
	            'value' => 'sr1-posts',
	            'label' => __( 'Posts', 'arts-clf' )
	        ),
	        'sr1-twitter' => array(
	            'value' => 'sr1-twitter',
	            'label' => __( 'Twitter', 'arts-clf' )
	        ),
	        'sr1-flickr' => array(
	            'value' => 'sr1-flickr',
	            'label' => __( 'Flickr', 'arts-clf' )
	        ),
	        'sr1-rss' => array(
	            'value' => 'sr1-rss',
	            'label' => __( 'RSS', 'arts-clf' )
	        ),
	        'sr1-text' => array(
	            'value' => 'sr1-text',
	            'label' => __( 'Text', 'arts-clf' )
	        )
	    );
	   return $social_columns;
	}    
     	/**
	 * Returns and array of Social Columns for column 2
	 */
	function arts_social2_options() {
		$social_columns = array(
	        '0' => array(
	            'value' => '0',
	            'label' => __( 'Select', 'arts-clf' )
	        ),'sr2-posts' => array(
	            'value' => 'sr2-posts',
	            'label' => __( 'Posts', 'arts-clf' )
	        ),
	        'sr2-twitter' => array(
	            'value' => 'sr2-twitter',
	            'label' => __( 'Twitter', 'arts-clf' )
	        ),
	        'sr2-flickr' => array(
	            'value' => 'sr2-flickr',
	            'label' => __( 'Flickr', 'arts-clf' )
	        ),
	        'sr2-rss' => array(
	            'value' => 'sr2-rss',
	            'label' => __( 'RSS', 'arts-clf' )
	        ),
	        'sr2-text' => array(
	            'value' => 'sr2-text',
	            'label' => __( 'Text', 'arts-clf' )
	        )
	    );
	   return $social_columns;
	}  
        
        /**
	 * Returns and array of Social Columns for column 2
	 */
	function arts_social3_options() {
		$social_columns = array(
	        '0' => array(
	            'value' => '0',
	            'label' => __( 'Select', 'arts-clf' )
	        ),'sr3-posts' => array(
	            'value' => 'sr3-posts',
	            'label' => __( 'Posts', 'arts-clf' )
	        ),
	        'sr3-twitter' => array(
	            'value' => 'sr3-twitter',
	            'label' => __( 'Twitter', 'arts-clf' )
	        ),
	        'sr3-flickr' => array(
	            'value' => 'sr3-flickr',
	            'label' => __( 'Flickr', 'arts-clf' )
	        ),
	        'sr3-rss' => array(
	            'value' => 'sr3-rss',
	            'label' => __( 'RSS', 'arts-clf' )
	        ),
	        'sr3-text' => array(
	            'value' => 'sr3-text',
	            'label' => __( 'Text', 'arts-clf' )
	        )
	    );
	   return $social_columns;
	} 
        
     /**
     * number_of_social_items function.
     * @access public
     * @return void
     */
    static function number_of_social_items(){
        
        $number_of_social_items = array();
        
        $max_number_of_social_items = 30; 
        
        for($i=1; $i < $max_number_of_social_items; $i++){
            $number_of_social_items[$i] = array(
                'value' => $i,
                'label' => __( $i, 'ubc-clf' )
            );
        }
        return $number_of_social_items;
    }    
    /**
     * add_arts_logo_to_menu
     * Adds the Arts logo to primary menu
     * @access public
     * @return menu items
     */         
      function add_arts_logo_to_menu ( $items, $args ) {
            if ($args->theme_location == 'primary') {
                if(UBC_Collab_Theme_Options::get('arts-enable-logo')){
                    $items = '<li class="menu-item-foa"><a id="artslogo" href="'.self::$faculty_main_homepage.'" title="Arts" target="_blank"><img src="'.UBC_Collab_Theme_Options::get('arts-logo-url').'"/>Faculty of Art</a></li>'.$items;
                }
            }
            return $items;
       }
        
      /**
     * add_apply_now_to_menu
     * Adds the optional Apply Now button to the  primary menu
     * @access public
     * @return menu items
     */         
        function add_apply_now_to_menu( $items, $args ){
            if ($args->theme_location == 'primary') {
                if(UBC_Collab_Theme_Options::get('arts-enable-apply-now')){
                    $items .= '<a id="applybtn" href="'.UBC_Collab_Theme_Options::get('arts-apply-now-url').'" title="Apply Now">'.UBC_Collab_Theme_Options::get('arts-apply-now-text').'</a>';
                }
            }
            return $items;
        }
        
        /**
         * add_news_ticker will add the html code to the homepage if this feature is enabled
         * 
         */
        function add_news_ticker(){
           
            if(UBC_Collab_Theme_Options::get('arts-enable-news-ticker')!= 'disabled'){
                if(UBC_Collab_Theme_Options::get('arts-enable-news-ticker')== 'after_content'){
                    add_filter( self::$prefix.'_after_content', array(__CLASS__, 'add_news_ticker_content' ));
                } else{
                    add_filter( self::$prefix.'_before_content', array(__CLASS__, 'add_news_ticker_content' ));
                }
            }      
        }
        
        /**
         * Generates content for the news ticker based on the selected category
         * 
         */
        function add_news_ticker_content(){
            $query_attr = array();
            $category = UBC_Collab_Theme_Options::get('arts-news-ticker-category');
        
            if( in_array( $category, array( 0, 'all', '0') ) ):

                if( is_numeric($category) ):
                    $query_attr['cat']      = (int)$category;
                else:
                    $query_attr['category_name'] = $category;
                endif;
            else:

                $query_attr['cat']      = (int)$category;
            endif;

            $query_attr['posts_per_page'] = 5;

            $news_ticker_query = new WP_Query( $query_attr );

            $html = '<div class="row-fluid news-ticker"><div class="span6 offset3" id="news-ticker"><ul>';

            while ( $news_ticker_query->have_posts() ){ 
                $news_ticker_query->the_post(); 

                $html .='<li>'.get_the_title().'</li>';
            }
            $html .= '</ul></div></div>';
            
            if(!is_front_page()){
                $html = '';
            }
            
            echo $html;
        }
        
        /**
         * add_social_row calls the appropriate function to generate social media row html if this feature is enabled
         * 
         */
        function add_social_row(){
            if(UBC_Collab_Theme_Options::get('arts-enable-social-row')){
                add_filter( self::$prefix.'_after_content', array(__CLASS__, 'add_social_row_content' ));
            }
        }
        
        /**
         * Generates the html content and calls the appropriate function based on selection
         */
        function add_social_row_content(){
            $html = '<div class="row-fluid entry-content social-row">
                        <div class="span4 sr-col1">
                             <h3>'.UBC_Collab_Theme_Options::get('arts-social-column1-title').'</h3> 
                             <div class="sr-col1-content">'.UBC_FOA_Theme_Options::get_social_content(UBC_Collab_Theme_Options::get('arts-social-column1-type')).'</div> 
                        </div><!--end span4-->
                        <div class="span4 sr-col2">
                             <h3>'.UBC_Collab_Theme_Options::get('arts-social-column2-title').'</h3> 
                             <div class="sr-col2-content">'.UBC_FOA_Theme_Options::get_social_content(UBC_Collab_Theme_Options::get('arts-social-column2-type')).'</div> 
                        </div><!--end span4-->
                        <div class="span4 sr-col3">
                             <h3>'.UBC_Collab_Theme_Options::get('arts-social-column3-title').'</h3> 
                             <div class="sr-col3-content">'.UBC_FOA_Theme_Options::get_social_content(UBC_Collab_Theme_Options::get('arts-social-column3-type')).'</div> 
                        </div><!--end span4-->
                   </div><!--row-fluid--><br><br>';
            if(!is_front_page()){
                $html = '';
            }
            echo $html;
        }
        
        /**
         * Based on the selected content type, it will call the appropriate funtion that generates the
         * appropriate content
         * 
         * @param type $content_type
         * @return type
         */
        function get_social_content($content_type){
            $content = '';
            
            switch ($content_type) {
                /* Posts */
                case 'sr1-posts':
                    $content .= UBC_FOA_Theme_Options::get_social_posts(UBC_Collab_Theme_Options::get('arts-social-column1-post-category'), UBC_Collab_Theme_Options::get('arts-social-column1-num-items'));
                    break;
                case 'sr2-posts':
                    $content .= UBC_FOA_Theme_Options::get_social_posts(UBC_Collab_Theme_Options::get('arts-social-column2-post-category'), UBC_Collab_Theme_Options::get('arts-social-column2-num-items'));
                    break;
                case 'sr3-posts':    
                    $content .= UBC_FOA_Theme_Options::get_social_posts(UBC_Collab_Theme_Options::get('arts-social-column3-post-category'), UBC_Collab_Theme_Options::get('arts-social-column3-num-items'));
                    break;
                
                /* Twitter */
                case 'sr1-twitter':
                    $content .= UBC_FOA_Theme_Options::get_social_twitter(UBC_Collab_Theme_Options::get('arts-social-column1-twitter-widget-id'),
                                                                          UBC_Collab_Theme_Options::get('arts-social-column1-twitter-user'),
                                                                          UBC_Collab_Theme_Options::get('arts-social-column1-twitter-url'));
                    break;
                case 'sr2-twitter':
                    $content .= UBC_FOA_Theme_Options::get_social_twitter(UBC_Collab_Theme_Options::get('arts-social-column2-twitter-widget-id'),
                                                                          UBC_Collab_Theme_Options::get('arts-social-column2-twitter-user'),
                                                                          UBC_Collab_Theme_Options::get('arts-social-column2-twitter-url'));
                    break;
                case 'sr3-twitter':    
                    $content .= UBC_FOA_Theme_Options::get_social_twitter(UBC_Collab_Theme_Options::get('arts-social-column3-twitter-widget-id'),
                                                                          UBC_Collab_Theme_Options::get('arts-social-column3-twitter-user'),
                                                                          UBC_Collab_Theme_Options::get('arts-social-column3-twitter-url'));
                    break;
                
                /* Facebook */
                case 'sr1-facebook':
                    $content .= UBC_FOA_Theme_Options::get_social_facebook(UBC_Collab_Theme_Options::get('arts-social-column1-facebook'));
                    break;
                case 'sr2-facebook':
                    $content .= UBC_FOA_Theme_Options::get_social_facebook(UBC_Collab_Theme_Options::get('arts-social-column2-facebook'));
                    break;
                case 'sr3-facebook':    
                    $content .= UBC_FOA_Theme_Options::get_social_facebook(UBC_Collab_Theme_Options::get('arts-social-column3-facebook'));
                    break;
                
                /* Flickr */
                case 'sr1-flickr':
                    $content .= UBC_FOA_Theme_Options::get_social_flickr(UBC_Collab_Theme_Options::get('arts-social-column1-flickr'), UBC_Collab_Theme_Options::get('arts-social-column1-num-items'));
                    break;
                case 'sr2-flickr':
                    $content .= UBC_FOA_Theme_Options::get_social_flickr(UBC_Collab_Theme_Options::get('arts-social-column2-flickr'), UBC_Collab_Theme_Options::get('arts-social-column2-num-items'));
                    break;
                case 'sr3-flickr':
                    $content .= UBC_FOA_Theme_Options::get_social_flickr(UBC_Collab_Theme_Options::get('arts-social-column3-flickr'), UBC_Collab_Theme_Options::get('arts-social-column3-num-items'));
                    break;
                
                /* Instagram */
                case 'sr1-instagram':
                    $content .= UBC_FOA_Theme_Options::get_social_instagram(UBC_Collab_Theme_Options::get('arts-social-column1-instagram'), UBC_Collab_Theme_Options::get('arts-social-column1-num-items'));
                    break;
                case 'sr2-instagram':
                    $content .= UBC_FOA_Theme_Options::get_social_instagram(UBC_Collab_Theme_Options::get('arts-social-column2-instagram'), UBC_Collab_Theme_Options::get('arts-social-column2-num-items'));
                    break;
                case 'sr3-instagram':    
                    $content .= UBC_FOA_Theme_Options::get_social_instagram(UBC_Collab_Theme_Options::get('arts-social-column3-instagram'), UBC_Collab_Theme_Options::get('arts-social-column3-num-items'));
                    break;
                
                /* RSS */
                case 'sr1-rss':
                    $content .= UBC_FOA_Theme_Options::get_social_rss(UBC_Collab_Theme_Options::get('arts-social-column1-rss'), UBC_Collab_Theme_Options::get('arts-social-column1-num-items'));
                    break;
                case 'sr2-rss':
                    $content .= UBC_FOA_Theme_Options::get_social_rss(UBC_Collab_Theme_Options::get('arts-social-column2-rss'), UBC_Collab_Theme_Options::get('arts-social-column2-num-items'));
                    break;
                case 'sr3-rss':   
                    $content .= UBC_FOA_Theme_Options::get_social_rss(UBC_Collab_Theme_Options::get('arts-social-column3-rss'), UBC_Collab_Theme_Options::get('arts-social-column3-num-items'));
                    break;
                
                /* Text */
                case 'sr1-text':
                    $content .= UBC_FOA_Theme_Options::get_social_text(UBC_Collab_Theme_Options::get('arts-social-column1-content'));
                    break;
                case 'sr2-text':
                    $content .= UBC_FOA_Theme_Options::get_social_text(UBC_Collab_Theme_Options::get('arts-social-column2-content'));
                    break;
                case 'sr3-text':  
                    $content .= UBC_FOA_Theme_Options::get_social_text(UBC_Collab_Theme_Options::get('arts-social-column3-content'));
                    break;

                default:
                    $content .= $content_type;
                    break;
            }
            return $content;
        }
        
        /**
         * Generates items based WP posts associated with the selected category
         * 
         * @param type $category
         * @param type $number_of_items
         * @return string
         */
        function get_social_posts($category, $number_of_items=1){
            
            $query_attr = array();
        
            if( in_array( $category, array( 0, 'all', '0') ) ):

                if( is_numeric($category) ):
                    $query_attr['cat']      = (int)$category;
                else:
                    $query_attr['category_name'] = $category;
                endif;
            else:

                $query_attr['cat']      = (int)$category;
            endif;

            $query_attr['posts_per_page'] = $number_of_items;

            $posts_query = new WP_Query( $query_attr );

            $html = '';            
            while ( $posts_query->have_posts() ){ 
                $posts_query->the_post(); 
                $html .= '<div class="sr-post-item">';
                    $html .='<span>'.get_the_post_thumbnail().'</span>';
                    $html .= '<h4><a href="'.get_permalink().'">'.get_the_title().'</a></h4>';
                    $html .= '<p>'.get_the_excerpt().'</p>';
                $html .= '</div> <!--sr-post-item-->';
            }
            
            return $html;
        }
        
        /**
         * Generates the embed html based on the Widegt ID, user account and account URL
         * 
         * @param type $twitter_widget_id
         * @param type $twitter_user
         * @param type $twitter_page_url
         * @return string html
         */
        function get_social_twitter($twitter_widget_id, $twitter_user, $twitter_page_url){
            $html = '<a class="twitter-timeline" href="'.$twitter_page_url.'" data-widget-id="'.$twitter_widget_id.'">Tweets by '.$twitter_user.'</a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?\'http\':\'https\';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>';
            return $html;
        }
        
        /**
         * Generates the html code with the given facebook page url
         * 
         * @param type $facebook_page_url
         * @return string
         */
        function get_social_facebook($facebook_page_url){
            $fb_js_sdk = '<div id="fb-root"></div>
                            <script>(function(d, s, id) {
                              var js, fjs = d.getElementsByTagName(s)[0];
                              if (d.getElementById(id)) return;
                              js = d.createElement(s); js.id = id;
                              js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&appId=447142618703362&version=v2.0";
                              fjs.parentNode.insertBefore(js, fjs);
                            }(document, \'script\', \'facebook-jssdk\'));</script>';
            $html = $fb_js_sdk.'<div class="fb-like-box" data-href="'.$facebook_page_url.'" data-colorscheme="light" data-show-faces="false" data-header="false" data-height="530" data-stream="true" data-show-border="false"></div>';
            
            return $html;
        }
        
        /**
         * Generates flickr items based on the provided parameters
         * 
         * @param type $flickr_user_id
         * @param type $number_of_items
         * @return string
         */
        function get_social_flickr($flickr_user_id, $number_of_items=1){
            // Get a SimplePie feed object from the specified feed source.
            $rss_url = 'https://api.flickr.com/services/feeds/photos_public.gne/?id='.$flickr_user_id;
            $rss = fetch_feed( $rss_url );

            $maxitems = 0;

            if ( ! is_wp_error( $rss ) ) : // Checks that the object is created correctly

                // Figure out how many total items there are, but limit it to 5. 
                $maxitems = $rss->get_item_quantity( $number_of_items ); 

                // Build an array of all the items, starting with element 0 (first element).
                $rss_items = $rss->get_items( 0, $maxitems );

            endif;
           
            // Divide the array into two columns
            $rss_items_col1 = array_slice($rss_items, 0, $maxitems / 2);
            $rss_items_col2 = array_slice($rss_items, $maxitems / 2);

            if ( $maxitems == 0 ){
               $html .= '<ul class="flickr-container"><li> No items</li></ul>';
            }
            else {
                $html .= UBC_FOA_Theme_Options::flickr_items_to_html($rss_items_col1);
                $html .= UBC_FOA_Theme_Options::flickr_items_to_html($rss_items_col2);
            }
            
            return $html;
        }
        
        /**
         * Generates an unordered list of flickr items
         * 
         * @param type $flickr_items
         * @return string
         */
        function flickr_items_to_html($flickr_items){
            $html = '<ul class="flickr-container">';
            foreach ( $flickr_items as $item ){
                 $flickr_image_src = UBC_FOA_Theme_Options::get_flickr_img_src($item->get_description());
                 $html .= '<li class="flickr-wrapper">';
                         $html .= '<a href="'.$item->get_permalink().'" target="_blank">';
                            $html .= '<span class="flickr-text">'.$item->get_title().'</span>';
                            $html .= '<img src="'.$flickr_image_src.'" width="122">';
                        $html .= '</a>';
                 $html .= '</li>';
            }
            $html .= '</ul>';
            
            return $html;
        }
        
        /**
         * scrapes the img src from the given flickr item
         * 
         * @param type $item_html
         * @return type
         */
        function get_flickr_img_src($item_html){
            $flickr_html = explode('<p>', $item_html);
            $flickr_html = rtrim($flickr_html[2], '</p>');
            $flickr_html = explode('<img', $flickr_html);
            $flickr_html = rtrim($flickr_html[1], '</a>');
            $flickr_html = explode('"', $flickr_html);
            $flickr_image_src = $flickr_html[1];
            
            return $flickr_image_src;
        }

        /**
         * Generates the content based on the rss url
         * 
         * @param type $rss_url
         * @return type html
         */
        function get_social_rss($rss_url, $number_of_items=1){
            // Get a SimplePie feed object from the specified feed source.
            $rss = fetch_feed( $rss_url );

            $maxitems = 0;

            if ( ! is_wp_error( $rss ) ) : // Checks that the object is created correctly

                // Figure out how many total items there are, but limit it to 5. 
                $maxitems = $rss->get_item_quantity( $number_of_items ); 

                // Build an array of all the items, starting with element 0 (first element).
                $rss_items = $rss->get_items( 0, $maxitems );

            endif;
           

            $html = '<ul>';
                 if ( $maxitems == 0 ) :
                    $html .= '<li> No items</li>';
                 else :
           
                    foreach ( $rss_items as $item ) : 
                        $html .= '<li>';
                            $html .= '<h4><a target="_blank" href="'.$item->get_permalink().'"';
                                $html .= 'title="Posted: '.$item->get_date('j F Y | g:i a').'">';
                                $html .= $item->get_title();
                            $html .= '</a></h4>';
                        $html .= '</li>';
                    endforeach;
                endif;
            $html .= '</ul>';
            
            return $html;
        }
        
        /**
         * Returns the plain html content to be displayed
         * 
         * @param type $data
         * @return type html
         */
        function get_social_text($data){
            return $data;
        }
        
        /**
         * generates items based on the provided instagram user account
         * 
         * @param type $username
         * @param type $limit
         * @return string
         */
        function get_social_instagram($username, $limit = 10) {
	        $html = '';
            try{
	            $media_array = UBC_FOA_Theme_Options::scrape_instagram($username, $limit);
	            // Divide the array into two columns
	            $insta_items_col1 = array_slice($media_array, 0, $limit / 2);
	            $insta_items_col2 = array_slice($media_array, $limit / 2);
	            $html .= UBC_FOA_Theme_Options::instagram_items_to_html($insta_items_col1);
	            $html .= UBC_FOA_Theme_Options::instagram_items_to_html($insta_items_col2);
            }catch(Exception $e){
                $html .= 'Caught exception: ' . $e->getMessage();
            }
            
            return $html;
        }
        /**
         * Generates html of an unordered list of instagram items
         * 
         * @param type $instagram_items
         * @return string
         */
        function instagram_items_to_html($instagram_items){
            $size = 'thumbnail';
            $target = '_blank';
            
            $html = '<ul class="instagram-container">';
            foreach ($instagram_items as $item) {
                    $html .= '<li class="instagram-wrapper">'; 
                        $html .= '<a href="'. esc_url( $item['link'] ) .'" target="'. esc_attr( $target ) .'">'; 
                        $html .= '<span class="instagram-text">'.esc_attr( $item['description'] ).'</span>';
                        $html .= '<img src="'. esc_url($item[$size]) .'"  alt="'. esc_attr( $item['description'] ) .'" title="'. esc_attr( $item['description'] ).'"/>';
                        $html .= '</a>';
                    $html .= '</li>';
            }
            $html .='</ul>';
            
            return $html;
        }
        
        /**
         * Scrapes the instagram page with the associated user
         * based on https://gist.github.com/cosmocatalano/4544576
         * 
         * @param type $username
         * @param type $slice
         * @return \WP_Error
         */
	function scrape_instagram( $username, $slice = 9 ) {
		$username = strtolower( $username );
		if ( false === ( $instagram = get_transient( 'instagram-media-news-'.sanitize_title_with_dashes( $username ) ) ) ) {
			$remote = wp_remote_get( esc_url( 'https://instagram.com/' . trim( $username ) ) );
			if ( is_wp_error( $remote ) ) {
				throw new Exception( 'Unable to communicate with Instagram.' );
			}
			$response_code = wp_remote_retrieve_response_code( $remote );
			if ( 200 !== $response_code ) {
				throw new Exception( 'Instagram did not return a 200. Got response code:' . $response_code );
			}
			$shards = explode( 'window._sharedData = ', $remote['body'] );
			$insta_json = explode( ';</script>', $shards[1] );
			$insta_array = json_decode( $insta_json[0], TRUE );
			if ( !$insta_array ) {
				throw new Exception( 'Instagram has returned invalid data.' );
			}
			if ( isset( $insta_array['entry_data']['ProfilePage'][0]['graphql']['user']['edge_owner_to_timeline_media']['edges'] ) ) {
				$images = $insta_array['entry_data']['ProfilePage'][0]['graphql']['user']['edge_owner_to_timeline_media']['edges'];
			} else {
				throw new Exception( 'Instagram has returned invalid data.' );
			}
			if ( !is_array( $images ) ) {
				throw new Exception( 'Instagram has returned invalid data.' );
			}
			$instagram = array();
			foreach ( $images as $image ) {
				$image['display_src'] = preg_replace( '/^http:/i', '', $image['node']['display_url'] );
				if ( true === $image['is_video'] ) {
					$type = 'video';
				} else {
					$type = 'image';
				}
				$instagram[] = array(
					'description'   => __( $image['node']['edge_media_to_caption']['edges'][0]['node']['text'], 'wpiw' ),
					'link'		  	=> '//instagram.com/p/' . $image['node']['shortcode'],
					'time'		  	=> $image['node']['taken_at_timestamp'],
					'comments'	  	=> $image['comments']['count'],
					'likes'		 	=> $image['node']['edge_liked_by']['count'],
					'thumbnail'	 	=> $image['node']['thumbnail_resources'][0]['src'],
					'type'		  	=> $type
				);
			}
			// do not set an empty transient - should help catch private or empty accounts
			if ( ! empty( $instagram ) ) {
				$instagram = base64_encode( serialize( $instagram ) );
				set_transient( 'instagram-media-new-'.sanitize_title_with_dashes( $username ), $instagram, apply_filters( 'null_instagram_cache_time', HOUR_IN_SECONDS*2 ) );
			}
		}
		if ( ! empty( $instagram ) ) {
			$instagram = unserialize( base64_decode( $instagram ) );
			return array_slice( $instagram, 0, $slice );
		} else {
			throw new Exception( 'Instagram did not return any images.' );
		}
	}
        
        /**
         * Calls the add_event_carousel_content if this feature is set to be enable
         */
        function add_event_carousel(){
                if(UBC_Collab_Theme_Options::get('arts-enable-event-carousel')){
                    add_filter( self::$prefix.'_after_content', array(__CLASS__, 'add_event_carousel_content' ));
                }
        }
        
        /**
         * Generates event carousels based on the selected category
         * 
         */
        function add_event_carousel_content(){
            
            $query_attr = array();
            $category = UBC_Collab_Theme_Options::get('arts-event-carousel-category');
        
            if( in_array( $category, array( 0, 'all', '0') ) ):

                if( is_numeric($category) ):
                    $query_attr['cat']      = (int)$category;
                else:
                    $query_attr['category_name'] = $category;
                endif;
            else:

                $query_attr['cat']      = (int)$category;
            endif;

            $query_attr['posts_per_page'] = 5;

            $news_ticker_query = new WP_Query( $query_attr );

            $html = '<div class="row-fluid entry-content"><h3>Upcoming Events</h3><div class="responsive">';            
            
            while ( $news_ticker_query->have_posts() ){ 
                $news_ticker_query->the_post(); 
                $html .= '<div>';
                    $html .='<span class="mpp-event-date-time">'.get_post_field('event_date_time', get_the_ID()).'</span>';
                    $html .= '<div class="mpp-inner-event-box">';
                        $html .= '<span class="mpp-event-thumbnail">'.get_the_post_thumbnail().'</span>';
                        $html .= '<span class="mpp-event-title"><a href="'.get_permalink().'">'.get_the_title().'</a></span>';
                        $html .= '<p class="mpp-event-excerpt">'.get_the_excerpt().'</p>';
                    $html .= '</div><!--mpp-inner-event-box-->';
                $html .= '</div>';
            }
            $html .= '</div></div>';
            
            if(!is_front_page()){
                $html = '';
            }
            
            echo $html;   
        }
        
        /**
         * Hardcodes the arts layout option
         */
        function arts_frontpage_layout(){
            UBC_Collab_Theme_Options::update('frontpage-layout', 'layout-option4');
            // apply the right width divs to the columns
            //remove_filter( 'ubc_collab_sidebar_class', array(__CLASS__, 'add_sidebar_class' ), 10, 2 );
            remove_filter('ubc_collab_sidebar_class', $sidebar_class,  'frontpage');
	    add_filter( 'ubc_collab_sidebar_class', array(__CLASS__, 'add_sidebar_class' ), 10, 2 );
        }
        
	/**
	 * add_sidebar_class function.
	 * 
	 * @access public
	 * @param mixed $classes
	 * @return void
	 */
	function add_sidebar_class( $classes, $id  ) {
            if ( is_active_sidebar( 'frontpage' ) && is_front_page()){
		if (in_array($id, array("utility-before-content", "utility-after-content", "utility-after-singular") ) ){
			return $classes;
                } else{
                        //if content is span6
			return $classes." span6";
                }
            }
	}    
        /**
         * Hardcodes the option to remove the slider margin option
         */
        function remove_slider_margin(){
            UBC_Collab_Theme_Options::update('slider-remove-margin', 1);
        }
        /**
         * Hardcodes the selection of arts slider
         */
        function select_transparent_slider(){
            UBC_Collab_Theme_Options::update('slider-option', 'basic-sliding');
        }

        
    /**
     * wp_head
     * Appends some of the dynamic css and js to the wordpress header
     * @access public
     * @return void
     */        
        function wp_head(){ ?>
        <link rel="stylesheet" href="//cdn.arts.ubc.ca/foa-cdn/css/slick.css" >
        <link rel="stylesheet" href="//cdn.arts.ubc.ca/foa-cdn/css/foa.css">
        <style type="text/css" media="screen">

            .gradient-color{
                color: <?php echo UBC_Collab_Theme_Options::get('arts-gradient-colour')?>;
            }
            .main-color {
                color: <?php echo UBC_Collab_Theme_Options::get('arts-main-colour')?>;
            }
            .hover-color{
                color: <?php echo UBC_Collab_Theme_Options::get('arts-hover-colour')?>;
            }
            a#artslogo, .main-bg, #qlinks a, .basic-sliding .carousel-caption, .news-ticker{
                background-color:<?php echo UBC_Collab_Theme_Options::get('arts-main-colour')?>;
            }
            .gradient-bg{
                background-color:<?php echo UBC_Collab_Theme_Options::get('arts-gradient-colour')?>;
            }
            a{
                color: <?php echo UBC_Collab_Theme_Options::get('arts-main-colour')?>;
                text-decoration:none;
            }
            a:hover{
                color:<?php echo UBC_Collab_Theme_Options::get('arts-hover-colour')?>;
            }
            a#applybtn:hover, .hover-bg, #qlinks li a:hover {
                background-color: <?php echo UBC_Collab_Theme_Options::get('arts-hover-colour');?>;
            }
            a#applybtn {
                background-color:<?php echo UBC_Collab_Theme_Options::get('arts-main-colour');?>;
            }
            .slick-next, .slick-next:hover, .slick-prev, .slick-prev:hover {
                background: <?php echo UBC_Collab_Theme_Options::get('arts-main-colour');?>;
            }
            .sr-col1 {
                background-image: url("<?php echo UBC_Collab_Theme_Options::get('arts-social-column1-logo');?>");
            }
            .sr-col2 {
                background-image: url("<?php echo UBC_Collab_Theme_Options::get('arts-social-column2-logo');?>");
            }
            .sr-col3 {
                background-image: url("<?php echo UBC_Collab_Theme_Options::get('arts-social-column3-logo');?>");
            }

            .sr-col1-content, .sr-col2-content, .sr-col3-content {
                border-left: 1px solid <?php echo UBC_Collab_Theme_Options::get('arts-main-colour');?>;
            }

            @media (min-width: 1200px){
                <?php
                if(is_front_page()){ ?>
                    .entry-content {
                        width: 1170px;
                        margin: 0 auto;
                      }
                      <?php
                } else{
                    ?>
                    .full-width-container {
                        width: 1170px;
                        margin: 0 auto !important;
                      }
                    <?php
                }
                ?>
                .basic-sliding .carousel-caption {
                    left: <?php echo UBC_Collab_Theme_Options::get('arts-slider-logo-position');?>;
                    background-image: url("<?php echo UBC_Collab_Theme_Options::get('arts-slider-logo-url');?>");
                }
                .flex-control-nav {
                    <?php if(UBC_Collab_Theme_Options::get('arts-slider-logo-position')=='0'){echo 'left: 45px;';}else{echo 'right: 45px;';}?>
                    
                    }
            }
            body.home .nav-tabs > li > a{background-color:<?php echo UBC_Collab_Theme_Options::get('arts-main-colour');?>;}
            body.home .nav-tabs > .active > a, .nav-tabs > .active > a:hover{background-color:<?php echo UBC_Collab_Theme_Options::get('arts-gradient-colour');?>;border:none;}
            body.home .nav-tabs > li > a:hover{background-color:<?php echo UBC_Collab_Theme_Options::get('arts-gradient-colour');?>;}
            .basic-sliding .carousel-caption{
                background-color:<?php echo UBC_Collab_Theme_Options::get('arts-main-colour');?>;
                border:2px solid <?php echo UBC_Collab_Theme_Options::get('arts-gradient-colour');?>;
            }
        </style>
    <?php
    } 
    
    function wp_footer(){
        if(is_front_page()){
            ?>
            <script>
                jQuery(function() {   jQuery(".responsive").slick({   dots: true,   infinite: false,   speed: 300,   slidesToShow: 4,   slidesToScroll: 4,   responsive: [     {       breakpoint: 768,       settings: {         slidesToShow: 2,         slidesToScroll: 2,         infinite: true,         dots: true       }     },     {       breakpoint: 600,       settings: {         slidesToShow: 1,         slidesToScroll: 1       }     },     {       breakpoint: 480,       settings: {         slidesToShow: 1,         slidesToScroll: 1       }     }   ] }); });
            </script>
            <script>
                jQuery(function() { 
                    jQuery("#news-ticker").vTicker();
                 });   
            </script>
            <script src="//cdn.arts.ubc.ca/foa-cdn/js/ticker.js"></script>
            <script type="text/javascript" src="//cdn.arts.ubc.ca/foa-cdn/js/slick.min.js"></script>
            <?php
        }
    }
}
UBC_FOA_Theme_Options::init();