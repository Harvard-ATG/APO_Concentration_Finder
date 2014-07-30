<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('link','',TRUE);
	}
	
	function search()
	{

		# Get Links
		$categoriesPOST = $this->input->post('categories');

		if ($categoriesPOST == '[]')
		{
			echo "";
			return;
		}
		
		$linksArray = $this->link->getLinksWithCategories($categoriesPOST);
		
		
		# Score Links
		$categories = json_decode($categoriesPOST);
		
		
		if (is_array($categories))
		{
			foreach($linksArray as $link)
			{
				$link->score = 0;
				foreach ($categories as $category)
				{
					foreach ($category as $key => $value)
					{
						if ($key == "Divisions")
						{
							foreach ($category->Divisions as $like)
							{
								if (!(strpos($link->academicDivisions,"\"" . $like . "\"")==FALSE))
									$link->score = $link->score + 1;
									
							}
							
						}
						elseif ($key == "Likes")
						{
							foreach ($category->Likes as $like)
							{
								if (!(strpos($link->learningAspirations,"\"" . $like . "\"")==FALSE))
									$link->score = $link->score + 1;
							}
							
						}
						elseif ($key == "Careers")
						{
							foreach ($category->Careers as $like)
							{
								if (!(strpos($link->futureAspirations,"\"" . $like . "\"")==FALSE))
									$link->score = $link->score + 1;
							}
						}

					}
				}
			}
		}
		
		# Sort Links
		$score = array();
		foreach ($linksArray as $link)
		{
			$score[$link->id] = $link->score;
		}
		array_multisort($score, SORT_DESC, $linksArray);
		
		# Return Links
		echo json_encode($linksArray);
	}	

	function getAcademicDivisions()
	{	
		$categories = $this->link->getAcademicDivisions();
		return $this->getUniqueCategoryList($categories);
	}
	
	function getLearningAspirations()
	{
		$categories = $this->link->getLearningAspirations();
		return $this->getUniqueCategoryList($categories);
	}
	
	function getFutureAspirations()
	{
		$categories = $this->link->getFutureAspirations();
		return $this->getUniqueCategoryList($categories);
	}

	function getUniqueCategoryList($responseList) 
	{
		$categoryArray = array();
		if (empty($responseList)) 
		{
			return $categoryArray;
		}

		foreach ($responseList as $response)
		{
			$responseElementArray = json_decode($response->category);
			if (empty($responseElementArray)) 
			{
				continue;
			}
			
			foreach ($responseElementArray as $element) 
			{
				$categoryArray[] = $element;
			}
		}
			
		$categoryArray = array_unique($categoryArray);
		natcasesort($categoryArray);

		return $categoryArray;
	}
	
	function index()
	{
		$data['academicDivisions'] = $this->getAcademicDivisions();
		$data['learningAspirations'] = $this->getLearningAspirations();
		$data['futureAspirations'] = $this->getFutureAspirations();
		
		$this->load->view('home_view', $data);
	}
}
?>
