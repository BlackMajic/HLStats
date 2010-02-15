<?php
/**
 * Original development:
 * +
 * + HLStats - Real-time player and clan rankings and statistics for Half-Life
 * + http://sourceforge.net/projects/hlstats/
 * +
 * + Copyright (C) 2001  Simon Garner
 * +
 *
 * Additional development:
 * +
 * + UA HLStats Team
 * + http://www.unitedadmins.com
 * + 2004 - 2007
 * +
 *
 *
 * Current development:
 * +
 * + Johannes 'Banana' Keßler
 * + http://hlstats.sourceforge.net
 * + 2007 - 2010
 * +
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

class Chart {

	/**
	 * the current game
	 *
	 * @var string The game
	 */
	private $_game = false;

	/**
	 * the options for this class
	 */
	private $_option = array();

	/**
	 * the data object of pChart
	 */
	private $_pData = false;

	/**
	 * the pChart object
	 */
	private $_pChart = false;

	/**
	 * load up and set default values
	 */
	public function __construct($game) {

		if(!empty($game)) {
			$this->_game = $game;
		}
		else {
			new Exception("Game is missing for Players.class");
		}

		$this->setOption('width',660);
		$this->setOption('height',300);
		//$this->setOption('bgcolor','#ffffff');

		// set the current day value
		$this->setOption('curDate',date("Ymd"));
	}

	/**
	 * set a given option
	 *
	 * @param string $key The option name
	 * @param string $value The option value
	 *
	 * @return void
	 */
	public function setOption($key,$value) {
		if($key !== "" && $value !== "") {
			$this->_option[$key] = $value;
		}
	}

	/**
	 * either return the value or false
	 * @param string $key The param to get
	 * @return mixed The value or false
	 */
	public function getOption($key) {
		if(isset($this->_option[$key])) {
			return $this->_option[$key];
		}

		return false;
	}

	/**
	 * create the given chart
	 */
	public function getChart($mode,$extra=false) {
		$this->_loadClasses();

		$chart = false;

		switch($mode) {
			case 'playerActivity':
				$this->setOption('chartFile',"tmp/".$this->_game.'-playeractivity'."-".$this->_option['curDate'].".png");

				// check if we have already a picture
				// create one only once a day
				if(file_exists($this->_option['chartFile'])
					&& SHOW_DEBUG === false) {
					$chart = $this->_option['chartFile'];
				}
				else {
					// remove old charts
					$this->_cleanOldCharts($this->_game.'-playeractivity');
					$chart = $this->_getPlayerActivity();
				}
			break;

			case 'mostTimeOnline':
				$chart = $this->_mostTimeOnline();
			break;

			case 'playTimePerDay':
				if(empty($extra)) { return false; }
				$this->setOption('chartFile',"tmp/".$this->_game.'-playTimePerDay'.'-'.$extra.'-'.$this->_option['curDate'].".png");

				// check if we have already a picture
				// create one only once a day
				if(file_exists($this->_option['chartFile'])
					&& SHOW_DEBUG === false) {
					$chart = $this->_option['chartFile'];
				}
				else {
					// remove old charts
					$this->_cleanOldCharts($this->_game.'-playTimePerDay');
					$chart = $this->_getPlayerTimePerDay($extra);
				}
			break;

			default:
			//nothing
		}

		return $chart;
	}

	/**
	 * create the player time per day chart
	 * @todo
	 */
	private function _getPlayerTimePerDay($playerId) {
		if(!in_array('Player',get_declared_classes())) {
			require 'player.class.php';
		}
		$playerObj = new Player($playerId,false,$this->_game);
		$data = $playerObj->getPlaytimePerDayData();
	}

	/**
	 * get the chart for player activity
	 *
	 * @return the path to the image
	 */
	private function _getPlayerActivity() {
		if(!in_array('Players',get_declared_classes())) {
			require 'players.class.php';
		}
		$playersObj = new Players($this->_game);
		$data = $playersObj->getPlayerCountPerDay();

		$c = 0;
		$xLine = array();
		$connects = array();
		$disconnects = array();

		// we need only the count for each day
		foreach($data['connect'] as $d=>$e) {
			$connects[] = count($e);

			// this shows the date only every 5 days
			if($c % 5 == 0) { $xLine[] = $d; }
			else { $xLine[] = ''; }
			$c++;
		}

		// we need only the count for each day
		foreach($data['disconnect'] as $d=>$e) {
			$disconnects[] = count($e);
		}

		// add the connects
		$this->_pData->AddPoint($connects,'1');
		$this->_pData->AddSerie('1');
		$this->_pData->SetSerieName(l("Connects"),'1');

		// the dates for x axe
		$this->_pData->AddPoint($xLine,'x');
		$this->_pData->SetAbsciseLabelSerie("x");

		// add the disconnects
		$this->_pData->AddPoint($disconnects,'2');
		$this->_pData->AddSerie('2');
		$this->_pData->SetSerieName(l("Disconnects"),'2');


		$this->_pChart->setFontProperties("class/pchart/Fonts/tahoma.ttf",8);
		$this->_pChart->setGraphArea(50,30,$this->_option['width']-10,$this->_option['height']-70);
		$this->_pChart->drawFilledRoundedRectangle(3,3,$this->_option['width']-3,$this->_option['height']-3,5,240,240,240);
		$this->_pChart->drawGraphArea(255,255,255,TRUE);
		$this->_pChart->drawScale($this->_pData->GetData(),$this->_pData->GetDataDescription(),SCALE_NORMAL,150,150,150,TRUE,0,2);
		$this->_pChart->drawGrid(4,TRUE,230,230,230,50);

		// display only more the 3 days as a curve, otherwise as a bar
		if(count($xLine) >= 4) {
			// Draw the cubic curve graph
			$this->_pChart->drawCubicCurve($this->_pData->GetData(),$this->_pData->GetDataDescription());
		}
		else {
			// draw the bar graph
			$this->_pChart->drawBarGraph($this->_pData->GetData(),$this->_pData->GetDataDescription(),TRUE);
		}

		// Finish the graph
		//$this->_pChart->setFontProperties("class/pchart/Fonts/tahoma.ttf",8);
		$this->_pChart->drawLegend(10,$this->_option['height']-40,$this->_pData->GetDataDescription(),255,255,255);
		$this->_pChart->setFontProperties("class/pchart/Fonts/tahoma.ttf",10);
		$this->_pChart->drawTitle(0,20,l("Player activity"),50,50,50,$this->_option['width']);


		$this->_pChart->Render($this->_option['chartFile']);

		return $this->_option['chartFile'];
	}

	/**
	 * create the image for player activity
	 * @return array The path to the image
	 */
	private function _mostTimeOnline() {
		exit();

		if(!in_array('Players',get_declared_classes())) {
			require 'players.class.php';
		}
		$playersObj = new Players($this->_game);
		$data = $playersObj->getMostTimeOnline();


		$c = 0;
		$xLine = array();
		$connects = array();
		$disconnects = array();

		// we need only the count for each day
		foreach($data['connect'] as $d=>$e) {
			$connects[] = count($e);

			// this shows the date only every 5 days
			if($c % 5 == 0) { $xLine[] = $d; }
			else { $xLine[] = ''; }
			$c++;
		}

		// we need only the count for each day
		foreach($data['disconnect'] as $d=>$e) {
			$disconnects[] = count($e);
		}

		// add the connects
		$this->_pData->AddPoint($connects,'1');
		$this->_pData->AddSerie('1');
		$this->_pData->SetSerieName(l("Connects"),'1');

		// the dates for x axe
		$this->_pData->AddPoint($xLine,'x');
		$this->_pData->SetAbsciseLabelSerie("x");

		// add the disconnects
		$this->_pData->AddPoint($disconnects,'2');
		$this->_pData->AddSerie('2');
		$this->_pData->SetSerieName(l("Disconnects"),'2');



		$this->_pChart->setFontProperties("class/pchart/Fonts/tahoma.ttf",8);
		$this->_pChart->setGraphArea(50,30,$this->_option['width']-10,$this->_option['height']-70);
		$this->_pChart->drawFilledRoundedRectangle(3,3,$this->_option['width']-3,$this->_option['height']-3,5,240,240,240);
		$this->_pChart->drawGraphArea(255,255,255,TRUE);
		$this->_pChart->drawScale($this->_pData->GetData(),$this->_pData->GetDataDescription(),SCALE_NORMAL,150,150,150,TRUE,0,2);
		$this->_pChart->drawGrid(4,TRUE,230,230,230,50);

		#  // Draw the cubic curve graph
		$this->_pChart->drawCubicCurve($this->_pData->GetData(),$this->_pData->GetDataDescription());
		#
		#  // Finish the graph
		//$this->_pChart->setFontProperties("class/pchart/Fonts/tahoma.ttf",8);
		$this->_pChart->drawLegend(10,$this->_option['height']-40,$this->_pData->GetDataDescription(),255,255,255);
		$this->_pChart->setFontProperties("class/pchart/Fonts/tahoma.ttf",10);
		$this->_pChart->drawTitle(0,20,l("Player activity"),50,50,50,$this->_option['width']);

		return "tmp/".$this->_option['chartId'].".png";
	}

	/**
	 * load the required pChart classes
	 */
	private function _loadClasses() {
		require_once('class/pchart/pData.class.php');
		require_once('class/pchart/pChart.class.php');

		if($this->_pData == false) {
			$this->_pData = new pData();
		}

		if($this->_pChart == false) {
			$this->_pChart = new pChart($this->_option['width'],$this->_option['height']);
		}
	}

	/**
	 * cleans old chart data to avoid data waste
	 */
	private function _cleanOldCharts($name) {
		$data = glob('tmp/'.$name.'/*');
		if(!empty($data)) {
			foreach($data as $c) {
				unlink($c);
			}
		}
	}


}
?>
