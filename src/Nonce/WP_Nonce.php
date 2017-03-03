<?php
/*
* WP Nonce Class
* @version     1.0
* @author      Ivijan-Stefan Stipic <creativform@gmail.com>
*/
namespace Nonce;

class WP_Nonce
{
	private $setup = [
		'nonce_life'	=> '',
		'nonce_name' 	=> '_wpnonce',
		'nonce_action' 	=> '-1',
	];
	
	private $nonce = false;
	
	/*
	* Set Global Options
	* @version     1.0
	* @option      name           (string/array) Name of option or array like ['nonce_name' => '_wpnonce']
	* @option      option         (string/bool/array/int/float) Value of option (not work if $name is array)
	*/
	public function option($name, $option='')
	{
		if(is_array($name))
		{
			foreach($name as $key=>$value)
				$this->setup[$key]=$value;
		}
		else
			$this->setup[$name]=$option;
		
	}
	
	/*
	* Clean Global Options
	* @version     1.0
	*/
	public function clean()
	{
		$this->setup=[];
		$this->nonce = '';
	}
	
	/*
	* Init setups
	* @version     1.0
	*/	
	public function init()
	{
		/* Change Nonce Lifetime () */
		if(isset($this->setup['nonce_life']) && !empty($this->setup['nonce_life']))
		{
			$this->add_filter( 'nonce_life', $this, 'nonce_life' );
		}
	}
	
	/*
	* Setup Nonce lifetime
	* @version     1.0
	*/
	public function nonce_life() {
		return $this->setup['nonce_life'];
	}
	
	/*
	* Create Nonce
	* @version     1.0
	* @option      action             (string/array) Allow also array like ['some_thing',$post_id]
	* @return      string             The one use form token or empty string.
	*/
	public function create($action=''){
		if(isset($this->setup['nonce_action']) && !empty($this->setup['nonce_action']) && empty($action))
			$action=$this->setup['nonce_action'];
		
		if(is_array($action))
		{
			if(count($action)>0)
			{
				$action = join('-',$action);
				$this->nonce=wp_create_nonce( (string)$action );
				return $this->nonce;
			}
		}
		else
		{
			$this->nonce=wp_create_nonce( (string)$action );
			return $this->nonce;
		}
		return '';
	}
	
	/*
	* Retrieve URL with nonce added to URL query.
	* @version     1.0
	* @option      actionurl       (string) (required) URL to add nonce action
	* @option      action          (string) (optional) nonce action name
	* @option      name            (string) (optional, since 3.6) nonce name
	* @return      string          URL with nonce action added.
	*/
	public function url($actionurl, $action='', $name=''){
		if(isset($this->setup['nonce_action']) && !empty($this->setup['nonce_action']) && empty($action))
			$action=$this->setup['nonce_action'];
		
		if($this->wp_version_compare('3.6', '>='))
			return wp_nonce_url( (string)$actionurl, (string)$action, (string)$name );
		else
			return wp_nonce_url( (string)$actionurl, (string)$action);
	}
	
	/*
	* Retrieves or displays the nonce hidden form field.
	* @version     1.0
	* @option      referer         (boolean) (optional) Whether also the referer hidden form field should be created with the wp_referer_field() function.
	* @option      echo            (boolean) (optional) Whether to display or return the nonce hidden form field
	* @option      action          (string) (optional) Action name. Should give the context to what is taking place. Optional but recommended.
	* @option      name            (string) (optional) Nonce name. This is the name of the nonce hidden form field to be created.
	* @return      string          The nonce hidden form field, optionally followed by the referer hidden form field if the $referer argument is set to true.
	*/
	public function field($action='', $name='', $referer=true, $echo=true){
		if(isset($this->setup['nonce_action']) && !empty($this->setup['nonce_action']) && empty($action))
			$action=$this->setup['nonce_action'];
		
		if(isset($this->setup['nonce_name']) && !empty($this->setup['nonce_name']) && empty($name))
			$name=$this->setup['nonce_name'];
		
		if($echo===true)
			wp_nonce_field( $action, (string)$name, (bool)$referer, true);
		else
			return wp_nonce_field( $action, (string)$name, (bool)$referer, false );
	}
	
	/*
	* Display 'Are you sure you want to do this?' message to confirm the action being taken
	* @version     1.0
	* @option      echo           (bool) (optional).
	* @return      bool/string    Boolean false if the nonce is invalid. Otherwise, returns an integer 
	*/
	public function are_you_shure($echo=true){
		if($echo===true)
			wp_referer_field( true );
		else
			return wp_referer_field( false );
	}
	
	/*
	* Tests either if the current request carries a valid nonce (check_admin_referer()).
	* @version     1.0
	* @option      action          (string) (optional) Action name. Should give the context to what is taking place
	* @option      query_arg       (string) (optional) Where to look for nonce in the $_REQUEST PHP variable.
	* @return      bool/string     true / message
	*/
	public function admin_verify($action='', $query_arg=''){
		if(isset($this->setup['nonce_name']) && !empty($this->setup['nonce_name']) && empty($query_arg))
			$query_arg=$this->setup['nonce_name'];
		
		if(isset($this->setup['nonce_action']) && !empty($this->setup['nonce_action']) && empty($action))
			$action=$this->setup['nonce_action'];
		
		if($this->wp_version_compare('2.5', '>='))
			check_admin_referer( $action, (string)$query_arg );
		else if($this->wp_version_compare('2.5', '<') && $this->wp_version_compare('2.0.1', '>='))
			check_admin_referer( $action );
		else
			check_admin_referer();
	}
	
	/*
	* The standard function verifies the AJAX request
	* @version     1.0
	* @option      query_arg       (string) (optional) Where to look for nonce in the $_REQUEST PHP variable.
	* @option      die             (boolean) (optional) whether to die if the nonce is invalid
	* @option      action          (string) (optional) Action name. Should give the context to what is taking place
	* @return      bool/string     true / message
	*/
	public function ajax_verify($query_arg=false, $die=true, $action=''){
		if(isset($this->setup['nonce_action']) && !empty($this->setup['nonce_action']) && empty($action))
			$action=$this->setup['nonce_action'];
		
		if($this->wp_version_compare('2.5', '>='))
			check_ajax_referer( $action, $query_arg, (bool)$die );
		else
			check_ajax_referer( $action, (bool)$die );
	}
	
	/*
	* To verify a nonce passed in some other context, call wp_verify_nonce()
	* @version     1.0
	* @option      nonce          (string) (required) Nonce to verify.
	* @option      action         (string/int) (optional) Action name.
	* @return      bool/string    Boolean false if the nonce is invalid. Otherwise, returns an integer 
	*/
	public function wp_verify($nonce='', $action=''){
		if(!empty($this->nonce) && empty($nonce))
			$nonce = $this->nonce;
		
		if(isset($this->setup['nonce_action']) && !empty($this->setup['nonce_action']) && empty($action))
			$action=$this->setup['nonce_action'];
		
		return wp_verify_nonce( (string)$nonce, $action );
	}
	
	/*
	* Display 'Are you sure you want to do this?' message to confirm the action being taken
	* @version     1.0
	* @option      action         (string) (required) The nonce action.
	* @return      bool/string    Boolean false if the nonce is invalid. Otherwise, returns an integer 
	*/
	public function ays($action=''){
		if(isset($this->setup['nonce_action']) && !empty($this->setup['nonce_action']) && empty($action))
			$action=$this->setup['nonce_action'];
		
		wp_nonce_ays( $action );
	}
	
	
	/**************************************************************************************************/
	
	/* Replacemant for add_action() */
	protected function add_action($tag, $class, $function_to_add, $priority = 10, $accepted_args = 1){
		return add_action( (string)$tag, array($class, $function_to_add), (int)$priority, (int)$accepted_args );
	}
	
	/* Replacemant for add_filter() */
	protected function add_filter($tag, $class, $function_to_add, $priority = 10, $accepted_args = 1){
		return add_filter( (string)$tag, array($class, $function_to_add), (int)$priority, (int)$accepted_args );
	}
	
	/* Compare WP versions */
	protected function wp_version_compare($ver, $operator = null)
	{
		$ver1 = get_bloginfo('version');
		$ver2 = $ver;
		return (null !== $operator ? version_compare($ver1, $ver2, $operator) : version_compare($ver1, $ver2));
	}
}