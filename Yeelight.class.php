<?php
class Yeelight {
	private $jobs = array();

	public function __construct($ip, $port = 55443, $timeout = 30, $delay = 100, $debug = false, $reuse = false) {
		$this->ip = $ip;
		$this->port = $port;
		$this->timeout = $timeout;
		$this->delay = $delay;
		$this->debug = $debug;
		$this->reuse = $reuse;
		$this->resetID();
		$this->errno = -1;
		$this->errstr = "";
		if (!$this->verifyConnection()) throw new Exception("Failed connecting to Yeelight device at $this->ip:$this->port\n$this->errno: $this->errstr\n");
	}

	private function verifyConnection() {
		if($this->fp) {
			if($this->debug) print "[YEELIGHT] [DEBUG] [VERIFYCONNECTION] Active connection to $this->ip:$this->port\n";
		} else {
			if($this->debug) print "[YEELIGHT] [DEBUG] [VERIFYCONNECTION] No connection, attempting to connect\n";
			$this->fp = fsockopen($this->ip, $this->port, $this->errno, $this->errstr, $this->timeout);
			if ($this->fp) {
				stream_set_blocking($this->fp, false);
				stream_set_timeout($this->fp, $this->timeout);
				if($this->debug) print "[YEELIGHT] [DEBUG] [VERIFYCONNECTION] Connected Established To $this->ip:$this->port ($this->timeout)\n";
			} else {
				if($this->debug) print "[YEELIGHT] [DEBUG] [VERIFYCONNECTION] Failed to connect to $this->ip:$this->port\nERROR: $this->errno: $this->errstr\n";
			}
		}
		if(!$this->fp) return false;
		return true;
	}

	private function getNextID() {
		$this->id++;
		return $this->id;
	}
	
	private function resetID() {
		$this->id = 1000;
		return true;
	}
	
	private function commandDelay() {
		if($this->debug) print "[YEELIGHT] [DEBUG] [COMMIT] Sleeping for " . $this->delay * 1000 . " microseconds\n";
		usleep($this->delay * 1000);
		return true;
	}
	
	public function setDelay($value = 100) {
		$this->delay = $value;
		if($this->delay < 50) $this->delay = 50;
		if($this->delay > 99999) $this->delay = 99999;
		if($this->debug) print "[YEELIGHT] [DEBUG] [SETDELAY] Delay set to $this->delay\n";
		return true;
	}
	
	public function setDebug($value = false) {
		if($value) $this->debug = true;
		else $this->debug = false;
		return true;
	}
	
	public function setReuse($value = false) {
		if($value) $this->reuse = true;
		else $this->reuse = false;
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
		if (!$this->verifyConnection()) throw new Exception("Failed connecting to Yeelight device at $this->ip:$this->port\n$this->errno: $this->errstr\n");
		$jobcount = count($this->jobs);
		$this->commandDelay();
		foreach($this->jobs as $job) {
			if(count($job->params[0]) > 1) $job->params = $job->params[0];
			$jStr = json_encode($job);
			if($this->debug) print "[YEELIGHT] [DEBUG] [COMMIT] $jStr\n";
			fflush($this->fp);
			fwrite($this->fp, $jStr . "\r\n");
			fflush($this->fp);
			$this->commandDelay();
			$out[] = fgets($this->fp);
		}
		$this->jobs = array();
		if (!$this->reuse) $this->disconnect();
		if($this->debug) print "[YEELIGHT] [DEBUG] [COMMIT] Sent $jobcount commands\n";
		if(!empty($out)) return $out;
		else return true;
	}

	public function disconnect() {
		if ($this->fp) fclose($this->fp);
		$this->fp = null;
		if($this->debug) print "[YEELIGHT] [DEBUG] [DISCONNECTED] From $this->ip:$this->port\n";
	}
}
