<?php
require_once __DIR__.'/../../core/Session.php';

class DashboardController {

  private function csrf(){
    $t = Session::get('_csrf');
    if (!$t){ $t = bin2hex(random_bytes(16)); Session::put('_csrf',$t); }
    return $t;
  }

  /* ====================== GUEST ====================== */
  public function guest($req,$res,$ctx){
    $pdo  = $ctx['pdo'];
    $user = Session::get('user');
    $csrf = $this->csrf();

    // My bookings (with simple aggregation)
    $sql = "
      SELECT b.id AS booking_id, b.status, b.total_cents, b.created_at,
             MIN(bi.check_in) AS check_in, MAX(bi.check_out) AS check_out,
             GROUP_CONCAT(r.code ORDER BY r.code SEPARATOR ', ') AS rooms
      FROM bookings b
      LEFT JOIN booking_items bi ON bi.booking_id=b.id
      LEFT JOIN rooms r ON r.id=bi.room_id
      WHERE b.user_id=?
      GROUP BY b.id, b.status, b.total_cents, b.created_at
      ORDER BY b.created_at DESC
    ";
    $st = $pdo->prepare($sql); $st->execute([$user['id']]);
    $bookings = $st->fetchAll();

    return $res->view('dashboard/guest', compact('bookings','csrf'));
  }

  /* ====================== STAFF ====================== */
  public function staff($req,$res,$ctx){
    $pdo  = $ctx['pdo'];
    $csrf = $this->csrf();

    // Recent bookings to manage
    $sql = "
      SELECT b.id, b.status, b.total_cents, b.created_at, u.email,
             MIN(bi.check_in) AS check_in, MAX(bi.check_out) AS check_out,
             GROUP_CONCAT(r.code ORDER BY r.code SEPARATOR ', ') AS rooms
      FROM bookings b
      LEFT JOIN booking_items bi ON bi.booking_id=b.id
      LEFT JOIN rooms r ON r.id=bi.room_id
      LEFT JOIN users u ON u.id=b.user_id
      GROUP BY b.id, b.status, b.total_cents, b.created_at, u.email
      ORDER BY b.created_at DESC
      LIMIT 100
    ";
    $st = $pdo->query($sql);
    $bookings = $st->fetchAll();

    $statuses = ['pending','confirmed','checked_in','checked_out','cancelled','refunded'];
    return $res->view('dashboard/staff', compact('bookings','statuses','csrf'));
  }

  public function staffSetStatus($req,$res,$ctx){
    $pdo  = $ctx['pdo'];
    $csrf = $req->input('_csrf') ?? '';
    if (!hash_equals(Session::get('_csrf') ?? '', $csrf)) {
      Session::flash('error','Security check failed.'); return $res->redirect('/staff');
    }

    $id = (int)$req->input('booking_id');
    $status = $req->input('status');
    if (!in_array($status, ['pending','confirmed','checked_in','checked_out','cancelled','refunded'])) {
      Session::flash('error','Invalid status.'); return $res->redirect('/staff');
    }

    try{
      $pdo->beginTransaction();
      $pdo->prepare("UPDATE bookings SET status=? WHERE id=?")->execute([$status,$id]);

      // Free or occupy nights depending on status changes
      if ($status==='cancelled' || $status==='refunded') {
        $pdo->prepare("
          DELETE bn FROM booking_nights bn
          JOIN booking_items bi ON bn.booking_item_id=bi.id
          WHERE bi.booking_id=?
        ")->execute([$id]);
      }
      $pdo->commit();
      Session::flash('success','Status updated.');
    } catch(Throwable $e){
      $pdo->rollBack();
      Session::flash('error','Update failed: '.$e->getMessage());
    }
    return $res->redirect('/staff');
  }

  /* ====================== ADMIN ====================== */
  public function admin($req,$res,$ctx){
    $pdo  = $ctx['pdo'];
    $csrf = $this->csrf();

    // Rooms list
    $rooms = $pdo->query("
      SELECT r.id,r.code,r.status, rt.name AS type_name
      FROM rooms r JOIN room_types rt ON rt.id=r.room_type_id
      ORDER BY rt.name, r.code
    ")->fetchAll();

    $types = $pdo->query("SELECT id,name FROM room_types ORDER BY name")->fetchAll();

    // Very simple analytics
    $totals = $pdo->query("
      SELECT
        (SELECT COUNT(*) FROM bookings WHERE status IN ('confirmed','checked_in','checked_out')) AS total_bookings,
        (SELECT COALESCE(SUM(total_cents),0) FROM bookings WHERE status IN ('confirmed','checked_in','checked_out')) AS revenue_cents
    ")->fetch();

    // occupancy last 30 days
    $occ = $pdo->query("
      SELECT
        (SELECT COUNT(*) FROM booking_nights bn
         WHERE bn.stay_date >= CURDATE() - INTERVAL 30 DAY
           AND bn.stay_date <  CURDATE()) AS booked_nights,
        (SELECT COUNT(*) FROM rooms WHERE status='active') * 30 AS capacity_nights
    ")->fetch();

    $bookedNights = (int)$occ['booked_nights'];
    $capacity     = max(1, (int)$occ['capacity_nights']);
    $occupancyPct = round(($bookedNights / $capacity) * 100, 1);

    return $res->view('dashboard/admin', [
      'rooms'=>$rooms,
      'types'=>$types,
      'csrf'=>$csrf,
      'totals'=>$totals,
      'occupancyPct'=>$occupancyPct
    ]);
  }

  public function adminCreateRoom($req,$res,$ctx){
    $pdo  = $ctx['pdo'];
    $csrf = $req->input('_csrf') ?? '';
    if (!hash_equals(Session::get('_csrf') ?? '', $csrf)) {
      Session::flash('error','Security check failed.'); return $res->redirect('/admin');
    }
    $typeId = (int)$req->input('room_type_id');
    $code   = trim($req->input('code') ?? '');

    if ($typeId<=0 || $code===''){ Session::flash('error','Room type and code required.'); return $res->redirect('/admin'); }

    try{
      $st = $pdo->prepare("INSERT INTO rooms (room_type_id,code,status) VALUES (?,?, 'active')");
      $st->execute([$typeId,$code]);
      Session::flash('success','Room created.');
    } catch(Throwable $e){
      Session::flash('error','Create failed: '.$e->getMessage());
    }
    return $res->redirect('/admin');
  }

  public function adminSetRoomStatus($req,$res,$ctx){
    $pdo  = $ctx['pdo'];
    $csrf = $req->input('_csrf') ?? '';
    if (!hash_equals(Session::get('_csrf') ?? '', $csrf)) {
      Session::flash('error','Security check failed.'); return $res->redirect('/admin');
    }
    $id = (int)$req->input('room_id');
    $status = $req->input('status');
    if (!in_array($status,['active','maintenance','retired'])){
      Session::flash('error','Invalid status.'); return $res->redirect('/admin');
    }
    try{
      $pdo->prepare("UPDATE rooms SET status=? WHERE id=?")->execute([$status,$id]);
      Session::flash('success','Room status updated.');
    } catch(Throwable $e){
      Session::flash('error','Update failed: '.$e->getMessage());
    }
    return $res->redirect('/admin');
  }
}
