<?php

	$arrayDict = array();
    $arraySecret = array();

    //reading and creating array from file start
    
    $array2 = explode("\n", file_get_contents('stdin.txt'));

    $insertInDict   = 0;
    $insertInSecret = 0;

    $skipStDict   = 0;
    $skipStSecret = 0;

    foreach ($array2 as $key=>$value)
    {

        if(strpos($value, "//dict") !== false)
        {
            $insertInDict = 1;
        }

        if(strpos($value, "//secret") !== false)
        {
            $insertInSecret = 1;
            $insertInDict = 0;
        }

        if($insertInDict == 1)
        {
            if($skipStDict != 0)
            {
                $value = trim($value);
                array_push($arrayDict,$value);
            }
            $skipStDict++;
        }

        if($insertInSecret == 1)
        {
            if($skipStSecret != 0)
            {
                $value = trim($value);
                array_push($arraySecret,$value);
            }
            $skipStSecret++;
        }

    }

    
    //reading and creating array from file end
        
    $arraySameLengthWords = array();
    $tempArrayMapping = array();
    $bigArray = array();
    $keyBindings = array();
    $arrayLettersCommon = array();
    
    
    //we take each secret sentance because there are no 2 sentences encoded the same
    foreach ($arraySecret as $secretSentance)
    {
    	$arrayWordsInSecretSentance = explode(" ",$secretSentance);
    	$hiddenSentance = "";
    	foreach ($arrayWordsInSecretSentance as $secretWord)
    	{
    		$arraySameLengthWords = array();
    		foreach ($arrayDict as $wordInDict)
    		{
    			//we verify the same length because the word "45161" will not be "is" or "bob"
    			if(strlen($wordInDict) == strlen($secretWord) )
    			{
    				array_push($arraySameLengthWords,$wordInDict);
    			}
    		}
    		
    		map($arraySameLengthWords,$arrayDict,$secretWord,$secretSentance);
    		
    	}
    	
    	$arrayLettersMapped2 = resolveMostCommonLetters($arrayLettersCommon);
    	constructSecretSentance($arrayLettersMapped2,$secretSentance);
    	
    	//the secret sentance is shown, now we will reset the arrays;
    	$arraySameLengthWords = array();
	    $tempArrayMapping = array();
	    $bigArray = array();
	    $keyBindings = array();
	    $arrayLettersCommon = array();
    	
    }
    
    ///////////////////////////////////////////////////////////
    //
    //	This function will construct the secret sentance from 
    //  the given mapping array and the secret sentance 
    //
    ///////////////////////////////////////////////////////////
    function constructSecretSentance($tmpArray3,$secretSentance)
    {
    	
    	$splittedSecretSentance = str_split($secretSentance);
    	$revealedSentance = "";
    	foreach ($splittedSecretSentance as $key=>$letter)
    	{
    		
    		if($letter == " ")
    		{
    			$revealedSentance .= " ";
    		}else{
    			$revealedSentance .= array_search($letter,$tmpArray3);	
    		}
    		
    	}
    	
    	print $secretSentance." = ".$revealedSentance."<br>";
    	
    }
    
    ///////////////////////////////////////////////////////////
    //
    //	This function makes eliminates duplicate mappings 
    //
    ///////////////////////////////////////////////////////////
    function resolveMostCommonLetters($arrayLettersCommon)
    {
    	foreach ($arrayLettersCommon as $key=>$string)
    	{
    		$string = rtrim($string, ",");
    		$string = str_replace(",,",",",$string);
    		$letter = returnMostCommonLetter($string);
    		$arrayLettersCommon[$key] = rtrim($letter, ",");
    	}
    	
    	foreach ($arrayLettersCommon as $key=>$string)
    	{
    		if(strpos($string, ',') === false)
    		{
    			foreach ($arrayLettersCommon as $key2=>$string2)
		    	{
		    		if(strpos($string2, ',') !== false)
		    		{
		    			$arrayLettersCommon[$key2] = ltrim(rtrim(str_replace($string,"",$string2),","),",");
		    		}
		    	}
    		}
    	}
    	
    	foreach ($arrayLettersCommon as $key=>$string)
    	{
    		if(strpos($string, ',') !== false || strpos($string, ',,') !== false )
    		{
    			$arrayLettersCommon = resolveMostCommonLetters($arrayLettersCommon);
	    	}  	
    	}
    	
    	return $arrayLettersCommon;
    }
    
    ///////////////////////////////////////
    //
    //   This function will return the most common letter in the string, 
    //   if the number of letters occurance is the same it will return 
    //   the unreplaced string
    //
    ///////////////////////////////////////
    function returnMostCommonLetter($string)
    {
    	$unreplaced = $string;
    	$unreplaced = str_replace(",,",",",$string);
    	$string = str_replace(",,",",",$string);	
    	$string = str_replace(",","",$string);	
    	$arrayLettersInString = str_split($string);
    	$arrayLetterOccurance = array();
    	
    	foreach ($arrayLettersInString as $letter) 
    	{
    		$occurance = substr_count($string, $letter);
    		$arrayLetterOccurance[$letter] = $occurance;
    	}
    	
    	$lastLetter = "";
    	$lastOccurance = 1;
    	foreach ($arrayLetterOccurance as $letter=>$occuranceNumber)
    	{
    		if($lastLetter != $letter)
    		{
    			$lastLetter = $letter;
    			if($lastOccurance <= $occuranceNumber)
    			{
    				$lastOccurance = $occuranceNumber;
    			}
    		}
    	}
    	
    	if($lastOccurance > 1)
    		return array_search($lastOccurance,$arrayLetterOccurance);
    	else 
    		return $unreplaced;
    }
    
    
    ///////////////////////////////////////
    //
    //   This function creates the first secretLetter - wordLetter mapping
    //
    ///////////////////////////////////////
    function map($arraySameLengthWords,$arrayDict,$secretWord,$secretSentance)
    {
    	
    	global $arrayLettersCommon;
    	$arraySecretLetters = str_split($secretWord);
    	
		$tempArrayMapping = array();
    		
		foreach ($arraySameLengthWords as $wordInDict)
		{
			$tmpArray = array();
			$tmpArray2 = array();
			foreach ($arraySecretLetters as $keyLetter=>$valueLetter)
			{
				if(!array_key_exists($wordInDict[$keyLetter],$tmpArray))
				{
					$tmpArray[$wordInDict[$keyLetter]] = $valueLetter;
				}
			}
			$tempWord =  constructWordFromSecret($tmpArray,$secretWord);
			if(in_array($tempWord,$arraySameLengthWords))
			{
				array_push($tempArrayMapping,$tmpArray);
			}
			
		}
				
		$arrayLettersCommon = getKeyBindings($tempArrayMapping);
		
	}
	
	
	
	function getKeyBindings($tempArrayMapping)
	{
		global $keyBindings;
		foreach ($tempArrayMapping as $individualArray)
		{
			foreach ($individualArray as $key=>$value)
			{
				
					$keyBindings[$key] .= $value.",";
				
			}
		}
		return $keyBindings;
	}
	
	
	///////////////////////////////////////
    //
    //   This function will return a word constructed from a mapping array and a secret word, 
    //   whether or not it's a correct word , we will verify the return    
    //   result where the function is called
    //
    ///////////////////////////////////////
	function constructWordFromSecret($tmpArray,$secretWord)
	{
		$splitSecretWord = str_split($secretWord);
		$secretWord = "";
		foreach ($splitSecretWord as $secretLetter)
		{
			
			$secretWord .= array_search($secretLetter,$tmpArray);
			
		}
		
		return $secretWord;
	}
    
	
    
	
	
?>