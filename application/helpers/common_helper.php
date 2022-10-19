<?php

/*
 * Get Codeigniter Instance
 */
$_CI = &get_instance();

/*
 * Assets url helper
 */
if (!function_exists('assets_url')) {

    function assets_url($args = '') {
        return base_url(_ASSESTS_PATH . $args);
    }

}

if (!function_exists('is_user_logged_in')) {

    function is_user_logged_in($args = '') {
       $ci = &get_instance();
       if($ci->session->userdata('logged_in')){
        
           return true;
       }
       else{
           return false;
       }
    }

}
if (!function_exists('privileage_check')) {

    function privileage_check($args = '') {
   $check=  is_user_logged_in();
   if(!$check):
       redirect(site_url('login'));
   endif;
      
    }

}
/*
 * JS url helper
 */
if (!function_exists('js_url')) {

    function js_url($args = '') {
        return base_url(_SCRIPTS_PATH . $args);
    }

}

/*
 * css url helper
 */
if (!function_exists('css_url')) {

    function css_url($args = '') {
        return base_url(_CSS_PATH . $args);
    }

}

/*
 * images url helper
 */
if (!function_exists('img_url')) {

    function img_url($args = '') {
        return base_url(_IMAGE_PATH . $args);
    }

}
/*
 * admin url helper
 */
if (!function_exists('admin_url')) {

    function admin_url($args = '') {
        return base_url(_ADMIN_PATH . $args);
    }

}

if (!function_exists('getFlashMessages')) {

    function getFlashMessages() {
        $messages = "";
        $ci = &get_instance();
        if ($ci->session->flashdata('success')) {
            $messages .= "<div class='alert alert-success'>" . $ci->session->flashdata('success') . "</div>";
        }
        if ($ci->session->flashdata('error')) {
            $messages .= "<div class='alert alert-error'>" . $ci->session->flashdata('error') . "</div>";
        }
        return $messages;
    }

}

if (!function_exists('get_league_id')) {

    function get_league_id($args = '') {
        $ci = &get_instance();
        $ci->load->library('session');
        if ($ci->session->userdata('league_id')):

            $data = $ci->session->userdata('league_id');
            if (isset($data['league_id'])):
                return $data['league_id'];
            endif;
        endif;
        return false;
    }

}
if (!function_exists('get_league_id')) {

    function get_league_id($args = '') {
        $ci = &get_instance();
        $ci->load->library('session');
        if ($ci->session->userdata('league_id')):

            $data = $ci->session->userdata('league_id');
            if (isset($data['league_id'])):
                return $data['league_id'];
            endif;
        endif;
        return false;
    }

}
if (!function_exists('get_role_id')) {

    function get_role_id() {
        $ci = &get_instance();
        $ci->load->library('session');
        if ($ci->session->userdata('logged_in')):
            $data = $ci->session->userdata('logged_in');

            return $data['user_role_id'];
        else:
            return false;
        endif;
    }

}
if (!function_exists('is_super_admin')) {

    function is_super_admin() {
        $ci = &get_instance();
        $ci->load->library('session');
        if ($ci->session->userdata('logged_in')):
            $data = $ci->session->userdata('logged_in');

            return $data['is_super_admin'];
        else:
            return false;
        endif;
    }

}
if (!function_exists('get_session_data')) {

    function get_session_data($value = "user_id") {
        $ci = &get_instance();
        $ci->load->library('session');
        if ($ci->session->userdata('logged_in')):
            $data = $ci->session->userdata('logged_in');

            return $data[$value];
        else:
            return false;
        endif;
    }

}
if (!function_exists('get_company_id')) {

    function get_company_id() {
        $ci = &get_instance();
        $ci->load->library('session');
        if ($ci->session->userdata('logged_in')):
            $data = $ci->session->userdata('logged_in');

            return $data['company_id'];
        else:
            $home_url=  site_url();
            echo "Access Denied or Your Session is Expired.  <a href='$home_url'>Click here to Login </a>";
            exit;
        endif;
    }

}
if (!function_exists('get_user_id')) {

    function get_user_id() {
        $ci = &get_instance();
        $ci->load->library('session');
        if ($ci->session->userdata('logged_in')):
            $data = $ci->session->userdata('logged_in');

            return $data['user_id'];
        else:
            echo "No Company selected";
            exit;
        endif;
    }

}
// Get No of matches per rond
if (!function_exists('get_match_nos')) {

    function get_match_nos($league_id = '') {
        if (get_league_id() != ''):
            $league_id = get_league_id();

            $ci = &get_instance();
            $ci->load->model('match_type_configuration');
            $data = $ci->match_type_configuration->get_match_nos($league_id);
            return $data;
        endif;
        return false;
    }

}
if (!function_exists('get_sport_type')) {

    function get_sport_type($league_id = '') {
        if ($league_id == ''):
            $league_id = get_league_id();
        endif;
        $ci = &get_instance();
        $ci->load->model('league_model');
        $type = $ci->league_model->getSportType($league_id);
        return $type;
    }

}

if (!function_exists('get_participant_options')) {

    function get_participant_options($league_id = '') {
        if ($league_id == ''):
            $league_id = get_league_id();
        endif;
        $ci = &get_instance();
        $ci->load->model('league_model');
        $options = $ci->league_model->getParticipantOptions($league_id);
        return $options;
    }

}

if (!function_exists('is_round_saved')) {

    function is_round_saved($round_no) {

        $ci = &get_instance();
        $ci->load->model('event_model');
        $is_saved = $ci->event_model->check_event_exists($round_no);
        return $is_saved;
    }

}
if (!function_exists('get_user_role_id')) {

    function get_user_role_id() {
        $player_type_id = 1;
        $ci = &get_instance();

        $data = $ci->session->userdata('logged_in');
        if (is_null($data['PlayerTypeID'])):
            if (get_league_id()):
                $league_data = $ci->session->userdata('league_id');
                return $league_data['league_role_id'];
            endif;
        else:
            return $data['PlayerTypeID'];
        endif;
        return $player_type_id;
    }

}

if (!function_exists('is_access_allowed')) {

    function is_access_allowed($minimal_type_id) {

        $role_id = get_user_role_id();

        if ($role_id < $minimal_type_id):
            return false;
        endif;
        return true;
    }

    if (!function_exists('get_player_id')) {

        function get_player_id() {

            $ci = &get_instance();

            $data = $ci->session->userdata('logged_in');

            return $data['PlayerID'];
        }

    }
    if (!function_exists('isPlayerCaptain')) {

        function isPlayerCaptain($team_id, $player_id) {
            $ci = &get_instance();
            $ci->load->model('player_playertypes_model');
            $player_type = $ci->player_playertypes_model->isCaptain($team_id, $player_id);

            return $player_type;
        }

    }
    if (!function_exists('isPlayerInTeam')) {

        function isPlayerInTeam($team_id, $player_id) {
            $ci = &get_instance();
            $ci->load->model('player_playertypes_model');
            $res = $ci->player_playertypes_model->isPlayerInTeam($team_id, $player_id);

            return $res;
        }

    }
    if (!function_exists('check_is_captain')) {

        function check_is_captain($team1, $team2) {
            $ci = &get_instance();
            $ci->load->model('team_teamplayers_model');
            $res = $ci->team_teamplayers_model->is_player_in_teams($team1, $team2);
            return $res;
        }

    }
    if (!function_exists('get_last_months')) {

        function get_last_months($no,$from_date) {
            $no=$no-1;
            $first = strtotime($from_date);
            $weeks = array();

            for ($i = $no; $i >= 0; $i--) {
                array_push($weeks, date('d-M-Y', strtotime("-$i day", $first)));
            }
            return $weeks;
        }

    }
     if (!function_exists('convert_number')) {
    function convert_number($no) 
{ 
    $words = array('0'=> '' ,'1'=> 'One' ,'2'=> 'Two' ,'3' => 'Three','4' => 'Four','5' => 'Five','6' => 'Six','7' => 'Seven','8' => 'Eight','9' => 'Nine','10' => 'Ten','11' => 'Eleven','12' => 'Twelve','13' => 'Thirteen','14' => 'Fouteen','15' => 'Fifteen','16' => 'Sixteen','17' => 'Seventeen','18' => 'Eighteen','19' => 'Nineteen','20' => 'Twenty','30' => 'Thirty','40' => 'Forty','50' => 'Fifty','60' => 'Sixty','70' => 'Seventy','80' => 'Eighty','90' => 'Ninety','100' => 'Hundred and','1000' => 'Thousand','100000' => 'Lakh','10000000' => 'Crore');
    if($no == 0)
        return ' ';
    else {
	$novalue='';
	$highno=$no;
	$remainno=0;
	$value=100;
	$value1=1000;       
            while($no>=100)    {
                if(($value <= $no) &&($no  < $value1))    {
                $novalue=$words["$value"];
                $highno = (int)($no/$value);
                $remainno = $no % $value;
                break;
                }
                $value= $value1;
                $value1 = $value * 100;
            }       
          if(array_key_exists("$highno",$words))
              return $words["$highno"]." ".$novalue." ".convert_number($remainno);
          else {
             $unit=$highno%10;
             $ten =(int)($highno/10)*10;            
             return $words["$ten"]." ".$words["$unit"]." ".$novalue." ".convert_number($remainno);
           }
    } 
}
     }
}
function get_tax_value($percentage,$amt){
   
    if($percentage==0):
        return 0;
    endif;
    return round(($amt/100)*$percentage,2);
}
//