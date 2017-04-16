<?php

include "inc/function.php";

$gid = intval($_GET['page_id']);

$action = htmlspecialchars($_SERVER["PHP_SELF"]);

if(isset($_SERVER["QUERY_STRING"])){

$action = "?" . htmlspecialchars($_SERVER["QUERY_STRING"]);

  }




// بيانات القاعدة  pages_comm = pc_id,pc_name,pc_mail,pc_ip ,pc_date,pc_text,pc_active,page_id ,pc_from


$pcname = strip_tags($_POST['pc_name']);
$pcmail = trim(strip_tags($_POST['pc_mail']));
$pcip   = $_POST['pc_ip'];
$pcdate = $_POST['pc_date'];
$pctxt  =  $_POST['pc_text'] ;
$pcact  = $_POST['pc_active'];
$pcp_id = $_POST['page_id'];
$pcfrom = strip_tags($_POST['pc_from']);

#######################################################

if(isset($_POST['add']) && $_POST['add'] == 'comm'){

if($_SESSION ['commnetpage'] > time() - 60){

include "inc/okhead.php";
echo" <div class='txt'>لايمكنك أرسال استعلام ثاني الابعد 30 ثانية</div>";
include "inc/okfoot.php";
exit();
}


if($_POST['captcha'] == '' ){
include "inc/okhead.php";
echo" <div class='txt'>الرمز الامني غير صحيح</div>";
include "inc/okfoot.php";
exit();
  }

else if(empty($pcname)){
include "inc/okhead.php";
echo" <div class='txt'>أكتب أسمك لو سمحت</div>";
include "inc/okfoot.php";
exit();
}

else if(empty($pctxt)){

include "inc/okhead.php";
echo" <div class='txt'> أكتب التعليق لو سمحت</div>";
include "inc/okfoot.php";
exit();

}
else
if(strlen($pcname)> 30 or strlen($pcname)<3){
include "inc/okhead.php";
echo" <div class='txt'> يجب ان يكون أكبر من 3 حرف واقل من 30 حرف</div>";
include "inc/okfoot.php";
exit();

}
else
if(empty($pcmail)){
include "inc/okhead.php";
echo" <div class='txt'>  أكتب البريد الالكتروني لو سمحت</div>";
include "inc/okfoot.php";
exit();
}
else
if(!preg_match("/^[A-Z0-9_.-]{1,40}+@([A-Z0-9_-]){2,30}+\.([A-Z0-9]){2,20}$/i",$pcmail)){
include "inc/okhead.php";
echo" <div class='txt'>البريد الالكتروني غير صحيح</div>";
include "inc/okfoot.php";
exit();

}
else {

 $_SESSION['commnetpage'] = time ();

$addcomm=mysql_query("insert into pages_comm
(pc_name,pc_mail,pc_ip ,pc_date,pc_text,pc_active,page_id ,pc_from)
values
('$pcname','$pcmail','$pcip','$pcdate','$pctxt ','$pcact','$pcp_id','$pcfrom')") or die("mysql_error");

if(isset($_SERVER["QUERY_STRING"]  )) {

include "inc/okhead.php";
echo" <div class='txt'> تم إضافة التعليق بنجاح
</div>
<META HTTP-EQUIV='refresh' CONTENT='2;  url=".$_SERVER["PHP_SELF"]."?page_id=".$gid."'/>

";
include "inc/okfoot.php";
exit();

}
}
}

$showpage = mysql_query("SELECT * FROM pages where page_id='".$gid."' ") or die ("mysql_error");

 $rowshoepage = mysql_fetch_object($showpage);

 include "inc/header.php";

 if(!$gid){

   echo"

   <td valign='top' width='60%'>
&nbsp; <div class='head'>رساله الموقع</div>
 <div class='bodypanel'><center>الصفحة المطلوبة غير موجود</center></div>
</td>

   ";

 } else if  (mysql_num_rows($showpage) <1){

  echo"

</center>
   <td valign='top' width='60%'>
&nbsp; <div class='head'>رساله الموقع</div>
 <div class='bodypanel'>  <center>رقم الصفحة  ".$gid." لم يستعلم عنه او لايوجد في القاعدة</div>
</td>

  " ;

 }

  else if(isset ($gid)){

  $addziara = mysql_query("update pages set page_count = page_count + 1 where page_id='$gid'") or die ("mysql_error");

  if($rowshoepage->page_act == 1){


 echo"

 <td valign='top' width='60%'>
&nbsp; <div class='head'>".$rowshoepage->page_name."</div>
 <div class='bodypanel'>".$rowshoepage->page_content." </div>  ";


 /////////////////////loop commnet ////////////////////////////////

$showcommntes  = mysql_query("select  pc_id,pc_name,pc_mail,pc_date,pc_text,pc_active,page_id ,pc_from
 from pages_comm where page_id='".$gid."'  AND pc_active='1' ORDER BY pc_id DESC") or die ("error mysql");

 if(mysql_num_rows($showcommntes) > 0){
 echo"
  <div class='head'> التعليقات </div>
 <div class='bodypanel'>
 <table align='center' width='100%' cellpadding='2' cellspacing='3'>

 ";

 while($rowcomm = mysql_fetch_object($showcommntes)){

 echo"
 <tr>
 <td class='tbl1'>كتبت بواسطة  ".$rowcomm->pc_name." بتاريخ ".$rowcomm->pc_date." الدولة ".$rowcomm->pc_from."</td>

 </tr>
 <tr><td class='tbl2'> ".mb_substr($rowcomm->pc_text, 0, 120 , "UTF-8")." ...<hr /></td> </tr>

 ";
 }

  echo"</table></div> ";}
  //////////////////loop commnet ////////////////////////////////

////////////////////هذا هو الفورم ////////////////////////////////


  if($rowshoepage->page_comm_act == 1){

echo" <!--- comm form  ---->

 <div class='head'> أضــــافة تعليـــق</div>
 <div class='bodypanel'>
 <form action='".$action."' method='post'>
  <table align='center' width='100%' cellpadding='0' cellspacing='0'>

<tr>
<td widht='20%' class='tbl1'>أسم المعلق</td>
<td widht='80%' class='tbl1'>
<input type='text' name='pc_name' />
</td>
</tr>

<tr>
<td widht='20%' class='tbl2'>بريدك الالكتروني</td>
<td widht='80%' class='tbl2'>
<input type='text' name='pc_mail' />
</td>
</tr>

<tr>
<td widht='20%' class='tbl1'>الدوله</td>
<td widht='80%' class='tbl1'>
<select name='pc_from'>
<option></option>
<option>السودان</option>
<option>مصر</option>
</select>

</td>
</tr>

<tr>
<td widht='20%' class='tbl2'>التعليق</td>
<td widht='80%' class='tbl2'>
<textarea name='pc_text' rows='8' cols='48'> </textarea>
</td>
</tr>
<tr><td widht='20%' class='tbl1'> الرمز الامني</td>
<td  widht='80%' class='tbl1'>

 <input type='text' name='captcha' /> - insert code <img src='inc/captcha.php' alt='' />
</td></tr>

<tr>
<td widht='20%' colspan='2' align='center' class='tbl1'>
 <input class='buttons' type='submit' value='أضافة التعليق'>
</td>

</tr>



</table>
<input type='hidden' name='pc_ip' value='".$_SERVER["REMOTE_ADDR"]."'/>
<input type='hidden' name='pc_active'  value='2' />
<input type='hidden' name='page_id'  value='".$gid."' />
<input type='hidden' name='pc_date'  value='".date("d m Y - h:i:s")."' />
<input type='hidden' name='add'  value='comm' />
</form>
 </div>


</td>

 ";
 }

 //////////// اغلاق فورم الاضافة


 }
 else{
   echo"
   <td valign='top' width='60%'>
&nbsp; <div class='head'>رساله الموقع</div>
 <div class='bodypanel'><center> الصفحة غير متوفرة حاليا</center></div>
</td>
   ";

 }
  }

include "inc/footer.php";



 ?>
