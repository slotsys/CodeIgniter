<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* CodeIgniter Template Class
*
* Library templates for CodeIgniter
*
* @package CodeIgniter
* @category Libraries
* @author SlotSYS (www.slotsys.com), Rodolfo Silva (www.rodolfosilva.com)
* @link http://github.com/slotsys/CodeIgniter/
*/
class Template {
	private $CI;
	private $data			= array();
	private $template		= 'layouts/default';
	private $title			= '';
	private $desc			= '';
	private $style_block	= '';
	private $script_block	= '';
	private $links			= '';
	private $scripts		= '';
	private $contents		= '';

	/**
	 * Constructor
	 *
	 * Simply determines whether the template library exists.
	 *
	 */
	public function __construct(){
		$this->CI =& get_instance();
		log_message('debug', 'Template Class Initialized');
	}

	/**
	 * Set the variables
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @return	void
	 */
	public function set($key, $value = null){
		if( is_array($key) ){
			$this->data = $key;
		}else{
			$this->data[$key] = $value;
		}
	}

	/**
	 * Get the variables
	 *
	 * @access	public
	 * @param	string
	 * @return	array
	 */
	public function get($key = null){
		return $key === null ? $this->data : $this->data[$key];
	}

	/**
	 * Formats the source code
	 *
	 * @access	private
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	private function format($type = '', $source = ''){
		$script = '';
		if(trim($source) != ''){
			switch($type){
				case 'link':
				case 'script':
					$script .= trim($source);
				break;

				case 'style_block':
					$script .= "\n" . '<style type="text/css">' . "\n";
					$script .= trim($source);
					$script .= "\n" . '</style>' . "\n";
				break;

				case 'script_block':
					$script .= "\n" . '<script type="text/javascript">' . "\n" . '//<![CDATA[' . "\n";
					$script .= trim($source);
					$script .= "\n" . '//]]>' . "\n" . '</script>' . "\n";
				break;
			}
		}
		return $script;
	}

	/**
	 * Writes codes in memory
	 *
	 * @access	private
	 * @param	string
	 * @param	string
	 * @param	bolean
	 * @param	string
	 * @return	void
	 */
	private function set_source($type, $source = '', $inline = true, $after_source = -1){
		switch($type){
			case 'style_block':
			case 'script_block':
				if(isset($this->$type) && $source != '' && $inline === false){
					$after_source = $after_source === -1 ? "\n\n" . '/**--------------------------------------------**/' .  "\n\n" : $after_source;
					$after_source = (trim($source) != '' && $this->$type != '') ? $after_source : '' ;
					$this->$type .= ( $after_source . $source );
				}elseif(isset($this->$type) && $source != '' && $inline === true){
					echo $this->format($type, $source);
				}
			break;
		}
	}

	/**
	 * Convert array to attributes
	 *
	 * @access	private
	 * @param	array
	 * @param	array
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	private function parse_attributes($options, $default = array(), $insert_before = ' ', $insert_after = null){
		$options = array_merge($default, $options);
		$attributes = '';
		foreach ($options as $key => $value){
			if( ( $key == 'src' || $key == 'href' ) && strpos($value, '://') === false){
				$attributes .= $insert_before . $key . '="' . ( $this->CI->config->slash_item('base_url') . $value ) .'"' . $insert_after;
			}else{
				$attributes .= trim($value) === '' ? '' : ( $insert_before . $key . '="'. $value .'"' . $insert_after );
			}
		}
		return $attributes;
	}


	/**
	 * Set script block
	 *
	 * @access	public
	 * @param	string
	 * @param	bolean
	 * @param	string
	 * @return	void
	 */
	public function script_block($source, $inline = true, $after_source = -1){
		$this->set_source('script_block', $source, $inline, $after_source);
	}

	/**
	 * Set style block
	 *
	 * @access	public
	 * @param	string
	 * @param	bolean
	 * @param	string
	 * @return	void
	 */
	public function style_block($source, $inline = true, $after_source = -1){
		$this->set_source('style_block', $source, $inline, $after_source);
	}

	/**
	 * Set title
	 *
	 * @access	public
	 * @param	string
	 * @return	void
	 */
	public function set_title($title){
		$this->title = $title;
	}

	/**
	 * Set description
	 *
	 * @access	public
	 * @param	string
	 * @return	void
	 */
	public function set_desc($desc){
		$this->desc = $desc;
	}

	/**
	 * Set template
	 *
	 * @access	public
	 * @param	string
	 * @return	void
	 */
	public function set_template($template){
		$this->template = $template;
	}

	/**
	 * Search element by key
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	public function fetch($key = ''){
		$rtn = '';
		switch($key){
			case 'title':
				$rtn = $this->title;
			break;

			case 'desc':
				$rtn = $this->desc;
			break;

			case 'links':
				$rtn = "\r\n" . '<!-- ini Links -->' . "\n" . $this->format('link', $this->links) . "\n" . '<!-- end Links -->'. "\n";
			break;

			case 'styles':
				$rtn = "\r\n" . '<!-- ini Styles -->' . $this->format('style_block', $this->style_block) . '<!-- end Styles -->' . "\n";
			break;

			case 'scripts':
				$rtn = "\r\n" . '<!-- ini Scripts -->' . "\n" . $this->format('script', $this->scripts) . $this->format('script_block', $this->script_block) . '<!-- end Scripts -->'. "\n";
			break;

			case 'contents':
				$rtn = $this->contents;
			break;
		}
		return $rtn;
	}

	/**
	 * Loads interface
	 *
	 * @access	public
	 * @param	string
	 * @param	array
	 * @param	bolean
	 * @param	string
	 * @return	void
	 */
	public function load($view = '' , $view_data = array(), $return = false, $template = -1){
		$view_data = is_array($view_data) ? $view_data : array();

		$this->set_template( ( $template !== -1 ? $template : $this->template ) );
		$this->set( array_merge($this->get(), $view_data) );

		$this->contents = $this->CI->load->view($view, $this->get(), true);

		if($this->template == ''){
			if($return){
				return $this->fetch('contents');
			}else{
				echo $this->fetch('contents');
			}
		}else{
			return $this->CI->load->view($this->template, $this->get(), $return);
		}
	}

	/**
	 * Generates a script tag
	 *
	 * @access	public
	 * @param	string
	 * @param	array
	 * @param	bolean
	 * @return	void
	 */
	public function script($src = '', $options = array(), $inline = true){
		$options = is_array($options) ? $options : array();
		if(is_array($src)){
			$options = array_merge( $href, $options );
		}else{
			$options['src'] = $src;
		}

		$attributes = $this->parse_attributes($options, array( 'src' => '', 'type' => 'text/javascript'), ' ', null);

		$out = sprintf('<script%s></script> ' , $attributes);

		if($inline === true){
			echo $out . "\n";
		}else{
			$this->scripts .= $out . "\n";
		}
		
	}

	/**
	 * Generates a css tag
	 *
	 * @access	public
	 * @param	string
	 * @param	array
	 * @param	bolean
	 * @return	void
	 */
	public function css($href = '', $options = array(), $inline = true){
		$this->link($href, array( 'href' => '', 'rel' => 'stylesheet', 'type' => 'text/css', 'media' => 'all' ), $inline);
	}

	/**
	 * Generates a link tag
	 *
	 * @access	public
	 * @param	string
	 * @param	array
	 * @param	bolean
	 * @return	void
	 */
	public function link($href = '', $options = array(), $inline = true){
		$options = is_array($options) ? $options : array();
		if(is_array($href)){
			$options = array_merge( $href, $options );
		}else{
			$options['href'] = $href;
		}

		$attributes = $this->parse_attributes($options, array( 'href' => ''), ' ', null);

		$out = sprintf('<link%s/> ', $attributes);

		if($inline === true){
			echo $out . "\n";
		}else{
			$this->links .= $out . "\n";
		}
	}
}

/* End of file Template.php */
/* Location: ./system/application/libraries/Template.php */