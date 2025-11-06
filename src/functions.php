<?php
/**
 * Playfair Cipher functions
 * - I/J digabung
 * - Hanya huruf A-Z diproses
 * - Insert 'X' bila double letters dalam digraph
 */

function sanitize_alpha($s){
  $s = strtoupper($s);
  // replace J -> I
  $s = str_replace('J','I',$s);
  // remove non A-Z
  return preg_replace('/[^A-Z]/','',$s);
}

function build_playfair_matrix($key){
  $key = sanitize_alpha($key);
  $seen = [];
  $matrix = [];
  // add key letters
  for($i=0;$i<strlen($key);$i++){
    $c = $key[$i];
    if(!isset($seen[$c])){
      $seen[$c] = true;
      $matrix[] = $c;
    }
  }
  // add remaining alphabet A-Z without J
  for($c = ord('A'); $c<=ord('Z'); $c++){
    $ch = chr($c);
    if($ch === 'J') continue; // combine J into I
    if(!isset($seen[$ch])){ 
      $matrix[] = $ch; 
      $seen[$ch] = true; 
    }
  }
  // matrix as 5x5 array
  $mat = array_chunk($matrix,5);
  return $mat; // $mat[row][col]
}

function matrix_to_string($mat){
  $lines = [];
  foreach($mat as $row){
    $lines[] = implode(' ', $row);
  }
  return implode("\n", $lines);
}

// find position of letter in matrix
function find_pos($mat, $letter){
  for($r=0;$r<5;$r++){
    for($c=0;$c<5;$c++){
      if($mat[$r][$c] === $letter) return [$r,$c];
    }
  }
  return null;
}

// prepare plaintext: sanitize, split into digraphs, insert X between doubles, pad X at end if needed
function prepare_plaintext($text){
  $s = sanitize_alpha($text);
  $out = '';
  $i=0; $n = strlen($s);
  while($i < $n){
    $a = $s[$i];
    $b = ($i+1 < $n) ? $s[$i+1] : '';
    if($b === ''){
      // last single char, pad with X
      $out .= $a . 'X';
      $i += 1;
    } elseif($a === $b){
      // pair of same letters -> insert X after first
      $out .= $a . 'X';
      $i += 1; // move only one step
    } else {
      $out .= $a . $b;
      $i += 2;
    }
  }
  return $out; // even length
}

// For ciphertext input we sanitize and ensure even length
function prepare_ciphertext($text){
  $s = sanitize_alpha($text);
  if(strlen($s) % 2 !== 0) {
    // pad with X if odd
    $s .= 'X';
  }
  return $s;
}

function playfair_encrypt($prepared, $mat){
  global $processLog;
  $n = strlen($prepared);
  $res = '';
  for($i=0;$i<$n;$i+=2){
    $a = $prepared[$i];
    $b = $prepared[$i+1];
    list($r1,$c1) = find_pos($mat,$a);
    list($r2,$c2) = find_pos($mat,$b);
    $processLog[] = "Pair: $a$b (pos: $r1,$c1 & $r2,$c2)";
    if($r1 === $r2){
      // same row -> take right (wrap)
      $res .= $mat[$r1][($c1+1)%5];
      $res .= $mat[$r2][($c2+1)%5];
      $processLog[] = "  Aturan: Sama baris → geser kanan -> " . substr($res, -2);
    } elseif($c1 === $c2){
      // same column -> take below (wrap)
      $res .= $mat[($r1+1)%5][$c1];
      $res .= $mat[($r2+1)%5][$c2];
      $processLog[] = "  Aturan: Sama kolom → geser bawah -> " . substr($res, -2);
    } else {
      // rectangle -> swap columns
      $res .= $mat[$r1][$c2];
      $res .= $mat[$r2][$c1];
      $processLog[] = "  Aturan: Rectangle → tukar kolom -> " . substr($res, -2);
    }
  }
  $processLog[] = "Hasil akhir: $res";
  return $res;
}

function playfair_decrypt($prepared, $mat){
  global $processLog;
  $n = strlen($prepared);
  $res = '';
  for($i=0;$i<$n;$i+=2){
    $a = $prepared[$i];
    $b = $prepared[$i+1];
    list($r1,$c1) = find_pos($mat,$a);
    list($r2,$c2) = find_pos($mat,$b);
    $processLog[] = "Pair (decrypt): $a$b (pos: $r1,$c1 & $r2,$c2)";
    if($r1 === $r2){
      // same row -> take left (wrap)
      $res .= $mat[$r1][($c1+4)%5];
      $res .= $mat[$r2][($c2+4)%5];
      $processLog[] = "  Aturan: Sama baris → geser kiri -> " . substr($res, -2);
    } elseif($c1 === $c2){
      // same column -> take above (wrap)
      $res .= $mat[($r1+4)%5][$c1];
      $res .= $mat[($r2+4)%5][$c2];
      $processLog[] = "  Aturan: Sama kolom → geser atas -> " . substr($res, -2);
    } else {
      // rectangle -> swap columns
      $res .= $mat[$r1][$c2];
      $res .= $mat[$r2][$c1];
      $processLog[] = "  Aturan: Rectangle → tukar kolom -> " . substr($res, -2);
    }
  }
  $processLog[] = "Hasil akhir decrypt: $res";
  return rtrim($res, 'X');
}

/**
 * ✅ Tambahan fungsi untuk integrasi Streamlit:
 * Mengambil seluruh log proses Playfair Cipher
 */
function get_process_log(){
  global $processLog;
  return isset($processLog) ? $processLog : [];
}
?>
