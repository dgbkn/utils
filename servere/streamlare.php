<?php
 /* resolve streamlare
 * Copyright (c) 2019 vb6rocod
 *
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * examples of usage :
 * $filelink = input file
 * $link --> video_link
 */
  // https://streamlare.com/v/oLvgezwJoEPDbp8E
if (strpos($filelink,"streamlare") !== false) {
   function xor_string($string, $key) {
    for($i = 0; $i < strlen($string); $i++)
        $string[$i] = ($string[$i] ^ $key[$i % strlen($key)]);
    return $string;
   }
  if (preg_match('/(\/\/[\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $filelink, $s)) {
    $srt="https:".$s[1];
  }
  $link="";
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:80.0) Gecko/20100101 Firefox/80.0";
  $host=parse_url($filelink)['host'];
  $ch = curl_init($filelink);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $html = curl_exec($ch);
  curl_close ($ch);
  $html=htmlspecialchars_decode($html,ENT_QUOTES);

  $t1=explode('hashid":"',$html);
  $t2=explode('"',$t1[1]);
  $id=$t2[0];
  $t1=explode('csrf-token" content="',$html);
  $t2=explode('"',$t1[1]);
  $csrf=$t2[0];
  $l="https://".$host."/api/video/get";
  $l="https://".$host."/api/video/stream/get";
  $post='{"id":"'.$id.'"}';
  $head=array('Accept: application/json, text/plain, */*',
   'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
   'Accept-Encoding: deflate',
   'Referer: '.$filelink,
   'X-Requested-With: XMLHttpRequest',
   'X-CSRF-TOKEN: '.$csrf,
   'Content-Type: application/json;charset=utf-8',
   'Content-Length: '.strlen($post),
   'Origin: https://'.$host,
   'Connection: keep-alive');
  $ch = curl_init($l);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_POST,1);
  curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
  curl_setopt($ch, CURLOPT_ENCODING,"");
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close ($ch);
  $x=json_decode($h,1);
  // see app.js
  //if (isset($x['result']['Original']['src']))
  // $link = xor_string(base64_decode($x['result']['Original']['src']),"3");
  if (isset($x['result']['playlist']))
   $link= $x['result']['playlist'];
  elseif (isset($x['result']['Original']['src']))
   $link=$x['result']['Original']['src'];
  elseif (isset($x['result']['Original']['file']))
   $link=$x['result']['Original']['file'];
  elseif (isset($x['result']['file']))
   $link=$x['result']['file'];
}
echo $link;
?>
