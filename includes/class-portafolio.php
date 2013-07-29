<?php
/**
 * This file contains the Dal_Portfolio class.
 *
 * This class handles the creation of the "Portfolio" post type, and creates a
 * UI to display the Portfolio-specific data on the admin screens.
 */
class Dal_Portfolio {

    /**
     * Construct Method
     */
    function __construct() {

        /** Post Type and Taxonomy creation */
	add_action( 'init', array( $this, 'create_post_type' ) );
	add_action( 'init', array( $this, 'create_taxonomy' ) );
   

        /** Post Thumbnail Support */
        add_action( 'after_setup_theme', array( $this, 'add_post_thumbnail_support' ), '9999' );
	add_image_size( 'portfolio-mini', 125, 125, TRUE );
	add_image_size( 'portfolio-thumb', 225, 180, TRUE );
	add_image_size( 'portfolio-large', 620, 9999 );

        /** Modify the Post Type Admin Screen */
        add_action( 'admin_head', array( $this, 'admin_style' ) );
	add_filter( 'manage_edit-portfolio_columns', array( $this, 'columns_filter' ) );
	add_action( 'manage_posts_custom_column', array( $this, 'columns_data' ) );
	add_filter( 'post_updated_messages', array( $this, 'updated_messages' ) );

        /** Add our Scripts */
	add_action( 'init', array( $this , 'register_script' ) );
	add_action( 'wp_footer', array( $this , 'print_script' ) );
	add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_css' ) );

        /** Create/Modify Dashboard Widgets */
	add_action( 'right_now_content_table_end', array( $this, 'right_now' ) );
	add_action( 'wp_dashboard_setup', array( $this, 'register_dashboard_widget' ) );

        /** Add Shortcode */
	add_shortcode( 'dal_portfolio', array( $this, 'portfolio_shortcode' ) );
    add_filter( 'widget_text', 'do_shortcode' );

      if (function_exists('mfields_set_default_object_terms')) {
            add_action( 'save_post', 'mfields_set_default_object_terms', 100, 2 );
        }

    }

    /**
     * This var is used in the shortcode to flag the loading of javascript
     * @var type boolean
     */
    static $load_js;


    /**
     * Create Portfolio Post Type
     *
     * @since 0.9
     */
    function create_post_type() {

	$args = apply_filters( 'dal_portfolio_post_type_args',
	    array(
		'labels' => array(
		    'name' => __( 'Apps', 'dal-portfolio' ),
		    'singular_name' => __( 'App', 'dal-portfolio' ),
		    'add_new' => __( 'Add new', 'dal-portfolio' ),
		    'add_new_item' => __( 'Add new app', 'dal-portfolio' ),
		    'edit' => __( 'Edit', 'dal-portfolio' ),
		    'edit_item' => __( 'Edit app', 'dal-portfolio' ),
		    'new_item' => __( 'New App', 'dal-portfolio' ),
		    'view' => __( 'View', 'dal-portfolio' ),
		    'view_item' => __( 'View app', 'dal-portfolio' ),
		    'search_items' => __( 'Search Apps', 'dal-portfolio' ),
		    'not_found' => __( 'No Apps found', 'dal-portfolio' ),
		    'not_found_in_trash' => __( 'No Apps found in trash', 'dal-portfolio' ),

		),
		'public' => true,
		'query_var' => true,
		'menu_position' => 20,
		'menu_icon' => dal-portfolio_URL . 'images/portfolio-icon-16x16.png',
		'has_archive' => true,
		'supports' => array( 'title', 'thumbnail' ),
		'rewrite' => array( 'slug' => 'portfolio', 'with_front' => false ),
        //'taxonomies' => array('post_tag')
	    )
	);

	register_post_type( 'portfolio' , $args);
    }

    /**
     * Create the Custom Taxonomy
     *
     * @since 0.9
     */
    function create_taxonomy() {

	$args = apply_filters( 'dal_portfolio_taxonomy_args',
	    array(
		'labels' => array(
		    'name' => __( 'National Award', 'dal-portfolio' ),
		    'singular_name' => __( 'National Award', 'dal-portfolio' ),
		    'all_items' => __( 'All National awards', 'dal-portfolio' ),
		    'parent_item' => null,
		    'parent_item_colon' => null,
		    'edit_item' => __( 'Edit National Award' , 'dal-portfolio' ),
		    'update_item' => __( 'Update Award', 'dal-portfolio' ),
		    'add_new_item' => __( 'Add new National Award', 'dal-portfolio' ),
		    'new_item_name' => __( 'New National Award', 'dal-portfolio' ),
		    'separate_items_with_commas' => __( 'separate items with commas', 'dal-portfolio' ),
		    'add_or_remove_items' => __( 'Add or remove National Awards', 'dal-portfolio' ),
		    'choose_from_most_used' => __( 'Choose from most granted', 'dal-portfolio' ),
		    'menu_name' => __( 'National Awards', 'dal-portfolio' ),
		),
		'hierarchical' => true,
		'show_ui' => true,
		'update_count_callback' => '_update_post_term_count',
		'query_var' => true,
		'rewrite' => array( 'slug' => 'countryaward' )
	    )
	);
  
    if (!taxonomy_exists('countryaward')) {
    	register_taxonomy( 'countryaward', 'portfolio', $args );
       

         if (!term_exists( 'empty', 'countryaward')){
         wp_insert_term('empty', 'countryaward');
       }
  
        if (!term_exists( '1er Lugar', 'countryaward')){
         wp_insert_term(
          '1er Lugar', //the term
          'countryaward'
          );
       }

        if (!term_exists( '2do Lugar', 'countryaward')){
         wp_insert_term(
          '2do Lugar',
          'countryaward'
          /*array(
            'slug' => '2lugarpais',
            )*/
          );
       }

        if (!term_exists( '3er Lugar', 'countryaward')){
         wp_insert_term(
          '3er Lugar', 
          'countryaward'
         /* array(
            'slug' => '3lugarpais',
            )*/
          );
       }

    };

  
     

 if (!taxonomy_exists('premioregional')) {
      register_taxonomy( 'premioregional', 'portfolio',

         array(
          'labels' => array(
              'name' => __( 'Regional Award', 'dal-portfolio' ),
              'singular_name' => __( 'Regional Award', 'dal-portfolio' ),
              'search_items' =>  __( 'Search Regional Awards', 'dal-portfolio' ),
              'popular_items' => __( 'Populars', 'dal-portfolio' ),
              'all_items' => __( 'All Regional Awards', 'dal-portfolio' ),
              'parent_item' => null,
              'parent_item_colon' => null,
              'edit_item' => __( 'Edit Regional Award' , 'dal-portfolio' ),
              'update_item' => __( 'Update Regional Award', 'dal-portfolio' ),
              'add_new_item' => __( 'Add new Regional Award', 'dal-portfolio' ),
              'new_item_name' => __( 'New Regional Award', 'dal-portfolio' ),
              'separate_items_with_commas' => __( 'Separate with commas', 'dal-portfolio' ),
              'add_or_remove_items' => __( 'Add or remove Regional Award', 'dal-portfolio' ),
              'menu_name' => __( 'Regional Awards', 'dal-portfolio' ),
          ),
          'hierarchical' => true,
          'show_ui' => true,
          'update_count_callback' => '_update_post_term_count',
          'query_var' => true,
          'rewrite' => array( 'slug' => 'premioregional' )
        )

       );
       

         if (!term_exists( 'empty', 'premioregional')){
         wp_insert_term('empty', 'premioregional');
       }
       
        if (!term_exists( '1er', 'premioregional')){
         wp_insert_term('1er', 'premioregional');
       }

        if (!term_exists( '2do', 'premioregional')){
         wp_insert_term('2do', 'premioregional');
       }

        if (!term_exists( '3er', 'premioregional')){
         wp_insert_term('3er', 'premioregional');
       }


    };




    if (!taxonomy_exists('apps_tags')) {
       
        register_taxonomy( 'apps_tags', 'portfolio', array( 'hierarchical' => false, 'label' => __('App Tags', 'dal-portfolio' ), 'query_var' => 'apps_tags', 'rewrite' => array( 'slug' => 'apps_tags' ) ) );
    };

    $labels = array(
        'name' => _x( 'Tracks', 'taxonomy general name', 'dal-portfolio' ),
        'singular_name' => _x( 'Track', 'taxonomy singular name', 'dal-portfolio' ),
        'search_items' =>  __( 'Search tracks', 'dal-portfolio' ),
        'all_items' => __( 'All the tracks', 'dal-portfolio' ),
        'parent_item' => __( 'parent Track', 'dal-portfolio' ),
        'parent_item_colon' => __( 'Parent track:', 'dal-portfolio' ),
        'edit_item' => __( 'Edit track', 'dal-portfolio' ), 
        'update_item' => __( 'Update track', 'dal-portfolio' ),
        'add_new_item' => __( 'Add newtrack', 'dal-portfolio' ),
        'new_item_name' => __( 'New track', 'dal-portfolio' ),
        'menu_name' => __( 'Tracks', 'dal-portfolio' ),
      );    
 

    if (!taxonomy_exists('apps_tracks')) {

        register_taxonomy('apps_tracks', 
          array('portfolio', 'dal_country'), 
            array(
              'hierarchical' => True,
              'labels' => $labels,
              'show_ui' => true,
              'query_var' => 'track', 
              'rewrite' => array( 'slug' => 'track' ) 
              )
        );
    };

    $yearlabels = array(
        'name' => _x( 'Year', 'taxonomy general name', 'dal-portfolio' ),
        'singular_name' => _x( 'Year', 'taxonomy singular name', 'dal-portfolio' ),
        'search_items' =>  __( 'Search by Year', 'dal-portfolio' ),
        'all_items' => __( 'All Years', 'dal-portfolio' ),
        'parent_item' => __( 'Parent year', 'dal-portfolio' ),
        'parent_item_colon' => __( 'Parent year:', 'dal-portfolio' ),
        'edit_item' => __( 'Edit year', 'dal-portfolio' ), 
        'update_item' => __( 'Update Year', 'dal-portfolio' ),
        'add_new_item' => __( 'Add new year', 'dal-portfolio' ),
        'new_item_name' => __( 'New year', 'dal-portfolio' ),
        'menu_name' => __( 'App year', 'dal-portfolio' ),
      );   
       if (!taxonomy_exists('app_year')) {

        register_taxonomy('app_year', 
          array('portfolio'), 
            array(
              'hierarchical' => True,
              'labels' => $yearlabels,
              'show_ui' => true,
              'query_var' => 'app_year', 
              'rewrite' => array( 'slug' => 'app_year' ) 
              )
        );

        if (!term_exists( '2012', 'app_year')){
         wp_insert_term('2012', 'app_year');
       }

        if (!term_exists( '2011', 'app_year')){
         wp_insert_term('2011', 'app_year');
       }
       if (!term_exists( '2013', 'app_year')){
         wp_insert_term('2013', 'app_year');
       }
         if (!term_exists( '2014', 'app_year')){
         wp_insert_term('2014', 'app_year');
       }
    };

    $applabels = array(
        'name' => __( 'App Country', 'taxonomy general name', 'dal-portfolio' ),
        'singular_name' => __( 'App Country', 'taxonomy singular name', 'dal-portfolio' ),
        'search_items' =>  __( 'Search countries', 'dal-portfolio' ),
        'all_items' => __( 'All the countries', 'dal-portfolio' ),
        'parent_item' => __( 'Parent country', 'dal-portfolio' ),
        'parent_item_colon' => __( 'Parent country:', 'dal-portfolio' ),
        'edit_item' => __( 'Edit country', 'dal-portfolio' ), 
        'update_item' => __( 'Update app country', 'dal-portfolio' ),
        'add_new_item' => __( 'Add new app country', 'dal-portfolio' ),
        'new_item_name' => __( 'New App Country', 'dal-portfolio' ),
        'menu_name' => __( 'App Country', 'dal-portfolio' ),
      );   


     if (!taxonomy_exists('appcountry')) {

        register_taxonomy( 'appcountry', 
          'portfolio', 
            array(
              'hierarchical' => false,
              'labels' => $applabels,
              'show_ui' => true,
              'query_var' => 'appcountry', 
              'rewrite' => array( 'slug' => 'appcountry' ) 
              )
              
        );
        
      if (!term_exists( 'Argentina', 'appcountry')){
         wp_insert_term('Argentina', 'appcountry');
       }

       if (!term_exists( 'Bolivia', 'appcountry')){
        wp_insert_term('Bolivia', 'appcountry');
      }

      if (!term_exists( 'Brasil', 'appcountry')){
        wp_insert_term('Brasil', 'appcountry');
      }
      if (!term_exists( 'Chile', 'appcountry')){
        wp_insert_term('Chile', 'appcountry');
      }
      if (!term_exists( 'Colombia', 'appcountry')){
        wp_insert_term('Colombia', 'appcountry');
      }
      if (!term_exists( 'Costa-Rica', 'appcountry')){
        wp_insert_term('Costa-Rica', 'appcountry');
      }
      if (!term_exists( 'Cuba', 'appcountry')){
        wp_insert_term('Cuba', 'appcountry');
      }
      if (!term_exists( 'Ecuador', 'appcountry')){
        wp_insert_term('Ecuador', 'appcountry');
      }
      if (!term_exists( 'El-Salvador', 'appcountry')){
      wp_insert_term('El-Salvador', 'appcountry');
      }
      if (!term_exists( 'Guatemala', 'appcountry')){
        wp_insert_term('Guatemala', 'appcountry');
      }
      if (!term_exists( 'Haiti', 'appcountry')){
        wp_insert_term('Haiti', 'appcountry');
      }
      if (!term_exists( 'Honduras', 'appcountry')){
        wp_insert_term('Honduras', 'appcountry');
      }
      if (!term_exists( 'Mexico', 'appcountry')){
        wp_insert_term('Mexico', 'appcountry');
      }
      if (!term_exists( 'Nicaragua', 'appcountry')){
        wp_insert_term('Nicaragua', 'appcountry');
      }
      if (!term_exists( 'Panama', 'appcountry')){
        wp_insert_term('Panama', 'appcountry');
      }
      if (!term_exists( 'Paraguay', 'appcountry')){
        wp_insert_term('Paraguay', 'appcountry');
      }
      if (!term_exists( 'Peru', 'appcountry')){
        wp_insert_term('Peru', 'appcountry');
      }
      if (!term_exists( 'Republica-Dominicana', 'appcountry')){
        wp_insert_term('Republica-Dominicana', 'appcountry');
      }
      if (!term_exists( 'Uruguay', 'appcountry')){
        wp_insert_term('Uruguay', 'appcountry');
      }
      if (!term_exists( 'Venezuela', 'appcountry')){
        wp_insert_term('Venezuela', 'appcountry');
      }
      if (!term_exists( 'Puerto-Rico', 'appcountry')){
        wp_insert_term('Puerto-Rico', 'appcountry');
      }
       //The Caribbean

       if (!term_exists( 'Antigua-and-Barbuda', 'appcountry')){
         wp_insert_term('Antigua-and-Barbuda', 'appcountry');
       }
       if (!term_exists( 'Belize', 'appcountry')){
         wp_insert_term('Belize', 'appcountry');
       }
       if (!term_exists( 'Dominica', 'appcountry')){
         wp_insert_term('Dominica', 'appcountry');
       }
       if (!term_exists( 'Grenada', 'appcountry')){
         wp_insert_term('Grenada', 'appcountry');
       }
       if (!term_exists( 'Jamaica', 'appcountry')){
         wp_insert_term('Jamaica', 'appcountry');
       }
       if (!term_exists( 'Saint-Kitts-and-Nevis', 'appcountry')){
         wp_insert_term('Saint-Kitts-and-Nevis', 'appcountry');
       }
       if (!term_exists('Saint-Lucia', 'appcountry')){
         wp_insert_term('Saint-Lucia', 'appcountry');
       }
       if (!term_exists( 'Saint-Vincent-and-the-Grenadines', 'appcountry')){
         wp_insert_term('Saint-Vincent-and-the-Grenadines', 'appcountry');
       }
        if (!term_exists( 'Trinidad-and-Tobago', 'appcountry')){
         wp_insert_term('Trinidad-and-Tobago', 'appcountry');
       }
        };

    }



    /**
     * Correct messages when Portfolio post type is saved
     *
     * @global type $post
     * @global type $post_ID
     * @param type $messages
     * @return type
     * @since 0.9
     */
    function updated_messages( $messages ) {
	global $post, $post_ID;

	$messages['portfolio'] = array(
	    0 => '', // Unused. Messages start at index 1.
	    1 => sprintf( __('App updated. <a href="%s">View app</a>', 'dal-portfolio' ), esc_url( get_permalink($post_ID) ) ),
	    2 => __('Custom field updated .', 'dal-portfolio' ),
	    3 => __('Custom field deleted .', 'dal-portfolio' ),
	    4 => __('App updated.', 'dal-portfolio' ),
	    /* translators: %s: date and time of the revision */
	    5 => isset($_GET['revision']) ? sprintf( __(' App restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
	    6 => sprintf( __('App published. <a href="%s">View app </a>', 'dal-portfolio' ), esc_url( get_permalink($post_ID) ) ),
	    7 => __('App saved.', 'dal-portfolio' ),
	    8 => sprintf( __('App sent <a target="_blank" href="%s">Preview</a>', 'dal-portfolio' ), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
	    9 => sprintf( __('App programmed by: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview</a>', 'dal-portfolio' ),
	      // translators: Publish box date format, see http://php.net/date
	      date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
	    10 => sprintf( __('Draft <a target="_blank" href="%s">Preview app</a>', 'dal-portfolio' ), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
	);

      return $messages;
    }

    /**
     * Filter the columns on the admin screen and define our own
     *
     * @param type $columns
     * @return string
     * @since 0.9
     */
    function columns_filter ( $columns ) {

	$columns = array(
	    'cb' => '<input type="checkbox" />',
	    'portfolio_thumbnail' => __( 'image', 'dal-portfolio' ),
	    'title' => __( 'Title', 'dal-portfolio' ),
	    'portfolio_description' => __( 'Description', 'dal-portfolio' ),
	    'portfolio_countryawardes' => __( 'National Awards', 'dal-portfolio' )
	);

	return $columns;
    }

    /**
     * Filter the data that shows up in the columns we defined above
     *
     * @global type $post
     * @param type $column
     * @since 0.9
     */
    function columns_data( $column ) {

	global $post;

	switch( $column ) {
	    case "portfolio_thumbnail":
		printf( '<p>%s</p>', the_post_thumbnail('portfolio-mini' ) );
		break;
	    case "portfolio_description":
		the_excerpt();
		break;
	    case "portfolio_countryawardes":
		echo get_the_term_list( $post->ID, 'countryaward', '', '', '', '' );
		break;
	}
    }

    /**
     * Check for post-thumbnails and add portfolio post type to it
     *
     * @global type $_wp_theme_countryawardes
     * @since 0.9
     */
    function add_post_thumbnail_support() {

	global $_wp_theme_countryawardes;

	if( !isset( $_wp_theme_countryawardes['post-thumbnails'] ) ) {

	    $_wp_theme_countryawardes['post-thumbnails'] = array( array( 'portfolio' ) );
	}

	elseif( is_array( $_wp_theme_countryawardes['post-thumbnails'] ) ) {

	    $_wp_theme_countryawardes['post-thumbnails'][0][] = 'portfolio';
	}
    }

    /**
     * DAL-Portfolio Shortcode
     *
     * @param type $atts
     * @param type $content
     * @since 0.9
     * @version 1.1
     */


     
     



    function portfolio_shortcode( $atts, $content = null ) {
        
	/*
	Supported Attributes
	    link =>  'page', image
	    thumb => any built-in image size
	    full => any built-in image size (this setting is ignored of 'link' is set to 'page')
            title => above, below or 'blank' ("yes" is converted to "above" for backwards compatibility)
	    display => content, excerpt (leave blank for nothing)
            heading => When displaying the 'apptrack' items in a row above the Apps, define the heading text for that section.
            orderby => date or any other orderby param available. http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters
            order => ASC (ascending), DESC (descending)
            terms => a 'apptrack' tag you want to filter on
            operator => 'IN', 'NOT IN' filter for the term tag above

	*/

	/**
	 * Currently 'image' is the only supported link option right now
	 *
	 * While 'page' is an available option, it can potentially require a lot of work on the part of the
	 * end user since the plugin can't possibly know what theme it's being used with and create the necessary
	 * page structure to properly integrate into the theme. Selecting page is only advised for advanced users.
   *------------------------
   ****For DAL and Dalboot theme the link=page option is available and ACTIVE AS DEFAULT!
	 */

	/** Load the javascript */
	self::$load_js = true;
	/** Shortcode defaults */
	$defaults = apply_filters( 'dal_portfolio_shortcode_args',
	    array(
		'link' => __( 'page', 'dal-portfolio' ),
		'thumb' => __( 'portfolio-thumb', 'dal-portfolio' ),
		'full'     => __( 'portfolio-large', 'dal-portfolio' ),
    'title' => __( 'above', 'dal-portfolio' ),
		'display' => '',
    'heading' => __( 'Display', 'dal-portfolio' ),
		'orderby' => __( 'date', 'dal-portfolio' ),
		'order' => __( 'desc', 'dal-portfolio' ),
    'datitos'=> __( 'info', 'dal-portfolio' ),
    'terms' => '',
    'operator' => __( 'IN', 'dal-portfolio' ),
    'appcountry'=> $appcountry,
    'app_year'=> $app_year,
    'award'=> __( 'national', 'dal-portfolio' ),
    
    
    
	    )
	);

	extract( shortcode_atts( $defaults, $atts ) );
        
        if( $title == "yes" ) $title == "above"; // For backwards compatibility

	/** Default Query arguments -- can be overridden by filter */
	$args = apply_filters( 'dal_portfolio_shortcode_query_args',
	    array(
		'post_type' => 'portfolio',
		'posts_per_page' => -1, // show all
    'meta_key' => '_thumbnail_id', // Should pull only items with featured images
		'orderby' => $orderby,
		'order' => $order,
    'taxonomy' =>'appcountry',
    'tax_query'=> array()
    

	    )
	);

        /** If the user has defined any tax (countryaward) terms, then we create our tax_query and merge to our main query  */
        //si tiene un lugar hace esto

        if( $appcountry ) {
            $post_meta_data = get_post_custom($post->ID);
            $args['tax_query'][]=   array(
                        'taxonomy' => 'appcountry',
                        'terms' => $appcountry,
                        'field' => 'slug',
                      
                    );         
        }

        if ($app_year) {
          $post_meta_data = get_post_custom($post->ID);
          $args['tax_query'][]=   array(
                        'taxonomy' => 'app_year',
                        'terms' => $app_year,
                        'field' => 'slug',
                      
                    );    
        }
       

          /* if ($countryaward){
             
              $post_meta_data = get_post_custom($post->ID);
              $countryaward = get_terms('countryaward');
              foreach ($countryaward as $premiopai => $premiopa) {
               print_r($premiopa);
             

              }
              $args['tax_query'][]=   array(
                            'taxonomy' => 'countryaward',
                            'terms' => $premiopa,
                            'field' => 'slug',            
                            );
           
           
          }   
   

*/

       

          switch( $award ) {
            case "national" :

              $termspremio = get_terms( 'countryaward' );
              $arraypremiosnac = array();

                     foreach ( $termspremio as $termp ) {
                       $arraypremiosnac[] = $termp ->slug;       

                     }
                         

              $args['tax_query'][]=   array(
                            'taxonomy' => 'countryaward',
                            'terms'=>$arraypremiosnac,
                            'field' => 'slug',            
                            );
            break;
            case "regional" :
             
              $termspremioreg = get_terms( 'premioregional' );
              $arraypremiosreg = array();

                     foreach ( $termspremioreg as $termpr ) {
                       $arraypremiosreg[] = $termpr ->slug;       

                     }
                         

              $args['tax_query'][]=   array(
                            'taxonomy' => 'premioregional',
                            'terms'=>$arraypremiosreg,
                            'field' => 'slug',            
                            );
            break;      
            default:
            break;

          }   
       



        /** Create a new query based on our own arguments */
	$portfolio_query = new WP_Query( $args );
  $pais_tracks = new WP_Query( $args );

        if( $portfolio_query->have_posts() ) {
            $a ='';

            
            if( $terms ) {
                
                /** Change the get_terms argument based on the shortcode $operator */
                switch( $operator) {
                    case "IN":
                        $a = array( 'include' => $terms );
                        break;
                
                    case "NOT IN":
                        $a = array( 'exclude' => $terms );
                        break;
                
                    default:
                        break;
                }
                
            }


            /** We're simply recycling the variable at this point */
            $terms = get_terms( 'apps_tracks', $a );
            $terms_nuevos = array();
            while( $pais_tracks->have_posts() ) 
            {
              ($pais_tracks->the_post());
               $terms_tracks = get_the_terms( get_the_ID(), 'apps_tracks' );
               
               if (!empty($terms_tracks)){ //do not draw it if there is not term assigned
                foreach ( $terms_tracks as $term_track ) {
                               $terms_nuevos[$term_track->slug] = $term_track->name;
                               }
                             
              }
            }
            

            /** If there are multiple terms in use, then run through our display list */
            $uid= uniqid();

            if( count( $terms ) > 1)  {
                if ($award == 'national'){
                    $preposicion =__('National Awards', 'dal-portfolio');
                }

                if ($award == 'regional'){
                    $preposicion =__('Regional Awards', 'dal-portfolio');

                } 
                 if (empty( $award) ){
                    $preposicion =__('Apps', 'dal-portfolio');

                } 

                $return .= '<div class="'.$uid.'" ><h2 class="dal-portf-title">'.$preposicion.'';

                if ($appcountry){
                  $return .= ' <span style="text-transform: capitalize;">'.$appcountry. '</span>';
                 }
               if ($app_year){
                $return .= ' '.$app_year.'';
               }

                $return .= '</h2></div>'; 
                 if ($award == 'national' ){
                $return .= '<div class="head-premios premios-nac"></div>';
              }
               if ($award == 'regional'){
                $return .= '<div class="head-premios"></div>';
              }
                if (!($award == 'national' || $award == 'regional' )){
                 
                  
                    $return .= '<ul class="dal-portfolio-filtro '.$uid.' "><li class="dal-portfolio-category-title">';
                    $return .= $heading;
                    $return .= '</li><li class="active"><a href="javascript:void(0)" class="all">all</a></li>';

                    $term_list = '';

                    /** break each of the items into individual elements and modify its output */



                    foreach( $terms_nuevos as $slug => $name ) {
                    
                        $term_list .= '<li><a href="javascript:void(0)" class="' . $slug . '">' . $name . '</a></li>';
                      
                        
                    }

                    /** Return our modified list */
                    $return .= $term_list . '</ul>';
                }
                $return .= "<script>";
                $return .= "jQuery(document).ready(function(){";
                $return .= "if(jQuery().quicksand) {portfolio_quicksand('".$uid."')}";
                $return .= "});</script>";
                
            }
            if ($award == 'national' || $award == 'regional' ){
                   $return .= '<ul class="dal-portfolio-grid colorganador '.$uid.'">';
                  } else {
                    
                  
            $return .= '<ul class="dal-portfolio-grid '.$uid.'">';
            }

            while( $portfolio_query->have_posts() ) : $portfolio_query->the_post();

                /** Get the terms list */
                $terms = get_the_terms( get_the_ID(), 'apps_tracks' );
                
                

                /** Add each term for a given App as a data type so it can be filtered by Quicksand */
               
                  $return .= '<li data-id="id-' . get_the_ID() . '" data-type="';
                
                if (!empty($terms)){ //do not draw it if there is not term assigned
                 
                  foreach ( $terms as $term ) {
                      $return .= $term->slug . ' ';
                  }
                }
                
                  $return .= '">';
                
                //get the year
                          $tyears =  wp_get_post_terms(get_the_ID(), 'app_year', array("fields" => "all"));
                          $countyear = count($tyears);

                             
                                   


                /** Above image Title output */
                if( $title == "above" ) $return .= '<h2 class="dal-portfolio-title">' . get_the_title() . '</h2>';
                if ( $countyear > 0 ){
                          
                               foreach ( $tyears as $tyear ) {
                                 $return .= "<div class='dal-portf-ano dal-ano-".$tyear->slug ."'>".$tyear->slug ."</div>";
                               }
                           
                             }      



                /** Handle the image link */
                switch( $link ) {
                    case "page" :
                        $return .= '<a href="' . get_permalink() . '" rel="bookmark">';
                  			$return .= get_the_post_thumbnail( get_the_ID(), $thumb );
                  			$return .= '</a>';


                        break;

                    case "image" :
                        $_portfolio_img_url = wp_get_attachment_image_src( get_post_thumbnail_id(), $full );

                        $return .= '<a href="' . $_portfolio_img_url[0] . '" title="' . the_title_attribute( 'echo=0' ) . '" >';
                        $return .= get_the_post_thumbnail( get_the_ID(), $thumb );
                        $return .= '</a>';
                        break;

                    default : // If it's anything else, return nothing.
                        break;
                }

		            /** Below image Title output */
                if( $title == "below" ) $return .= '<h2 class="dal-portfolio-title">' . get_the_title() . '</h2>';
                  
                /*datitos*/
                  switch($datitos) {
                    case "info" : 
                    
                    $return .="<div class='dal-portfolio-datitos'> <div class='dal-fila'>";
                        //get the flags
                        $terms = get_the_terms( get_the_ID(), 'appcountry' );
                        if( count( $terms ) > 0 )  {
                           $return .= '<div class="dal-meta-item">';
                               foreach ( $terms as $term ) {
                                 $return .= "<div class='dal-portfolio-flag flag-". $term->slug. "'></div>";
                               }
                                 $return .= "</div>";
                             }

                       //get the tracks
                        $terms = get_the_terms( get_the_ID(), 'apps_tracks' );
                        if( count( $terms ) > 0 )  {
                           $return .= '<div class="dal-meta-item dal-portfolio-tracks">';
                            if (!empty($terms)){ //do not draw it if there is not term assigned
                               foreach ( $terms as $term ) {
                                 $return .= '<div>'. $term->name .'</div>';
                               }
                             }
                                 $return .= "</div>";
                             }
                       
                        //get the national prizes
                          $tcountryawardes = wp_get_post_terms(get_the_ID(), 'countryaward', array("fields" => "all"));
                          $countppais = count($tcountryawardes);
                           
                           if ( $countppais > 0 ){
                              
                               foreach ( $tcountryawardes as $tcountryaward ) {
                                 $return .= "<div class='dal-portf-premio premioNac dal-portf-".$tcountryaward->slug ."'></div>";
                               }
                             
                             }

                           //get the regional prizes
                          $tpremioregionales =  wp_get_post_terms(get_the_ID(), 'premioregional', array("fields" => "all"));
                          $countpreg = count($tpremioregionales);

                             
                           if ( $countpreg > 0 ){
                          
                               foreach ( $tpremioregionales as $tpremioregional ) {
                                 $return .= "<div class='dal-portf-premio premioReg dal-portf-".$tpremioregional->slug ." '></div>";
                               }
                           
                             }          

                         $return .= "</div>"; //end .dal-portfolio-datitos



                        break;

                  }


                /** Display the content */
                switch( $display ) {
                    case "content" :
                        $return .= '<div class="dal-portfolio-text">' . get_the_content() . '</div>';
                        break;

                    case "excerpt" :
                        $return .= '<div class="dal-portfolio-text">' . get_the_excerpt() . '</div>';
                        break;


                    default : // If it's anything else, return nothing.
                        break;
                }

               
            
                $return .= '</li>';


            endwhile;

            $return .= '</ul>';
        }

	return $return;
    }


    /**
     * Add the Portfolio Post type to the "Right Now" Dashboard Widget
     *
     * @link http://bajada.net/2010/06/08/how-to-add-custom-post-types-and-taxonomies-to-the-wordpress-right-now-dashboard-widget
     * @since 0.9
     */
    function right_now() {
	include_once( dirname( __FILE__ ) . '/views/right-now.php' );
    }


    /**
     * Style the portfolio icon on the admin screen
     *
     * @since 0.9
     */
    function admin_style() {
	printf( '<style type="text/css" media="screen">.icon32-posts-portfolio { background: transparent url(%s) no-repeat !important; }</style>', dal-portfolio_URL . 'images/portfolio-icon-32x32.png' );
    }


    /**
     * Register the necessary javascript, which can be overriden by creating your own file and
     * placing it in the root of your theme's folder
     *
     * @since 1.0
     * @version 1.1.0
     */
    function register_script() {

        wp_register_script( 'jquery-quicksand', "/wp-content/plugins/dal-portfolio/" . 'includes/js/jquery.quicksand.js', array( 'jquery' ), '1.2.2', true );
        wp_register_script( 'jquery-easing', "/wp-content/plugins/dal-portfolio/" . 'includes/js/jquery.easing.1.3.js', array( 'jquery' ), '1.3', true );

	if( file_exists( get_stylesheet_directory() . "/dal-portfolio.js" ) ) {
	    wp_register_script( 'dal-portfolio-js', get_stylesheet_directory_uri() . '/dal-portfolio.js', array( 'jquery-quicksand', 'jquery-easing' ), dal-portfolio_VERSION, true );
	}
	elseif( file_exists( get_template_directory() . "/dal-portfolio.js" ) ) {
	    wp_register_script( 'dal-portfolio-js', get_template_directory_uri() . '/dal-portfolio.js', array( 'jquery-quicksand', 'jquery-easing' ), dal-portfolio_VERSION, true );
	}
	else {
        wp_register_script( 'dal-portfolio-js', "/wp-content/plugins/dal-portfolio/" . 'includes/js/portfolio.js', array( 'jquery-quicksand', 'jquery-easing' ), dal-portfolio_VERSION, true );
	}
    }


    /**
     * Check the state of the variable. If true, load the registered javascript
     *
     * @since 1.0
     */
    function print_script() {

	if( ! self::$load_js )
	    return;

	wp_print_scripts( 'dal-portfolio-js' );
    }


    /**
     * Load the plugin css. If the css file is present in the theme directory, it will be loaded instead,
     * allowing for an easy way to override the default template
     *
     * @since 0.9
     * @version 1.0
     */
    function enqueue_css() {
  wp_enqueue_style( 'dal-portfolio',  "/wp-content/plugins/dal-portfolio/". '/includes/dal-portfolio.css', array(), dal-portfolio_VERSION );
    }


    /**
     * Adds a widget to the dashboard.
     *
     * @since 0.9.1
     */
    function register_dashboard_widget() {
        wp_add_dashboard_widget( 'ac-portfolio', 'Dal Portfolio', array( $this, 'dashboard_widget_output' ) );
    }


    /**
     * Output for the dashboard widget
     *
     * @since 0.9.1
     * @version 1.0
     */
    function dashboard_widget_output() {

        echo '<div class="rss-widget">';

        wp_widget_rss_output( array(
            'url' => 'http://dalpc.com/tag/dal-portfolio/feed', // feed url
            'title' => 'Dal Portfolio Posts', // feed title
            'items' => 3, //how many posts to show
            'show_summary' => 1, // display excerpt
            'show_author' => 0, // display author
            'show_date' => 1 // display post date
        ) );

        echo '<div class="dal-portfolio-widget-bottom"><ul>'; ?>
            <li><a href="http://arcnx.co/apwiki"><img src="<?php echo dal-portfolio_URL . 'images/page-16x16.png'?>">Wiki Page</a></li>
            <li><a href="http://arcnx.co/aphelp"><img src="<?php echo dal-portfolio_URL . 'images/help-16x16.png'?>">Support Forum</a></li>
            <li><a href="http://arcnx.co/aptrello"><img src="<?php echo dal-portfolio_URL . 'images/trello-16x16.png'?>">Dev Board</a></li>
        <?php echo '</ul></div>';
        echo "</div>";

        // handle the styling
        echo '<style type="text/css">
            #ac-portfolio .rsssummary { display: block; }
            #ac-portfolio .dal-portfolio-widget-bottom { border-top: 1px solid #ddd; padding-top: 10px; text-align: center; }
            #ac-portfolio .dal-portfolio-widget-bottom ul { list-style: none; }
            #ac-portfolio .dal-portfolio-widget-bottom ul li { display: inline; padding-right: 9%; }
            #ac-portfolio .dal-portfolio-widget-bottom img { padding-right: 3px; vertical-align: top; }
        </style>';
    }


}



?>
