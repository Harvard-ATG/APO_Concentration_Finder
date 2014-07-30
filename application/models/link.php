<?php
Class Link extends CI_Model
{

	/*
	"Array
(
    [0] => stdClass Object
        (
            [Health and Safety] => Array
                (
                    [0] => Immediate Crisis
                )

        )

    [1] => stdClass Object
        (
            [Residential Life] => Array
                (
                )

        )

)
1"
*/

    function returnAllLinks()
	{

		$sql = "SELECT * FROM links";			
		$query = $this->db->query($sql);

		return $query->result();
	}
	
	function returnAllLinksAsCSV()
	{
		$links = $this->returnAllLinks();

		$output = "";

		$rows_total = count($links);
		$columns_total = count((array)$links[0]);

		// Get Records from the table
		foreach ($links as &$link) {
			$linkArray = array_values((array)$link);
			for ($i = 0; $i<$columns_total; $i++)
			{
				if ($i != $columns_total - 1) {
					$output.= "\"" . $linkArray[$i]. "\"";
					$output.=",";
				}
				else {
					$output.="\"\"";
				}
			}
			$output.="\n";
		}

		return $output;
	}
	
	function getAcademicDivisions()
	{
		$sql = "SELECT DISTINCT `academicDivisions` AS category FROM links";
		$query = $this->db->query($sql);
		
		return $query->result();
	}
	
	function getLearningAspirations()
	{
		$sql = "SELECT DISTINCT `learningAspirations` AS category FROM links";
		$query = $this->db->query($sql);
		
		return $query->result();
	}
	
	function getFutureAspirations()
	{
		$sql = "SELECT DISTINCT `futureAspirations` AS category FROM links";
		$query = $this->db->query($sql);
		
		return $query->result();
	}
	
	function getLinksWithCategories($categories)
	{
		
		
		$categories = json_decode($categories);
		
		$queryString = '';
		
		if (is_array($categories))
		{
			foreach ($categories as $category)
			{
				foreach ($category as $key => $value)
				{
					if ($key == "Divisions")
					{
						foreach ($category->Divisions as $division)
						{
							if ($queryString == '')
							{
								$queryString = "(academicDivisions LIKE '%$division%')";
							}
							else
							{
								$queryString = $queryString . "AND (academicDivisions LIKE '%$division%')";
							}
						}
						
					}
					elseif ($key == "Likes")
					{
						foreach ($category->Likes as $like)
						{
							if ($queryString == '')
							{
								$queryString = "(learningAspirations LIKE '%$like%')";
							}
							else
							{
								$queryString = $queryString . "AND (learningAspirations LIKE '%$like%')";
							}
						}
						
					}
					elseif ($key == "Careers")
					{
						foreach ($category->Careers as $career)
						{
							if ($queryString == '')
							{
								$queryString = "(futureAspirations LIKE '%$career%')";
							}
							else
							{
								$queryString = $queryString . "AND (futureAspirations LIKE '%$career%')";
							}
						}
					}
				}		
			}
			
		}
				
		$sql = "SELECT * FROM links WHERE " . $queryString;		
		$query = $this->db->query($sql);
		
		#return $sql;
		return $query->result();
	}
}
?>
