<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
function fail($message, $code = 400) { http_response_code($code); echo json_encode(['error'=>$message]); exit; }
if (!isset($_SESSION['user_id'])) fail('Entre na sua conta.', 401);
$uid = (int)$_SESSION['user_id'];
$_SESSION['campaign_csrf'] = $_SESSION['campaign_csrf'] ?? bin2hex(random_bytes(24));
$post = $_SERVER['REQUEST_METHOD'] === 'POST';
$d = $post ? json_decode(file_get_contents('php://input'), true) : $_GET;
if (!is_array($d)) fail('Pedido inválido.');
if ($post && !hash_equals($_SESSION['campaign_csrf'], $d['csrf'] ?? '')) fail('Atualize a página.',403);
session_write_close();
try {
 mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
 $db = new mysqli(getenv('ARCANA_DB_HOST') ?: 'localhost', getenv('ARCANA_DB_USER') ?: 'root', getenv('ARCANA_DB_PASSWORD') !== false ? getenv('ARCANA_DB_PASSWORD') : 'usbw', getenv('ARCANA_DB_NAME') ?: 'UserDB');
 $db->set_charset('utf8mb4');
 $db->query('CREATE TABLE IF NOT EXISTS arcana_campaigns (id INT AUTO_INCREMENT PRIMARY KEY, owner INT NOT NULL, name VARCHAR(100) NOT NULL, code CHAR(32) NOT NULL UNIQUE, journal TEXT NOT NULL, shield TEXT NOT NULL)');
 $db->query('CREATE TABLE IF NOT EXISTS arcana_members (campaign INT NOT NULL, user_id INT NOT NULL, PRIMARY KEY(campaign,user_id))');
 $db->query('CREATE TABLE IF NOT EXISTS arcana_characters (id INT AUTO_INCREMENT PRIMARY KEY, campaign INT NOT NULL, owner INT NOT NULL, name VARCHAR(100) NOT NULL, hp INT NOT NULL, level INT NOT NULL, body TEXT NOT NULL, version INT NOT NULL DEFAULT 1)');
 $db->query('CREATE TABLE IF NOT EXISTS arcana_rolls (campaign INT NOT NULL, user_id INT NOT NULL, result TEXT NOT NULL, PRIMARY KEY(campaign,user_id))');
 function query($sql, $types='', ...$args) { global $db; $s=$db->prepare($sql); if ($types) $s->bind_param($types,...$args); $s->execute(); return $s; }
 function textValue($key,$limit) { global $d; $s=trim((string)($d[$key]??'')); if(strlen($s)>$limit) fail('Texto muito longo.'); return $s; }
 $action=$d['action']??'list';
 if (!$post && $action !== 'list' && $action !== 'room') fail('Método inválido.',405);
 if($action==='create') { $name=textValue('name',100); if(!$name) fail('Informe o nome.'); query('INSERT INTO arcana_campaigns(owner,name,code,journal,shield) VALUES(?,?,?,\'\',\'\')','iss',$uid,$name,bin2hex(random_bytes(16))); $id=$db->insert_id; query('INSERT INTO arcana_members VALUES(?,?)','ii',$id,$uid); }
 if($action==='join') { $c=query('SELECT id FROM arcana_campaigns WHERE code=?','s',textValue('code',32))->get_result()->fetch_assoc(); if(!$c) fail('Convite não encontrado.'); query('INSERT IGNORE INTO arcana_members VALUES(?,?)','ii',$c['id'],$uid); }
 if(in_array($action,['list','create','join'],true)) { $rows=query('SELECT c.id,c.name FROM arcana_campaigns c JOIN arcana_members m ON m.campaign=c.id WHERE m.user_id=? ORDER BY c.id DESC','i',$uid)->get_result()->fetch_all(MYSQLI_ASSOC); echo json_encode(['campaigns'=>$rows,'csrf'=>$_SESSION['campaign_csrf']]); exit; }
 $id=(int)($d['campaign']??0);
 $c=query('SELECT c.* FROM arcana_campaigns c JOIN arcana_members m ON m.campaign=c.id WHERE c.id=? AND m.user_id=?','ii',$id,$uid)->get_result()->fetch_assoc();
 if(!$c) fail('Campanha não encontrada.',403); $gm=(int)$c['owner']===$uid;
 if($action==='character') {
  $name=textValue('name',100); if(!$name) fail('Informe o nome.'); $body=textValue('body',12000); $hp=max(0,min(99999,(int)($d['hp']??20))); $level=max(1,min(999,(int)($d['level']??1))); $cid=(int)($d['id']??0);
  if($cid) { $s=query('UPDATE arcana_characters SET name=?,hp=?,level=?,body=?,version=version+1 WHERE id=? AND campaign=? AND version=? AND (owner=? OR ?=1)','siisiiiii',$name,$hp,$level,$body,$cid,$id,(int)($d['version']??0),$uid,(int)$gm); if(!$s->affected_rows) fail('A ficha mudou ou você não tem permissão. Abra a ficha novamente.',409); }
  else query('INSERT INTO arcana_characters(campaign,owner,name,hp,level,body) VALUES(?,?,?,?,?,?)','iisiis',$id,$uid,$name,$hp,$level,$body);
 }
 if($action==='journal'||$action==='shield') { if(!$gm) fail('Somente o mestre pode salvar.',403); $body=textValue('body',20000); query('UPDATE arcana_campaigns SET '.$action.'=? WHERE id=?','si',$body,$id); }
 if($action==='roll') { $sides=(int)($d['sides']??20); $count=(int)($d['count']??1); if(!in_array($sides,[4,6,8,10,12,20,100],true)||$count<1||$count>10) fail('Dados inválidos.'); $values=[]; for($i=0;$i<$count;$i++) $values[]=random_int(1,$sides); $roll=['values'=>$values,'sides'=>$sides,'total'=>array_sum($values)]; if(!empty($d['secret'])) { if(!$gm) fail('Somente o mestre.',403); } else query('REPLACE INTO arcana_rolls VALUES(?,?,?)','iis',$id,$uid,json_encode($roll)); echo json_encode($roll); exit; }
 $c=query('SELECT * FROM arcana_campaigns WHERE id=?','i',$id)->get_result()->fetch_assoc(); if(!$gm) { unset($c['shield'],$c['code']); }
 echo json_encode(['campaign'=>$c,'gm'=>$gm,'uid'=>$uid,'characters'=>query('SELECT * FROM arcana_characters WHERE campaign=? AND (owner=? OR ?=1)','iii',$id,$uid,(int)$gm)->get_result()->fetch_all(MYSQLI_ASSOC),'rolls'=>query('SELECT user_id,result FROM arcana_rolls WHERE campaign=?','i',$id)->get_result()->fetch_all(MYSQLI_ASSOC)]);
} catch(Throwable $e) { error_log($e->getMessage()); fail('Não foi possível acessar a campanha. Verifique a conexão com o banco.',503); }
