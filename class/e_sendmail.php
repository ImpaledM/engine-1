<?php
class e_sendmail {
	var $headers;
	var $multipart;
	var $mime;
	var $html;
	var $text;
	var $kod;
	var $parts = array ();
	
	function __construct($headers = '') {
		$this->headers = $headers;
		$this->setCharSet ( 'utf-8' );
	}
	
	function setCharSet($kod) {
		$this->kod = $kod;
	}
	function addHtml($html = '') {
		$this->html .= html_entity_decode ( stripslashes($html) );
	}
	
	function addText($text = '') {
		$this->text .= $text;
	}
	
	private function buildHtml($orig_boundary) {
		$this->multipart .= "--$orig_boundary\n";
		(func_num_args () > 1) ? $content_type = 'text/html' : $content_type = 'text/plain';
		$this->multipart .= "Content-Type: " . $content_type . "; charset=\"$this->kod\"\n";
		$this->multipart .= "Content-Transfer-Encoding: 8bit\n\n";
		(func_num_args () > 1) ? $this->multipart .= "{$this->html}\n\n" : $this->multipart .= "{$this->text}\n\n";
	}
	
	function addAttach($path = '', $name = '', $c_type = 'application/octet-stream') {
		if (! file_exists ( $path . $name ))
			return;
		$fp = fopen ( $path . $name, 'r' );
		if (! $fp) {
			print "File $path.$name couldn't be read.";
			return;
		}
		$file = fread ( $fp, filesize ( $path . $name ) );
		fclose ( $fp );
		$this->parts [] = array ("body" => $file, "name" => $name, "c_type" => $c_type );
	}
	
	function addFile($file, $name = 'file', $c_type = 'application/octet-stream') {
		if (trim ( $file ) == '')
			return;
		$this->parts [] = array ("body" => $file, "name" => $name, "c_type" => $c_type );
	}
	
	private function buildPart($i) {
		$message_part = "";
		$message_part .= "Content-Type: " . $this->parts [$i] ["c_type"];
		if ($this->parts [$i] ["name"] != "")
			$message_part .= "; name = \"" . $this->parts [$i] ["name"] . "\"\n";
		else
			$message_part .= "\n";
		
		$message_part .= "Content-Transfer-Encoding: base64\n";
		$message_part .= "Content-Disposition: inline; filename = \"" . $this->parts [$i] ["name"] . "\"\n";
		$message_part .= "Content-ID: <" . str_replace(".","_",$this->parts[$i]["name"]) . ">\n\n";
		
		$message_part .= chunk_split ( base64_encode ( $this->parts [$i] ["body"] ), 76,"\n" ) . "\n";
		return $message_part;
	}
	
	private function buildMessage() {
		$boundary = "=_" . md5 ( uniqid ( time () ) );
		$this->headers .= "MIME-Version: 1.0\n";
		$this->headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\n";
		$this->multipart = '';
		$this->multipart .= "This is a MIME encoded message.\n\n";
		if ($this->html != '')
			$this->buildHtml ( $boundary, 'html' );
		if ($this->text != '')
			$this->buildHtml ( $boundary );
		for($i = (count ( $this->parts ) - 1); $i >= 0; $i --)
			$this->multipart .= "--$boundary\n" . $this->buildPart ( $i );
		$this->mime = "{$this->multipart}--$boundary--\n";
	}

	function send($to, $subject, $addHeader = '', $copy = true, $bcc = false) {
		$subject = html_entity_decode ( $subject );
		$this->buildMessage ();
		if ($addHeader == '')
			if (defined ( 'EMAIL_FROM' ))
				$addHeader = 'FROM:=?' . $this->kod . '?B?' . base64_encode ( EMAIL_FROM_TEXT ) . '?= <' . EMAIL_FROM . '>';
			else
				$addHeader = 'FROM: robot@' . DOMAIN_CLEAR;
		
		if (defined ( 'EMAIL_CC' ) && EMAIL_CC != '') {
			if ($copy)
				$addHeader .= "\nCC: " . EMAIL_CC;
			
			if ($bcc)
				$addHeader .= "\nBCC: " . EMAIL_CC;
		
		}
		if (DEBUG)
			$res = mail ( EMAIL_CC, "=?{$this->kod}?B?" . base64_encode ( $to.'->'.$subject ) . '?=', $this->mime, $addHeader . "\n" . $this->headers );
		else $res = mail ( $to, "=?{$this->kod}?B?" . base64_encode ( $subject ) . '?=', $this->mime, $addHeader . "\n" . $this->headers );
		return $res;
	}
}