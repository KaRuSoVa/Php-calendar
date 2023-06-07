<!-- Written By Gokhan Basturk -->

<?php

require_once('./utils/auth.php');
include "database.php";
require '../vendor/autoload.php';

$username=$_SESSION["username"];
$editby =$_SESSION["username"];


$sql = "SELECT id, title, description, start, end, color,file_path FROM events ";

$req = $auth->prepare($sql);
$req->execute();

$events = $req->fetchAll();

?>
<?php
try {
    $auth = new PDO('mysql:host=localhost;dbname=offer;charset=utf8', 'root', '');
}
catch(Exception $error) {
    die('Error : ' . $error->getMessage());
}

// Etkinlikleri veritabanından al
$sql = "SELECT * FROM events";
$stmt = $auth->prepare($sql);
$stmt->execute();
$events = $stmt->fetchAll();

// Hatırlatma süresi (3 saat)
$reminderTimeStart = time();
$reminderTimeEnd = strtotime("+3 hour");

// Hatırlatma mesajı için etkinlikleri kontrol et
$upcomingEvents = array();
foreach ($events as $event) {
  $startTime = strtotime($event['start']);

  // Etkinlik hatırlatma zamanını geçtiyse
  if ($startTime > $reminderTimeStart && $startTime <= $reminderTimeEnd) {
    $upcomingEvents[] = $event;
  }
}

// Hatırlatma mesajını oluştur
$reminderMessage = "";
if (count($upcomingEvents) > 0) {
    $reminderMessage .= "Événements à venir :\n";
    foreach ($upcomingEvents as $event) {
        $startTime = strtotime($event['start']);
        $timeRemaining = $startTime - time();
        $hoursRemaining = floor($timeRemaining / 3600);
        $minutesRemaining = floor(($timeRemaining % 3600) / 60);

        $timeString = $hoursRemaining . "h " . $minutesRemaining . "m";
        $reminderMessage .= $event['title'] . " commence dans " . $timeString . " !\n";
    }
} else {
    $reminderMessage .= "Aucun événement à venir.\n";
}

// Hatırlatma mesajını ekrana yazdır
?>
<script>
    const reminderMessage = `<?php echo $reminderMessage; ?>`;

    if (reminderMessage.trim() !== "Aucun événement à venir.") {
        window.addEventListener('DOMContentLoaded', () => {
            showAlert2(reminderMessage);
        });
    }

    function showAlert(message) {
        alert(message);
    }
</script>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="">
<meta name="author" content="">
<title>FullCalendar - JSON</title>

<!-- FullCalendar -->
<link href='css/fullcalendar.min.css' rel='stylesheet' />
<!-- Bootstrap Core CSS -->
<link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css'>
<script src='https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js'></script>
<script src='js/moment.min.js'></script>
<!-- jQuery Version 1.9.1 -->
<script src="https://code.jquery.com/jquery-1.9.1.min.js"></script>
<!-- FullCalendar -->
<script src='js/fullcalendar.min.js'></script>

	<!-- Bootstrap Core JavaScript -->
<script src='https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js'></script>



<!-- Custom CSS -->
<style>
#calendar {
	max-width: 1200px;
	margin-bottom: 30px;
}
.custom-alert {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 50%;
      top: 50%;
      transform: translate(-50%, -50%);
      background-color: white;
      border: 1px solid #ccc;
      padding: 20px;
      text-align: center;
      font-family: Arial, sans-serif;
      border-radius: 4px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .custom-alert .message {
      margin-bottom: 20px;
	  font-weight: bold;
    }
	
    .custom-alert button {
      background-color: #007bff;
      color: white;
      border: none;
      padding: 10px 20px;
      font-size: 16px;
      cursor: pointer;
      border-radius: 4px;
    }
.nocheckbox {
	display: none;
}
.label-on {
	border-radius: 3px;
	background: red;
	color: #ffffff;
	padding: 6px 10px;
	border: 1px solid red;
	display: table-cell;
}
.label-off {
	border-radius: 3px;
	background: white;
	border: 1px solid red;
	padding: 6px 10px;
	display: table-cell;
}

#recurrence-form, #monthly {
	display: none;
}

#calendar a.fc-event {
	color: #fff; /* bootstrap default styles make it black. undo */
	background-color: #0065A6;
}
.btn {
	margin-left: 10px!important;
}
@media (min-width: 576px) {
.modal-dialog {
    max-width: 550px;
}
}
.auto-height {
    overflow: hidden;
    resize: none;
}
.form-check {
  position: relative;
  display: block;
  padding-left: 0;
  
}

.form-check-input {
  position: absolute;
  margin-top: 0.25rem;
  margin-left: -1.25rem;
}
.modal-backdrop {
  z-index: 1040;
}
.modal {
  z-index: 1050;
}
.form-check-label {
  position: relative;
  display: block;
  padding-left: 1.25rem;
  margin-left:0px;
}

.form-check-inline {
  display: inline-block;
  margin-right: 1rem;
}
input[type=checkbox] {
  margin: 4px 0;
  line-height: 1.2;
  font-size: 16px;
  color: #555;
}

input[type=checkbox]:focus {
  outline: none;
}

input[type=checkbox]:checked + label {
  font-weight: bold;
  color: #00539b;
}

label.checkbox {
  display: inline-block;
  vertical-align: middle;
  margin: 0 0 0 8px;
  line-height: 1.2;
  font-size: 16px;
  color: #00539b;
}
input[type=file]
{
color:transparent;
}

input[type="file"]::before {
        content: "Choisir le dossier"; /* Dosya seçimi için özelleştirilmiş metin */
        display: inline-block;
        background: #007bff;
        color: #fff;
        padding: 8px 20px;
        border-radius: 4px;
        cursor: pointer;
        margin-right: 10px;
    }
    input[type="file"]::-webkit-file-upload-button {
        visibility: hidden;
		
    }
	#file-selected {
    display: inline-block;
    max-width: 250px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap; /* Bu özellik metnin tek satırda kalmasını sağlar */
    margin-left: 30px; /* İstediğiniz gibi ayarlayabilirsiniz */
}

</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.min.css"  />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.full.min.js"></script>
<script>
  $(document).ready(function() {
    // DateTimePicker'ı etkinleştir
    $('#start, #end').datetimepicker({
      format: 'Y-m-d H:i', // tarih ve saat formatı
      step: 30, // dakika adımı
      timepickerScrollbar: true, // kaydırma çubuğunu göster
      scrollMonth: false, // ay seçimi için kaydırma çubuğunu gizle
      scrollInput: false, // inputlarda kaydırma çubuğunu gizle
	  local: 'fr' // Dil ayarını Türkçe olarak belirle

    });
  });
</script>
</head>
<body>
<div id="deleteModal" class="modal fade"  role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete This File</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="location.reload();">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this file ?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" id="confirmDelete" >Delete</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
<div class="custom-alert" id="customAlert1">
    <div class="message" id="customAlertMessage1"></div>
    <button id="customAlertButton1">Close</button>
</div>

<div class="custom-alert" id="customAlert2">
    <div class="message" id="customAlertMessage2"></div>
    <button id="customAlertButton2">Close</button>
</div>

<script>
    const customAlert1 = document.getElementById('customAlert1');
    const customAlertMessage1 = document.getElementById('customAlertMessage1');
    const customAlertButton1 = document.getElementById('customAlertButton1');

    customAlertButton1.addEventListener('click', () => {
        customAlert1.style.display = 'none';
        location.reload();
    });

    function showAlert1(message) {
        customAlertMessage1.textContent = message;
        customAlert1.style.display = 'block';
    }

    const customAlert2 = document.getElementById('customAlert2');
    const customAlertMessage2 = document.getElementById('customAlertMessage2');
    const customAlertButton2 = document.getElementById('customAlertButton2');

    customAlertButton2.addEventListener('click', () => {
        customAlert2.style.display = 'none';
    });

    function showAlert2(message) {
        customAlertMessage2.textContent = message;
        customAlert2.style.display = 'block';
    }
</script>

<div id="deleteevent" class="modal fade"  role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete event File</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this event ?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" id="confirmDeleteevent" >Delete</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
<div class="custom-alert" id="customAlert3">
    <div class="message" id="customAlertMessage3"></div>
    <button id="customAlertButton3">Close</button>
</div>

<script>
    const customAlert3 = document.getElementById('customAlert3');
    const customAlertMessage3 = document.getElementById('customAlertMessage3');
    const customAlertButton3 = document.getElementById('customAlertButton3');

    customAlertButton3.addEventListener('click', () => {
        customAlert3.style.display = 'none';
    });

    function showAlert3(message) {
        customAlertMessage3.textContent = message;
        customAlert3.style.display = 'block';
    }
</script>
<!-- Page Content -->
<div class="container">

	<div class="row">
		<div class="col-lg-12 text-center">
		<div style="height:20px"></div>
			<div id="calendar" class="col-centered">
			</div>
		</div>
	</div>

	<!-- Add Modal -->
		<div class="modal fade" id="ModalAdd" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
				<form class="form-horizontal" method="POST" enctype="multipart/form-data" action="./core/add-event.php">
					<div class="modal-header">
						<h4 class="modal-title" id="myModalLabel">Add Event</h4>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					</div>
					<div class="modal-body">
						<div class="row">
							<div class="form-group">
								<label for="start" class="col-sm-12 control-label">Start date</label>
								<div class="col-sm-12">
									<input type="text" name="start" class="form-control" id="start" required>
								</div>
							</div>
							<div class="form-group">
								<label for="end" class="col-sm-12 control-label">End date</label>
								<div class="col-sm-12">
									<input type="text" name="end" class="form-control" id="end" required>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="form-group">
								<label for="title" class="col-sm-12 control-label">Title</label>
								<div class="col-sm-12">
									<input type="text" name="title" class="form-control" id="title" placeholder="Title" required>
								</div>
							</div>
							<div class="form-group">
								<label for="color1" class="col-sm-12 control-label">Color</label>
								<div class="col-sm-12">
								<input type="color" name="color" class="form-control" id="color" value="#0071c5" style="width:205px; height: 37px;">

								</div>
							</div>
						</div>
						<div class="row">
							<div class="form-group" style="width: 236px;">
								<label for="description" class="col-12 control-label">Description</label>
								<div class="col-12">
								<textarea name="description" class="form-control auto-height" id="description" placeholder="Description" required></textarea>
								</div>
								
							</div>
							<div class="form-group">
								<label for="user" class="col-12 control-label">User</label>
								<div class="col-12">
								<input type="text"  class="form-control" name="writtenby" value="<?php echo $username; ?>" readonly>
								</div>
							</div>
							<div class="form-group" style="padding-left: 15px;"style="width: 220px;">
								<label for="responsible" class="col-sm-12 control-label" style="padding-left: 0px;" >Responsible</label>
								<div class="form-control"  style="padding-left: 0px;" >

							<div class="col-sm-12" id="responsible_add" style="width: 193px;">
								<?php
									$sql = "SELECT * FROM users";
									$stmt = $auth->prepare($sql);
									$stmt->execute();
									$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
									foreach ($users as $user) {
									if ($user['username'] !== $username) {
									echo '<div class="form-check">';
									echo '<input type="checkbox" class="form-check-input" name="responsible[]" value="' . $user['username'] . '" id="responsible_add_' . $user['username'] . '"> ';
									echo '<label class="form-check-label" for="responsible_add_' . $user['username'] . '">' . $user['username'] . '</label>';
									echo '</div>';
										}
									}
							?>
							</div>
							</div>
							
							</div>
							<div class="row">
    <div class="form-group">
        <label for="file" class="col-sm-12 control-label" style="margin-left: 30px;">File</label>
        <div class="col-sm-12 custom-file-upload">
		<input type="file" name="file[]" class="form-control" id="file" accept=".jpg, .png, .pdf,.doc,.docx,.xlsx" onchange="displaySelectedFiles(this.files);" style="margin-left: 30px; width:185px;" multiple>            <div id="file-selected"></div>
        </div>
    </div>
</div>
<script>
    function displaySelectedFiles(files) {
        var selectedFiles = "";
        for (var i = 0; i < files.length; i++) {
            selectedFiles += files[i].name + "<br>";
        }
        document.getElementById("file-selected").innerHTML = selectedFiles;
    }
</script>
<script>
    $('#ModalEdit').on('hidden.bs.modal', function () {
        // Dosya seçimini sıfırla
        document.getElementById("file").value = "";
        document.getElementById("file-selected").innerHTML = "";
    });
</script>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						<button type="submit" class="btn btn-primary">Save</button>
					</div>
					</form>
				</div>
			</div>
		</div>
	<script>
	$('#recurrence-option .btn').change(function() {
		var val = $(this).find('input').val();
		switch (val) {
				case 'weekly':
					$('#monthly').hide();
					$('#weekly').show();
				break;
				case 'monthly':
					$('#weekly').hide();
					$('#monthly').show();
				break;
  		}
	});
	</script>
	<!-- Edit Modal -->
	<div class="modal fade" id="ModalEdit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
			<form class="form-horizontal" method="POST" action="core/editEventTitle.php" enctype="multipart/form-data">
				<div class="modal-header">
					<h4 class="modal-title" id="myModalLabel">Edit Event</h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				</div>
				<div class="modal-body">
				<div class="row">
				<div class="form-group">
	
						</div>
						</div>
					<div class="row">
						<div class="form-group">
							<label for="start" class="col-sm-12 control-label">Start date</label>
							<div class="col-sm-12">
								<input type="text" name="start" class="form-control" id="start" >
							</div>
						</div>
						<div class="form-group">
							<label for="end" class="col-sm-12 control-label">End date</label>
							<div class="col-sm-12">
								<input type="text" name="end" class="form-control" id="end">
							</div>
						</div>
					</div>
					<div class="row">
						<div class="form-group">
							<label for="title" class="col-sm-12 control-label">Title</label>
							<div class="col-sm-12">
							<input type="text" name="title" class="form-control" id="title" placeholder="Title">
							</div>
						</div>
						<div class="form-group">
    						<label for="color1" class="col-sm-12 control-label">Color</label>
    						<div class="col-sm-12">
        					<input type="color" name="color" class="form-control" id="color" value="#0071c5" style="width:205px; height: 37px;">
    						</div>
						</div>
					</div>
					<div class="row">
						<div class="form-group" style="width: 236px;">
							<label for="description" class="col-12 control-label">Description</label>
							<div class="col-12">
							<textarea name="description" class="form-control auto-height" id="description" placeholder="Description" required></textarea>
							</div>
						</div>
						<div class="form-group">
							<label for="user" class="col-12 control-label">User</label>
							<div class="col-12">
							<input type="text" class="form-control" name="writtenby" id="writtenby" value="" readonly>
							</div>
						</div>
						<div class="form-group" style="width: 220px;">
    <label for="responsible_edit" class="col-sm-12 control-label">Responsible</label>
    <div class="col-sm-12" id="responsible_edit">
        <!-- Kullanıcılar burada yerleştirilecek -->
    </div>
</div>
							<div class="form-group">
							<label for="user" class="col-12 control-label" style="padding-left: 30px;">Last Editor</label>
							<div class="col-12" style="padding-left: 30px;">
							<input type="text" class="form-control" name="updated_by" id="updated_by" value="" readonly>
							<input type="hidden" id="sessionEditBy" value="<?php echo $_SESSION['username']; ?>" />
							</div>
						</div>
						
							<div class="form-group"> 
								<div class="col-sm-12">
									
								</div>
								
							</div>
							
						</div>
					</div>
					<div class="form-group">
    <label for="file_path" style="margin-left: 15px;">File:</label>
    <div id="file_path" style="margin-left: 15px;"></div>
</div>
<div class="form-group">
    <label for="edit-file" class="col-sm-12 control-label" style="margin-left: 0px;">File</label>
    <div class="col-sm-12 custom-file-upload">
        <input type="file" name="edit-file[]" class="form-control" id="edit-file" accept=".jpg, .png, .pdf,.doc,.docx,.xlsx" onchange="displayEditSelectedFiles(this.files);" style="margin-left: 0px; width:185px;" multiple>
        <div id="edit-file-selected"></div>
	
</div><script>
    function displayEditSelectedFiles(files) {
        var selectedFiles = "";
        for (var i = 0; i < files.length; i++) {
            selectedFiles += files[i].name + "<br>";
        }
        document.getElementById("edit-file-selected").innerHTML = selectedFiles;
    }
</script>
<script>
    $('#ModalEdit').on('hidden.bs.modal', function () {
        // Dosya seçimini sıfırla
        document.getElementById("edit-file").value = "";
        document.getElementById("edit-file-selected").innerHTML = "";
    });
</script>

					<div id="editRecurrence">
						<div class="modal-header">
							<h5 class="modal-title" id="myModalLabel">Edit Recurrence</h5>
						</div>
						<div class="modal-body">
							<div class="row">
								<div class="form-group"> 
									<label class="col-sm-12 control-label">Delete Recurrence</label>
									<div class="col-sm-12">
<label class="deleteRecurrence label-off" for="delRec" id="deleteRecurrence">Delete</label>
<input class="nocheckbox" type="checkbox" id="delRec" name="deleteRecurrence" value="deleteRecurrence">									</div>
								</div>
							</div>
							<input type="hidden" name="id" class="form-control" id="id">
							<input type="hidden" name="rid" class="form-control" id="rid">
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						<button type="submit" class="btn btn-primary">Save</button>
						<button type="button" class="btn btn-danger" id="deleteEventBtn">Delete Event</button>

					</div>
					</form>
				</div>
			</div>
		</div>

	</div>
	<script>
	$(document).ready(function() {
	$('#deleteEventBtn').on('click', function() {
		var eventId = $('#id').val();
		var writtenBy = $('#writtenby').val();
		var sessionEditBy = $('#sessionEditBy').val();
		if (confirm('Are you sure you want to delete this event?')) {
			if (writtenBy == sessionEditBy) {
				$.ajax({
					url: 'core/editEventTitle.php',
					type: 'POST',
					data: {
						delete: 'delete',
						id: eventId
					},
					success: function(response) {
						window.location.reload();
					},
					error: function(xhr, textStatus, errorThrown) {
						alert('An error occurred. Please try again later.');
					}
				});
			} else {
				alert('You are not authorized to delete this event.');
			}
		}
	});
});
</script>
	<script>		
	$("#del, #delRec").change(function() {	
		var self = $(this);
		var aValue = self.attr("value");
		var aClass = self.attr("class");
		var checked = $("input:checkbox[class='" + aClass + "']");

		if ( $(self).is(':checked') ) {
			$("." + aValue).removeClass("label-off").addClass('label-on');
			$(checked).not(self).removeAttr("checked");
			if ( aValue == 'deleteRecurrence' ) {
				$('#delete').removeClass("label-on").addClass('label-off');
			} else {
				$('#deleteRecurrence').removeClass("label-on").addClass('label-off');
			}
		} else {
			$("." + aValue).removeClass("label-on").addClass('label-off');
		}
	});
	</script>

<script>
	
	$(function () {
    $('#calendar').fullCalendar({
        header: {
            left: 'prev,next,today',
            center: 'title',
            right: 'month,agendaWeek,agendaDay,listWeek,year,'
        },
        customButtons: {
            year: {
                text: 'Annuel',
                click: function () {
                    $('#calendar').fullCalendar('changeView', 'year');
                }
            }
        },
        views: {
            year: {
                type: 'listYear',
                buttonText: 'Annuel'
            }
        },
		locale: 'fr', // Takvim dilini Fransızca'ya ay
		height: 540,
		businessHours: {
			dow: [ 1, 2, 3, 4, 5 ],
			start: '8:00',
			end: '17:00',
		},
		nowIndicator: true,
		now: new Date(),
		scrollTime: '08:00:00',
        defaultDate: new Date(), // Güncel tarihi kullan
		editable: true,
		navLinks: true,
		eventLimit: true, // allow "more" link when there are too many events
		selectable: true,
		selectHelper: true,
		select: function(start, end) {
    $('#ModalAdd #start').val(moment(start).format('YYYY-MM-DD HH:mm:ss'));
    $('#ModalAdd #startTime').val(moment(start).format('HH:mm:ss'));
    $('#ModalAdd #startDate').val(moment(start).format('YYYY-MM-DD'));
    $('#ModalAdd #end').val(moment(end).format('YYYY-MM-DD HH:mm:ss'));
    $('#ModalAdd #endTime').val(moment(end).format('HH:mm:ss'));
    $('#ModalAdd #endDate').val(moment(start).add(2, 'w').format('YYYY-MM-DD'));
    $('#ModalAdd').modal('show');
},
		eventAfterRender: function(eventObj, $el) {
			var request = new XMLHttpRequest();
			request.open('GET', 'data/events.json', true);
			request.onload = function () {
				$el.popover({
					title: eventObj.title,
					content: eventObj.description,
					trigger: 'hover',
					placement: 'top',
					container: 'body'
				});
			}
		request.send();
		},
		eventRender: function(event, element) {
    element.bind('click', function() {
        $('#ModalEdit #id').val(event.id);
        $('#ModalEdit #rid').val(event.rid);
        $('#ModalEdit #start').val(moment(event.start).format('YYYY-MM-DD HH:mm:ss'));
        $('#ModalEdit #end').val(moment(event.end).format('YYYY-MM-DD HH:mm:ss'));
        $('#ModalEdit #title').val(event.title);
        $('#ModalEdit #description').val(event.description);
        $('#ModalEdit #color').val(event.color);
        $('#ModalEdit #rstatus').val(event.eventType);
        $('#ModalEdit #writtenby').val(event.writtenby);
		$('#ModalEdit #updated_by').val(event.updated_by);
		$('#ModalEdit #file_path').val(event.file_path);
		$('#ModalEdit #file_path').html(''); // Clear previous file information

		var file_paths = event.file_path.split(",");
var file_links = '';
for (var i = 0; i < file_paths.length; i++) {
    if (file_paths[i].trim() !== '') {
        file_links += '<tr>';
        file_links += '<td><a href="./core/' + file_paths[i] + '" target="_blank">' + file_paths[i].split('/').pop() + '</a></td>';
        file_links += '<td><button class="btn btn-danger btn-sm delete-file" data-file-path="' + file_paths[i] + '" type="button">Delete</button></td>';
        file_links += '</tr>';
    }
}
if (file_links !== '') {
    $('#ModalEdit #file_path').html('<table>' + file_links + '</table>');
} else {
    $('#ModalEdit #file_path').html('No file uploaded.');
}

$(document).on('click', '.delete-file', function(event) {
    var file_path = $(this).data('file-path');
    var event_id = $('#ModalEdit #id').val();

    $('#ModalEdit').modal('hide'); // Hide the edit modal
    $('#deleteModal').data('file-path', file_path);
    $('#deleteModal').data('event-id', event_id);
    $('#deleteModal').modal('show');
});

$(document).on('click', '.deleteBtn', function() {

// Silinecek dosyanın yolu ve etkinlik ID'si alınır
var filePath = $(this).prev('a').attr('href');
var eventId = $(this).data('event-id');

// Delete modalını aç ve verileri aktar
$('#deleteModal').data('file-path', filePath);
$('#deleteModal').data('event-id', eventId);
$('#deleteModal').modal('show');

});

// Delete modalındaki onay butonuna tıklandığında
$(document).on('click', '#confirmDelete', function() {

var filePath = $('#deleteModal').data('file-path');
var eventId = $('#deleteModal').data('event-id');

$.ajax({
	url: 'delete_file.php',
	type: 'POST',
	data: {
		id: eventId,
		file_path: filePath
	},
	success: function(response) {
		if (response === "File successfully deleted.") {
			showAlert1(response);
			var self = $('[data-event-id="' + eventId + '"]').closest('.event-item');
			self.prev('a').remove(); // Remove the file link
			self.next('br').remove(); // Remove the line break
			self.remove(); // Remove the delete button

			// Onay mesajını göster
			$('#deleteConfirm').show();

			// Kapat butonuna tıklandığında sayfayı yenile
			$('#deleteConfirmClose').on('click', function() {
				location.reload();
			});

		} else {
			// Hata oluştuğunda buradaki kod çalışacaktır.
			showAlert1("Hata: " + response);
		}
	},
	error: function(jqXHR, textStatus, errorThrown) {
		showAlert1('Dosya silinirken hata oluştu: ' + errorThrown);
	}
});

$('#deleteModal').modal('hide');

});

        if (event.eventType === 'repeating event') {
            $('#editRecurrence').show();
        } else {
            $('#editRecurrence').hide();
        }
        var responsible_persons = event.responsible_persons.split(",");
        var writtenby = event.writtenby;
		var updated_by = event.updated_by;
		var file_path = event.file_path;

        var options = '';
        <?php
        $sql = "SELECT * FROM users";
        $stmt = $auth->prepare($sql);
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        ?>
        var users = <?php echo json_encode($users); ?>;
        users.forEach(function(user) {
            if (user.username !== writtenby) {
                options += '<input type="checkbox" name="responsible_edit[]" value="' + user.username + '" id="responsible_edit_' + user.username + '">';
                options += '<label for="responsible_edit_' + user.username + '">' + user.username + '</label><br>';
            }
        });
        $('#responsible_edit').html(options);
        $("#responsible_edit input[type='checkbox']").each(function () {
            if ($.inArray($(this).val(), responsible_persons) !== -1) {
                $(this).prop('checked', true);
            }
        });
        $('#ModalEdit').modal('show');
    });
},
		eventDrop: function(event, delta, revertFunc) { // si changement de position
			edit(event);
		},
		eventResize: function(event,dayDelta,minuteDelta,revertFunc) { // si changement de longueur
			edit(event);
		},
		events: [
			<?php foreach($events as $event): 
			
				$start = explode(" ", $event['start']);
				$end = explode(" ", $event['end']);
				if($start[1] == '00:00:00'){
					$start = $start[0];
				}else{
					$start = $event['start'];
				}
				if($end[1] == '00:00:00'){
					$end = $end[0];
				}else{
					$end = $event['end'];
				}
			?>
				{
					id: '<?php echo $event['id']; ?>',
					title: '<?php echo $event['title']; ?>',
					description: '<?php echo preg_replace("/[\r\n]+/", " ", addslashes($event['description'])); ?>',
					start: '<?php echo $start; ?>',
					end: '<?php echo $end; ?>',
					color: '<?php echo $event['color']; ?>',
					responsible_persons: '<?php echo $event['responsible_persons']; ?>', // Sorumlu kişileri burada ekleyin
					writtenby: '<?php echo $event['writtenby']; ?>',
					updated_by: '<?php echo $event['updated_by']; ?>',
					file_path: '<?php echo $event['file_path']; ?>',



				},
			<?php endforeach; ?>
			]
		});
	function edit(event) {
		var emailAddresses = ['gokhanbasturk12@gmail.com'];

		start = event.start.format('YYYY-MM-DD HH:mm:ss');
		if (event.end) {
			end = event.end.format('YYYY-MM-DD HH:mm:ss');
		} else {
			end = start;
		}
		id = event.id;
		Event = [];
		Event[0] = id;
		Event[1] = start;
		Event[2] = end;
		
		$.ajax({
			url: '../calendar/core/edit-date.php',
			type: "POST",
			data: {Event:Event, Email: emailAddresses},
			success: function(rep) {

			}
		});
		var responsible = $('#ModalEdit input[name="responsible_edit[]"]:checked').map(function() {
    return this.value;
  }).get();

  if (responsible.length == 0) {
    responsible.push('');
  }

  
}
});

</script>
<script>
$(document).ready(function() {
    $('.auto-height').on('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });
});
function updateHeight(textarea) {
    textarea.style.height = 'auto';
    textarea.style.height = (textarea.scrollHeight) + 'px';
}
$(document).ready(function() {
    $('.auto-height').each(function() {
        updateHeight(this);
    });

    $('.auto-height').on('input', function() {
        updateHeight(this);
    });
});
$('#ModalEdit').on('shown.bs.modal', function () {
    $('.auto-height').each(function() {
        updateHeight(this);
    });
});
$('#ModalAdd').on('shown.bs.modal', function () {
    $('.auto-height').each(function() {
        updateHeight(this);
    });
});

</script>
</body>
</html>
