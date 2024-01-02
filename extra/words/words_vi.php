<?php
/* See file LICENSE for permissions and conditions to use the file. */
?>

<?php
$sitename = 'spamdforum';

// header.html
$indexphp_link_index = 'Trang chu';
$indexphp_link_account = 'Tai khoan';

// --------- profiles.php ---------------------------
if ($_SERVER['SCRIPT_NAME'] == '/profiles.php') {
	$title = "Ho so nguoi dung";
	$profilesphp['reg_date'] = "Ngay dang ky";
	$profilesphp['last_visit'] = "Truy cap lan cuoi";
	$profilesphp['err_not_found'] = "Khong tim thay nguoi dung!";
}
// --------------------------------------------------

// --------- login.php ------------------------------
if ($_SERVER['SCRIPT_NAME'] == '/account/login.php') {
	$title = "Dang nhap";
	$loginphp['h1_title'] = "Dang nhap tai khoan";
	$loginphp['h2_info'] = "Dang nhap vao tai khoan cua ban";
	$loginphp['cred_prompt'] = "Nhap vao thong tin dang nhap";
	$loginphp['msg'] = [
		'err_email' => "Nhap vao dia chi thu dien tu cua ban! \n",
		'err_password' => "Nhap vao mat khau cua ban! \n",
		'err_no_email' => "Dia chi thu dien tu khong co trong CSDL! \n",
		'err_wrong_auth' => "Mat khau sai, hoac tai khoan cua ban da bi vo hieu hoa! \n",
		'err_tryagain' => "Hay thu lai. \n"
	];

	$loginphp['form_input'] = [
		'email' => "Dia chi thu dien tu",
		'password' => "Mat khau",
		'login' => "Dang nhap"
	];
}
// --------------------------------------------------


// --------- register.php ---------------------------
if ($_SERVER['SCRIPT_NAME'] == '/account/register.php') {
	$title = "Dang ky tai khoan!";
	$registerphp['h1_title'] = "Dang ky tai khoan";
	$registerphp['h2_info'] = "Dang ky tai khoan de co nhieu quyen truy cap hon vao cac tinh nang cua website";
	$registerphp['reg_disabled'] = "Trang nay hien khong cho phep dang ky.";

	$registerphp['msg'] = [
		'err_name' => "Vui long nhap ten hop le! \n",
		'err_email' => "Vui long nhap dia chi thu dien tu hop le! \n",
		'err_password' => "Vui long nhap mat khau hop le! \n",
		'err_password_mismatch' => "Mat khau khong khop voi xac nhan! \n",
		'err_email_existed' => "Dia chi thu dien tu da duoc su dung. \n",
		'err_server' => "<h3>Ban khong the dang ky do mot loi he thong. Chung toi xin loi vi su co nay.</h3> \n",
		'err_tryagain' => "Vui long thu lai!"
	];

	$registerphp['form_input'] = [
		'name' => "Ten",
		'email' => "Thu dien tu",
		'password' => "Mat khau",
		'verify' => "Xac nhan",
		'register' => "Dang ky"
	];
}

// --------------------------------------------------

// --------- settings.php ---------------------------
if ($_SERVER['SCRIPT_NAME'] == '/account/settings.php') {
	$title = "Cai dat tai khoan";
	$settingsphp['h1_title'] = "Cai dat tai khoan";
	$settingsphp['msg'] = [
		'err_name' => "Vui long nhap ten hop le! \n",
		'err_email' => "Vui long nhap dia chi thu dien tu hop le! \n",
		'err_password' => "Vui long nhap mat khau hop le! \n",
		'err_password_mismatch' => "Mat khau khong khop voi xac nhan! \n",
		'err_email_existed' => "Dia chi thu dien tu da duoc su dung. \n",
		'err_server' => "<h3>Ban khong the dang ky do mot loi he thong. Chung toi xin loi vi su co nay.</h3> \n",
		'err_tryagain' => "Vui long thu lai!"
];

        $settingsphp['form_input'] = [
		'auth' => "Mat khau hien tai",
		'name' => "Ten",
		'email' => "Thu dien tu",
		'password' => "Mat khau",
		'verify' => "Xac nhan",
		'update_info' => "Cap nhat thong tin"
];
}
// --------------------------------------------------

// --------- usertable.php --------------------------
if ($_SERVER['SCRIPT_NAME'] == '/account/admin/usertable.php') {
	$title = 'Quan ly nguoi dung';
	$usertablephp['h1_title'] = "Quan ly nguoi dung";
	$usertablephp['p_red_notice'] = "Luu y: Thao tac xoa nguoi dung la KHONG THE HOAN TAC!";
	$usertablephp['msg'] = [
		'added_to_list' => "Them vao danh sach xoa:",
		'delete_request' => "Yeu cau xoa:",
		'delete_failed' => "Chua xoa duoc:",
		'err_priv_unmet' => "Vai tro cua ban chua du quyen de thuc hien tac vu nay. \n",
		'err_not_found' => "Khong tim thay nguoi dung! \n",
		'err_nopriv' => "Vai tro cua ban khong cho phep thuc hien hanh dong nay! \n"
	];
	$usertablephp['user_num_msg'] = [
		0 => "So nguoi dung da dang ky:",
		1 => "Hien chua co nguoi dung nao dang ky."
	];
	$usertablephp['th_usertable'] = [
		'id' => "ID",
		'name' => "Ten",
		'email' => "Thu dien tu",
		'reg_date' => "Ngay dang ky",
		'last_visit' => "Lan truy cap cuoi"
	];
	$usertablephp['input'] = [
		'delete' => "Xoa nguoi dung"
	];
}
// ---------------------------------------------------

// --------- pwlvltable.php --------------------------
if ($_SERVER['SCRIPT_NAME'] == '/account/admin/pwlvltable.php') {
	$title = "Quan ly quyen han";
	$pwlvltablephp['h1_title'] = "Quan ly quyen han";
	$pwlvltablephp['msg'] = [                                                                        
		'err_priv_unmet' => "Quyen han cua ban la chua du de thuc hien tac vu nay. \n",
		'err_not_found' => "Khong tim thay nguoi dung. \n"                             
	];
	$pwlvltablephp['input'] = [
		'update_power' => "Cap nhat quyen han"
	];
	$pwlvltablephp['th_pwlvltable'] = [
		'id' => "ID",
		'email' => "Thu dien tu",
		'powerlevel' => "Quyen han"
	];
	$pwlvltablephp['user_num_msg'][1] = "Hien chua co nguoi dung nao co quyen han dac biet (!= 0).";

	function get_msg($id, $word, $param=NULL) {
		global $pwlvltablephp;
		if (isset($param)) {
			if ($id == 'msg') {
				$pwlvltablephp['msg'] = [
					'update_success' => "Da cap nhat vai tro cua {$param['email']} tu {$param['old_pwlvl']} thanh {$param['new_pwlvl']} \n",
					'update_failed' => "Khong the cap nhat vai tro cua {$param['email']} tu {$param['old_pwlvl']} thanh {$param['new_pwlvl']} \n"
				];
			}
			if ($id == 'user_num_msg') {
				$pwlvltablephp['user_num_msg'] = [
					0 => "Co {$param['user_num']} quyen han dac biet (!= 0)",
				];
			}
		}
		return $pwlvltablephp[$id][$word];
	}

}
// ---------------------------------------------------

?>
