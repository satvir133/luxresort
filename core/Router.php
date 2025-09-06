<?php
class Router {
  private static array $routes = [];
  private static function norm(string $p): string { $p='/'.ltrim($p,'/'); return $p==='/'?'/':rtrim($p,'/'); }
  public static function get(string $p, callable $h, array $mw=[]){ self::$routes['GET'][self::norm($p)]=[$h,$mw]; }
  public static function post(string $p, callable $h, array $mw=[]){ self::$routes['POST'][self::norm($p)]=[$h,$mw]; }
  public static function dispatch(Request $req, Response $res, array $ctx){
    $m=$req->method(); $p=self::norm($req->path()); $match=self::$routes[$m][$p]??null;
    if(!$match){ $res->status(404); return "Not Found"; }
    [$handler,$mws]=$match;
    foreach($mws as $mw){ if($mw($req,$res,$ctx)===false) return $res->body(); }
    return $handler($req,$res,$ctx);
  }
}
