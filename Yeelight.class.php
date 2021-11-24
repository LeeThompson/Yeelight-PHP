<?php
class Yeelight {
	private $jobs = array();

	public function __construct($ip, $port = 55443, $timeout = 30) {
		$this->ip = $ip;
		$this->port = $port;
		$this->delay = 100;
		$this->debug = false;
		$this->timeout = $timeout;
		if(!$this->verifyConnection()) throw new Exception("Failed connecting to Yeelight device.");
	}

	private function verifyConnection() {
		$this->fp = fsockopen($this->ip, $this->port, $this->errno, $this->errstr, $this->timeout);
		if(!$this->fp) return false;

		stream_set_blocking($this->fp, false);
		if($this->debug) print "[YEELIGHT] [DEBUG] [CONNECTED] To $this->ip:$this->port ($this->timeout)\n";
		return true;
	}

	private function getNextID() {
		if(!empty($this->jobs)) return count($this->jobs);
		else return 0;
	}
	
	public function setDelay($value = 100) {
		$this->delay = $value;
		if($this->delay < 50) $this->delay = 50;
		if($this->delay > 99999) $this->delay = 99999;
		return true;
	}
	
	public function setDebug($value = false) {
		if($value) $this->debug = true;
		else $this->debug = false;
		return true;
	}

	public function __call($method, $args) {
		$jObj = new stdClass;
		$jObj->id = $this->getNextID();
		$jObj->method = $method;
		$jObj->params = $args;
		$this->jobs[] = $jObj;
		return $this;
	}

	public function commit() {
		if (!$this->verifyConnection()) throw new Exception("Failed connecting to Yeelight device.");
		foreach($this->jobs as $job) {
			if(count($job->params[0]) > 1) $job->params = $job->params[0];
			$jStr = json_encode($job);
			if($this->debug) print "[YEELIGHT] [DEBUG] [COMMIT] $jStr\n";
			fwrite($this->fp, $jStr . "\r\n");
			fflush($this->fp);
			if($this->debug) print "[YEELIGHT] [DEBUG] [COMMIT] Sleeping for " . $this->delay * 1000 . " microseconds\n";
			usleep($this->delay * 1000);
			$out[] = fgets($this->fp);
		}
		$this->jobs = array();
		if(!empty($out)) return $out;
		else return true;
	}

	public function disconnect() {
		fclose($this->fp);
		if($this->debug) print "[YEELIGHT] [DEBUG] [DISCONNECTED] From $this->ip:$this->port\n";
	}
}
