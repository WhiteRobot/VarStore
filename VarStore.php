<?php


/*
Robust variable storage for PHP via xml and xpath.
*/

Class VarStore {
	
	private $varXML_location = null;
	private $varXML = null;
	private $varBase = './system/';
	
	
	
	function loadUp($context = 'global')
	{
		
		if($context == 'global')
		{
			$this->varXML_location = $this->varBase.'variables.xml';
		}
		elseif($context == 'session')
		{
			session_start();
			$salt = getVar('sessionsalt', false, 'global');
			
			if(!$salt)
			{
				$salt = mt_rand();
				setVar('sessionsalt', $salt, 'global');
			}
			
			$this->varXML_location = $this->varBase.'variables-sess'.md5($salt.session_id()).'.xml';
		}
		else
		{
			$this->varXML_location = $this->varBase.'variables-user'.md5($context).'.xml';
		}
		
		$this->varXML = new DOMDocument('1.0', 'utf-8');
		
		if(!file_exists($this->varXML_location))
		{
			$root = $this->varXML->createElement('variables');
			$this->varXML->appendChild($root);
			$this->varXML->save($this->varXML_location);
		}
		
		$this->varXML->load($this->varXML_location);
		
	}//end loadUp
	
	
	
	//saves a new variable.
	function setVar($var, $value, $context = 'global')
	{
		loadUp($context);
		
		$root = $this->varXML->documentElement;
		
		$xpath = new DOMXPath($this->varXML);
		
		
		$new_variable = $this->varXML->createElement('variable', xmlentities(serialize($value)));
		$new_variable->setAttribute('name', xmlentities($var));
		
		
		if($node = $xpath->query('//variable[@name="'.xmlentities($var).'"]', $root))
		{
			if(@$node->item(0)->textContent)
			{
				
				try
				{
					$root->replaceChild($new_variable, $node->item(0));
					
					@flock(fopen($this->varXML_location, 'c'), LOCK_EX);
					$success = $this->varXML->save($this->varXML_location);
					@fclose($this->varXML_location);
					
					//return our success in saving the variable
					return $success;
				}
				catch(Exception $e)
				{
					return false;
				}
				
			}
		}
		
		
		$root = $root->appendChild($new_variable);
		
		@flock(fopen($this->varXML_location, 'c'), LOCK_EX);
		$success = $this->varXML->save($this->varXML_location);
		@fclose($this->varXML_location);
		
		//return our success in saving the variable
		return $success;
		
	}//end setVar
	
	
	
	//retrieves a variable or returns the default value if it's not found.
	function getVar($var, $default, $context = 'global')
	{
		loadUp($context);
		
		$root = $this->varXML->documentElement;
		
		$xpath = new DOMXPath($this->varXML);
		
		$name = $var;
		
		
		if($node = $xpath->query('//variable[@name="'.xmlentities( $name ).'"]', $root))
		{
			if(@$node->item(0)->textContent)
			{
				//we return the first node found, a thought for the future is to allow a full query
				
				$val = unserialize($node->item(0)->textContent);
				
				
				return $val;
			}
		}
		
		//nothing found or there was an error, return the default provided
		return $default;
		
	}//end getVar
	
	
	
	//search for a variable or set of variables by name. returns an array of names
	function searchVar($query, $default, $context = 'global')
	{
		loadUp($context);
		
		$root = $this->varXML->documentElement;
		
		$xpath = new DOMXPath($this->varXML);
		
		
		$val = $default;
		
		//find a name if it contains the supplied text. 
		//matches should be used when xpath 2 is supported as it allows regex.
		if($nodes = $xpath->query('//variable[contains(@name,"'.xmlentities( $query ).'")]', $root))
		{
			//just in case the default wasn't an array.
			$val = array();
			
			for($a=0;$a<$nodes->length;$a++)
			{
				$val[] = $nodes->item($a)->getAttribute('name');
			}
		}
		
		return $val;
		
	}//end searchVar
	
	
	
	//appends data to an existing variable or creates it if it doesn't exist yet and returns success indicator.
	function appendVar($var, $value, $context = 'global')
	{
		return setVar($var, getVar($var, null, $context).$value, $context);
		
	}//end appendVar
	
	
	
	//deletes a variable and returns whether successful or not.
	function dropVar($var, $context = 'global')
	{
		loadUp($context);
		
		$root = $this->varXML->documentElement;
		
		$xpath = new DOMXPath($this->varXML);
		
		if($node = $xpath->query('//variable[@name="'.xmlentities($var).'"]', $root))
		{
			if($node && $node->item(0))
			{
				try
				{
					$node->item(0)->parentNode->removeChild($node->item(0));
					
					@flock(fopen($this->varXML_location, 'c'), LOCK_EX);
					$success = $this->varXML->save($this->varXML_location);
					@fclose($this->varXML_location);
					
					//return our success in saving the variable
					return $success;
				}//end try
				catch(Exception $e)
				{
					return false;
				}//end catch
			}//end if
		}//end if
		
	}//end dropVar
	
	
	
	//helper function, just escapes entities that cause issues in XML
	//as per: http://www.w3.org/TR/REC-xml/#sec-predefined-ent
	function xmlentities($string)
	{
		return str_replace(array('&', '"', "'", '<', '>'), array('&amp;', '&quot;', '&apos;', '&lt;', '&gt;'), $string);
		
	}//end xmlentities


};//end class


//end