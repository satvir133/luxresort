<?php
require_once __DIR__.'/../../core/Session.php';

class BookingController {
  // Show booking form
  public function showForm($req, $res, $ctx){
    $csrf = Session::get('_csrf') ?? bin2hex(random_bytes(16));
    Session::put('_csrf', $csrf);
    return $res->view('booking/form', compact('csrf'));
  }

  // Search available rooms
  public function search($req, $res, $ctx){
    $pdo = $ctx['pdo'];
    $roomTypeId = (int)$req->input('room_type_id');
    $checkIn    = $req->input('check_in');
    $checkOut   = $req->input('check_out');

    $sql = "SELECT r.*
            FROM rooms r
            WHERE r.room_type_id = :type
              AND r.status='active'
              AND NOT EXISTS (
                SELECT 1 FROM booking_nights bn
                WHERE bn.room_id = r.id
                  AND bn.stay_date >= :ci
                  AND bn.stay_date < :co
              )";
    $st = $pdo->prepare($sql);
    $st->execute([':type'=>$roomTypeId, ':ci'=>$checkIn, ':co'=>$checkOut]);
    $rooms = $st->fetchAll();

    return $res->view('booking/results', compact('rooms','checkIn','checkOut'));
  }

  // Confirm booking
  public function book($req, $res, $ctx){
    $pdo = $ctx['pdo'];
    $user = Session::get('user');
    if(!$user){ Session::flash('error','Login required'); return $res->redirect('/login'); }

    $roomId   = (int)$req->input('room_id');
    $checkIn  = new DateTime($req->input('check_in'));
    $checkOut = new DateTime($req->input('check_out'));
    $nights   = $checkOut->diff($checkIn)->days;

    try {
      $pdo->beginTransaction();

      // create booking
      $pdo->prepare("INSERT INTO bookings (user_id,status,total_cents) VALUES (?,?,0)")
          ->execute([$user['id'],'pending']);
      $bookingId = $pdo->lastInsertId();

      // create booking_item
      $pdo->prepare("INSERT INTO booking_items (booking_id,room_id,check_in,check_out,price_cents) VALUES (?,?,?,?,0)")
          ->execute([$bookingId,$roomId,$checkIn->format('Y-m-d'),$checkOut->format('Y-m-d')]);
      $itemId = $pdo->lastInsertId();

      // insert nights
      $nightPrice = 30000; // TODO: fetch from room_type/base_price
      $total = 0;
      for($i=0;$i<$nights;$i++){
        $date = clone $checkIn; $date->modify("+$i day");
        $pdo->prepare("INSERT INTO booking_nights (booking_item_id,room_id,stay_date,price_cents) VALUES (?,?,?,?)")
            ->execute([$itemId,$roomId,$date->format('Y-m-d'),$nightPrice]);
        $total += $nightPrice;
      }

      // update totals
      $pdo->prepare("UPDATE booking_items SET price_cents=? WHERE id=?")->execute([$total,$itemId]);
      $pdo->prepare("UPDATE bookings SET total_cents=?,status='confirmed' WHERE id=?")->execute([$total,$bookingId]);

      $pdo->commit();
      Session::flash('success','Booking confirmed!');
    } catch(Exception $e){
      $pdo->rollBack();
      Session::flash('error','Booking failed: '.$e->getMessage());
    }
    return $res->redirect('/dashboard');
  }

  
  public function cancel($req,$res,$ctx){
    $pdo  = $ctx['pdo'];
    $user = Session::get('user');
    $csrf = $req->input('_csrf') ?? '';
    if (!hash_equals(Session::get('_csrf') ?? '', $csrf)) {
      Session::flash('error','Security check failed.'); return $res->redirect('/dashboard');
    }

    $bookingId = (int)$req->input('booking_id');
    if ($bookingId<=0){ Session::flash('error','Invalid booking.'); return $res->redirect('/dashboard'); }

    // Ensure this booking belongs to the current user OR staff/admin (staff/admin handled in staffSetStatus)
    $st = $pdo->prepare("SELECT id,status FROM bookings WHERE id=? AND user_id=? LIMIT 1");
    $st->execute([$bookingId, $user['id']]);
    $row = $st->fetch();
    if (!$row){ Session::flash('error','Booking not found.'); return $res->redirect('/dashboard'); }

    if (!in_array($row['status'], ['pending','confirmed'])) {
      Session::flash('error','Booking cannot be cancelled at this stage.');
      return $res->redirect('/dashboard');
    }

    try{
      $pdo->beginTransaction();
      // Set booking to cancelled & free inventory by deleting nights (items cascade is kept; we only free nights)
      $pdo->prepare("UPDATE bookings SET status='cancelled' WHERE id=?")->execute([$bookingId]);

      // Remove nights to free rooms
      $pdo->prepare("
        DELETE bn FROM booking_nights bn
        JOIN booking_items bi ON bn.booking_item_id = bi.id
        WHERE bi.booking_id = ?
      ")->execute([$bookingId]);

      $pdo->commit();
      Session::flash('success','Booking cancelled.');
    } catch(Throwable $e){
      $pdo->rollBack();
      Session::flash('error','Cancel failed: '.$e->getMessage());
    }
    return $res->redirect('/dashboard');
  }
}
