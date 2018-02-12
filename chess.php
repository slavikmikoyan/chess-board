<?php

/*
Make sure that chess->saveFile() "$fp = fopen($filePath, "w");" (131 Line) will have privilegues to create file
*/

class chess{
	
	const REPOSITORY_TYPE = 0; // "0" => save to file "1" => save to db
	const COORDINATE_SEPARATOR = '-';
	public $desk;
	private $message;
	

	function __construct(){

		$figures = [
			0 => 'pawn', //пешка
			1 => 'rook', //ладья
			2 => 'knight', //конь
			3 => 'bishop', //слон
			4 => 'queen', //королева
			5 => 'king', //король
		];

		$add_figure = $_GET['add_figure'];
		$remove_figure = $_GET['remove_figure'];

		$desk_alphabet = [
			'a','b','c','d','e','f','g','h'
		];
		$desk_nubmers = [
			'1','2','3','4','5','6','7','8'
		];

		$desk = [];

		foreach ($desk_alphabet as $key_char => $char) {
			foreach ($desk_nubmers as $key_number => $nuber) {
				$desk[$char][$nuber] = [
					'figure_id' => NULL
				];
			}
		}

		$this->desk = $desk;
		$this->read();
	}

	function addFigureToDesk($figure_id, $coordinate){

		$this->custom_message($coordinate, NULL, $figure_id, 0);

		$coordinate = $this->decodeCoordinate($coordinate);
		$coordinate1 = $coordinate[0];
		$coordinate2 = $coordinate[1];

		$this->desk[$coordinate1][$coordinate2]['figure_id'] = $figure_id;

		$this->save();
	}

	function removeFigureFromDesk($coordinate){

		$coordinate_decoded = $this->decodeCoordinate($coordinate);

		$coordinate1 = $coordinate_decoded[0];
		$coordinate2 = $coordinate_decoded[1];

		if($this->desk[$coordinate1][$coordinate2]['figure_id']){

			$this->desk[$coordinate1][$coordinate2]['figure_id'] = NULL;
			$this->custom_message($coordinate, NULL, NULL, 1);
		
		}
		
		
		$this->save();
	}

	function editFigureOnDesk($oldCoordinate, $newCoordinate){

		$this->custom_message($oldCoordinate, $newCoordinate, NULL, 2);

		$oldcoordinate = $this->decodeCoordinate($oldCoordinate);
		$newcoordinate = $this->decodeCoordinate($newCoordinate);

		$oldcoordinate1 = $oldcoordinate[0];
		$oldcoordinate2 = $oldcoordinate[1];
		$newcoordinate1 = $newcoordinate[0];
		$newcoordinate2 = $newcoordinate[1];

		$figure_id = $this->desk[$oldcoordinate1][$oldcoordinate2]['figure_id'];
		
		if(!is_null($figure_id)){
			$this->desk[$oldcoordinate1][$oldcoordinate2]['figure_id'] = NULL;
			$this->desk[$newcoordinate1][$newcoordinate2]['figure_id'] = $figure_id;
			
			$this->save();
		}

	}

	function decodeCoordinate($coordinate){
		return explode(self::COORDINATE_SEPARATOR, $coordinate);
	}

	function save(){

		if(self::REPOSITORY_TYPE === 0){
			$this->saveFile();
		}elseif(self::REPOSITORY_TYPE === 1){
			//
		}

		echo $this->message;
	}

	function read(){

		if(self::REPOSITORY_TYPE === 0){
			$this->readFile();
		}elseif(self::REPOSITORY_TYPE === 1){
			//
		}
	}

	function saveFile(){
		$objData = serialize($this->desk);
		$filePath = getcwd().DIRECTORY_SEPARATOR."data.txt";
		
	    $fp = fopen($filePath, "w"); 
	    fwrite($fp, $objData); 
	    fclose($fp);
	}

	function readFile(){
		$filePath = getcwd().DIRECTORY_SEPARATOR."data.txt";
		if (file_exists($filePath)){
		    $objData = file_get_contents($filePath);
		    $obj = unserialize($objData);           
		    if (!empty($obj)){
		    	$this->desk = $obj;
		    }
		}
	}

	function custom_message($coordinate1, $coordinate2, $figure_id, $type){

		switch ($type) {
			case 0: //added
				if($figure_id === 2){
					$this->message = 'Добавлена ладья на '.$coordinate1.'; ';
				}else{
					$this->message = 'Добавлена фигура на '.$coordinate1.'; ';
				}
				break;
			case 1: //removed
				$this->message = 'Фигура с '.$coordinate1.' удалена; ';
				break;
			case 2: //edited
				$this->message = 'Фигура с '.$coordinate1.' перемещена на '.$coordinate2.'; ';
				break;
			default:
				# code...
				break;
		}
	}

	function showDesk(){
		var_dump($this->desk);
	}
}

$chess = new chess;

// $chess->addFigureToDesk(2, 'a-1');
// $chess->removeFigureFromDesk('a-1');
// $chess->editFigureOnDesk('a-1', 'b-3');

$chess->showDesk();