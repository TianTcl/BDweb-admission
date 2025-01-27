<?php
	$APP_RootDir = str_repeat("../", substr_count($_SERVER["PHP_SELF"], "/"));
	require_once($APP_RootDir."private/script/start/API.php");
	API::initialize();
	$year = $_SESSION["stif"]["t_year"];
	require_once($APP_RootDir."public_html/e/enroll/api/_log-v1.php");
	// Execute
	if (!isset($_SESSION["auth"]) || $_SESSION["auth"]["type"] <> "s") API::errorMessage(1, "You are unauthorized."); else
	switch (API::$action) {
		case "get": {
			switch (API::$command) {
				case "status": {
					$getInfo = $APP_DB[5] -> query("SELECT a.choose,a.filetype,a.lastupdate AS time,a.ip,b.name,d.name AS new FROM admission_confirm a INNER JOIN admission_sgroup b ON a.type=b.code LEFT JOIN admission_change c ON a.stdid=c.stdid LEFT JOIN admission_sgroup d ON c.choose=d.code WHERE a.stdid=$APP_USER");
					$getHist = $APP_DB[5] -> query("SELECT 1 FROM admission_switch WHERE stdid=$APP_USER");
					if (!$getInfo || !$getHist) {
						API::errorMessage(3, "Unable to load data.");
						syslog_e(null, "admission", "swt", "getStatus", "", false, "", "InvalidQuery");
					} else if (!$getInfo -> num_rows) {
						API::errorMessage(1, "You are not allowed to perform this action for you are not on the list of those eligible to study.");
						syslog_e(null, "admission", "swt", "getStatus", "", false, "", "Empty");
					} else {
						$read = $getInfo -> fetch_array(MYSQLI_ASSOC);
						if ($read["choose"] == null) API::successState(array("chosen" => false));
						else API::successState(array(
							"chosen" => true,
							"choose" => $read["choose"] == ADMISSION_ANSWER_YES,
							"at" => array(
								"date" => date("d/m/Y", strtotime($read["time"])),
								"time" => date("H:i", strtotime($read["time"]))
							), "ip" => $read["ip"],
							"group" => $read["name"],
							"hasRecords" => $getHist -> num_rows > 0,
							"hasUploadedDocument" => ($read["choose"] == ADMISSION_ANSWER_NO && !empty($read["filetype"]))
						));
					}
				break; }
				case "history": {
					$get = $APP_DB[5] -> query("SELECT refID,prev,reason,time FROM admission_switch WHERE stdid=$APP_USER ORDER BY time DESC");
					if (!$get) {
						API::errorMessage(3, "Unable to load history.");
						syslog_e(null, "admission", "swt", "getHistory", "", false, "", "InvalidQuery");
					} else if (!$get -> num_rows) {
						API::errorMessage(1, "Sorry, but your changes history is currently not available.");
						syslog_e(null, "admission", "swt", "getHistory", "", false, "", "Empty");
					} else {
						$hist = array();
						while ($read = $get -> fetch_assoc()) array_push($hist, array(
							"reference" => switch_ref_encrypt($read["refID"]),
							"newChoice" => $read["prev"] == ADMISSION_ANSWER_NO,
							"hasMemorandum" => strlen($read["reason"]) > 0,
							"timestamp" => date("วันที่ d/m/Y เวลา H:i น.", strtotime($read["time"]))
						)); API::successState($hist);
					}
				break; }
				case "memorandum": {
					$refID = escapeSQL(switch_ref_decrypt(API::$attr));
					$get = $APP_DB[5] -> query("SELECT stdid,reason FROM admission_switch WHERE refID=$refID");
					if (!$get) {
						API::errorMessage(3, "Unable to get notes.");
						syslog_e(null, "admission", "swt", "getNote", "", false, "", "InvalidQuery");
					} else if (!$get -> num_rows) {
						API::errorMessage(1, "Memorandum not found.");
						syslog_e(null, "admission", "swt", "getNote", "", false, "", "Empty");
					} else {
						$read = $get -> fetch_array(MYSQLI_ASSOC);
						if ($read["stdid"] <> $APP_USER) {
							API::errorMessage(3, "You don't have permission to view this memorandum.");
							syslog_e(null, "admission", "swt", "getNote", "", false, "", "Unauthorized");
						} else API::successState($read["reason"]);
					}
				break; }
				default: API::errorMessage(1, "Invalid command"); break;
			}
		break; }
		case "answer": {
			switch (API::$command) {
				case "switch": {
					$get = $APP_DB[5] -> query("SELECT a.choose,a.filetype,b.shortname FROM admission_confirm a INNER JOIN admission_sgroup b ON a.type=b.code WHERE a.stdid=$APP_USER");
					if (!$get) {
						API::errorMessage(3, "Unable to load data.");
						syslog_e(null, "admission", "swt", "updateRights", "", false, "", "InvalidGetQuery");
					} else if (!$get -> num_rows) {
						API::errorMessage(1, "You are not allowed to perform this action for you are not on the list of those eligible to study.");
						syslog_e(null, "admission", "swt", "updateRights", "", false, "", "Empty");
					} else {
						$read = $get -> fetch_array(MYSQLI_ASSOC);
						if ($read["choose"] == null) {
							API::errorMessage(2, "You haven't choose any rights previously. You cannot perform this action.");
							syslog_e(null, "admission", "swt", "updateRights", "", false, "", "Unauthorized");
						} else {
							// Update and record
							$chose = $read["choose"];
							$choose = $chose == ADMISSION_ANSWER_YES ? ADMISSION_ANSWER_NO : ADMISSION_ANSWER_YES;
							$reason = escapeSQL(nl2br(htmlspecialchars(API::$attr["reason"])));
							if (API::$attr["school"] <> null) $reason = escapeSQL('เข้าศึกษาต่อ ณ <span data-title="'.API::$attr["school"]["ID"].'">โรงเรียน'.API::$attr["school"]["name"]."</span>").(strlen($reason) ? "<hr>" : "").$reason;
							$update = $APP_DB[5] -> query("UPDATE admission_confirm SET choose='$choose',ip='$USER_IP' WHERE stdid=$APP_USER");
							$success = $APP_DB[5] -> query("INSERT INTO admission_switch (stdid,prev,reason,ip) VALUE ($APP_USER,'$chose','$reason','$USER_IP')");
							if (!$update || !$success) {
								API::errorMessage(3, "Unable to update information. Please try again.");
								syslog_e(null, "admission", "swt", "updateRights", "$chose → $choose", false, "", "InvalidQuery");
							} else {
								$reference = switch_ref_encrypt($APP_DB[5] -> insert_id);
								API::successState(array(
									"token" => $reference,
									"signature" => date("วันที่ d/m/Y เวลา H:i น.")
								)); syslog_e(null, "admission", "swt", "updateRights", "$chose → $choose", true);
								if ($choose == ADMISSION_ANSWER_NO) {
									require($APP_RootDir."private/script/lib/TianTcl/LINE.php");
									$LINE -> setToken("3Iy4xiCuirfOo2BxvU5ruqTafbt2FAKYqUXliNlBhjf");
									$LINE -> notify("มีนักเรียนสละสิทธิ์\r\nประเภท: นักเรียนเดิม\r\nกลุ่ม: ".$read["shortname"]."\r\nเลขประจำตัว: $APP_USER\r\nชื่อ: ".$_SESSION["auth"]["name"]["th"]["a"]);
								}
							}
						}
					}
				break; }
				case "add_evi_file": {
					function try_upload_file($dir) {
						if (!isset($_FILES["usf"])) return false;
						global $APP_USER, $fileType;
						$target_dir = "../resource/upload/$dir/";
						$newFileName = "$APP_USER.$fileType"; $target_file = $target_dir.$newFileName;
						$uploadOk = ($_FILES["usf"]["size"] > 0 && $_FILES["usf"]["size"] <= 10240000); // 10 MB
						if (!in_array($fileType, array("png", "jpg", "jpeg", "gif", "heic", "pdf"))) $uploadOk = false;
						if ($uploadOk) {
							if (file_exists($target_file)) unlink($target_file);
							if (move_uploaded_file($_FILES["usf"]["tmp_name"], $target_file)) return true;
							else {
								API::errorMessage(1, "ไฟล์ที่นักเรียนเลือกมีคุณสมบัติไม่ตรงกับที่กำหนดไว้. กรุณาเลือกไฟล์ใหม่."); // Upload error
								slog($APP_USER, "admission", "swt", "addEviFile", $fileType, false, "", "UploadError");
							}
						} else {
							API::errorMessage(3, "เกิดข้อผิดพลาดในการอัปโหลดไฟล์ กรุณาลองใหม่อีกครั้ง."); // Ineligible file
							slog($APP_USER, "admission", "swt", "addEviFile", $fileType, false, "", "FileIneligible");
						} return false;
					} $name = "confirm"; $sqlTail = "a INNER JOIN admission_timerange b ON a.timerange=b.trid WHERE a.stdid=$APP_USER";
					$getchk = $APP_DB[5] -> query("SELECT a.choose, b.start, b.stop FROM admission_$name $sqlTail");
					$fileType = isset($_FILES["usf"]) ? strtolower(pathinfo(basename($_FILES["usf"]["name"]), PATHINFO_EXTENSION)) : "";
					if (!$getchk) {
						API::errorMessage(3, "เกิดข้อผิดพลาดในการตรวจสอบสิทธิ์ กรุณาลองใหม่อีกครั้ง"); // Error get
						syslog_e($APP_USER, "admission", "swt", "addEviFile", $fileType, false, "", "InvalidQueryG");
					} else if ($getchk -> num_rows == 1) {
						$readchk = $getchk -> fetch_array(MYSQLI_ASSOC);
						if (empty($readchk["choose"])) {
							API::errorMessage(2, "คุณยังไม่ได้ทำการใช้สิทธิ์"); // Not responded
							syslog_e($APP_USER, "admission", "swt", "addEviFile", $fileType, false, "", "Empty");
						} else if (false && !inTimerange($readchk["start"], $readchk["stop"])) {
							API::errorMessage(2, "ขณะนี้หมดเวลาในการยืนยันสิทธิ์ของนักเรียนแล้ว"); // Timeout
							syslog_e($APP_USER, "admission", "swt", "addEviFile", $fileType, false, "", "Timeout");
						} else if (!isset($_FILES["usf"])) {
							API::errorMessage(2, "นักเรียนไม่ได้เลือกไฟล์หลักฐานสำหรับการอัปโหลด. กรุณาลองใหม่อีกครั้ง"); // No file
							syslog_e($APP_USER, "admission", "swt", "addEviFile", $fileType, false, "", "NoFile");
						} else if (try_upload_file($name)) {
							$success = $APP_DB[5] -> query("UPDATE admission_$name SET filetype='$fileType',ip='$USER_IP' WHERE stdid=$APP_USER");
							if ($success) {
								API::successState();
								syslog_e($APP_USER, "admission", "swt", "addEviFile", $fileType, true);
							} else {
								API::errorMessage(3, "เกิดข้อผิดพลาดในการบันทึกข้อมูล กรุณาลองใหม่อีกครั้ง"); // Error record
								syslog_e($APP_USER, "admission", "swt", "addEviFile", $fileType, false, "", "InvalidQueryR");
							}
						}
					} else {
						API::errorMessage(1, "นักเรียนไม่มีสิทธิ์ในการเข้าศึกษาต่อ หรือมีมากกว่าหนึ่งสิทธิ์. หากเป็นข้อผิดพลาด กรุณาติดต่อผู้ดูแลระบบ."); // Invalid response
						syslog_e($APP_USER, "admission", "swt", "addEviFile", $fileType, false, "", "InvalidResponse");
					}
				break; }
				default: API::errorMessage(1, "Invalid command"); break;
			}
		break; }
		default: API::errorMessage(1, "Invalid type"); break;
	} $APP_DB[5] -> close();
	API::sendOutput();
?>