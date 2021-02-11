<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Version details.
 *
 * @package    report
 * @subpackage mycourses
 * @copyright  2021 Modernlms {@link http://modernlms.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


function report_mycourses_get_preview(){

Global $CFG, $USER, $DB;

//get current user login
$currentuser = $USER->id;

$url = new moodle_url('/course/view.php?id');
$logourl = new moodle_url('/report/mycourses/img/new.png');

// get course not set date end
$sqlgetenrol = "SELECT * 
             FROM {user_enrolments} as A
             JOIN {enrol} as B 
             ON A.enrolid LIKE B.id
             JOIN {course} as C 
             ON B.courseid LIKE C.id
             WHERE A.userid LIKE '$currentuser'
             AND C.enddate LIKE 0
             ORDER BY C.startdate DESC";

$datasqlgetenrol = $DB->get_records_sql($sqlgetenrol);
$percentage = 0;
$dpercentage = 0;
foreach ($datasqlgetenrol as $datadatasqlgetenrol) 
{   
            //get category 
            $categoryname = '';
            $sqlcategory = "SELECT * 
             FROM {course_categories}
             WHERE id LIKE '$datadatasqlgetenrol->category'";
            $resultsqlcategory = $DB->get_records_sql($sqlcategory);
            foreach ($resultsqlcategory as $dataresultsqlcategory) 
            {
                $categoryname = $dataresultsqlcategory->name;
            } 

                // get total course need taken
                $totalcount = 0;
                $totalpercentage = 0;
                $sqlcountcourse = "SELECT COUNT(course) as totalcount  
                    FROM {course_modules}
                    WHERE course LIKE '$datadatasqlgetenrol->courseid'
                    AND deletioninprogress != 1
                    AND completion > 0";
                $rescountcourse = $DB->get_records_sql($sqlcountcourse);
                foreach ($rescountcourse as $lcountcourse => $resdatacountcourse) {
                        //all module
                        $totalcount = $resdatacountcourse->totalcount;
                }

                $sqlcountcompletedcourse = "SELECT COUNT(*) as totalcountcompleted  
                  FROM {course_modules} as A
                  JOIN {course_modules_completion} as B 
                  ON A.id LIKE B.coursemoduleid
                  WHERE A.course LIKE '$datadatasqlgetenrol->courseid'
                  AND A.deletioninprogress != 1
                  AND B.userid LIKE '$currentuser'
                  AND A.completion > 0
                  AND B.completionstate > 0";
                $rescountcompletedcourse = $DB->get_records_sql($sqlcountcompletedcourse);
                foreach ($rescountcompletedcourse as $lcountcompletedcourse => $resdatacountcompletedcourse) {
                        //amount complete
                        $totalcountcompleted = $resdatacountcompletedcourse->totalcountcompleted;
                }
                
                
                if($totalcount==0){
                    $totalcount =1;
                }
                
                $totalpercentage = number_format(($totalcountcompleted / $totalcount) * 100,2);

                if($totalpercentage>100){
                    $totalpercentage = 100;
                    $totalpercentage = 100;
                }
                if($totalpercentage==NAN){
                    $totalpercentage = 0;
                    $totalpercentage = 0;
                }

                $dpercentage = 100-$totalpercentage;  


                //get user score
                //get grade from history
                $sqlmarkq =  "SELECT A.finalgrade as tfinalgrade
                    FROM {grade_grades_history} as A 
                    JOIN {grade_items} as B
                    ON A.itemid LIKE B.id
                    WHERE A.userid LIKE '$currentuser'
                    AND A.finalgrade != ''
                    AND B.itemname != ''
                    AND B.courseid LIKE '$datadatasqlgetenrol->courseid'";
                $resmarkq = $DB->get_records_sql($sqlmarkq);
                $markquizt = 0;
                foreach ($resmarkq as $lmarkq => $resdatamarkq) {

                        $markquizt = $markquizt + $resdatamarkq->tfinalgrade;
                        
                }  

                $currentdatenow = time();
                $imgnew = '';
                // if not less then 3 month
                if(7257600>=($currentdatenow-$datadatasqlgetenrol->startdate))
                {
                  $imgnew = '<img src="'.$logourl.'" width="30px" height="30px">';
                }



        $markdisplay .= '
        <tr>
            <td>'.++$bilcount.'</td>
            <td>
                <a href="'.$url.'='.$datadatasqlgetenrol->courseid.'">'.$datadatasqlgetenrol->fullname.'</a>
                '.$imgnew.'
            </td>
            <td>'.round($markquizt).'</td>
            <td>'.$categoryname.'</td>
            <td>NOT SET</td>
            <td>
                <div class="svg-item">
                <svg width="100%" height="100%" viewBox="0 0 40 40" class="donut">
                 <circle class="donut-hole" cx="20" cy="20" r="15.91549430918954" fill="#fff"></circle>
                 <circle class="donut-ring" cx="20" cy="20" r="15.91549430918954" fill="transparent" stroke-width="3.5"></circle>
                 <circle class="donut-segment" cx="20" cy="20" r="15.91549430918954" fill="transparent" stroke-width="3.5" stroke-dasharray="'.$totalpercentage.' '.$dpercentage.'" stroke-dashoffset="25"></circle>
                 <g class="donut-text">

                   <text y="50%" transform="translate(0, 2)">
                     <tspan x="50%" text-anchor="middle" class="donut-percent">'.(int)$totalpercentage.'%</tspan>   
                   </text>
                 </g>
                </svg>
                </div>
            </td>
        </tr>';

}


echo '<div class="modernlms-lb container">
    <div class="row">
    <table id="modernlms_mycourses" class="table table-striped table-bordered" style="width:100%">
        <thead>
            <tr>
                <th>'.get_string('no', 'report_mycourses').'</th>
                <th>'.get_string('course', 'report_mycourses').'</th>
                <th>'.get_string('currentscore', 'report_mycourses').'</th>
                <th>'.get_string('type', 'report_mycourses').'</th>
                <th>'.get_string('dateend', 'report_mycourses').'</th>
                <th>'.get_string('completion', 'report_mycourses').'</th>
            </tr>
        </thead>
        <tbody>
            '.$markdisplay.'
        </tbody>
    </table>
    </div>
</div>';
}