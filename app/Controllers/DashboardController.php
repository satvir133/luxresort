<?php
class DashboardController {
  public function guest($req,$res,$ctx){ return $res->view('dashboard/guest.php'); }
  public function staff($req,$res,$ctx){ return $res->view('dashboard/staff.php'); }
  public function admin($req,$res,$ctx){ return $res->view('dashboard/admin.php'); }
}
