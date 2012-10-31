<?php if ( ! defined('BASEPATH')) exit('Acesso n&atilde;o permitido a este roteiro');

class Template {
	private $data			= array();

	private $template		= 'layouts/default';

	private $title			= '';

	private $desc			= '';

	private $style_block	= '';

	private $script_block	= '';

	private $links			= '';

	private $scripts		= '';

	private $contents		= '';

	private $CI;

	public function __construct(){ $this->CI =& get_instance(); }

	public function set($key, $value = null){ if( is_array($key) ){ $this->data = $key; }else{ $this->data[$key] = $value; } }

	public function get($key = null){ return $key === null ? $this->data : $this->data[$key]; }

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

	public function script_block($source, $inline = true, $after_source = -1){ $this->set_source('script_block', $source, $inline, $after_source); }

	public function style_block($source, $inline = true, $after_source = -1){ $this->set_source('style_block', $source, $inline, $after_source); }

	public function set_title($title){ $this->title = $title; }

	public function set_desc($desc){ $this->desc = $desc; }

	public function set_template($template){ $this->template = $template; }

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

	public function load($view = '' , $view_data = array(), $return = false, $template = -1){
		$view_data = is_array($view_data) ? $view_data : array();

		$this->set_template( ( $template !== -1 ? $template : $this->template ) );
		$this->set( array_merge($this->get(), $view_data) );

		$this->contents = $this->CI->load->view($view, $this->get(), true);

		if($this->template == ''){
			if($return){ return $this->fetch('contents'); }else{ echo $this->fetch('contents'); }
		}else{
			return $this->CI->load->view($this->template, $this->get(), $return);
		}
	}

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

	public function css($href = '', $options = array(), $inline = true){
		$this->link($href, array( 'href' => '', 'rel' => 'stylesheet', 'type' => 'text/css', 'media' => 'all' ), $inline);
	}

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