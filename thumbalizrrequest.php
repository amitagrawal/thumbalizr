<?
if(!defined('_THUMBALIZR'))
	die('no access');

class thumbalizrRequest {
	public $config;
	
	public function __construct($config) {
		if(!is_array($config))
			return false;
		
		$this->config = array(
			// Equivalent to $thumbalizr_config.
			'api_key' => (!$config['api_key']) ? '' : $config['api_key'],
			'service_url' => (!$config['service_url']) ? 'http://api.thumbalizr.com/' : $config['service_url'],
			'use_local_cache' => (!$config['use_local_cache']) ? true : $config['use_local_cache'],
			'local_cache_dir' => (!$config['local_cache_dir']) ? 'cache' : $config['local_cache_dir'],
			'local_cache_expire' => (!$config['local_cache_expire']) ? 300 : $config['local_cache_expire'],
			// Equivalent to $thumbalizr_defaults.
			'width' => (!$config['width']) ? 250 : $config['width'],
			'delay' => (!$config['delay']) ? 8 : $config['delay'],
			'encoding' => (!$config['encoding']) ? 'png' : $config['encoding'],
			'quality' => (!$config['quality']) ? 90 : $config['quality'],
			'bwidth' => (!$config['bwidth']) ? 1280 : $config['bwidth'],
			'bheight' => (!$config['bheight']) ? 1024 : $config['bheight'],
			'mode' => (!$config['mode']) ? 'screen' : $config['mode']
		);
	}
	private function build_request($url) {
		$this->local_cache_subdir = $this->config['local_cache_dir'] .'/'. substr(md5($url),0,2);
		$this->local_cache_file = $this->local_cache_subdir .'/'. md5($url) .'_'. $this->config['bwidth'] .'_'. $this->config['bheight'] .'_'. $this->config['delay'] .'_'. $this->config['quality'] .'_'. $this->config['width'] .'.'. $this->config['encoding'];
		$this->request_url = $this->config['service_url'] .'?'. http_build_query(array(
			'api_key' => $this->config['api_key'],
			'quality' => $this->config['quality'],
			'width' => $this->config['width'],
			'encoding' => $this->config['encoding'],
			'delay' => $this->config['delay'],
			'mode' => $this->config['mode'],
			'bwidth' => $this->config['bwidth'],
			'bheight' => $this->config['bheight'],
			'url' => $url,
		));
	}
	public function request($url) {
		$this->build_request($url);
		
		if(file_exists($this->local_cache_file)) {
			$cachetime = time() - filemtime($this->local_cache_file) - $this->config['local_cache_expire'];
		} else {
			$cachetime = -1;
		}
		
		if(!file_exists($this->local_cache_file) || $cachetime >= 0 ) {
			$headers = '';
			$this->img = @file_get_contents($this->request_url);
			
			foreach($http_response_header as $tmp) {
				if(strpos($tmp,'X-Thumbalizr-') !== false) {
					$tmp1 = explode('X-Thumbalizr-',$tmp);
					$tmp2 = explode(': ',$tmp1[1]);
					$headers[$tmp2[0]] = $tmp2[1];
				}
			}
			
			$this->headers = $headers;
			$this->save();
		} else {
			$this->img = @file_get_contents($this->local_cache_file);
			$this->headers['URL'] = $url;
			$this->headers['Status'] = 'LOCAL';
		}
	}
	private function save() {
		if($this->img && $this->config['use_local_cache'] === TRUE && $this->headers['Status'] == 'OK') {
			if(!file_exists($this->local_cache_subdir))
				mkdir($this->local_cache_subdir);
			
			$fp = fopen($this->local_cache_file,'w');
			fwrite($fp,$this->img);
			fclose($fp);
		}
	}
	public function output($sendHeader=true,$destroy=true) {
		if($this->img) {
			if($sendHeader) {
				if($this->config['encoding'] == 'jpg') {
					header('Content-type: image/jpeg');
				} else {
					header('Content-type: image/png');
				}
				
				foreach($this->headers as $key => $val) {
					header('X-Thumbalizr-'. $key .': '. $val);
				}
			}
			
			echo $this->img;
		} else {
			return false;
		}
	}
	public function __destruct() {
		if($destroy)
			$this->img = null;
		
		$this->config = null;
		$this->headers = null;
		$this->local_cache_subdir = null;
		$this->local_cache_file = null;
		$this->request_url = null;
	}
}
?>