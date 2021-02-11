<?php


// The number of lines in front of config file determine the // hierarchy of files.
require_once(dirname(dirname(__FILE__)).'/../config.php');
require_login();
$PAGE->set_context(get_system_context());
$PAGE->set_pagelayout('admin');
$PAGE->set_title("My Courses");
$PAGE->set_heading("My Courses");


echo $OUTPUT->header();

Global $CFG, $USER, $DB;

$url = new moodle_url('/course/view.php?id');

//css circle round
echo '<link rel="stylesheet" href="js/style3.css">';


//get current user login
$currentuser = $USER->id;


// get course have date end
$sqlgetenrol = "SELECT * 
             FROM {user_enrolments} as A
             JOIN {enrol} as B 
             ON A.enrolid LIKE B.id
             JOIN {course} as C 
             ON B.courseid LIKE C.id
             -- JOIN {course_completions} as D 
             -- ON C.id LIKE D.course 
             WHERE A.userid LIKE '$currentuser'
             AND C.enddate > 0
             -- AND D.userid LIKE '$currentuser'
             -- AND D.timecompleted LIKE ''
             ORDER BY C.enddate ASC";

$datasqlgetenrol = $DB->get_records_sql($sqlgetenrol);
$bilcount = 1;
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
                    AND deletioninprogress != 1";
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
                  AND A.completion > 0";
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



                //get course compoletion
                $sqlgetcompleted = "SELECT E.timecompleted  
                  FROM {course_completions} as E
                  WHERE E.course LIKE '$datadatasqlgetenrol->courseid'
                  AND E.userid LIKE '$currentuser'";
                $ressqlgetcompleted = $DB->get_records_sql($sqlgetcompleted);
                foreach ($ressqlgetcompleted as $resdataressqlgetcompleted) {

                  //amount complete
                  if($resdataressqlgetcompleted->timecompleted!='')
                  {
                    $totalpercentage = '100';
                  }

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
                if(7257600>=($currentdatenow-$datadatasqlgetenrol->startdate))
                {
                  $imgnew = '<span class="badge badge-success">New</span>';
                }

        if($totalpercentage!=100)
        {
          $markdisplay .= '
          <tr>
              <td class="modernlms-bil">'.$bilcount.'</td>
              <td>
                  <a href="'.$url.'='.$datadatasqlgetenrol->courseid.'">'.$datadatasqlgetenrol->fullname.'</a>
                  '.$imgnew.'
              </td>
              <td class="modernlms-grade">'.round($markquizt).'</td>
              <td class="modernlms-cat">'.$categoryname.'</td>
              <td class="modernlms-date">'.date("d-M-Y",$datadatasqlgetenrol->enddate).'</td>
              <td class="modernlms-percent">
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

          $bilcount++;

        }
}




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
                    AND deletioninprogress != 1";
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
                  AND A.completion > 0";
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
                if(7257600>=($currentdatenow-$datadatasqlgetenrol->startdate))
                {
                  $imgnew = '<span class="badge badge-success">New</span>';
                }


        $markdisplay .= '
        <tr>
            <td class="modernlms-bil">'.$bilcount.'</td>
            <td>
                <a href="'.$url.'='.$datadatasqlgetenrol->courseid.'">'.$datadatasqlgetenrol->fullname.'</a>
                '.$imgnew.'
            </td>
            <td class="modernlms-grade">'.round($markquizt).'</td>
            <td class="modernlms-cat">'.$categoryname.'</td>
            <td class="modernlms-date">NOT SET</td>
            <td class="modernlms-percent">
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
        $bilcount++;
}


// get course complete
// get course have date end
$sqlgetenrol = "SELECT * 
             FROM {user_enrolments} as A
             JOIN {enrol} as B 
             ON A.enrolid LIKE B.id
             JOIN {course} as C 
             ON B.courseid LIKE C.id
             -- JOIN {course_completions} as D 
             -- ON C.id LIKE D.course 
             WHERE A.userid LIKE '$currentuser'
             AND C.enddate > 0
             -- AND D.userid LIKE '$currentuser'
             -- AND D.timecompleted LIKE ''
             ORDER BY C.enddate ASC";

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
                    AND deletioninprogress != 1";
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
                  AND A.completion > 0";
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



                //get course compoletion
                $sqlgetcompleted = "SELECT E.timecompleted  
                  FROM {course_completions} as E
                  WHERE E.course LIKE '$datadatasqlgetenrol->courseid'
                  AND E.userid LIKE '$currentuser'";
                $ressqlgetcompleted = $DB->get_records_sql($sqlgetcompleted);
                foreach ($ressqlgetcompleted as $resdataressqlgetcompleted) {

                  //amount complete
                  if($resdataressqlgetcompleted->timecompleted!='')
                  {
                    $totalpercentage = '100';
                  }

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
                if(7257600>=($currentdatenow-$datadatasqlgetenrol->startdate))
                {
                  $imgnew = '<span class="badge badge-success">New</span>';
                }

        if($totalpercentage==100)
        {
          $markdisplay .= '
          <tr>
              <td class="modernlms-bil">'.$bilcount.'</td>
              <td>
                  <a href="'.$url.'='.$datadatasqlgetenrol->courseid.'">'.$datadatasqlgetenrol->fullname.'</a>
                  '.$imgnew.'
              </td>
              <td>'.round($markquizt).'</td>
              <td>'.$categoryname.'</td>
              <td>'.date("d-M-Y",$datadatasqlgetenrol->enddate).'</td>
              <td>
                  <div class="svg-item">
                  <svg width="100%" height="100%" viewBox="0 0 40 40" class="donut">
                   <circle class="donut-hole" cx="20" cy="20" r="15.91549430918954" fill="#fff"></circle>
                   <circle class="donut-ring" cx="20" cy="20" r="15.91549430918954" fill="transparent" stroke-width="3.5"></circle>
                   <circle class="donut-segment" cx="20" cy="20" r="15.91549430918954" fill="transparent" stroke-width="3.5" stroke-dasharray="'.$totalpercentage.' '.$dpercentage.'" stroke-dashoffset="25"></circle>
                   <g class="donut-text">

                     <text y="50%" transform="translate(0, 2)">
                       <tspan x="50%" text-anchor="middle" class="donut-percent">'.$totalpercentage.'%</tspan>   
                     </text>
                   </g>
                  </svg>
                  </div>
              </td>
          </tr>';

          $bilcount++;
          
        }
}

echo '<div class="modernlms-lb container">
    <div class="row">
    <table id="modernlms_mycourse" class="table table-striped table-bordered" style="width:100%">
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




echo $OUTPUT->footer();