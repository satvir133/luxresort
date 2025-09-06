<?php
class AuthService {
  public static function attempt(PDO $pdo, string $email, string $password): bool {
    $st=$pdo->prepare("SELECT * FROM users WHERE email=? AND is_active=1");
    $st->execute([$email]); $u=$st->fetch();
    if(!$u || !password_verify($password, $u['password_hash'])) return false;

    $roles = self::rolesFor($pdo, (int)$u['id']);
    Session::regenerate();
    Session::put('user', [
      'id'=>$u['id'], 'email'=>$u['email'],
      'name'=>trim(($u['first_name']??'').' '.($u['last_name']??'')),
      'roles'=>$roles
    ]);
    return true;
  }

  public static function registerGuest(PDO $pdo, array $data): int {
    $pdo->beginTransaction();
    $st=$pdo->prepare("INSERT INTO users(email,phone,password_hash,first_name,last_name)
                       VALUES(?,?,?,?,?)");
    $st->execute([
      $data['email'], $data['phone'] ?? null,
      password_hash($data['password'], PASSWORD_DEFAULT),
      $data['first_name'] ?? null, $data['last_name'] ?? null
    ]);
    $uid=(int)$pdo->lastInsertId();
    // attach 'guest' role
    $pdo->prepare("INSERT INTO user_roles(user_id,role_id)
                   SELECT ?, id FROM roles WHERE name='guest'")->execute([$uid]);
    $pdo->commit();
    return $uid;
  }

  public static function rolesFor(PDO $pdo, int $uid): array {
    $st=$pdo->prepare("SELECT r.name FROM roles r
                       JOIN user_roles ur ON ur.role_id=r.id WHERE ur.user_id=?");
    $st->execute([$uid]); return array_column($st->fetchAll(),'name');
  }

  public static function logout(): void { Session::flush(); }
}
