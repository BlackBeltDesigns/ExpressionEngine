<?php

class Security_test extends PHPUnit_Framework_TestCase {

	// -------------------------------------------------------------------- 
	
	public function setUp()
	{
		$this->security = load_class('Security', 'core');
	}

	// --------------------------------------------------------------------
	
	public function testNullStrings()
	{
		/* </script> voids anything above that do-while loop */
			
		$vectors = array(
				'<</script>?php text goes here :) ?'.'</script>>' => 
						'<[removed]?php text goes here :) ?[removed]>',
				'IE: <scr%00ipt>moveTo(0,0);</scr%00ipt>' =>
						'IE: [removed]moveTo(0,0);[removed]',
				'Webkit: <a href="javasc%00ript:moveTo(0,0);">test</a>' =>
						'Webkit: <a href="[removed]moveTo(0,0);">test</a>'
				
		);

		foreach ($vectors as $vector => $assertion)
		{
				$this->assertEquals($this->security->xss_clean($vector),
														$assertion);					
		}
	}		
	
	// --------------------------------------------------------------------
	
	public function testComplexWebkitEncodedString()
	{				
		$encoded = $this->_nullhex_encode();
		
		$urls = array(
			'Webkit(before): <a href="javas%00cript:'.$encoded.'">Google</a>' => "Webkit(before): <a location='http://www.google.com'>Google</a>",
			'Webkit: <a href="javas%00&#x00;cript:'.$encoded.'">Google</a>' => "Webkit: <a location='http://www.google.com'>Google</a>"
		);

		foreach ($urls as $url => $assertion)
		{
			$this->assertEquals($this->security->xss_clean($url),
					$assertion);					
		}						
	}
			
	// -------------------------------------------------------------------- 

	public function testNullStringsAttack()
	{				
		$vectors = array(
				'<label for="test" on%03mouseover="moveTo(0,0);resizeTo(3000,3000);">mouseover</label>' => 
						'<label for="test">mouseover</label>',
				'<a href="java%03script:moveTo(0,0);resizeTo(3000,3000);">webkit</a>'			=>
						'<a href="[removed]moveTo(0,0);resizeTo(3000,3000);">webkit</a>',
				'<label for="test" on&#00;mouseover="moveTo(0,0);resizeTo(3000,3000);">mouseover</label>'		=>
						'<label for="test">mouseover</label>',
				'<a href="java&#00;script:moveTo(0,0);resizeTo(3000,3000);">webkit</a>'			=>
						'<a href="[removed]moveTo(0,0);resizeTo(3000,3000);">webkit</a>',
				'<label for="test" on&#x00;mouseover="moveTo(0,0);resizeTo(3000,3000);">mouseover</label>'		=>
						'<label for="test">mouseover</label>',
				'<a href="java&#x00;script:moveTo(0,0);resizeTo(3000,3000);">webkit</a>'		=>
						'<a href="[removed]moveTo(0,0);resizeTo(3000,3000);">webkit</a>'						
		);

		foreach ($vectors as $el => $assertion)
		{
				$this->assertEquals($this->security->xss_clean($el),
						$assertion);					
		}
	}

	// -------------------------------------------------------------------- 
	
	public function testRecursiveHtmlTagNesting()
	{
		// UTF-7 : hex entities
		$utf7 = "+ADw-script+AD4-alert(+ACI-hey there+ACI)+ADw-/script+AD4-";
		$utf7b = base64_encode($utf7);
			
		$vectors = array(
				'<a title="<>>" href="data:text/html;charset=utf-7,'.$utf7.'">utf7</a>' => 
						'<a title="&lt;&gt;&gt;">utf7</a>',
				
				// Can't match to href=	 either...
				'<a title="href=<>>" href=data:text/html;charset=utf-7;base64,'.$utf7b.'>utf7b</a>'		=>
						'<a title="href=&lt;&gt;&gt;">utf7b</a>',
							
				// Add complexity - hooray for the pumping lemma
				'<a title="<<>href=>" rel=">" href=data:text/html;charset=utf-7;base64,'.$utf7b.'>utf7b<'				=> 
						'<a title="&lt;&lt;&gt;href=&gt;" rel="&gt;">utf7b<'
		);
			
		foreach ($vectors as $test => $assertion)
		{
				$this->assertEquals($this->security->xss_clean($test),
														$assertion);
		}
	}

	// --------------------------------------------------------------------
	
	public function testDataUrls()
	{
		// inspiration: http://www.mozilla.org/quality/networking/testing/datatests.html		

		// UTF-8 : Clean
		$utf8 = "data:text/html;charset=utf-8,%3cscript%3ealert(1);%3c/script%3e";
			
		// UTF-7 : Clean
		$utf7 = "data:text/html;charset=utf-7,+ADw-script+AD4-alert(+ACI-hey there+ACI)+ADw-/script+AD4-";

		// Base64 : Uncleaned
		$base64 = base64_encode('<script>alert("hey there")</script>');
			
		// Base64 UTF-7 : Uncleaned
		$base64_utf7 = base64_encode('+ADw-script+AD4-alert(+ACI-hey there+ACI)+ADw-/script+AD4-');
			
		$vectors = array(
				'<a href="data:text/html,'.$utf8.'">utf8</a>'	=> 
						'<a >utf8</a>',
				'<a href="data:text/html;charset=utf-7,'.$utf7.'">utf7</a>'		=>
						'<a >utf7</a>',
				'<a href="data:text/html;base64,'.$base64.'">base64</a>'		=>
						'<a >base64</a>',
				'<a href="data:text/html;charset=utf-7;base64,'.$base64_utf7.'">base64_utf7</a>' =>
						'<a >base64_utf7</a>'
		);

		foreach ($vectors as $attack => $assert)
		{
				$this->assertEquals($this->security->xss_clean($attack),
														$assert);
		}
	}

	// --------------------------------------------------------------------
	
	private function _nullhex_encode()
	{
			$str = 'alert(\'howdy ho!\');document.location=\'http://www.google.com\';';
			
			$encoded = '';
			
			for ($i = 0; $i < strlen($str); $i++)
			{
					$char = $str{$i};
					$encoded .= '%%00'.dechex(ord($char));
			}
			
			return $encoded;
	}

	// -------------------------------------------------------------------- 
	
	private function _replace_x($str)
	{
			/* Seperators any range from 00 - 08 */
			$url = '%03';
			$reg = '&#00;';
			$hex = '&#x00;';
			
			$return = array();
			$return = array_merge($return, str_replace('{x}', $url, $str));
			$return = array_merge($return, str_replace('{x}', $reg, $str));			
			$return = array_merge($return, str_replace('{x}', $hex, $str));
							
			return $return;
	}


	// -------------------------------------------------------------------- 
	
	
	
	
}