<?php

require_once(dirname(__FILE__) . '/trie.php');

//----------------------------------------------------------------------------------------
// http://stackoverflow.com/a/5996888/9684
function translate_quoted($string) {
  $search  = array("\\t", "\\n", "\\r");
  $replace = array( "\t",  "\n",  "\r");
  return str_replace($search, $replace, $string);
}

//----------------------------------------------------------------------------------------

$filename = 'wikidata/countries.csv';

$trie = new Trie();
	
$headings = array();

$row_count = 0;

$file = @fopen($filename, "r") or die("couldn't open $filename");

		
$file_handle = fopen($filename, "r");
while (!feof($file_handle)) 
{
	$row = fgetcsv(
		$file_handle, 
		0, 
		translate_quoted(','),
		translate_quoted('"') 
		);
		
	$go = is_array($row);
	
	if ($go)
	{
		if ($row_count == 0)
		{
			$headings = $row;		
		}
		else
		{
			$obj = new stdclass;
		
			foreach ($row as $k => $v)
			{
				if ($v != '')
				{
					switch ($headings[$k])
					{
						// ensure coordinates are treated as numbers in JSON
						case 'longitude':
						case 'latitude':
							$obj->{$headings[$k]} = floatval($v);
							break;
					
						default:
							$obj->{$headings[$k]} = $v;
							break;							
					}
					
				}
			}
			$trie->add($obj);
		}
	}	
	$row_count++;
}

/*
$dot = $trie->toDot();
echo $dot;
*/

//echo serialize($trie);

$filename = 'trie.dat';
file_put_contents($filename, serialize($trie));



?>
