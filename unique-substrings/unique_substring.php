<?php
/////////////////////////////
//	author : Razvan AVRAM
//  date   : 24-12-2013
//  email  : rzv.avram@gmail.com 
/////////////////////////////



/////////////////////////////
// fscanf(STDIN,"%s",$stdinString); //line for facebook testing platform
/////////////////////////////

$stdinString = "abababababababababababababababababab";

$substringsArray = getSubstrings($stdinString);

function getSubstrings($stringValue){
	$substringArray = array();
	$strLength = strlen($stringValue);
	
	for($i=0; $i<$strLength; $i++)
	{
	    for($j=$i; $j<$strLength; $j++)
	    {
	    	$stringToSet = substr($stringValue, $i, ($j - $i) + 1);
	    	//print $stringToSet."<br>";
	    	if(!in_array($stringToSet,$substringArray))
	    	{
	        	array_push($substringArray,$stringToSet);
	    	}   
	    }   
	}   
	
	return $substringArray;
}

print count($substringsArray);



?>