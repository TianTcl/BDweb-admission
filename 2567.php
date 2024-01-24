<?php
	$APP_RootDir = str_repeat("../", substr_count($_SERVER["PHP_SELF"], "/"));
	require($APP_RootDir."private/script/start/PHP.php");
	$header["title"] = "ระบบยืนยันสิทธิ์การเข้าศึกษาต่อ";

	if (false && !has_perm("dev")) {
		require($APP_RootDir."private/script/lib/TianTcl/various.php");
		$TCL -> http_response_code(909);
	}

	$admission = array(
		"year" => "2567",
		"link" => "/go?url=https%3A%2F%2Fbodin.ac.th%2Fhome%2F2024%2F0"
	);
?>
<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<?php require($APP_RootDir."private/block/core/heading.php"); require($APP_RootDir."private/script/start/CSS-JS.php"); ?>
		<style type="text/css">
			app[name=main] main .wrapper {
				margin: 25px 0 !important;
				display: flex; justify-content: space-evenly; flex-wrap: wrap; gap: 12.5px;
			}
			app[name=main] main .wrapper .card {
				margin: 0px 10px 10px;
				width: 275px; min-height: 300px;
				box-shadow: 1px 2px 10px -2.5px;
				border-radius: 1.25rem;
				display: flex; justify-content: space-between; flex-direction: column;
				transition: var(--time-tst-medium);
			}
			app[name=main] main .wrapper .card:hover, app[name=main] main .wrapper .card:focus-within {
				transform: translateY(-1.25px);
				transition: var(--time-tst-fast);
			}
			app[name=main] main .wrapper .card .info > * { margin: 0px 0px 10px; }
			app[name=main] main .wrapper .card .action a {
				width: calc(100% - 22px);
				justify-content: center;
			}
			/* app[name=main] main .wrapper .card:hover .action a, app[name=main] main .wrapper .card:focus-within .action a { font-size: calc(1em + 1.25px); } */
			app[name=main] main .wrapper .card .action a:not(:last-child) { margin-bottom: 5px; }
			app[name=main] main .wrapper ~ h3 { font-weight: 500; }
			app[name=main] main ul li.label {
				margin: 2.5px 0px 1.25px;
				line-height: 1.25;
				list-style-type: none;
			}
			app[name=main] main ul li:not(.label) { line-height: 1.12; }
			@media only screen and (max-width: 768px) {
				app[name=main] main .wrapper { justify-content: center; }
				app[name=main] main .wrapper .card { width: 275px; min-height: 150px; }
			}
		</style>
		<script type="text/javascript">
			const TRANSLATION = "e+enroll";
			$(document).ready(function() {
				page.init();
				Spotlight_background.init();
			});
			const page = (function(d) {
				const cv = { API_URL: AppConfig.APIbase + "" };
				var sv = {inited: false};
				var initialize = function() {
					if (sv.inited) return;

					sv.inited = true;
				};
				var myFunction = function() {

				};
				return {
					init: initialize,
					myFunction
				};
			}(document));
		</script>
		<script type="text/javascript" src="<?=$APP_CONST["baseURL"]?>_resx/static/script/core/spotlight-background.js"></script>
	</head>
	<body>
		<app name="main">
			<?php require($APP_RootDir."private/block/core/top-panel/enroll.php"); ?>
			<main class="bg-rainbow-img">
				<section class="container">
					<h2>การเข้าศึกษาต่อโรงเรียนบดินทรเดชา (สิงห์ สิงหเสนี)</h2>
					<p><span class="ref-00001">ติดตามข่าว</span><a target="_blank" href="/go?url=https%3A%2F%2Fbodin.ac.th%2Fhome%2Fcategory%2Fnewstudent-2567"><span class="ref-00002">การเข้าศึกษาต่อโรงเรียนบดินทรเดชา (สิงห์ สิงหเสนี) ในปีการศึกษา</span> <?=$admission["year"]?></a> <span class="ref-00003">ได้ที่นี่</span></p>
					<div class="wrapper">
						<div class="card message purple">
							<div class="info">
								<h3>นักเรียนใหม่</h3>
								<p><span class="ref-00004">นักเรียนที่สอบเข้ามาใหม่ และมีลำดับที่ใน</span><a href="#/announcement/new-student">การประกาศผล</a><span class="ref-00005">ประจำปีการศึกษา</span> <?=$admission["year"]?></p>
							</div>
							<div class="action">
								<a href="new" role="button" class="pill ripple-click"><span class="text">ดำเนินการ</span></a>
							</div>
						</div>
						<div class="card message blue">
							<div class="info">
								<h3>นักเรียนเดิม</h3>
								<p><span class="ref-00006">นักเรียนที่จบจากชั้นมัธยมศึกษาปีที่ 3 โรงเรียนบดินทรเดชา (สิงห์ สิงหเสนี) ที่มีลำดับที่ใน</span><a href="#/announcement/current-student">รายชื่อผู้มีสิทธิ์เข้าศึกษาต่อ</a><span class="ref-00007">ชั้นมัธยมศึกษาปีที่ 4 โรงเรียนบดินทรเดชา (สิงห์ สิงหเสนี)</span></p>
							</div>
							<div class="action">
								<a disabled <?=isset($_SESSION["auth"]) ? 'href="M4/"' : 'onClick="sys.auth.orize(\'e%2Fenroll%2FM4%2F\')" href="javascript:"'?> role="button" class="pill ripple-click"><span class="text">ดำเนินการ</span></a>
							</div>
						</div>
					</div>
					<!-- <center class="message orange">ขณะนี้ยังไม่มีประกาศใดๆ กรุณาเข้ามาใหม่ภายหลัง</center>
					<center>.<br>.<br>.</center> -->
					<h3 class="ref-00008">ประกาศทั่วไป</h3>
					<ul>
						<li ><a href="2567/statistics">สถิติการสมัครเข้าศึกษาต่อ ณ โรงเรียนบดินทรเดชา (สิงห์ สิงหเสนี)</a></li>
						<!-- <li class="label"><span class="ref-00010">ประกาศสำคัญ (ปีการศึกษา</span> <?=$admission["year"]?>)</li>
						<li disabled data-release=""><a target="_blank" href="<?=$admission["link"]?>_%2F_____">การเปิดใช้งานบัญชีผู้ใช้งานเครือข่าย สำหรับนักเรียนใหม่</a></li>
						<li disabled data-release=""><a target="_blank" href="<?=$admission["link"]?>_%2F_____">กิจกรรมเตรียมความพร้อมความเป็นลูกบดินทร</a></li>
						<li class="label ref-00011">ผลนักเรียนที่ผ่านการคัดเลือก (เข้าโครงการ)</li>
						<li disabled data-release="__-__"><a target="_blank" href="<?=$admission["link"]?>_%2F_____"><b>วิทยาศาสตร์พลังสิบ</b>ชั้นมัธยมศึกษา<b>ตอนต้น</b></a></li>
						<li class="label ref-00012">ข่าวสำหรับนักเรียนชั้นมัธยมศึกษาตอนปลาย</li>
						<li disabled data-release="__-__"><a target="_blank" href="<?=$admission["link"]?>_%2F_____">ผลการจัด<b>กลุ่มการเรียน</b>นักเรียนชั้นมัธยมศึกษาปีที่ 3 ที่มีสิทธิ์เข้าเรียนต่อชั้นมัธยมศึกษาปีที่ <b>4</b></a></li>
						<li disabled data-release="__-__"><a target="_blank" href="<?=$admission["link"]?>_%2F_____">ผลการจัด<b>กลุ่มการเรียน</b>นักเรียนชั้นมัธยมศึกษาปีที่ 4 ที่<b>สอบคัดเลือกเข้า</b></a></li> -->
					</ul>
					<h3 class="ref-00009">ประกาศผลรายชื่อผู้มีสิทธิ์เข้าศึกษาต่อโรงเรียนบดินทรเดชา (สิงห์ สิงหเสนี)</h3>
					<ul>
						<li class="label ref-00013" id="/announcement/new-student">ประกาศผลนักเรียนที่ผ่านการคัดเลือก (สอบเข้าใหม่)</li>
						<li disabled data-release="__-__"><a target="_blank" href="<?=$admission["link"]?>_%2F_____">ประเภท<b>ห้องเรียนปกติ</b>ชั้นมัธยมศึกษาปีที่ <b>1</b></a></li>
						<li disabled data-release="__-__"><a target="_blank" href="<?=$admission["link"]?>_%2F_____">ประเภท<b>ห้องเรียนปกติ</b>ชั้นมัธยมศึกษาปีที่ <b>1</b> (<b>ความสามารถพิเศษ</b>)</a></li>
						<li disabled data-release="__-__"><a target="_blank" href="<?=$admission["link"]?>_%2F_____">ประเภท<b>ห้องเรียนพิเศษ</b>ชั้นมัธยมศึกษาปีที่ <b>1</b></a></li>
						<li disabled data-release="__-__"><a target="_blank" href="<?=$admission["link"]?>_%2F_____">ประเภท<b>ห้องเรียนปกติ</b>ชั้นมัธยมศึกษาปีที่ <b>4</b></a></li>
						<li disabled data-release="__-__"><a target="_blank" href="<?=$admission["link"]?>_%2F_____">ประเภท<b>ห้องเรียนพิเศษ</b>ชั้นมัธยมศึกษาปีที่ <b>4</b></a></li>
						<li data-release="01-22"><a target="_blank" href="<?=$admission["link"]?>1%2F31238">ประเภท<b>ห้องเรียนพสวท. (สู่ความเป็นเลิศ)</b> ชั้นมัธยมศึกษาปีที่ <b>4</b></a></li>
						<!-- <li class="label ref-00014" id="/announcement/current-student">ประกาศผลนักเรียนชั้นมัธยมศึกษาปีที่ 3 ที่มีสิทธิ์เข้าเรียนต่อชั้นมัธยมศึกษาปีที่ 4 โรงเรียนเดิม</li>
						<li disabled data-release="__-__"><a target="_blank" href="<?=$admission["link"]?>_%2F_____">ประเภทห้องเรียนปกติ (<b>รอบ 1</b>)</a></li>
						<li disabled data-release="__-__"><a target="_blank" href="<?=$admission["link"]?>_%2F_____">ประเภทห้องเรียนปกติ (<b>รอบ 2</b>)</a></li>
						<li disabled data-release="__-__"><a target="_blank" href="<?=$admission["link"]?>_%2F_____">ประเภทห้องเรียนปกติ (<b>รอบ 3</b>)</a></li>
						<hr>
						<li class="label"><span class="ref-00015">การเรียกแทนผู้สละสิทธิ์ในปีการศึกษา</span> <?=$admission["year"]?></li>
						<li disabled data-release="__-__"><a target="_blank" href="<?=$admission["link"]?>_%2F_____">ประเภท<b>ห้องเรียนพสวท. (สู่ความเป็นเลิศ)</b> รอบที่ <b>1</b></a></li> -->
					</ul>
					<center class="message black"><span class="ref-00016">ศึกษารายระเอียดทั้งหมดที่</span> <a target="_blank" href="/go?url=https%3A%2F%2Fbodin.ac.th%2Fhome%2Fadmission">งานรับนักเรียน</a><hr><a target="_blank" href="/go?url=https%3A%2F%2Fbodin.ac.th%2Fhome%2Fcostume">เครื่องแบบและระเบียบการแต่งกาย</a><span class="ref-00017">โรงเรียนบดินทรเดชา (สิงห์ สิงหเสนี)</span></center>
				</section>
			</main>
			<?php
				$resourcePath["navtab"] = "private/block/core/side-panel/enroll.php";
				require($APP_RootDir."private/block/core/material/main.php");
			?>
		</app>
	</body>
</html>