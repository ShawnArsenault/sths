<?php 
function pageinfo_editor_roster($db,$teamid,$showDropdown=true){
	// $db = sqlite DB
	// $teamid is a teamid to use that teams roster.
	// $showDropdown is a flag if you want to toggle between teams.

	$id = "rostereditor";
	$confirmbanner = "";
	$sql = "";
	// If the Save Lines button has been clicked.
	if(isset($_POST["sbtRoster"])){
		// Create an array to organize the information
		// $arrSort[$table][$playerid][$status]
		// 		$table = Player or Goalie
		// 		$playerid = Player Number from PlayerInfo Table
		// 		$status = selected status for that game. 
		$arrSort = array();
		// Loop through the txtRoster array. txtRoster[$nextgame][] = Divider = LINE|LineType, Player = FirstName LastName| Number | PositionNumber | PositionString
		// Explode at the pipe | 
		// If the count of the explode is 2 then its a different line
		// Section.  Switch the vakue of what the status should be
		// $_POST["txtRoster"][$game][$status]
		// $game = int 1-10
		// $status = int 0-3
		foreach($_POST["txtRoster"] AS $statuses=>$status){
			foreach($status AS $s){
				$explodeValue = explode("|",$s);
				if(count($explodeValue) == 2){
					if($explodeValue[1] == "ProDress")$playerStatus = 3;
					elseif($explodeValue[1] == "ProScratch") $playerStatus = 2;
					elseif($explodeValue[1] == "FarmDress") $playerStatus = 1;
					else $playerStatus = 0;
				}else{
					$table = ($explodeValue[2] == 16) ? "Goaler" : "Player";
					$arrSort[$table][$explodeValue[1]]["Status". $statuses] = $playerStatus;
				}// End if count($explodeValue)
			} // End foreach $status
		} // End foreach $_POST["txtRoster"]

		// Loop through the arrSort variable to make 1 individual line of SQL
		// Per player to update the Status values in the DB.
		foreach($arrSort AS $table=>$player){
			foreach($player AS $number=>$statuses){
				$sql .= "UPDATE " . $table . "Info ";
				$sql .= "SET ";
				foreach($statuses AS $status=>$s){
					$sql .= $status . " = " . $s . ", ";
				}
				$sql = rtrim($sql,", ") . " ";
				$sql .= "WHERE Number = " . $number . ";";
			} // End foreach $player
		}// End foreach $arrSort
		//Update the database and save the lines.
		$db->exec("pragma journal_mode=memory;");
		$db->exec($sql);
		$confirmbanner = "<div class=\"confirm\">Lines have been saved.</div>";  
	} // End if isset($_POST["sbtRoster"])
	
	// Get the team selection form from the html API if needed
	if($showDropdown){
		html_form_teamid($db);
	} // End if there is a showDropdown flag

	// If there is a team ID to use
	if($teamid > 0){?>
		<div id="<?= $id ?>">
			<?php 
			echo $confirmbanner;
			$status = array();
			// Use the player_base SQL API to get the base information
			// loop for players and goalies
			// Add add your own order and query
			$sql = "";
			foreach(array("Player","Goaler") AS $type){
				$sql .= sql_players_base($type);
				$sql .= "WHERE Team = ". $teamid ." ";
				$sql .= "UNION ";
			}// End foreach array(Player,Goalie)

			$sql = rtrim($sql,"UNION ") . " ";
			$sql .= "ORDER BY PositionNumber, Overall DESC";
			$oRS = $db->query($sql);

			// Loop through queries result and add values to new array to display players and goalies
			while($row = $oRS->fetchArray()){
				// Loop s for each status on each player
				for($s=1;$s<=10;$s++){
					$status[$s][$row["Status".$s]][$row["Number"]]["Number"] = $row["Number"];
					$status[$s][$row["Status".$s]][$row["Number"]]["Name"] = $row["Name"];
					$status[$s][$row["Status".$s]][$row["Number"]]["Injury"] = $row["Injury"];
					$status[$s][$row["Status".$s]][$row["Number"]]["PositionString"] = $row["PositionString"];
					$status[$s][$row["Status".$s]][$row["Number"]]["PositionNumber"] = $row["PositionNumber"];
					$status[$s][$row["Status".$s]][$row["Number"]]["Status"] = $row["Status".$s];
					$status[$s][$row["Status".$s]][$row["Number"]]["Overall"] = $row["Overall"];
					$status[$s][$row["Status".$s]][$row["Number"]]["ForceWaiver"] = $row["ForceWaiver"];
				} // End for loop for statuses
			} // End while loop for players in result.
			
			// Create a next 10 games array to see the games both Pro and Farm will play.
			// Make the SQL for these 10 games.
			$nextgames = array();
			foreach(array("Pro","Farm") AS $league){
				$count = 0;
				$sql = "SELECT GameNumber, Day, VisitorTeamName, HomeTeamName, VisitorTeam, ";
				$sql .= "CASE WHEN VisitorTeam = ". $teamid ." THEN 'AT' ELSE 'VS' END AS AtVs, ";
				$sql .= "CASE WHEN VisitorTeam = ". $teamid ." THEN HomeTeamName ELSE VisitorTeamName END AS Opponent ";
				$sql .= "FROM Schedule" . $league . " ";
				$sql .= "WHERE VisitorTeam = ". $teamid ." AND Play = 'False' ";
				$sql .= "OR HomeTeam = ". $teamid ." AND Play = 'False' ";
				$sql .= "LIMIT 10 ";
				$RS = $db->query($sql);
				
				// Loop through next 10 games result to make an array of next games for both pro and farm
				while($row = $RS->fetchArray()){
					$nextgames[++$count][$league]["GameNumber"] = $row["GameNumber"];
					$nextgames[$count][$league]["Day"] = $row["Day"];
					$nextgames[$count][$league]["Opponent"] = $row["Opponent"];
					$nextgames[$count][$league]["AtVs"] = $row["AtVs"];
				} // End while for the schedule
			} // End foreach array(Pro,Farm)

			//Its possible that no schedule has been created yet. If this is the case, we don't need an accordion of rosters, just 1 using Status1.
			if(empty($nextgames)){
				foreach(array("Pro","Farm") AS $league){
					$nextgames[1][$league]["GameNumber"] = "";
					$nextgames[1][$league]["Day"] = "";
					$nextgames[1][$league]["Opponent"] = "";
					$nextgames[1][$league]["AtVs"] = "";
				}
			}

			// start the form to submit the roster.
			?>
			<form name="frmRosterEditor" method="POST" id="frmRoster">
				<?php 
					foreach(dbresult_roster_editor_fields($db,$teamid) AS $k=>$f){
						if(!is_numeric($k)){
							?><input type="hidden" id="<?= $k ?>" value="<?=strtolower($f); ?>"><?php 
							echo "\n";
						}
					}
				?>

				<!--<input type="button" id="change" value="Copy Roster 1 to other days." >-->
				<input id="saveroster" type="submit" name="sbtRoster" value="Save Rosters">
				<?php  
				// This accordion id is a JQuery accordion. If this ID changes then the JQuery has to be changed as well.
				?>
				<div id="accordionfuture">
					<?php 
					// Loop through the next games variable to get the lines for the next 10 games.
					foreach($nextgames AS $nextgame=>$games){?>
						<?php  //$accordionhead = ($games["Pro"]["Day"] != "") ? $nextgame . ". Pro Day " . $games["Pro"]["Day"] ." - " . $games["Pro"]["AtVs"] . " " . $games["Pro"]["Opponent"] ." | Farm: Day " . $games["Farm"]["Day"] . " - " . $games["Farm"]["AtVs"] . " " . $games["Farm"]["Opponent"] : "Currently No Schedule"; ?>
						<?php  $accordionhead = ($games["Pro"]["Day"] != "") ? "Next Game: Pro Day " . $games["Pro"]["Day"] ." - " . $games["Pro"]["AtVs"] . " " . $games["Pro"]["Opponent"] ." | Farm: Day " . $games["Farm"]["Day"] . " - " . $games["Farm"]["AtVs"] . " " . $games["Farm"]["Opponent"] : "Currently No Schedule"; ?>
						<h3><?= $accordionhead?> <span id="linevalidate<?=$nextgame;?>"></span></h3>
						<div>
							<div id="rostererror<?= $nextgame ?>" class="rostererror"></div>
							<div class="columnwrapper"><?php 
								for($x=3;$x>=0;$x--){
									if($x == 3){
										$type = "Pro Dress";	
									}elseif($x == 2){
										$type = "Pro Scratch";
									}elseif($x == 1){
										$type = "Farm Dress";
									}else{
										$type = "Farm Scratch";
									}
									$columnid = str_replace(" ","",$type);
									$colcount = 0;
									
									// the id in the ol will be one of #sortProDress, #sortProScratch, #sortFarmDress, #sortFarmScratch.
									// These id's are in the JQuery call to make the columns sortable via drag and drop. If the IDs change
									// the calls will have to change in the JQuery.
									?>
									<div class="col4">
										<ol id="sort<?= str_replace(" ","",$columnid)?>" class="sort<?= str_replace(" ","",$columnid) . $nextgame; ?> connectedSortable ui-sortable">
											<h4 class="columnheader"><?= $type?></h4>
											<input class="rosterline<?=$nextgame; ?>" type="hidden" name="txtRoster[<?=$nextgame; ?>][]" value="LINE|<?= $columnid; ?>">
											<?php  	
												// Checks to see if there are players in the current category.
												// example, if there is at least 1 player in the ProScratch category, loop through and display
												if(array_key_exists($x, $status[$nextgame])){
													foreach($status[$nextgame][$x] AS $sid=>$s){
														// Checks to see if a player is injured. if so, it will add an injury class
														// to the <li> which will not allow him to be part of the JQuery drag and drop
														// therefore unmovable. 
														$inj = ($s["Injury"] != "") ? " injury": "";
														
														// playerrow class is the class JQuery is looking for to allow the drag and drop process
														// if an <li> field has this, it can potentially be moved up and down the column.
														?>
														<li id="line<?=$nextgame . "_" . MakeCSSClass($s["Name"])?>"class="playerrow <?= $columnid . $inj; ?>">
															<div class="rowinfo">
																<?php 
																// Use a hidden field in the form to get the info to save to the SQLite DB.
																// The value of the hidden field is a string separated by pipes (|) to parse
																// on submit "fieldName|fieldNumber|positionNumber(1-16)|positionString(C,LW)"
																$value = $s["Name"] ."|";
																$value .= $s["Number"] ."|";
																$value .= $s["PositionNumber"]."|";
																$value .= $s["PositionString"] ."|";
																$value .= $s["Status"] . "|";
																$value .= $s["Overall"] . "|";
																$value .= strtolower($s["ForceWaiver"]) . "|";
																$value .= MakeCSSClass($s["Name"]);
																?>
																<input class="rosterline<?=$nextgame; ?> <?= "input".$columnid . $nextgame?>" id="g<?=$nextgame;?>t<?=$columnid;?><?= $colcount++;?>" type="hidden" name="txtRoster[<?=$nextgame; ?>][]" value="<?= $value; ?>">
																<div class="rowname"><?= $s["Name"]?></div><div class="rowinfoline"><?= $s["PositionString"]?> - <?= $s["Overall"]?>OV</div>
															</div>
														</li>
													<?php }
												}?>
										</ol>
									</div>
									<?php 

								}?>
							</div><!-- End .columnwrapper-->
						</div><!-- End classless/id-less div for the accordion--><?php 
					break;
					} // End foreach $nextgames?>
				</div><!-- End #accordion-->
			</form> <!-- End frmRostereditor -->
		</div><!-- End #rostereditor->$id --><?php 
	}// End if there is a teamid as a parameter
}
function pageinfo_editor_lines($db,$teamid=0,$league=false,$showDropdown=true){
	// $db = sqlite DB
	// $teamid is a teamid to use that teams roster.
	// $league is "Pro" or "Farm" based on selection.
	// $showDropdown is a flag if you want to toggle between teams.
	// Set the page id
	$id = "lineeditor";
	
	// Check to see if there is a team selected.
	if($teamid > 0){
		// Set the status value if the league is Pro or Farm
		$status = ($league == "Pro") ? 3: 1;
		// Select all the players and goalies if they are dressed.
		$sql = "SELECT Number, Name FROM PlayerInfo WHERE Team = " . $teamid . " AND Status1 = " . $status . " ";
		$sql .= "UNION ";
		$sql .= "SELECT Number, Name FROM GoalerInfo WHERE Team = " . $teamid . " AND Status1 = " . $status . " ";

		// Get the recordset of all the players
		$oRS = $db->query($sql);
		// Make an array of available players to use.
		// This makes comparing from a roster change easier.
		// i.e. Database could show a player in a position in the lines table, but if that
		// player was scratched, or moved between farm and pro, there has to be a way to
		// show he isn't there and show blank on the line. 
		$availableplayers = array();
		while($row = $oRS->fetchArray()){
			$availableplayers[MakeCSSClass($row["Name"])]["id"] = $row["Number"];
			$availableplayers[MakeCSSClass($row["Name"])]["Name"] = $row["Name"];
		}
	}// end if $teamid

	// If the updatelines submit button is clicked 
	if(isset($_POST["sbtUpdateLines"])){
		foreach($_POST["txtLine"] AS $line=>$name){
			// Update the Regular Lines table
			$db->exec("pragma journal_mode=memory;");
			$SQL = "UPDATE Team". $_REQUEST["League"] ."Lines SET " . $line . " = '" . sqlite_escape($name) . "' WHERE TeamNumber = " . $_REQUEST["TeamID"] . ";";
			$db->exec($SQL);
			// Update the Numbersonly Lines table.
			$SQL = "UPDATE Team". $_REQUEST["League"] ."LinesNumberOnly SET " . $line . " = '" . $availableplayers[MakeCSSClass($name)]["id"] . "' WHERE TeamNumber = " . $_REQUEST["TeamID"] . ";";
			$db->exec($SQL);
		}
	}// end isset $_POST[sbtUpdateLines]

	// Get the team selection form from the html API if needed ?>
	<div id="<?= $id ?>"><?php 
		if($showDropdown){
			html_form_teamid($db,true);
		} // End if there is a showDropdown flag

		// If there is a team selected
		if($teamid > 0 && $league){
			// Error block for updating feedback to the user.
			?><div id="errors"></div><?php 
			if($league == "Pro"){
				// Set Pro variables
				$status1 = 3;
				$isPro = true;
			}else{
				// Set Farm variables
				$status1 = 1;
				$isPro = false;
			}
			// get the fields needed for the ChangePlayer function onClick
			$dbfields = dbresult_line_editor_fields($db);
			$cpfields = "";
			foreach($dbfields AS $f){$cpfields .= strtolower($f) .",";}
			$cpfields = rtrim($cpfields,",");
			
			// Get all the players and goalies of the team who are dressed
			$sql = sql_players_base("Player",$isPro);
			$sql .= "WHERE Team = " . $teamid . " AND Status1 = " . $status1 . " ";
			$sql .= "UNION ";
			$sql .= sql_players_base("Goaler",$isPro);
			$sql .= "WHERE Team = " . $teamid . " AND Status1 = " . $status1 . " ";
			$sql .= "ORDER BY PositionNumber, Name ";
			?>
			<div class="playerlist">
				<form name="frmPlayerList">
					<select size="23" id="sltPlayerList">
						<?php 	// Loop through the players and add to the select list.
							$oRS = $db->query($sql);
							$first = true;
							while($row = $oRS->fetchArray()){
								//if its the first item in the loop, select the item as default.
								if($first){$s = " selected";$first = false;}else{$s = "";}
								// Separate Name and number with a pipe '|' to split in the javascript.
								?><option<?= $s?> value="<?= $row["Name"]?>|<?= $row["Number"]?>"><?= $row["Name"];?> - <?= $row["PositionString"];?> <?php 
							}?>
					</select><!--end sltPlayerList-->
				</form><!-- end frmPlayerList-->
			</div><!-- end playerlist-->
			<?php 
			// Select all the lines and players in them for the next game.
			$sql = "SELECT l.* FROM Team". $league ."Lines AS l LEFT JOIN Team". $league ."Info AS t ON t.Number = l.TeamNumber ";
			$sql .= "WHERE t.Number = '" . $teamid . "' AND Day = 1 ";
			$oRS = $db->query($sql);
			$row = $oRS->fetchArray();

			// Fill in arrays needed. 
			//		tabs = line pages, 
			//		blocks =  section per page, 
			//		positions = different position combination for the blocks, 
			//		strategy = strategy slider info.  

			$tabs = get_line_arrays("tabs");
			$blocks = get_line_arrays("blocks");
			$positions = get_line_arrays("positions");
			$strategy = get_line_arrays("strategy");
			$count = 0;
			?>
			
			<?php  // Start the tabs for pages of lines.?>
			<div class="linetabs">
				<div id="tabs">
					<ul>
						<?php  // loop through the tab names creating clickable tabs. ?>
						<?php  
						foreach($tabs AS $i=>$t){
							?><li class="tabitem"><a href="#tabs-<?= ++$count?>"><?= $t?></a></li><?php 
						}?>	
					</ul>
					<?php $count = 0;?>
					<form name="frmEditLines" method="POST" onload="checkCompleteLines();"><?php 
						// Loop through the tabs info making the lines pages.
						foreach($tabs AS $i=>$t){
							?><div id="tabs-<?= ++$count ?>" class="tabcontainer"><?php 
								if($i == "Forward" || $i == "Defense" || $i == "PP" || $i == "PK4" || $i == "4VS4" || $i == "PK3"){	
									// Each of the if'ed statements above have the same kind of info. 
									// display the blocks based on which tabbed page you are on.
									make_blocks($row,$blocks,$positions,$strategy,$i,$availableplayers,$cpfields,$league);
								}elseif($i == "Others"){?>
									<?php // Start with the goalies. ?>
									<div class="linesection <?= MakeCSSClass($i)?> goalies">
										<?php 
											foreach(array(1=>"Starting Goalie",2=>"Backup Goalie") AS $gid=>$g){?>
												<h4><?= $g?></h4>
												<div class="blockcontainer">
													<?php  $row["Goaler" . $gid] = (isset($availableplayers[MakeCSSClass($row["Goaler".$gid])])) ? $row["Goaler".$gid]: "";?>
													<div class="<?php  MakeCSSClass($g)?>"><?= "<input id=\"Goaler". $gid ."\" onclick=\"ChangePlayer('Goaler". $gid ."','". $league ."',".$cpfields.");\"  readonly type=\"text\" name=\"txtLine[Goaler". $gid ."]\" value=\"". $row["Goaler".$gid] ."\">";?></div>
												</div><?php 
											}
										?>
									</div><!-- end linesection <?= MakeCSSClass($i)?> goalies-->
									<?php 
									// Get the other page fields needed for the blocks.
									$field = get_line_arrays("otherfield");

									// Make the extra forwards and extra defense.
									foreach($field["start"] AS $fsid=>$fs){?>
										<div class="linesection <?= MakeCSSClass($i)?> extra <?= $fs?>">
											<h4>Extra <?= $fs?></h4>
											<div class="blockcontainer">
												<?php foreach($field["end"] AS $feid=>$fe){
													$usefield = "Extra" .$fsid . $fe;
													if(array_key_exists($usefield, $row)){?>
														<div class="positionline">
															<div class="positionlabel"><?= $fe?></div>
															<div class="positionname">
																<?php  $row[$usefield] = (isset($availableplayers[MakeCSSClass($row[$usefield])])) ? $row[$usefield] : "";?>
																<input id="<?= $usefield ?>" onclick="ChangePlayer('<?= $usefield ?>','<?= $league ?>',<?=$cpfields?>);" class="textname" readonly type="text" name="txtLine[<?= $usefield ?>]" value="<?= $row[$usefield] ?>">
															</div>
														</div><?php 
													}
												}?>
											</div><!--end blockcontainer -->
										</div><!-- end linesection <?= MakeCSSClass($i)?> extra <?= $fs?>--><?php 
									}?>
									<?php // Make the penalty shots order?>
									<div class="linesection <?= MakeCSSClass($i)?> penaltyshots">
										<h4>Penalty Shots</h4>
										<div class="blockcontainer">								
											<div class="penaltyshot">
												<?php  for($x=1;$x<6;$x++){?>
												<div class="positionline">
													<div class="positionname">
														<?php  $row["PenaltyShots" . $x] = (isset($availableplayers[MakeCSSClass($row["PenaltyShots" . $x])])) ? $row["PenaltyShots" . $x] : "";?>
														<input id="PenaltyShots<?= $x ?>" onclick="ChangePlayer('PenaltyShots<?= $x ?>','<?= $league ?>',<?=$cpfields?>);" class="textname" readonly type="text" name="txtLine[PenaltyShots<?= $x ?>]" value="<?= $row["PenaltyShots" . $x] ?>">
													</div>	
												</div>
												<?php }?>
											</div>
										</div><!-- end blockcontainer-->
									</div><!-- end linesection <?= MakeCSSClass($i)?> penaltyshots-->
									<?php 
								}else{
									// Make the Offsensive and Defensive Lines.
									$types = array("Off"=>"Offensive Line","Def"=>"Defensive Line");
									foreach($types AS $tid=>$t){?>
										<div class="linesection <?= MakeCSSClass($i)?> penaltyshots">
											<h4><?= $t?></h4>
											<div class="blockcontainer"><?php 
												$fordef = array("Forward", "Defense");
												foreach($fordef AS $fd){
													foreach($positions[$fd] AS $pid=>$pos){
														$usefield = "LastMin" . $tid . $fd . $pid;
														if(array_key_exists($usefield, $row)){
															?>
															<div class="positionline">
																<div class="positionlabel"><?= $pos?></div>
																<div class="positionname">
																	<?= "<input id=\"". $usefield ."\" onclick=\"ChangePlayer('". $usefield ."','". $league ."',".$cpfields.");\" class=\"textname\" readonly type=\"text\" name=\"txtLine[". $usefield ."]\" value=\"". $row[$usefield] ."\">";?>
																</div>
															</div><?php 
														}
													}
												}?>
											</div><!-- end blockcontainer-->
										</div><!-- end linesection <?= MakeCSSClass($i)?> penaltyshots--><?php 
									}
								}
							?></div><!-- end tabs-<?= $count ?>--><?php 
						}?>
						<input id="linesubmit" type="submit" value="Update Lines" name="sbtUpdateLines">
					</form>
				</div><!-- end tabs-->
			</div><!-- end linetabs--><?php 
		}// end if a team is selected?>
	</div><?php 
}
function make_blocks($row,$blocks,$positions,$strategy,$i,$availableplayers,$cpfields,$league){
	$bcount = 0;
	foreach($blocks[$i] AS $bid=>$block){?>
		<div class="linesection <?= MakeCSSClass($i)?> <?= MakeCSSClass($bid)?>">
			<h4><?= $block ?></h4>
			<div class="blockcontainer">
				<div class="positionwrapper">
					<?php 	// Depending on which page you are on sets up how many blocks are needed.
						// If its anything but 5vs5
						if($i == "PP" || $i == "PK4" || $i == "4VS4" || $i == "PK3"){
							if($bid == strtolower($i) . "line1" || $bid == strtolower($i) . "line2"){
								if($i == "PP"){
									$posit = $positions["Forward"];
								}elseif($i == "PK3"){
									$posit = $positions["Forward3"];
								}else{
									$posit = $positions["Forward4"];
								}
								$exp = explode("line",$bid);
								$field = "Line". $exp[1] ."". $i ."Forward";
							}else{
								$exp = explode("pair",$bid);
								$posit = $positions["Defense"];
								$field = "Line". $exp[1] ."". $i ."Defense";
							}
						// else its 5vs5
						}else{
							$field = "Line". ++$bcount ."5vs5" . $i;
							$posit = $positions[$i];
						}?>
					<?php foreach($posit AS $pid=>$pos){?>
						<div class="positionline">
							<div class="positionlabel"><?= $pos?></div>
							<div class="positionname">
								<?php  $row[$field . $pid] = (isset($availableplayers[MakeCSSClass($row[$field . $pid])])) ? $row[$field . $pid]: "";?>
								<?= "<input id=\"". $field . $pid ."\" onclick=\"ChangePlayer('". $field . $pid ."','". $league ."',".$cpfields.");\" class=\"textname\" readonly type=\"text\" name=\"txtLine[". $field . $pid ."]\" value=\"".  $row[$field . $pid] ."\">";?>
							</div>
						</div>
					<?php }?>
				</div><!-- end positionwrapper-->
				<div class="sliders">
					<div class="strategywrapper">
						<div class="strategy">
							<?php foreach($strategy AS $sid=>$strat){?>
								<div class="strats">
									<div class="stratlabel"><?= $sid?> : </div>
									<div class="stratvalue">
										<?php make_strategies($row,$field,$sid,true,$cpfields);?>
									</div>
								</div>
							<?php }?>
						</div><!-- end strategy-->
					</div><!-- end strategywrapper-->
					<div class="timewrapper">
						<div class="time">
							<div class="timelabel">Time % : </div>
							<div class="timevalue">
								<?php make_strategies($row,$field,"Time",false,$cpfields);?>
							</div>
						</div>
					</div><!-- end timerwrapper-->
				</div><!-- end sliders-->
			</div><!-- end blockcontainer-->
		</div><!-- end linesection <?= MakeCSSClass($i)?> <?= MakeCSSClass($bid)?>--><?php 
	}
}
function make_strategies($row,$field,$sid,$strat=true,$cpfields=""){?>
	<?php $use = ($strat) ? "Strat" : "Time";?>
	<?php $id = $field . $sid; ?>
	<input class="updown down" onclick="valChange('<?= $id ?>','<?= $use ?>','<?=$field?>','down',<?=$cpfields?>);" type="button" name="btnDown" value="">
	<input readonly size="1" id="<?= $id?>" class="stratval" type="text" name="txtStartegies[<?= $id ?>]" value="<?= $row[$id] ?>">
	<input class="updown up" onclick="valChange('<?= $id ?>','<?= $use ?>','<?=$field?>','up',<?=$cpfields?>);" type="button" name="btnUp" value="">
	<?php 
}
function get_line_arrays($type="blocks"){
	// This returns an array of needed information.
	$arr["tabs"] = array("Forward"=>"Forward","Defense"=>"Defense","PP"=>"PP","4VS4"=>"4vs4","PK4"=>"PK4","PK3"=>"PK3","Others"=>"Others","LastMin"=>"Last Min");
	$arr["blocks"]["Forward"] = array("line1"=>"Lines #1","line2"=>"Lines #2","line3"=>"Lines #3","line4"=>"Lines #4");
	$arr["blocks"]["Defense"] = array("pair1"=>"Pair #1","pair2"=>"Pair #2","pair3"=>"Pair #3","pair4"=>"Pair #4");
	$arr["blocks"]["PP"] = array("ppline1"=>"PP Lines #1","ppline2"=>"PP Lines #2","pppair1"=>"PP Pair #1","pppair2"=>"PP Pair #2");
	$arr["blocks"]["4VS4"] = array("4vs4line1"=>"4 vs 4 Lines #1","4vs4line2"=>"4 vs 4 Lines #2","4vs4pair1"=>"4 vs 4 Pair #1","4vs4pair2"=>"4 vs 4 Pair #2");
	$arr["blocks"]["PK4"] = array("pk4line1"=>"PK4 Lines #1","pk4line2"=>"PK4 Lines #2","pk4pair1"=>"PK4 Pair #1","pk4pair2"=>"PK4 Pair #2");
	$arr["blocks"]["PK3"] = array("pk3line1"=>"PK3 Lines #1","pk3line2"=>"PK3 Lines #2","pk3pair1"=>"PK3 Pair #1","pk3pair2"=>"PK3 Pair #2");

	$arr["positions"]["Forward"] = array("Center"=>"C","LeftWing"=>"LW","RightWing"=>"RW");
	$arr["positions"]["Forward3"] = array("Center"=>"F");
	$arr["positions"]["Forward4"] = array("Center"=>"C","Wing"=>"W");
	$arr["positions"]["Defense"] = array("Defense1"=>"LD","Defense2"=>"RD");
	$arr["strategy"] = array("Phy"=>"Phy","DF"=>"DF","OF"=>"OF");

	$arr["otherfield"]["start"] = array("Forward"=>"Forwards","Defense"=>"Defensemen");
	$arr["otherfield"]["end"] = array("N1","N2","N3","PP1","PP2","PK","PP","PK1","PK2");

	return $arr[$type];
}
?>
