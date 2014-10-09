<?php
//echo(file_get_contents('http://www.google.com/calendar/feeds/auditor400@gmail.com/private-c04059392434b2201ee8b1ceeb58c302/basic'));
echo(file_get_contents('http://www.google.com/calendar/ical/auditor400@gmail.com/private-c04059392434b2201ee8b1ceeb58c302/basic.ics'));
//
die();
/*
 *
 *
 *


BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//Mozilla.org/NONSGML Mozilla Calendar V1.0//EN
METHOD:PUBLISH
BEGIN:VEVENT
UID
  :9363BE64-E919-11D7-AF7F-000A27E16A94
SUMMARY
  :SAT I&II
DESCRIPTION
  :Deadlines:\nUS Regular:  Oct. 30\, 2003\nUS Late:  Nov. 12\, 2003\nIntl
   Early: Oct. 8\, 2003\nIntl Regular: Oct. 30\, 2003\n\nGo to
  URL for specific details on application deadlines and test 
administration\n
  \n\n\n
URL:http://www.collegeboard.com/student/testing/sat/calenfees.html
CLASS:PUBLIC
X-MOZILLA-RECUR-DEFAULT-INTERVAL:0
DTSTART;VALUE=DATE:20031206
DTEND;VALUE=DATE:20031207
DTSTAMP:20030917T141559Z
END:VEVENT
END:VCALENDAR

?>
<?php
    $Filename = "SSPCAEvent" . $_GET['ID'] . ".vcs"
    header("Content-Type: text/x-vCalendar");
    header("Content-Disposition: inline; filename=$Filename");

    //Put mysql connection and query statements here 

$DescDump = str_replace("\r", "=0D=0A=", $row['Detail']);

$vCalStart = date("Ymd\THi00", $row['EventStart']);
$vCalEnd = date("Ymd\THi00", $row['EventEnd']);
?>
BEGIN:VCALENDAR
VERSION:1.0
PRODID:SSPCA Web Calendar
TZ:-07
BEGIN:VEVENT
SUMMARY:<?php echo $row['Summary'] . "\n"; ?>
DESCRIPTION;ENCODING=QUOTED-PRINTABLE: <?php echo $DescDump . "\n"; ?>
DTSTART:<?php echo $vCalStart . "\n"; ?>
DTEND:<?php echo $vCalEnd . "\n"; ?>
END:VEVENT
END:VCALENDAR
 */
?>
