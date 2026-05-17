<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		/*header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");*/
		$this->load->helper('date');
		$this->load->helper('betscore_admin');
		if(empty($this->session->userdata('admin_access_data'))) {
			redirect('/admin_login');
		}
	}
	
	private function string_data() {
		return "SELECT
			    (
			        SELECT 
			            COUNT(u.id)
			        FROM 
			            users as u
			    ) as `total_user`,
			    (
			        SELECT
			            COUNT(c.id)
			        FROM
			            club_users as c
			    ) as `total_club`,
			    (
			        SELECT
			            SUM(d.amount)
			        FROM
			            deposit as d
					WHERE d.status='SUCCESS'
			    ) as `total_deposit`,
			    (
			        SELECT
			            SUM(w.amount)
			        FROM
			            withdraw_req AS w
					WHERE w.status='SUCCESS'
			    ) as `total_withdraw`,
			    (
			        SELECT
			            SUM(cw.amount)
			        FROM
			            club_withdraw_req AS cw
					WHERE cw.status='SUCCESS'
			    ) as `total_club_withdraw`,
			    (
			        SELECT
			            SUM(bt.amount)
			        FROM
			            balance_transfer AS bt
			    ) as `total_balance_transfer`,
			    (
			        SELECT
			            COUNT(m.id)
			        FROM
			            matchname AS m
			        WHERE m.active_status='1' AND status=1
			    ) as `in_play`,
			    (
			        SELECT
			            COUNT(m.id)
			        FROM
			            matchname AS m
			        WHERE m.active_status='2' AND status=1
			    ) as `live`,
			    (
			        SELECT
			            COUNT(m.id)
			        FROM
			            matchname AS m
			        WHERE m.active_status='3' AND status=1
			    ) as `upcoming`";
	}

	public function index() {

		// dd();

		$data = array();
		$data['title'] = 'Dashboard';
		$data['sum_data'] = $this->db->query($this->string_data())->row();
		$data['get_users'] = $this->db->query("SELECT name, username, email, phone FROM users ORDER BY id DESC LIMIT 5")->result();
		$data['get_deposit'] = $this->db->query("SELECT d.*, c.club_name, u.name, u.username FROM `deposit` AS d INNER JOIN club_users AS c ON d.club_user_id=c.id INNER JOIN users AS u ON d.user_id=u.id ORDER BY id DESC LIMIT 5")->result();
		$this->load->view('admin/dashboard', $data);
	}

	public function manage_user()
	{
		$data = array();
		$data['title'] = 'Manage User';
		$data['all_users'] = $this->select_model->get_all_users();
		$data['body'] = $this->load->view('admin/manage_user/manage_user', $data, TRUE);
		$this->load->view('admin/common/admin_master', $data);
	}

	// -- reports functionality
	public function match_bit_report() {
		$data = [];
		$this->load->view('admin/report/match_bit_report', $data);
	}

	public function match_win_report() {
		$data = [];
		$this->load->view('admin/report/match_win_report', $data);
	}

	public function match_failed_report() {
		$data = [];
		$this->load->view('admin/report/match_failed_report', $data);
	}

	public function balance_report() {
		$data = [];
		$this->load->view('admin/report/balance_report', $data);
	}

	public function withdraw_report() {
		$data = [];
		$this->load->view('admin/report/withdraw_report', $data);
	}

	public function match_report() {
		$data = [];
		$this->load->view('admin/report/match_report', $data);
	}

	public function user_match_report() {
		$data = [];
		$this->load->view('admin/report/user_match_report', $data);
	}

	// -- end report functionality

	public function remove_user() {
		$id = $this->uri->segment(3);
		$this->db->query("DELETE FROM users WHERE id='{$id}'");
		$this->session->set_flashdata('msg', 'User deleted successfully');
		redirect('admin/manage_user');
	}

	public function manage_category()
	{
		$data = array();
		$data['title'] = 'Manage Category';
		$data['category'] = $this->db->query("SELECT * FROM category")->result();
		$data['body'] = $this->load->view('admin/manage_category/manage_category', $data, TRUE);
		$this->load->view('admin/common/admin_master', $data);
	}

    public function manage_sports()
    {
        $data = array();
        $data['title'] = 'Manage Sports';
        $data['names'] = $this->db->query("SELECT * FROM sportscategory")->result();
        // $this->load->view('admin/liveinplay/manage_sports', $data);
        $this->load->view('admin/liveinplay/manage_sports', $data);
	}

	public function match_details() {
    	$data = array();
		$data['title'] = 'Match Details';
		$data['sports'] = $this->db->query("SELECT * FROM sportscategory")->result();
		$data['league_data'] = $this->db->query("SELECT id, league_name FROM league_names WHERE status=1")->result();
		// $data['matches'] = $this->db->query("SELECT m.*, s.name FROM matchname AS m INNER JOIN sportscategory AS s ON m.sportscategory_id=s.id WHERE m.status IN(1,2) ORDER BY m.id DESC")->result();
		$data['matches'] = $this->db->query("SELECT m.*, s.name FROM matchname AS m INNER JOIN sportscategory AS s ON m.sportscategory_id=s.id WHERE m.active_status NOT IN(5, 8) ORDER BY m.id DESC")->result();
        $this->load->view('admin/liveinplay/match_details', $data);
	}

	public function manage_bet_ratio() {
		$data = [];
		$data['matches'] = $this->db->query("SELECT m.*, s.name FROM matchname AS m INNER JOIN sportscategory AS s ON m.sportscategory_id=s.id WHERE m.status IN(1,2) ORDER BY m.id DESC")->result();

		foreach($data['matches'] as $val) {
            //if($val->active_status==1 || $val->active_status==2 || $val->active_status==6) {
            if($val->active_status==1 || $val->active_status==2 || $val->active_status==7) {
				$data['live'][] = $val;
			}
			else if($val->active_status==3) {
				$data['upcoming'][] = $val;
			}
		}

		// dd($data['matches']);

		$this->load->view('admin/liveinplay/manage_bet_ratio', $data);
	}

	public function winner_selection() {
		$data = [];

		// $data['matches'] = $this->db->query("SELECT DISTINCT m.* FROM matchname AS m JOIN match_option AS mo ON mo.match_id=m.id WHERE m.active_status IN(1, 2, 3, 4, 6) AND mo.status IN(0,3) ORDER BY m.id DESC")->result();

		$data['matches'] = $this->db->query("SELECT DISTINCT m.* FROM matchname AS m JOIN match_option AS mo ON mo.match_id=m.id WHERE m.active_status IN(1, 2, 3, 4, 6, 7) AND mo.status IN(0,1,2,4) ORDER BY m.id DESC")->result();
		// dd($data);

		$this->load->view('admin/liveinplay/winner_selection', $data);
	}

	public function match_abandoned() {
		$red = $_SERVER['HTTP_REFERER'];
		$id = $this->uri->segment(3);

		if(!empty($id) && is_numeric($id)) {
			// -- update match status
			$this->db->query("UPDATE matchname SET active_status=5 WHERE id='{$id}' LIMIT 1");
			$matches = $this->db->query("SELECT * FROM match_option WHERE status NOT IN(3,4) AND match_id='{$id}'")->result();
			if(empty($matches)) {
				$this->session->set_flashdata('error', 'No bet found. status updated.');
				redirect($red);
			}

			foreach($matches as $vv) {
				// -- update match option status
				$this->db->query("UPDATE match_option SET status=4 WHERE id='{$vv->id}' LIMIT 1");

				$data = $this->db->query("SELECT * FROM matchbit_coin WHERE match_bet_option_id='{$vv->id}'")->result();

				// - update match bit coin
				$this->db->query("UPDATE matchbit_coin SET bet_status='CANCEL_ADMIN' WHERE match_bet_option_id='{$vv->id}'");

				foreach($data as $val) {
					// -- insert data in my coin
					$user_balance = get_user_current_balance($val->user_id);
					$cur_balance = $user_balance + $val->bet_coin;
			        $data_arr2 = array(
			        	'user_id'			=> $val->user_id,
			        	'club_id'			=> $val->club_id,
						'coin'				=> $val->bet_coin,
						'current_balance'	=> $cur_balance,
						'coin_type'			=> 'BETCANCEL',
						'method'			=> 'GET',
						'bet_option_id'		=> $val->match_bit_id,
						'created_at'		=> date("Y-m-d H:i:s")
			        );
			        $this->db->insert('my_coin', $data_arr2);
				}
			}
			$this->session->set_flashdata('msg', 'Match cancelled successfully');
			redirect($red);
		}

	}

	public function match_history() {
    	$data = array();
		$data['title'] = 'Match History';
		$data['sports'] = $this->db->query("SELECT * FROM sportscategory")->result();
		$data['league_data'] = $this->db->query("SELECT id, league_name FROM league_names WHERE status=1")->result();
		$data['matches'] = $this->db->query("SELECT m.*, s.name FROM matchname AS m INNER JOIN sportscategory AS s ON m.sportscategory_id=s.id WHERE m.active_status IN(5, 8) ORDER BY m.id DESC")->result();
        $this->load->view('admin/liveinplay/match_history', $data);
	}

	public function manage_questions() {
		$data = array();
		$data['title'] = 'Manage Questions';
		$match_id = $this->uri->segment(2);
		$data['match_name'] = $this->db->query("SELECT * FROM matchname WHERE id='{$match_id}'")->row();

		$data['title_1'] = "";
		$data['title_2'] = "";
		$title = explode("vs", strtolower($data['match_name']->title));
		if(!empty($title[1])) {
			$data['title_1'] = $title[0];
			$data['title_2'] = $title[1];
		}

		$data['match_id'] = $match_id;
		$data['sports'] = $this->db->query("SELECT * FROM sportscategory")->result();
		$data['matches'] = $this->db->query("SELECT * FROM match_option WHERE match_id='{$match_id}'")->result();
		$data['questions'] = $this->db->query("SELECT id, question_title FROM bet_questions WHERE sports_id='{$data['match_name']->sportscategory_id}' AND status=1")->result();
        $this->load->view('admin/liveinplay/manage_questions', $data);
	}
	
	public function add_question_page() {

		$data = array();
		$data['title'] = 'Add Question Page';
		$match_id = $this->uri->segment(2);
		$data['match_name'] = $this->db->query("SELECT sportscategory_id, title, league_title FROM matchname WHERE id='{$match_id}'")->row();

		$data['match_title'] = $data['match_name']->title;
		$data['title_1'] = "";
		$data['title_2'] = "";
		$title = explode("vs", strtolower($data['match_name']->title));
		if(!empty($title[1])) {
			$data['title_1'] = $title[0];
			$data['title_2'] = $title[1];
		}

		$data['match_id'] = $match_id;
		$data['sports'] = $this->db->query("SELECT * FROM sportscategory")->result();
		$data['matches'] = $this->db->query("SELECT * FROM match_option WHERE match_id='{$match_id}'")->result();
		$data['questions'] = $this->db->query("SELECT id, question_title, answer_type FROM bet_questions WHERE sports_id='{$data['match_name']->sportscategory_id}' AND status=1 AND answer_type!='NOT_SET'")->result();

		$my_array = [];
		foreach($data['matches'] as $vvv) {
			$my_array[] = strtolower($vvv->match_option_title);
		}

		$new_questions = [];
		for( $i=0; $i<count($data['questions']); $i++ ) {
			if( !in_array(strtolower($data['questions'][$i]->question_title), $my_array) ) {
				$new_questions[] = $data['questions'][$i];
			}
		}
		$data['new_questions'] = $new_questions;
        $this->load->view('admin/liveinplay/add_question_page', $data);

	}

	public function manage_answers() {
		$data = array();
		$data['title'] = 'Manage Answers';
		$q_id = $this->uri->segment(2);
		$data['question_data'] = $this->db->query("SELECT mp.*, m.title FROM match_option AS mp INNER JOIN matchname AS m ON mp.match_id=m.id WHERE mp.id='{$q_id}'")->row();

		// dd($data['question_data']);
		$data['question_id'] = $q_id;
		$data['answers'] = $this->db->query("SELECT * FROM match_option_details WHERE match_option_id='{$q_id}'")->result();
		// dd($data);
        $this->load->view('admin/liveinplay/manage_answers', $data);
	}

	public function post_create_answer() {
		$data = array();

		if($this->input->post('submit')) {

			$match_id = $this->input->post('match_id');
			$match_option_id = $this->input->post('match_option_id');
			$option_title = $this->input->post('option_title');
			$option_coin = $this->input->post('option_coin');
			$status = $this->input->post('status');
			$red = $_SERVER['HTTP_REFERER'];

			if( empty($option_title) && empty($option_coin) ) {
				$this->session->set_flashdata('error', 'All fields are required');
				redirect($red);
			}

	        $data_arr = array(
	        	'match_id'				=> $match_id,
	        	'match_option_id'		=> $match_option_id,
	        	'option_title'			=> $option_title,
	        	'option_coin'			=> $option_coin,
	        	'status'				=> $status,
	        	'created_at'			=> date('Y-m-d H:i:s')
	        );
	        $this->db->insert('match_option_details', $data_arr);
	        $last_id = $this->db->insert_id();

			$this->session->set_flashdata('msg', 'Answer created successfully');
			redirect($red);
		}
	}

	public function post_edit_answer() {
		$red = $_SERVER['HTTP_REFERER'];
		$option_details_id = $this->input->post('option_details_id');
		$option_title = $this->input->post('option_title');
		$option_coin = $this->input->post('option_coin');
		$status = $this->input->post('status');

		if( empty($option_details_id) || empty($option_title) || empty($option_coin) ) {
			$this->session->set_flashdata('error', 'All fields are required');
			redirect($red);
		}

		$this->db->query("UPDATE match_option_details SET
			option_title='{$_POST['option_title']}',
			option_coin='{$_POST['option_coin']}',
			status='{$_POST['status']}'
			WHERE id='{$_POST['option_details_id']}' LIMIT 1");
		$this->session->set_flashdata('msg', 'Answer edited successfully');
		redirect($red);
	}

	public function delete_answer() {
		$id = $this->uri->segment(3);
		$red = $_SERVER['HTTP_REFERER'];

		$this->db->query("DELETE FROM match_option_details WHERE id='{$id}' LIMIT 1");
		$this->session->set_flashdata('msg', 'Answer deleted successfully');
		redirect($red);
	}

	public function delete_question() {
		$id = $this->uri->segment(3);
		$red = $_SERVER['HTTP_REFERER'];

		$this->db->query("DELETE FROM match_option WHERE id='{$id}' LIMIT 1");
		$this->session->set_flashdata('msg', 'Question deleted successfully');
		redirect($red);
	}

	public function manage_questions_history() {
		$data = array();
		$data['title'] = 'Manage Questions';
		$match_id = $this->uri->segment(2);
		$data['match_name'] = $this->db->query("SELECT sportscategory_id, title, league_title FROM matchname WHERE id='{$match_id}'")->row();

		$data['title_1'] = "";
		$data['title_2'] = "";
		$title = explode("vs", strtolower($data['match_name']->title));
		if(!empty($title[1])) {
			$data['title_1'] = $title[0];
			$data['title_2'] = $title[1];
		}

		$data['match_id'] = $match_id;
		$data['sports'] = $this->db->query("SELECT * FROM sportscategory")->result();
		$data['matches'] = $this->db->query("SELECT * FROM match_option WHERE match_id='{$match_id}'")->result();
		$data['questions'] = $this->db->query("SELECT id, question_title FROM bet_questions WHERE sports_id='{$data['match_name']->sportscategory_id}' AND status=1")->result();
        $this->load->view('admin/liveinplay/manage_questions_history', $data);
	}

	public function matchbit_coin() {
		$data = array();
		$data['title'] = 'Match Bit Coin';
		$query_string = "SELECT mbc.*, mopd.option_title, mopd.match_id, mopd.match_option_id, mp.match_option_title, m.league_title, m.title, s.name FROM matchbit_coin AS mbc INNER JOIN match_option_details AS mopd ON mbc.match_bit_id=mopd.id INNER JOIN match_option AS mp ON mopd.match_option_id=mp.id INNER JOIN matchname AS m ON mopd.match_id=m.id INNER JOIN sportscategory AS s ON m.sportscategory_id=s.id WHERE mbc.bet_status='MATCH_RUNNING' ORDER BY mbc.id DESC LIMIT 100";
		$data['match_bit'] = $this->db->query($query_string)->result();
		// dd($data);
    	$this->load->view('admin/liveinplay/matchbit_coin', $data);
	}

	public function show_bet_list() {
		$data = array();
		$bet_id = $this->uri->segment(2);
		$bet_details = $this->db->query("SELECT mop.match_option_title, m.league_title, m.title, m.match_date_var, m.match_time_var FROM match_option AS mop INNER JOIN matchname AS m ON mop.match_id=m.id WHERE mop.id='{$bet_id}'")->row();
		$data['bet_title'] = $bet_details->title . ' => ' . $bet_details->league_title . ' => ' . ' <= ' . $bet_details->match_date_var . ' => ' . $bet_details->match_time_var . ' >> ' . $bet_details->match_option_title;

		$query_string = "SELECT mbc.*, mopd.option_title, mopd.match_id, mopd.match_option_id, mp.match_option_title, m.league_title, m.title, s.name FROM matchbit_coin AS mbc INNER JOIN match_option_details AS mopd ON mbc.match_bit_id=mopd.id INNER JOIN match_option AS mp ON mopd.match_option_id=mp.id INNER JOIN matchname AS m ON mopd.match_id=m.id INNER JOIN sportscategory AS s ON m.sportscategory_id=s.id WHERE mp.id='{$bet_id}' ORDER BY mbc.id DESC";
		$data['match_bit'] = $this->db->query($query_string)->result();
    	$this->load->view('admin/liveinplay/show_bet_list', $data);
	}

	public function matchbit_coin_prev() {
		$data = array();
		$data['title'] = 'Match Bit Coin Prev';
		$data['match_bit'] = $this->db->query("SELECT * FROM match_bit_coin ORDER BY id DESC LIMIT 100")->result();
    	$this->load->view('admin/liveinplay/matchbit_coin_prev', $data);
	}

	public function post_create_match() {
		$data = array();

		if($this->input->post('submit')) {
			$match_title = $this->input->post('match_title');
			$notification = $this->input->post('notification');
			$league_title = $this->input->post('league_title');
			$min_bet_limit = $this->input->post('min_bet_limit');
			$max_bet_limit = $this->input->post('max_bet_limit');
			$match_status = $this->input->post('match_status');

			$ball_by_ball = $this->input->post('ball_by_ball');
			$over_by_over = $this->input->post('over_by_over');
			$total_over = $this->input->post('total_over');
			if(empty($total_over)) {
				$total_over = 0;
			}

			$sports_name = $this->input->post('sports_name');
			$active_status = 1;
			// $score_1 = $this->input->post('score_1');
			// $score_2 = $this->input->post('score_2');
			$red = 'admin/match_details/';

			if(empty($sports_name) || empty($match_title) || empty($match_status) || empty($active_status)) {
				$this->session->set_flashdata('error', 'All fields are required');
				redirect($red);
			}

			$match_time_local = $_POST['starting_time'];
			$starting_time = date("Y-m-d H:i:s", strtotime($match_time_local));
			$match_date_var = date("M d,Y", strtotime($match_time_local));
			$match_time_var = date("h:i A", strtotime($match_time_local));

			$end_time_local = $_POST['ending_time'];
			$ending_time = date("Y-m-d H:i:s", strtotime($end_time_local));

	        $data_arr = array(
	        	'sportscategory_id'		=> $sports_name,
	        	'min_bet_limit'			=> $min_bet_limit,
	        	'max_bet_limit'			=> $max_bet_limit,
	        	'title'					=> $match_title,
	        	'active_status'			=> $match_status,
	        	'notification'			=> $notification,
	        	'league_title'			=> $league_title,

	        	'match_time_local'		=> $match_time_local,
	        	'match_date_var'		=> $match_date_var,
	        	'match_time_var'		=> $match_time_var,
	        	'starting_time'			=> $starting_time,

	        	'end_time_local'		=> $end_time_local,
	        	'ending_time'			=> $ending_time,

	        	'ball_by_ball'				=> $ball_by_ball,
	        	'over_by_over'				=> $over_by_over,
	        	'total_over'				=> $total_over,

	        	'status'				=> $active_status,
	        	'created_at'			=> date("Y-m-d H:i:s")
	        );

	        $this->db->insert('matchname', $data_arr);
			$this->session->set_flashdata('msg', 'Match created successfully');
			redirect($red);
		}
	}

	public function post_edit_match() {
		$data = array();
		$id = $_POST['match_edit_id'];

		if( $this->input->post('submit') ) {
			$sports_name = $this->input->post('sports_name');
			$match_title = $this->input->post('match_title');
			$league_title = $this->input->post('league_title');
			$min_bet_limit = $this->input->post('min_bet_limit');
			$max_bet_limit = $this->input->post('max_bet_limit');
			$match_status = $this->input->post('match_status');

			$ball_by_ball = $this->input->post('ball_by_ball');
			$over_by_over = $this->input->post('over_by_over');
			$total_over = $this->input->post('total_over');

			$active_status = 1;
			$notification = $this->input->post('notification');
			$red = 'admin/match_details/';

			if(empty($sports_name) || empty($match_title) || empty($match_status) || empty($active_status)) {
				$this->session->set_flashdata('error', 'All fields are required');
				redirect($red);
			}

			$match_time_local = $_POST['starting_time'];
			$starting_time = date("Y-m-d H:i:s", strtotime($match_time_local));
			$match_date_var = date("M d,Y", strtotime($match_time_local));
			$match_time_var = date("h:i A", strtotime($match_time_local));

			$end_time_local = $_POST['ending_time'];
			$ending_time = date("Y-m-d H:i:s", strtotime($end_time_local));

	        $data_arr = array(
	        	'sportscategory_id'		=> $sports_name,
				'title'					=> $match_title,
				'league_title'			=> $league_title,
				'min_bet_limit'			=> $min_bet_limit,
				'max_bet_limit'			=> $max_bet_limit,
	        	'active_status'			=> $match_status,
	        	'notification'			=> $notification,

	        	'match_time_local'		=> $match_time_local,
	        	'match_date_var'		=> $match_date_var,
	        	'match_time_var'		=> $match_time_var,
	        	'starting_time'			=> $starting_time,

	        	'end_time_local'		=> $end_time_local,
	        	'ending_time'			=> $ending_time,

	        	'ball_by_ball'				=> $ball_by_ball,
	        	'over_by_over'				=> $over_by_over,
	        	'total_over'				=> $total_over,

	        	'status'				=> $active_status,
	        	'created_at'			=> date("Y-m-d H:i:s")
	        );
			$this->db->where('id', $id);
			$this->db->update('matchname', $data_arr);
			$this->session->set_flashdata('msg', 'Match updated successfully');
			redirect($red);
		}
	}

	public function change_match_details_status() {
		$red = 'admin/match_details/';
		$this->db->query("UPDATE matchname SET status='{$_POST['match_status']}' WHERE id='{$_POST['match_id']}' LIMIT 1");
		$this->session->set_flashdata('msg', 'Match status changed successfully');
		redirect($red);
	}

	public function change_user_status() {
		$red = 'customer_user/';
		$this->db->query("UPDATE users SET status='{$_POST['match_status']}' WHERE id='{$_POST['hidden_user_id']}' LIMIT 1");
		$this->session->set_flashdata('msg', 'User status changed successfully');
		redirect($red);
	}

	public function post_create_club() {
		$data = array();

		if($this->input->post('submit')) {
			$club_name = $this->input->post('club_name');
			// $club_email = $this->input->post('club_email');
			$username = $this->input->post('username');
			$club_ratio = $this->input->post('club_ratio');
			// $show_ratio = $this->input->post('show_ratio');
			$club_mobile = $this->input->post('club_mobile');
			$password = $this->input->post('password');
			$red = 'club_user/';

			if(empty($club_name) || empty($username) || empty($club_mobile) || empty($password)) {
				$this->session->set_flashdata('error', 'All fields are required');
				redirect($red);
			}

			// -- if club exists
			$x_club = $this->db->query("SELECT * FROM club_users WHERE club_name='{$club_name}' OR username='{$username}'");
			$x_club = $x_club->result();
			if(!empty($x_club)) {
				$this->session->set_flashdata('error', 'Sorry, This club name or username already exists');
				redirect($red);
			}

	        $data_arr = array(
	        	'club_name'		=> $club_name,
	        	'username'	=> $username,
	        	'club_ratio'	=> $club_ratio,
	        	// 'show_ratio'	=> $show_ratio,
	        	'club_mobile'	=> $club_mobile,
	        	'password'		=> md5($password),
	        	'created_at'	=> date("Y-m-d H:i:s")
	        );
	        $this->db->insert('club_users', $data_arr);
			$this->session->set_flashdata('msg', 'Club created successfully');
			redirect($red);
		}
	}

	public function post_edit_club() {
		$data = array();

		if($this->input->post('submit')) {
			$club_name = $this->input->post('club_name');
			// $club_email = $this->input->post('club_email');
			$username = $this->input->post('username');
			$club_ratio = $this->input->post('club_ratio');
			$show_ratio = $this->input->post('show_ratio');
			$club_mobile = $this->input->post('club_mobile');
			$status = $this->input->post('status');
			$club_id = $this->input->post('club_id');
			$red = 'club_user/';
			// dd($_POST);

			if(empty($club_name) || empty($username) || empty($club_mobile)) {
				$this->session->set_flashdata('error', 'All fields are required');
				redirect($red);
			}

			// -- if club exists
			$prev_club = $this->db->query("SELECT * FROM club_users WHERE id='{$club_id}'")->row();

			if($club_name != $prev_club->club_name) {
				$x_club = $this->db->query("SELECT * FROM club_users WHERE club_name='{$club_name}'");
				$x_club = $x_club->result();
				if(!empty($x_club)) {
					$this->session->set_flashdata('error', 'Sorry, This club already exists');
					redirect($red);
				}
			}

			if($username != $prev_club->username) {
				$x_club = $this->db->query("SELECT * FROM club_users WHERE username='{$username}'");
				$x_club = $x_club->result();
				if(!empty($x_club)) {
					$this->session->set_flashdata('error', 'Sorry, This club already exists');
					redirect($red);
				}
			}

	        $data_arr = array(
	        	'club_name'		=> $club_name,
	        	'username'	=> $username,
	        	'club_ratio'	=> $club_ratio,
	        	'show_ratio'	=> $show_ratio,
	        	'club_mobile'	=> $club_mobile,
	        	'status'		=> $status,
	        	'updated_at'	=> date("Y-m-d H:i:s")
	        );
	        $this->db->where('id', $club_id);
	        $this->db->update('club_users', $data_arr);
			$this->session->set_flashdata('msg', 'Club edited successfully');
			redirect($red);
		}
	}

	public function add_notice() {
		$data = array();
		$red = 'notice-panel';
		if($this->input->post('submit')) {
			$description = $this->input->post('description');

			if(!empty($description)) {
		        $data_arr = array(
		        	'description'		=> $description,
		        	'created_at'	=> date("Y-m-d H:i:s")
		        );
		        $this->db->insert('notice', $data_arr);
				$this->session->set_flashdata('msg', 'Notice created successfully');
				redirect($red);
			}
		}
	}

	public function notice_inactive_status() {
		$id = $this->uri->segment(3);
		$red = 'notice-panel';
		$this->db->query("UPDATE notice SET status=0 WHERE id='{$id}' LIMIT 1");
		$this->session->set_flashdata('msg', 'Notice status changed successfully');
		redirect($red);
	}

	public function notice_active_status() {
		$id = $this->uri->segment(3);
		$red = 'notice-panel';
		$this->db->query("UPDATE notice SET status=1 WHERE id='{$id}' LIMIT 1");
		$this->session->set_flashdata('msg', 'Notice status changed successfully');
		redirect($red);
	}

	public function remove_notice() {
		$id = $this->uri->segment(3);
		$red = 'notice-panel';
		$this->db->query("DELETE FROM notice WHERE id='{$id}' LIMIT 1");
		$this->session->set_flashdata('msg', 'Notice deleted successfully');
		redirect($red);
	}

	public function edit_notice() {
		$data = array();
		$red = 'notice-panel';
		if($this->input->post('submit')) {
			$description = $this->input->post('description');
			$status = $this->input->post('status');
			$notice_id = $this->input->post('notice_id');

			if(!empty($description) && !empty($notice_id)) {
		        $data_arr = array(
		        	'description'		=> $description,
		        	'status'		=> $status,
		        	'updated_at'	=> date("Y-m-d H:i:s")
		        );
		        $this->db->where('id', $notice_id);
		        $this->db->update('notice', $data_arr);
				$this->session->set_flashdata('msg', 'Notice edited successfully');
				redirect($red);
			}
		}
	}

	public function club_user_change_password() {
		$data = array();

		if($this->input->post('submit')) {
			$password = $this->input->post('password');
			$club_id = $this->input->post('hidden_club_id');
			$red = 'club_user/';
			// dd($_POST);

			if(empty($password) || empty($club_id)) {
				$this->session->set_flashdata('error', 'All fields are required');
				redirect($red);
			}

	        $data_arr = array(
	        	'password'		=> md5($password),
	        	'updated_at'	=> date("Y-m-d H:i:s")
	        );
	        $this->db->where('id', $club_id);
	        $this->db->update('club_users', $data_arr);
			$this->session->set_flashdata('msg', 'Password changed successfully');
			redirect($red);
		}
	}

	public function change_match_status() {
		$red = 'admin/match_details/';
		$this->db->query("UPDATE matchname SET active_status='{$_POST['ch_match_status']}' WHERE id='{$_POST['match_status_id']}' LIMIT 1");
		$this->session->set_flashdata('msg', 'Match status changed successfully');
		redirect($red);
	}

	public function option_inactive() {
		$red = $_SERVER['HTTP_REFERER'];
		$id = $this->uri->segment(3);
		$this->db->query("UPDATE match_option SET status=0 WHERE id='{$id}' LIMIT 1");
		$this->session->set_flashdata('msg', 'Bet hide successfully');
		redirect($red);
	}

	public function option_active() {
		$red = $_SERVER['HTTP_REFERER'];
		$id = $this->uri->segment(3);
		$this->db->query("UPDATE match_option SET status=1 WHERE id='{$id}' LIMIT 1");
		$this->session->set_flashdata('msg', 'Bet active successfully');
		redirect($red);
	}

	public function remove_match() {
		$id = $this->uri->segment(2);
		$red = 'admin/match_details/';
		$this->db->query("DELETE FROM matchname WHERE id='{$id}' LIMIT 1");
		$this->session->set_flashdata('msg', 'Match deleted successfully');
		redirect($red);
	}

	public function edit_option_coin() {
		$red = $_SERVER['HTTP_REFERER'];
		$this->db->query("UPDATE match_option_details SET option_coin='{$_POST['option_coin']}' WHERE id='{$_POST['hidden_match_coin']}' LIMIT 1");
		$this->session->set_flashdata('msg', 'Coin updated successfully');
		redirect($red);
	}

	public function edit_option_title() {
		$red = $_SERVER['HTTP_REFERER'];
		$this->db->query("UPDATE match_option_details SET
			option_title='{$_POST['option_title']}',
			option_coin='{$_POST['option_coin']}'
			WHERE id='{$_POST['hidden_option_title']}' LIMIT 1");
		$this->session->set_flashdata('msg', 'Answer updated successfully');
		redirect($red);
	}

	public function remove_answer() {
		$id = $this->uri->segment(3);
		$red = $_SERVER['HTTP_REFERER'];
		$this->db->query("DELETE FROM match_option_details WHERE id='{$id}' LIMIT 1");
		$this->session->set_flashdata('msg', 'Answer deleted successfully');
		redirect($red);
	}

	public function create_match_option() {
		$data = array();

		if($this->input->post('submit')) {
			$match_option_name = $this->input->post('match_option_name');
			$red = $_SERVER['HTTP_REFERER'];

			if( empty($match_option_name) ) {
				$this->session->set_flashdata('error', 'All fields are required');
				redirect($red);
			}

	        $data_arr = array(
	        	'match_id'				=> $_POST['match_option_id'],
	        	'match_option_title'	=> $match_option_name,
	        	'capital'				=> $_POST['capital'],
	        	'min_bet_limit'			=> $_POST['min_bet_limit'],
	        	'max_bet_limit'			=> $_POST['max_bet_limit'],
	        	'status'				=> $_POST['bet_status'],
	        	'created_at'			=> date('Y-m-d H:i:s')
	        );
	        $this->db->insert('match_option', $data_arr);
	        $last_id = $this->db->insert_id();

			$this->session->set_flashdata('msg', 'Match question created successfully');
			redirect($red);
		}
	}

	public function create_match_option_static_new() {
		date_default_timezone_set( 'Asia/Kolkata' );
		$data = array();

		if($this->input->post('submit')) {

			// dd($_POST);
			// $match_option_name = $this->input->post('match_option_name');
			// $capital = $this->input->post('capital');
			$red = $_SERVER['HTTP_REFERER'];

			/*if( empty($match_option_name) ) {
				$this->session->set_flashdata('error', 'All fields are required');
				redirect($red);
			}*/

			/*$exists = $this->db->query("SELECT * FROM match_option WHERE match_id='{$_POST['match_option_id']}' AND match_option_title='{$match_option_name}'")->row();
			if(!empty($exists)) {
				$this->session->set_flashdata('error', 'This question already exists');
				redirect($red);
			}*/

			for($i=0; $i<count($_POST['questions']); $i++) {

				$end_time = NULL;
				if(!empty($_POST['end_time'][$i])) {
					$end_time = $_POST['end_time'][$i];
					$end_time = explode(" ", $end_time);
					$time = $end_time[1];
					$end_time = explode("/", $end_time[0]);
					$end_time = $end_time[2]."-".$end_time[0]."-".$end_time[1] . " " . $time.":00";
				}

		        $data_arr = array(
		        	'match_id'				=> $_POST['match_option_id'],
		        	'match_option_title'	=> $_POST['questions'][$i],
		        	'match_option_serial'	=> $i,
		        	'capital'				=> $_POST['capital'][$i],
		        	'min_bet_limit'			=> $_POST['min'][$i],
		        	'max_bet_limit'			=> $_POST['max'][$i],
		        	'end_time'				=> $end_time,
		        	'created_at'			=> date('Y-m-d H:i:s')
		        );
		        $this->db->insert('match_option', $data_arr);
		        $last_id = $this->db->insert_id();

		        for( $j=0; $j<count($_POST['answers_'.$i]); $j++ ) {
			        $data_arr = array(
			        	'match_id'				=> $_POST['match_option_id'],
			        	'match_option_id'		=> $last_id,
			        	'option_title'			=> $_POST['answers_'.$i][$j],
			        	'option_coin'			=> $_POST['ratios_'.$i][$j],
			        	// 'option_serial'			=> $_POST['first_option_serial'][$i],
			        	'created_at'			=> date('Y-m-d H:i:s')
			        );
			        $this->db->insert('match_option_details', $data_arr);
		        }
		        $this->db->query("UPDATE matchname SET has_question=1 WHERE id='{$_POST['match_option_id']}' LIMIT 1");
			}

			$this->session->set_flashdata('msg', 'Match question with answer created successfully');
			redirect($red);
		}
	}
	
	public function create_match_option_static() {
		$data = array();

		if($this->input->post('submit')) {

			$match_option_name = $this->input->post('match_option_name');
			$capital = $this->input->post('capital');
			$red = $_SERVER['HTTP_REFERER'];

			if( empty($match_option_name) ) {
				$this->session->set_flashdata('error', 'All fields are required');
				redirect($red);
			}

			$exists = $this->db->query("SELECT * FROM match_option WHERE match_id='{$_POST['match_option_id']}' AND match_option_title='{$match_option_name}'")->row();
			if(!empty($exists)) {
				$this->session->set_flashdata('error', 'This question already exists');
				redirect($red);
			}

	        $data_arr = array(
	        	'match_id'				=> $_POST['match_option_id'],
	        	'match_option_title'	=> $match_option_name,
	        	'capital'				=> $capital,
	        	'created_at'			=> date('Y-m-d H:i:s')
	        );
	        $this->db->insert('match_option', $data_arr);
	        $last_id = $this->db->insert_id();

	        $_POST['first_option_title'] = array_values(array_filter($_POST['first_option_title']));
	        $_POST['first_option_coin'] = array_values(array_filter($_POST['first_option_coin']));
	        // dd($_POST);

	        for($i=0; $i<count($_POST['first_option_title']); $i++) {
		        $data_arr = array(
		        	'match_id'				=> $_POST['match_option_id'],
		        	'match_option_id'		=> $last_id,
		        	'option_title'			=> $_POST['first_option_title'][$i],
		        	'option_coin'			=> $_POST['first_option_coin'][$i],
		        	// 'option_serial'			=> $_POST['first_option_serial'][$i],
		        	'created_at'			=> date('Y-m-d H:i:s')
		        );
		        $this->db->insert('match_option_details', $data_arr);
	        }

			$this->session->set_flashdata('msg', 'Match question with answer created successfully');
			redirect($red);
		}
	}

	public function edit_match_bet_name() {
		$red = $_SERVER['HTTP_REFERER'];

		if( empty($_POST['match_option_title']) ) {
			$this->session->set_flashdata('error', 'All fields are required');
			redirect($red);
		}

		$this->db->query("UPDATE match_option SET
			match_option_title='{$_POST['match_option_title']}',
			capital='{$_POST['capital']}',
			min_bet_limit='{$_POST['min_bet_limit']}',
			max_bet_limit='{$_POST['max_bet_limit']}',
			status='{$_POST['bet_status']}'
			WHERE id='{$_POST['hidden_match_bet_id']}' LIMIT 1");
		$this->session->set_flashdata('msg', 'Match question edited successfully');
		redirect($red);
	}

	public function edit_match_data() {
		$match_id = $_POST['match_id'];
		$data = $this->db->query("SELECT * FROM matchname WHERE id='{$match_id}'")->row();
		echo json_encode($data);
	}

	public function static_match_data() {
		$data=[];
		$match_id = $_POST['match_id'];
		$data['match_data'] = $this->db->query("SELECT * FROM matchname WHERE id='{$match_id}'")->row();
		// -- previous


		$data['match_name'] = $this->db->query("SELECT sportscategory_id, title, league_title FROM matchname WHERE id='{$match_id}'")->row();

		$data['match_title'] = $data['match_name']->title;
		$data['title_1'] = "";
		$data['title_2'] = "";
		$title = explode("vs", strtolower($data['match_name']->title));
		if(!empty($title[1])) {
			$data['title_1'] = $title[0];
			$data['title_2'] = $title[1];
		}

		$data['match_id'] = $match_id;
		// $data['sports'] = $this->db->query("SELECT * FROM sportscategory")->result();
		$data['matches'] = $this->db->query("SELECT * FROM match_option WHERE match_id='{$match_id}'")->result();
		$data['questions'] = $this->db->query("SELECT id, question_title, answer_type FROM bet_questions WHERE sports_id='{$data['match_name']->sportscategory_id}' AND status=1 AND answer_type!='NOT_SET'")->result();
		$my_array = [];
		foreach($data['matches'] as $vvv) {
			$my_array[] = strtolower($vvv->match_option_title);
		}

		$new_questions = [];
		for( $i=0; $i<count($data['questions']); $i++ ) {
			if( !in_array(strtolower($data['questions'][$i]->question_title), $my_array) ) {
				$new_questions[] = $data['questions'][$i];
			}
		}

		$data['new_questions'] = $new_questions;

		if(strpos($title[0], "[]")) {
			$first_team = trim(substr($title[0], strpos($title[0], "[]")+2));
			$data['first_team'] = ucfirst($first_team);
		}
		else {
			$data['first_team'] = ucfirst($title[0]);
		}
		// dd($title);
		
		if(strpos($title[1], "[]")) {
			$second_team = trim(substr($title[1], 0, strpos($title[1], "[]")));
			$data['second_team'] = ucfirst($second_team);
		}
		else {
			$data['second_team'] = ucfirst($title[1]);
		}

		// echo "<pre>";
		// print_r($data);
		// die;

		
		if($data['match_name']->sportscategory_id=='6') {
			$html = $this->load->view('admin/liveinplay/static_match_data_football', $data, true);
		}
		else {
			$html = $this->load->view('admin/liveinplay/static_match_data', $data, true);
		}
		echo $html;
	}

	public function view_match_data() {
		$match_id = $_POST['match_id'];
		$match_data = $this->db->query("SELECT * FROM match_option WHERE match_id='{$match_id}'")->result();

			?>
				<table class="table table-bordered">
					<tr>
						<th>Match Bet Name</th>
						<th>Serial</th>
						<th>Status</th>
						<th>Created</th>
						<th>Action</th>
					</tr>

					<?php foreach($match_data as $val) : ?>
						<tr>
							<td><?php echo $val->match_option_title; ?></td>
							<td><?php echo $val->match_option_serial; ?></td>
							<td><?php echo $val->status; ?></td>
							<td><?php echo $val->created_at; ?></td>
							<td></td>
						</tr>
					<?php endforeach; ?>
				</table>
			<?php
	}

	public function view_table() {
		$data = array();
		$this->load->view('admin/liveinplay/view_table', $data);
	}

	public function view_form() {
		$data = array();
		$this->load->view('admin/liveinplay/view_form', $data);
	}

	public function customer_deposit() {
    	$data = array();
		$data['title'] = 'Customer Deposit';
		$data['get_data'] = $this->db->query("SELECT d.*, c.club_name, u.name, u.username FROM `deposit` AS d INNER JOIN club_users AS c ON d.club_user_id=c.id INNER JOIN users AS u ON d.user_id=u.id WHERE d.created_at >= DATE(NOW()) - INTERVAL 7 DAY ORDER BY id DESC")->result();

		if($this->input->post('submit')) {
			$search_data = $this->input->post('search_data');
			$start_date = $this->input->post('start_date');
			$end_date = $this->input->post('end_date');
			// dd($_POST);
			if( !empty($search_data) && empty($start_date) && empty($end_date) ) {
				$data['get_data'] = $this->db->query("SELECT d.*, c.club_name, u.name, u.username FROM `deposit` AS d INNER JOIN club_users AS c ON d.club_user_id=c.id INNER JOIN users AS u ON d.user_id=u.id WHERE
					(d.created_at >= DATE(NOW()) - INTERVAL 7 DAY) AND
					(u.name LIKE '%$search_data%' OR
										u.username LIKE '%$search_data%' OR
										c.club_name LIKE '%$search_data%' OR
										d.transaction_id LIKE '%$search_data%' OR
										d.payment_method LIKE '%$search_data%' OR
										d.status LIKE '%$search_data%' OR
										d.user_phone LIKE '%$search_data%')
					
					ORDER BY id DESC")->result();
			}
			else if( !empty($start_date) && !empty($end_date) && empty($search_data) ) {
				$start_date = $start_date . " 00:00:00";
				$end_date = $end_date . " 23:59:59";

				$data['get_data'] = $this->db->query("SELECT d.*, c.club_name, u.name, u.username FROM `deposit` AS d INNER JOIN club_users AS c ON d.club_user_id=c.id INNER JOIN users AS u ON d.user_id=u.id WHERE
					d.created_at BETWEEN '{$start_date}' AND '{$end_date}'
					ORDER BY id DESC")->result();
			}
			else if( !empty($search_data) && !empty($start_date) && !empty($end_date) ) {
				$start_date = $start_date . " 00:00:00";
				$end_date = $end_date . " 23:59:59";

				$data['get_data'] = $this->db->query("SELECT d.*, c.club_name, u.name, u.username FROM `deposit` AS d INNER JOIN club_users AS c ON d.club_user_id=c.id INNER JOIN users AS u ON d.user_id=u.id WHERE
					(d.created_at BETWEEN '{$start_date}' AND '{$end_date}')
					AND
					(u.name LIKE '%$search_data%' OR
										u.username LIKE '%$search_data%' OR
										c.club_name LIKE '%$search_data%' OR
										d.transaction_id LIKE '%$search_data%' OR
										d.payment_method LIKE '%$search_data%' OR
										d.status LIKE '%$search_data%' OR
										d.user_phone LIKE '%$search_data%')
					
					ORDER BY id DESC")->result();
			}
			// dd($this->db->last_query());
		}

        $this->load->view('admin/customer/customer_deposit', $data);
	}

	public function show_deposit_data() {
    	$data = array();
		$data['get_data'] = $this->db->query("SELECT d.*, c.club_name, u.name, u.username FROM `deposit` AS d INNER JOIN club_users AS c ON d.club_user_id=c.id INNER JOIN users AS u ON d.user_id=u.id WHERE d.created_at >= DATE(NOW()) - INTERVAL 7 DAY ORDER BY id DESC LIMIT 20")->result();
        $this->load->view('admin/customer/customer_deposit_view', $data);
	}

	public function confirm_deposit() {
		$red = 'customer_deposit';

		// $status = $this->input->post('status');
		$deposit_id = $this->uri->segment(2);
		$chk_pending = $this->db->query("SELECT * FROM deposit WHERE id='{$deposit_id}' AND status='PENDING'")->row();
		if(empty($chk_pending)) {
			$this->session->set_flashdata('error', 'This deposit request already changed its status');
			redirect($red);
		}

		if(!empty($chk_pending) && !empty($deposit_id)) {

			$coin = $chk_pending->amount;
			$user_id = $chk_pending->user_id;
			$club_id = $chk_pending->club_user_id;
			$current_balance = get_user_current_balance($user_id);
			$current_balance = $current_balance + $coin;

	        $data_arr = array(
	        	'user_id'			=> $user_id,
	        	'club_id'			=> $club_id,
	        	'coin'				=> $coin,
	        	'current_balance'	=> $current_balance,
	        	'coin_type'			=> 'DEPOSIT',
	        	'method'			=> 'GET',
	        	'created_at'		=> date('Y-m-d H:i:s')
	        );
	        $this->db->insert('my_coin', $data_arr);

	        $data_arr2 = array(
				'status'	=> 'SUCCESS',
				'updated_at'	=> date("Y-m-d H:i:s")
	        );
			$this->db->where('id', $deposit_id);
			$this->db->update('deposit', $data_arr2);
			$this->session->set_flashdata('msg', 'Deposit status updated');
			redirect($red);
			return;

		}

	}

	public function approve_deposit() {
		$red = 'customer_deposit';
		if($this->input->post('submit')) {
			$status = $this->input->post('status');
			$deposit_id = $this->input->post('deposit_id');
			if(!empty($status) && !empty($deposit_id)) {
				if($status == 'SUCCESS') {
					$coin = $this->input->post('coin');

					if(empty($coin)) {
						$this->session->set_flashdata('error', 'Coin is required');
						redirect($red);
					}

					$deposit_data = $this->db->query("SELECT * FROM deposit WHERE id='{$deposit_id}'")->row();
					$user_id = $deposit_data->user_id;
					$club_id = $deposit_data->club_user_id;
					$current_balance = get_user_current_balance($user_id);
					$current_balance = $current_balance + $coin;

			        $data_arr = array(
			        	'user_id'			=> $user_id,
			        	'club_id'			=> $club_id,
			        	'coin'				=> $coin,
			        	'current_balance'	=> $current_balance,
			        	'coin_type'			=> 'DEPOSIT',
			        	'method'			=> 'GET',
			        	'created_at'		=> date('Y-m-d H:i:s')
			        );
			        $this->db->insert('my_coin', $data_arr);
				}

		        $data_arr2 = array(
					'status'	=> $status,
					'updated_at'	=> date("Y-m-d H:i:s")
		        );
				$this->db->where('id', $deposit_id);
				$this->db->update('deposit', $data_arr2);
				$this->session->set_flashdata('msg', 'Deposit status updated');
				redirect($red);
				return;

			}
		}
	}

	public function customer_withdraw() {
    	$data = array();
		$data['title'] = 'Customer Withdraw';
		$data['withdraw_data'] = $this->db->query("SELECT w.*, u.id AS c_user_id, u.name, u.username, c.club_name FROM withdraw_req AS w INNER JOIN users AS u ON w.user_id=u.id INNER JOIN club_users AS c ON w.club_user_id=c.id WHERE w.created_at >= DATE(NOW()) - INTERVAL 7 DAY ORDER BY id DESC LIMIT 20")->result();

		if($this->input->post('submit')) {
			$search_data = $this->input->post('search_data');
			$start_date = $this->input->post('start_date');
			$end_date = $this->input->post('end_date');
			// dd($_POST);
			if( !empty($search_data) && empty($start_date) && empty($end_date) ) {

				$data['withdraw_data'] = $this->db->query("SELECT w.*, u.name, u.username, c.club_name FROM withdraw_req AS w INNER JOIN users AS u ON w.user_id=u.id INNER JOIN club_users AS c ON w.club_user_id=c.id
					WHERE
					(w.created_at >= DATE(NOW()) - INTERVAL 7 DAY) AND
					(u.name LIKE '%$search_data%' OR
										u.username LIKE '%$search_data%' OR
										c.club_name LIKE '%$search_data%' OR
										w.payment_type LIKE '%$search_data%' OR
										w.payment_method LIKE '%$search_data%' OR
										w.from_no LIKE '%$search_data%' OR
										w.status LIKE '%$search_data%' OR
										w.account_number LIKE '%$search_data%')
					ORDER BY id DESC")->result();

			}
			else if( !empty($start_date) && !empty($end_date) && empty($search_data) ) {
				$start_date = $start_date . " 00:00:00";
				$end_date = $end_date . " 23:59:59";

				$data['withdraw_data'] = $this->db->query("SELECT w.*, u.name, u.username, c.club_name FROM withdraw_req AS w INNER JOIN users AS u ON w.user_id=u.id INNER JOIN club_users AS c ON w.club_user_id=c.id WHERE
					w.created_at BETWEEN '{$start_date}' AND '{$end_date}'
					ORDER BY id DESC")->result();
			}
			else if( !empty($search_data) && !empty($start_date) && !empty($end_date) ) {
				$start_date = $start_date . " 00:00:00";
				$end_date = $end_date . " 23:59:59";

				$data['withdraw_data'] = $this->db->query("SELECT w.*, u.name, u.username, c.club_name FROM withdraw_req AS w INNER JOIN users AS u ON w.user_id=u.id INNER JOIN club_users AS c ON w.club_user_id=c.id WHERE
					(w.created_at BETWEEN '{$start_date}' AND '{$end_date}')
					AND
					(u.name LIKE '%$search_data%' OR
										u.username LIKE '%$search_data%' OR
										c.club_name LIKE '%$search_data%' OR
										w.payment_type LIKE '%$search_data%' OR
										w.payment_method LIKE '%$search_data%' OR
										w.from_no LIKE '%$search_data%' OR
										w.status LIKE '%$search_data%' OR
										w.account_number LIKE '%$search_data%')
					
					ORDER BY id DESC")->result();
			}
			// dd($this->db->last_query());
		}

        $this->load->view('admin/customer/customer_withdraw', $data);
	}

	public function show_withdraw_data() {
    	$data = array();
		$data['withdraw_data'] = $this->db->query("SELECT w.*, u.id AS c_user_id, u.name, u.username, c.club_name FROM withdraw_req AS w INNER JOIN users AS u ON w.user_id=u.id INNER JOIN club_users AS c ON w.club_user_id=c.id WHERE w.created_at >= DATE(NOW()) - INTERVAL 7 DAY ORDER BY id DESC LIMIT 20")->result();
        $this->load->view('admin/customer/customer_withdraw_view', $data);
	}

	public function approve_withdraw() {
		$red = 'customer_withdraw';
		if($this->input->post('submit')) {
			// $status = $this->input->post('status');
			$from_no = $this->input->post('from_no');
			$status = 'SUCCESS';
			$withdraw_id = $this->input->post('withdraw_id');
			if(!empty($from_no) && !empty($withdraw_id)) {

				
		        $data_arr2 = array(
					'status'	=> $status,
					'from_no'	=> $from_no,
					'updated_at'	=> date("Y-m-d H:i:s")
		        );
				$this->db->where('id', $withdraw_id);
				$this->db->update('withdraw_req', $data_arr2);

				/*if($status == 'CANCEL') {
					$data = $this->db->query("SELECT * FROM withdraw_req WHERE id='{$withdraw_id}'")->row();

			        $get_cur_balance = get_user_current_balance($data->user_id) + $data->amount;
			        $data_arr_post = array(
			        	'user_id' => $data->user_id,
			        	'club_id' => $data->club_user_id,
						'coin'	=> $data->amount,
						'current_balance'	=> $get_cur_balance,
						'coin_type' => 'WITHDRAW_CANCEL',
						'method' => 'GET',
						'created_at'	=> date("Y-m-d H:i:s")
			        );
					$this->db->insert('my_coin', $data_arr_post);
				}*/

				$this->session->set_flashdata('msg', 'Withdraw confirmed successfully');
				redirect($red);
				return;

			}
		}
	}

	public function approve_club_withdraw() {
		$red = 'club_withdraw';
		if($this->input->post('submit')) {
			$status = 'SUCCESS';
			$withdraw_id = $this->input->post('withdraw_id');
			if(!empty($status) && !empty($withdraw_id)) {

				$from_no = $this->input->post('from_no');
				if($status == 'SUCCESS') {

					if(empty($from_no)) {
						$this->session->set_flashdata('error', 'From number is required');
						redirect($red);
					}
				}

				if($status == 'CANCEL' || $status=='REJECT') {

					$data = $this->db->query("SELECT * FROM club_withdraw_req WHERE id='{$withdraw_id}'")->row();
			        $get_cur_balance = get_club_balance($data->club_user_id) + $data->amount;
			        $data_arr_post = array(
			        	'club_id' => $data->club_user_id,
						'coin'	=> $data->amount,
						'current_balance'	=> $get_cur_balance,
						'source' => 'WITHDRAW_CANCEL',
						'method' => 'GET',
						'created_at'	=> date("Y-m-d H:i:s")
			        );
					$this->db->insert('club_coin', $data_arr_post);

				}

		        $data_arr2 = array(
					'status'	=> $status,
					'from_no'	=> $from_no,
					'updated_at'	=> date("Y-m-d H:i:s")
		        );
				$this->db->where('id', $withdraw_id);
				$this->db->update('club_withdraw_req', $data_arr2);
				$this->session->set_flashdata('msg', 'Club withdraw status updated');
				redirect($red);
				return;

			}
		}
	}

	public function customer_complain() {
    	$data = array();
		$data['title'] = 'Customer Complain';
		$data['complain_data'] = $this->db->query("SELECT c.*, u.name FROM complain AS c INNER JOIN users AS u ON c.user_id=u.id WHERE c.created_at >= ( CURDATE() - INTERVAL 7 DAY ) ORDER BY c.id DESC")->result();
        $this->load->view('admin/customer/customer_complain', $data);
	}

	public function view_complain() {
		$red = 'customer_complain';
		$uri = $this->uri->segment(3);
		$this->db->query("UPDATE complain SET admin_view=1 WHERE id='{$uri}' LIMIT 1");
		redirect($red);
	}

	public function reply_complain() {
		$red = 'customer_complain';
		if($this->input->post('submit')) {
			$reply = $this->input->post('reply');
			$complain_id = $this->input->post('complain_id');
			if(!empty($reply) && !empty($complain_id)) {

		        $data_arr2 = array(
					'reply'	=> $reply,
					'updated_at'	=> date("Y-m-d H:i:s")
		        );
				$this->db->where('id', $complain_id);
				$this->db->update('complain', $data_arr2);
				$this->session->set_flashdata('msg', 'Reply message sent to user successfully');
				redirect($red);
				return;

			}
		}
	}

	public function remove_complain() {
		$id = $this->uri->segment(2);
		$red = 'customer_complain/';

		$res = $this->db->query("DELETE FROM complain WHERE id='{$id}' LIMIT 1");
		$this->session->set_flashdata('msg', 'Complain deleted successfully');
		redirect($red);
	}

	public function reject_withdraw() {
		$id = $this->uri->segment(2);
		$red = 'customer_withdraw/';
		$status = 'REJECT';
		$data = $this->db->query("SELECT * FROM withdraw_req WHERE id='{$id}'")->row();

        $get_cur_balance = get_user_current_balance($data->user_id) + $data->amount;
        $data_arr_post = array(
        	'user_id' => $data->user_id,
        	'club_id' => $data->club_user_id,
			'coin'	=> $data->amount,
			'current_balance'	=> $get_cur_balance,
			'coin_type' => 'WITHDRAW_CANCEL',
			'method' => 'GET',
			'created_at'	=> date("Y-m-d H:i:s")
        );
		$this->db->insert('my_coin', $data_arr_post);

        $data_arr2 = array(
			'status'	=> $status,
			'updated_at'	=> date("Y-m-d H:i:s")
        );
		$this->db->where('id', $id);
		$this->db->update('withdraw_req', $data_arr2);
		$this->session->set_flashdata('msg', 'Withdraw rejected successfully');
		redirect($red);
	}

	public function reject_club_withdraw() {
		$id = $this->uri->segment(3);
		$red = 'club_withdraw/';
		$status = 'REJECT';
		$data = $this->db->query("SELECT * FROM club_withdraw_req WHERE id='{$id}'")->row();

        $get_cur_balance = get_club_balance($data->club_user_id) + $data->amount;
        $data_arr_post = array(
        	'club_id' => $data->club_user_id,
        	// 'club_user_id' => $data->club_user_id,
			'coin'	=> $data->amount,
			'current_balance'	=> $get_cur_balance,
			'source' => 'WITHDRAW_CANCEL',
			'method' => 'GET',
			'created_at'	=> date("Y-m-d H:i:s")
        );
		$this->db->insert('club_coin', $data_arr_post);

        $data_arr2 = array(
			'status'	=> $status,
			'updated_at'	=> date("Y-m-d H:i:s")
        );
		$this->db->where('id', $id);
		$this->db->update('club_withdraw_req', $data_arr2);
		$this->session->set_flashdata('msg', 'Withdraw rejected');
		redirect($red);
	}

	public function customer_balance_transfer() {
    	$data = array();
		$data['title'] = 'Customer Balance Transfer';
		$data['transfer_data'] = $this->db->query("SELECT * FROM balance_transfer ORDER BY id DESC LIMIT 20")->result();
		// $data['transfer_data'] = $this->db->query("SELECT * FROM my_coin AS mc WHERE coin_type IN('TRANSFER_GET', 'TRANSFER_POST') ORDER BY mc.id DESC")->result();
        $this->load->view('admin/customer/customer_balance_transfer', $data);
	}

	public function club_withdraw() {
    	$data = array();
		$data['title'] = 'Club Withdraw';
		$data['withdraw_data'] = $this->db->query("SELECT cw.*, c.club_name FROM club_withdraw_req AS cw INNER JOIN club_users AS c ON cw.club_user_id=c.id WHERE cw.created_at >= DATE(NOW()) - INTERVAL 27 DAY ORDER BY id DESC")->result();
        $this->load->view('admin/club/club_withdraw', $data);
	}

	// user panel data
	public function club_user() {
    	$data = array();
		$data['title'] = 'Club User';
		$data['club_data'] = $this->db->query("SELECT * FROM club_users ORDER BY id DESC LIMIT 20")->result();
        // $this->load->view('admin/customer/club_user', $data);
        $this->load->view('admin/club/club_user', $data);
	}

	public function admin_user() {
    	$data = array();
		$data['title'] = 'Customer User';
		$data['role_data'] = $this->db->query("SELECT * FROM role_management WHERE id!=1")->result();
		$data['admin_data'] = $this->db->query("SELECT * FROM admin ORDER BY id DESC LIMIT 20")->result();
        $this->load->view('admin/user/admin_user', $data);
	}

	public function remove_admin() {
		$id = $this->uri->segment(3);
		$red = 'admin_user/';

		$res = $this->db->query("DELETE FROM admin WHERE id='{$id}' LIMIT 1");
		$this->session->set_flashdata('msg', 'Admin user deleted successfully');
		redirect($red);
	}

	public function remove_admin_role() {
		$id = $this->uri->segment(3);
		$red = 'manage_role/';

		$res = $this->db->query("DELETE FROM role_management WHERE id='{$id}' LIMIT 1");
		$this->session->set_flashdata('msg', 'Admin role deleted successfully');
		redirect($red);
	}

	public function manage_role() {
    	$data = array();
		$data['title'] = 'Admin Role';
		$data['roles'] = $this->db->query("SELECT * FROM role_data")->result();
		// dd($data['roles']);
		$data['role_data'] = $this->db->query("SELECT * FROM role_management ORDER BY id DESC LIMIT 20")->result();
        $this->load->view('admin/user/manage_role', $data);
	}

	public function notice_panel() {
    	$data = array();
		$data['title'] = 'Notice Panel';
		$data['get_data'] = $this->db->query("SELECT * FROM notice")->result();
        $this->load->view('admin/notice_panel', $data);
	}

	public function deposit_account() {
    	$data = [];
		$data['title'] = 'Deposit Account';
		$data['get_data'] = $this->db->query("SELECT * FROM deposit_account")->result();
        $this->load->view('admin/deposit_account', $data);
	}

	public function bet_question() {
    	$data = [];
    	$data['sports'] = $this->db->query("SELECT * FROM sportscategory WHERE active_status=1")->result();
    	//dd($data);
		// $data['get_data'] = $this->db->query("SELECT b.*, s.name FROM bet_questions AS b INNER JOIN sportscategory AS s ON b.sports_id=s.id WHERE b.sports_id=2 ORDER BY b.serial")->result();
		$data['get_data'] = $this->db->query("SELECT b.*, s.name FROM bet_questions AS b INNER JOIN sportscategory AS s ON b.sports_id=s.id ORDER BY b.serial")->result();
		//dd($data);
        $this->load->view('admin/bet_question', $data);
	}
	
	public function bet_question_football() {
    	$data = [];
    	$data['sports'] = $this->db->query("SELECT * FROM sportscategory WHERE active_status=1")->result();
    	// dd($data);
		$data['get_data'] = $this->db->query("SELECT b.*, s.name FROM bet_questions AS b INNER JOIN sportscategory AS s ON b.sports_id=s.id WHERE b.sports_id=6 ORDER BY b.serial")->result();
        $this->load->view('admin/bet_question_football', $data);
	}

	public function create_bet_question() {
		$data = array();
		$red = $_SERVER['HTTP_REFERER'];

		if($this->input->post('submit')) {
			$sports_id = $this->input->post('sports_id');
			$question_title = $this->input->post('question_title');

			if( empty($sports_id) || empty($question_title) ) {
				$this->session->set_flashdata('error', 'All fields are required');
				redirect($red);
			}

			$exists = $this->db->query("SELECT * FROM bet_questions WHERE sports_id='{$sports_id}' AND question_title='{$question_title}'")->row();
			if(!empty($exists)) {
				$this->session->set_flashdata('error', 'This question already exits in this category');
				redirect($red);
			}

			if( !empty($sports_id) && !empty($question_title) ) {
		        $data_arr = array(
		        	'sports_id'		=> $sports_id,
		        	'question_title'		=> $question_title,
		        	'serial'		=> $this->input->post('serial'),
		        	'created_at'	=> date("Y-m-d H:i:s")
		        );
		        $this->db->insert('bet_questions', $data_arr);
				$this->session->set_flashdata('msg', 'Bet question created successfully');
				redirect($red);
			}
		}
	}

	public function edit_bet_question() {
		$data = array();
		$id = $_POST['question_id'];

		if( $this->input->post('submit') ) {
			$sports_id = $this->input->post('sports_id');
			$question_title = $this->input->post('question_title');
			$status = $this->input->post('status');
			$red = $_SERVER['HTTP_REFERER'];

			if( empty($sports_id) || empty($id) || empty($question_title) ) {
				$this->session->set_flashdata('error', 'All fields are required');
				redirect($red);
			}

	        $data_arr = array(
	        	'sports_id'		=> $sports_id,
	        	'question_title'		=> $question_title,
	        	'serial'		=> $this->input->post('serial'),
	        	'status'		=> $status,
	        	'created_at'	=> date("Y-m-d H:i:s")
	        );
			$this->db->where('id', $id);
			$this->db->update('bet_questions', $data_arr);
			$this->session->set_flashdata('msg', 'Bet question updated successfully');
			redirect($red);
		}
	}

	public function remove_bet_question() {
		$id = $this->uri->segment(3);
		$red = $_SERVER['HTTP_REFERER'];
		$this->db->query("DELETE FROM bet_questions WHERE id='{$id}' LIMIT 1");
		$this->session->set_flashdata('msg', 'Question deleted successfully');
		redirect($red);
	}

	public function create_answer_option() {
		// dd($_POST);

		$data = array();

		if($this->input->post('submit')) {
			$match_option_name = $this->input->post('match_option_name');
			$question_id = $this->input->post('question_id');
			$red = $_SERVER['HTTP_REFERER'];

			if( empty($match_option_name) ) {
				$this->session->set_flashdata('error', 'All fields are required');
				redirect($red);
			}

			if($match_option_name == 'CUSTOM') {
		        $_POST['first_option_title'] = array_values(array_filter($_POST['first_option_title']));
		        // $_POST['first_option_coin'] = array_values(array_filter($_POST['first_option_coin']));

		        for($i=0; $i<count($_POST['first_option_title']); $i++) {
			        $data_arr = array(
			        	'question_id'	=> $question_id,
			        	'answer'		=> $_POST['first_option_title'][$i],
			        );
			        $this->db->insert('bet_answers', $data_arr);
		        }
			} else if($match_option_name == 'TEAM_NAME') {

			} else if($match_option_name == 'TEAM_NAME_WITH_DRAW') {

			}
			$this->db->query("UPDATE bet_questions SET answer_type='{$match_option_name}' WHERE id='{$question_id}' LIMIT 1");

			$this->session->set_flashdata('msg', 'Match question with answer created successfully');
			redirect($red);
		}

	}

	public function get_answer_data() {
		// dd($_POST);
		$data = [];
		$cus_data = [];
		$data['error'] = 0;
		$data['error_msg'] = "";
		$cus_data['answer_type'] = $_POST['answer_type'];

		$question_id = $_POST['question_id'];
		$answer_type = $_POST['answer_type'];
		$match_title = $_POST['match_title'];

		$cus_data['title_1'] = "";
		$cus_data['title_2'] = "";
		$title = explode("vs", strtolower($match_title));
		if(!empty($title[1])) {
			$cus_data['title_1'] = $title[0];
			$cus_data['title_2'] = $title[1];
		}

		if( !empty($question_id) && !empty($answer_type) ) {
			if( $answer_type == 'TEAM_NAME' || $answer_type == 'TEAM_NAME_WITH_DRAW' || $answer_type == 'TEAM_NAME_WITH_TIE' || $answer_type == 'TEAM_NAME_WITH_NOGOAL' ) {

				$view_data = $this->load->view('admin/liveinplay/answer_data', $cus_data, true);
				$new_data = [];
				$new_data['error'] = 0;
				$new_data['get_data'] = "$view_data";
				$new_data['error_msg'] = "";
				echo json_encode($new_data);
				exit();

			}
			else if( $answer_type == 'CUSTOM' ) {
				$row_data = $this->db->query("SELECT * FROM bet_answers WHERE question_id='{$question_id}'")->result();

				if(empty($row_data)) {
					$data['error'] = 1;
					$data['error_msg'] = "Invalid data source";
					echo json_encode($data);
					exit();
				}
				$cus_data['get_data'] = $row_data;

				$view_data = $this->load->view('admin/liveinplay/answer_data', $cus_data, true);
				$new_data = [];
				$new_data['error'] = 0;
				$new_data['get_data'] = $view_data;
				$new_data['error_msg'] = "";
				echo json_encode($new_data);
				exit();
			}
		}
		else {

			$data['error'] = 1;
			$data['error_msg'] = "Wrong data source";
			echo json_encode($data);
			exit();

		}

	}

	public function remove_question_answer() {
		$question_id = $this->uri->segment(3);
		$type = $this->uri->segment(4);
		$red = $_SERVER['HTTP_REFERER'];

		if($type=='TEAM_NAME' || $type=='TEAM_NAME_WITH_DRAW') {
			$this->db->query("UPDATE bet_questions SET answer_type='NOT_SET' WHERE id='{$question_id}' LIMIT 1");
		}
		else if($type=='CUSTOM') {
			$this->db->query("UPDATE bet_questions SET answer_type='NOT_SET' WHERE id='{$question_id}' LIMIT 1");
			$this->db->query("DELETE FROM bet_answers WHERE question_id='{$question_id}'");
		}
		$this->session->set_flashdata('msg', 'Answers deleted successfully');
		redirect($red);
	}


	// -- start league
	public function manage_league() {
    	$data = [];
		$data['get_data'] = $this->db->query("SELECT * FROM league_names")->result();
        $this->load->view('admin/manage_league', $data);
	}

	public function add_league_name() {
		$data = array();
		$red = 'manage_league';
		if($this->input->post('submit')) {
			$league_name = $this->input->post('league_name');

			if(!empty($league_name)) {
		        $data_arr = array(
		        	'league_name'		=> $league_name,
		        	'created_at'	=> date("Y-m-d H:i:s")
		        );
		        $this->db->insert('league_names', $data_arr);
				$this->session->set_flashdata('msg', 'League name created successfully');
				redirect($red);
			}
		}
	}

	public function edit_league_name() {
		$data = array();
		$id = $_POST['league_id'];

		if( $this->input->post('submit') ) {
			$league_name = $this->input->post('league_name');
			$status = $this->input->post('status');
			$red = 'manage_league/';

			if( empty($league_name) || empty($id) ) {
				$this->session->set_flashdata('error', 'All fields are required');
				redirect($red);
			}

	        $data_arr = array(
	        	'league_name'		=> $league_name,
	        	'status'		=> $status,
	        	'created_at'	=> date("Y-m-d H:i:s")
	        );
			$this->db->where('id', $id);
			$this->db->update('league_names', $data_arr);
			$this->session->set_flashdata('msg', 'League name updated successfully');
			redirect($red);
		}
	}

	public function remove_league_name() {
		$id = $this->uri->segment(3);
		$red = 'manage_league';
		$this->db->query("DELETE FROM league_names WHERE id='{$id}' LIMIT 1");
		$this->session->set_flashdata('msg', 'League name deleted successfully');
		redirect($red);
	}

	public function add_deposit_account() {
		$data = array();
		$red = 'deposit_account';
		if($this->input->post('submit')) {
			$account_name = $this->input->post('account_name');
			$account_no = $this->input->post('account_no');

			if(!empty($account_name) && !empty($account_no)) {
		        $data_arr = array(
		        	'account_name'		=> $account_name,
		        	'account_no'		=> $account_no,
		        	'created_at'	=> date("Y-m-d H:i:s")
		        );
		        $this->db->insert('deposit_account', $data_arr);
				$this->session->set_flashdata('msg', 'Account created successfully');
				redirect($red);
			}
		}
	}

	public function edit_deposit_account() {
		$data = array();
		$id = $_POST['account_id'];

		if( $this->input->post('submit') ) {
			$account_name = $this->input->post('account_name');
			$account_no = $this->input->post('account_no');
			$status = $this->input->post('status');
			$red = 'deposit_account/';

			if( empty($account_name) || empty($account_no) || empty($status) || empty($id) ) {
				$this->session->set_flashdata('error', 'All fields are required');
				redirect($red);
			}

	        $data_arr = array(
	        	'account_name'		=> $account_name,
	        	'account_no'		=> $account_no,
	        	'status'		=> $status,
	        	'created_at'	=> date("Y-m-d H:i:s")
	        );
			$this->db->where('id', $id);
			$this->db->update('deposit_account', $data_arr);
			$this->session->set_flashdata('msg', 'Account updated successfully');
			redirect($red);
		}
	}

	public function account_inactive_status() {
		$id = $this->uri->segment(3);
		$red = 'deposit_account';
		$this->db->query("UPDATE deposit_account SET status=0 WHERE id='{$id}' LIMIT 1");
		$this->session->set_flashdata('msg', 'Account status changed successfully');
		redirect($red);
	}

	public function account_active_status() {
		$id = $this->uri->segment(3);
		$red = 'deposit_account';
		$this->db->query("UPDATE deposit_account SET status=1 WHERE id='{$id}' LIMIT 1");
		$this->session->set_flashdata('msg', 'Account status changed successfully');
		redirect($red);
	}

	public function remove_deposit_account() {
		$id = $this->uri->segment(3);
		$red = 'deposit_account';
		$this->db->query("DELETE FROM deposit_account WHERE id='{$id}' LIMIT 1");
		$this->session->set_flashdata('msg', 'Account deleted successfully');
		redirect($red);
	}

	public function remove_deposit() {

		$red = 'customer_deposit';
		if($this->input->post('submit')) {
			$status = 'REJECT';
			$deposit_id = $this->input->post('deposit_reject_id');
			$note = $this->input->post('note');

			// dd($_POST);

			if(!empty($deposit_id)) {

		        $data_arr2 = array(
					'status'	=> $status,
					'deposit_note'	=> $note,
					'updated_at'	=> date("Y-m-d H:i:s")
		        );
				$this->db->where('id', $deposit_id);
				$this->db->update('deposit', $data_arr2);
				$this->session->set_flashdata('msg', 'Deposit request rejected');
				redirect($red);
				return;

			}
			else {
				$this->session->set_flashdata('error', 'Invalid request');
				redirect($red);
				return;
			}
		}
	}

	public function post_create_role() {
		$data = array();

		if($this->input->post('submit')) {
			$role_name = $this->input->post('role_name');
			$role_data = $this->input->post('role_data');
			$red = 'manage_role/';

			if(empty($role_name) || empty($role_data)) {
				$this->session->set_flashdata('error', 'All fields are required');
				redirect($red);
			}

			// -- if club exists
			$x_club = $this->db->query("SELECT * FROM role_management WHERE role_name='{$role_name}'");
			$x_club = $x_club->row();
			if(!empty($x_club)) {
				$this->session->set_flashdata('error', 'Sorry, This admin role already exists');
				redirect($red);
			}

	        $data_arr = array(
	        	'role_name'		=> $role_name,
	        	'role_data'			=> implode(",", $role_data),
	        	'created_at'	=> date("Y-m-d H:i:s")
	        );
	        $this->db->insert('role_management', $data_arr);
			$this->session->set_flashdata('msg', 'Admin role created successfully');
			redirect($red);
		}
	}

	public function post_create_admin() {
		$data = array();

		if($this->input->post('submit')) {
			$full_name = $this->input->post('full_name');
			$email = $this->input->post('email');
			$password = $this->input->post('password');
			$red = 'admin_user/';

			if(empty($full_name) || empty($email) || empty($password)) {
				$this->session->set_flashdata('error', 'All fields are required');
				redirect($red);
			}

			// -- if club exists
			$x_club = $this->db->query("SELECT * FROM admin WHERE email='{$email}'");
			$x_club = $x_club->result();
			if(!empty($x_club)) {
				$this->session->set_flashdata('error', 'Sorry, This admin user already exists');
				redirect($red);
			}

	        $data_arr = array(
	        	'full_name'		=> $full_name,
	        	'email'			=> $email,
	        	'password'		=> md5($password),
	        	'role_data'			=> $this->input->post('role_id'),
	        	'created_at'	=> date("Y-m-d H:i:s")
	        );
	        $this->db->insert('admin', $data_arr);
			$this->session->set_flashdata('msg', 'Admin created successfully');
			redirect($red);
		}
	}

	public function customer_user() {
    	$data = array();
		$data['title'] = 'Customer User';
		$data['customer_data'] = $this->db->query("SELECT * FROM users ORDER BY id DESC")->result();
        $this->load->view('admin/user/customer_user', $data);
	}
	// -- end user panel data

	public function create_match() {
		$data = array();
		$data['title'] = "Create Match";
		$data['sports'] = $this->db->query("SELECT * FROM sportscategory")->result();

		if($this->input->post('submit')) {
			$survey_id = $this->input->post('survey_id');
			$sponsor_title = $this->input->post('sponsor_title');
			$upload = $_FILES['upload_file']['name'];
			$red = 'admin/create_sponsor/';

			if(!$upload || empty($sponsor_title)) {
				$this->session->set_flashdata('error', 'All fields are required');
				redirect($red);
			}

	        if($upload) {
	            $filename = $_FILES['upload_file']['name'];
	            $ext = pathinfo($filename, PATHINFO_EXTENSION);
	            $ext = strtolower($ext);
	            $accept_files = ["jpeg", "jpg", "png", "bmp", "gif"];
	            if(!in_array($ext, $accept_files)) {
					$this->session->set_flashdata('error', 'Invalid file extension. permitted file is .jpg, .jpeg & .png');
					redirect($red);
	            }
	            $destination = "uploads/sponsor/". $filename;
	            move_uploaded_file($_FILES['upload_file']['tmp_name'], $destination);
	        }
	        $attachment = ($upload) ? $upload : "";

	        $papana = array(
	        	'survey_id'		=> $survey_id,
	        	'sponsor_title' => $sponsor_title,
	        	'upload_file'	=> $attachment,
	        	'created_at'	=> now(),
	        	'status'		=> 1
	        );
	        $this->db->insert('sponsor_data', $papana);
			$this->session->set_flashdata('msg', 'Sponsor created successfully');
			redirect('admin/manage_sponsor');

		}
		$this->load->view('admin/liveinplay/create_match', $data);
	}

	public function create_signals() {

		$data = array();
		$data['title'] = "Create Signals";
		// $data['get_record'] = $this->db->query("SELECT * FROM currency WHERE currency_name NOT IN(SELECT currency_name FROM signals)")->result();
		$data['get_record'] = $this->db->query("SELECT * FROM currency")->result();

		if($this->input->post('submit')) {
			$currency_name = $this->input->post('currency_name');
			$description = $this->input->post('description');
			$signal_direction = $this->input->post('signal_color');
			$signal_value = $this->input->post('signal_value');
			$entry_price = $this->input->post('entry_price');
			$stop_loss = $this->input->post('stop_loss');
			$take_profit = $this->input->post('take_profit');
			$upload = $_FILES['upload_file']['name'];
			$red = 'admin/create_signals/';

			/*if(!$upload || empty($signal_direction) || empty($signal_value)) {
				$this->session->set_flashdata('error', 'All fields are required');
				redirect($red);
			}*/

	        if($upload) {
	            $filename = $_FILES['upload_file']['name'];
	            $ext = pathinfo($filename, PATHINFO_EXTENSION);
	            $ext = strtolower($ext);
	            $accept_files = ["jpeg", "jpg", "png", "bmp", "gif"];
	            if(!in_array($ext, $accept_files)) {
					$this->session->set_flashdata('error', 'Invalid file extension. permitted file is .jpg, .jpeg & .png');
					redirect($red);
	            }
	            $destination = "uploads/signals/". $filename;
	            move_uploaded_file($_FILES['upload_file']['tmp_name'], $destination);
	        }
	        $attachment = ($upload) ? $upload : "";

	        $created_at = now();
	        $papana = array(
	        	'currency_name'		=> $currency_name,
	        	'description' => $description,
	        	'signal_value' => $signal_value,
	        	'entry_price' => $entry_price,
	        	'stop_loss' => $stop_loss,
	        	'take_profit' => $take_profit,
	        	'signal_color' => $signal_direction,
	        	'upload_file'	=> $attachment,
	        	'created_at'	=> now(),
	        	'status'		=> $this->input->post('status')
	        );
	        $this->db->insert('signals', $papana);


	        // -- mail start
	        $logo = base_url() . 'assets/web/images/logo.png';
	        $url = base_url();
	        $signal_color = ($signal_direction=='BUY') ? "green" : "red";

	        $all_signals = $this->db->query("SELECT u.email FROM user_signals as us INNER JOIN users as u on u.id=us.user_id WHERE us.signal_id='{$currency_name}'")->result();

	        foreach($all_signals as $val) {

		$html =<<< HTML_MESSAGE

<html>
<head>
<title></title>
</head>
<body style="font-family: 'Open Sans', sans-serif; font-size: 16px;	line-height: 26px;	color: #666;">
	<div class="template-wrapper" style="padding: 100px 0;">
		<div class="email-template" style="	max-width: 700px; padding: 30px; margin: 0 auto;">
			<div class="template-logo" style="margin-bottom: 20px; background: #00584d; padding: 10px;">
				<a href="#" style="display: inline-block; background: #00584d">
					<img src="{$logo}" alt="" style="width: 180px;">
				</a>
			</div>

			<div class="template-contant">
				<a href="" class="closed" style="color: #222; font-weight: 700;	font-size: 18px; text-decoration: underline !important;	position: relative;	display: inline-block;	padding-bottom: 5px; text-decoration: none;	margin-bottom: 10px;">{$signal_value}</a>

				<ul class="email-info" style="margin-bottom: 20px; padding: 0; list-style: none;">
					<li style="	line-height: 30px; font-weight: 600; color: #3a3a3a;">Asset: <span style="margin-left: 10px; font-weight: 500;">{$currency_name}</span></li>
					<li style="	line-height: 30px; font-weight: 600; color: #3a3a3a;">Time: <span style="margin-left: 10px; font-weight: 500;">{$created_at}</span></li>
					<li style="	line-height: 30px; font-weight: 600; color: #3a3a3a;">Action: <span style="margin-left: 10px; font-weight: 500;color:{$signal_color}">{$signal_direction}</span></li>
					<li style="	line-height: 30px; font-weight: 600; color: #3a3a3a;">Entry Price: <span style="margin-left: 10px;	font-weight: 500;">{$entry_price}</span></li>
					<li style="	line-height: 30px; font-weight: 600; color: #3a3a3a;">Stop Loss: <span style="margin-left: 10px; font-weight: 500;">{$stop_loss}</span></li>
					<li style="	line-height: 30px; font-weight: 600; color: #3a3a3a;">Take Profit: <span style="margin-left: 10px;	font-weight: 500;">{$take_profit}</span></li>
					<li style="	line-height: 30px; font-weight: 600; color: #3a3a3a;">Status: <span style="margin-left: 10px;	font-weight: 500;">{$this->input->post('status')}</span></li>
				</ul>

				<a href="{$url}" class="see-template" style="color: #ff8623;	font-weight: 700;font-size: 20px; position: relative;	display: inline-block;padding-bottom: 5px;text-decoration: underline !important; margin-bottom: 10px;">See All Active Forex Signals here >></a>


				<div class="regards">
					<p style="color: #545454">
						Our success depends on your success,<br>
						Born2trading Team
					</p>
				</div><!-- /.regards -->
			</div><!-- /.template-contant -->

			<div class="email-footer" style="color: #444;  font-size: 12px;  text-align: center; line-height: 18px; margin-top: 10px; padding-top: 10px; border-top: 1px solid #eee;">

				<p>
					Trading spot foreign currencies (“forex”), indices, commodities or futures contract is not appropriate for all investors,
					and the risks of trading can be substantial. You should carefully consider whether trading is right for you in light of your
					particular circumstances and financial resources. By acknowledging this statement and contracting for the services provided
					by Born2Trading.com (“B2T”), you acknowledge that you have not sought advice from B2T regarding the appropriateness of trading
					for you, and B2T has not provided any such advice to you.
				</p>

				<p>
					You agree and acknowledge further that the trading signals provided to you by B2T (trading signals) are not, and are not
					intended to be, an offer or solicitation to enter into any trading transaction, or any type of trading or investment advice,
					recommendation or strategy. You acknowledge that it is solely your decision to determine which, if any, B2T trading signals
					to use for trading (whether actual or simulated) at a Retail Foreign Exchange Dealer, Futures Commission Merchant or other
					financial institution (collectively referred to as “Financial Institution”), and whether to send all, some, or none of the B2T
					trading signals to such Financial Institution for execution.
				</p>

				<p>
					You understand that past performance associated with any trading signals offered by B2T, whether actual, hypothetical or
					historically “back-tested” is neither necessarily indicative of nor a guarantee of future performance or success.
				</p>

				<p>
					Trading B2T signals may result in the total loss of the funds that you deposit at a Financial Institution, and you may incur
					losses in excess of your deposits. Leverage can work against you as well as for you, and can lead to large losses as well as
					gains. Moreover, funds held at such Financial Institution are typically not guaranteed or insured against misappropriation or
					the bankruptcy of the Financial Institution. You should only trade with funds that you can afford to lose.
				</p>

				<p>
					Based on the foregoing, you agree that you shall not seek to hold B2T responsible for any losses associated with any trading
					signals provided to you by B2T. In the event that any claims, suits, actions, damages, liabilities, obligations, losses, or
					expenses (including reasonable attorneys’ fees) arise out of or relate to the relationship between B2T and you, and/or any
					trading signals provided by B2T to you, you agree to indemnify and hold B2T harmless therefrom.
				</p>

				<p class="text-center footer-text-st">
					THIS BRIEF STATEMENT CANNOT DISCLOSE ALL THE RISKS AND OTHER ASPECTS OF THE MARKETS. YOU SHOULD CONSULT WITH YOUR FINANCIAL
					ADVISOR PRIOR TO TRADING OF ANY MARKETS.
				</p>
			</div><!-- /.email-footer -->
		</div><!-- /.email-template -->


	</div><!-- /.template-wrapper -->
</body>
</html>
HTML_MESSAGE;

// $headers  = "From: Born2trading\r\n";
$headers  = 'From: trading-alerts@born2trading.com' . "\r\n";
$headers  .= 'Reply-To: trading-alerts@born2trading.com' . "\r\n";
$headers .= "Content-type: text/html\r\n";
			mail($val->email, "New Signal Alert", $html, $headers);

	        }
	        // -- mail end

			$this->session->set_flashdata('msg', 'Signals created successfully');
			redirect('admin/manage_signals');

		}

		$data['body'] = $this->load->view('admin/signals/create_signals', $data, TRUE);
		$this->load->view('admin/common/admin_master', $data);
	}

	public function edit_signals() {
		$id = $this->uri->segment(3);
		$data = array();
		$data['title'] = "Edit Signals";
		$data['get_data'] = $this->db->query("SELECT * FROM signals WHERE id='{$id}'")->row();
		$data['get_record'] = $this->db->query("SELECT * FROM currency")->result();

		if($this->input->post('submit')) {
			$currency_name = $this->input->post('currency_name');
			$description = $this->input->post('description');
			$signal_value = $this->input->post('signal_value');
			$entry_price = $this->input->post('entry_price');
			$stop_loss = $this->input->post('stop_loss');
			$take_profit = $this->input->post('take_profit');
			$signal_direction = $this->input->post('signal_color');
			$upload = $_FILES['upload_file']['name'];
			$red = 'admin/edit_signals/'.$id;

			/*if(empty($signal_direction) || empty($signal_value)) {
				$this->session->set_flashdata('error', 'All fields are required');
				redirect($red);
			}*/

	        if($upload) {
	            $filename = $_FILES['upload_file']['name'];
	            $ext = pathinfo($filename, PATHINFO_EXTENSION);
	            $ext = strtolower($ext);
	            $accept_files = ["jpeg", "jpg", "png", "bmp", "gif"];
	            if(!in_array($ext, $accept_files)) {
					$this->session->set_flashdata('error', 'Invalid file extension. permitted file is .jpg, .jpeg & .png');
					redirect($red);
	            }
	            $destination = "uploads/signals/". $filename;
	            move_uploaded_file($_FILES['upload_file']['tmp_name'], $destination);
	        }
	        $attachment = ($upload) ? $upload : $data['get_data']->upload_file;

	        $papana = array(
	        	'currency_name' => $currency_name,
	        	'description'		=> $description,
	        	'signal_value' => $signal_value,
	        	'entry_price' => $entry_price,
	        	'stop_loss' => $stop_loss,
	        	'take_profit' => $take_profit,
	        	'signal_color' => $signal_direction,
	        	'upload_file'	=> $attachment,
	        	'updated_at'	=> now(),
	        	'status'		=> $this->input->post('status')
	        );
			$this->db->where('id', $id);
			$this->db->update('signals', $papana);

	        // -- mail start
	        $logo = base_url() . 'assets/web/images/logo.png';
	        $url = base_url();
	        $signal_color = ($signal_direction=='BUY') ? "green" : "red";
	        $created_at = now();

	        $all_signals = $this->db->query("SELECT u.email FROM user_signals as us INNER JOIN users as u on u.id=us.user_id WHERE us.signal_id='{$currency_name}'")->result();

	        foreach($all_signals as $val) {

		$html =<<< HTML_MESSAGE

<html>
<head>
<title></title>
</head>
<body style="font-family: 'Open Sans', sans-serif; font-size: 16px;	line-height: 26px;	color: #666;">
	<div class="template-wrapper" style="padding: 100px 0;">
		<div class="email-template" style="	max-width: 700px; padding: 30px; margin: 0 auto;">
			<div class="template-logo" style="margin-bottom: 20px; background: #00584d; padding: 10px;">
				<a href="#" style="display: inline-block; background: #00584d">
					<img src="{$logo}" alt="" style="width: 180px;">
				</a>
			</div>

			<div class="template-contant">
				<a href="" class="closed" style="color: #222; font-weight: 700;	font-size: 18px; text-decoration: underline !important;	position: relative;	display: inline-block;	padding-bottom: 5px; text-decoration: none;	margin-bottom: 10px;">{$signal_value}</a>

				<ul class="email-info" style="margin-bottom: 20px; padding: 0; list-style: none;">
					<li style="	line-height: 30px; font-weight: 600; color: #3a3a3a;">Asset: <span style="margin-left: 10px; font-weight: 500;">{$currency_name}</span></li>
					<li style="	line-height: 30px; font-weight: 600; color: #3a3a3a;">Time: <span style="margin-left: 10px; font-weight: 500;">{$created_at}</span></li>
					<li style="	line-height: 30px; font-weight: 600; color: #3a3a3a;">Action: <span style="margin-left: 10px; font-weight: 500;color:{$signal_color}">{$signal_direction}</span></li>
					<li style="	line-height: 30px; font-weight: 600; color: #3a3a3a;">Entry Price: <span style="margin-left: 10px;	font-weight: 500;">{$entry_price}</span></li>
					<li style="	line-height: 30px; font-weight: 600; color: #3a3a3a;">Stop Loss: <span style="margin-left: 10px; font-weight: 500;">{$stop_loss}</span></li>
					<li style="	line-height: 30px; font-weight: 600; color: #3a3a3a;">Take Profit: <span style="margin-left: 10px;	font-weight: 500;">{$take_profit}</span></li>
					<li style="	line-height: 30px; font-weight: 600; color: #3a3a3a;">Status: <span style="margin-left: 10px;	font-weight: 500;">{$this->input->post('status')}</span></li>
				</ul>

				<a href="{$url}" class="see-template" style="color: #ff8623;	font-weight: 700;font-size: 20px; position: relative;	display: inline-block;padding-bottom: 5px;text-decoration: underline !important; margin-bottom: 10px;">See All Active Forex Signals here >></a>


				<div class="regards">
					<p style="color: #545454">
						Our success depends on your success,<br>
						Born2trading Team
					</p>
				</div><!-- /.regards -->
			</div><!-- /.template-contant -->

			<div class="email-footer" style="color: #444;  font-size: 12px;  text-align: center; line-height: 18px; margin-top: 10px; padding-top: 10px; border-top: 1px solid #eee;">

				<p>
					Trading spot foreign currencies (“forex”), indices, commodities or futures contract is not appropriate for all investors,
					and the risks of trading can be substantial. You should carefully consider whether trading is right for you in light of your
					particular circumstances and financial resources. By acknowledging this statement and contracting for the services provided
					by Born2Trading.com (“B2T”), you acknowledge that you have not sought advice from B2T regarding the appropriateness of trading
					for you, and B2T has not provided any such advice to you.
				</p>

				<p>
					You agree and acknowledge further that the trading signals provided to you by B2T (trading signals) are not, and are not
					intended to be, an offer or solicitation to enter into any trading transaction, or any type of trading or investment advice,
					recommendation or strategy. You acknowledge that it is solely your decision to determine which, if any, B2T trading signals
					to use for trading (whether actual or simulated) at a Retail Foreign Exchange Dealer, Futures Commission Merchant or other
					financial institution (collectively referred to as “Financial Institution”), and whether to send all, some, or none of the B2T
					trading signals to such Financial Institution for execution.
				</p>

				<p>
					You understand that past performance associated with any trading signals offered by B2T, whether actual, hypothetical or
					historically “back-tested” is neither necessarily indicative of nor a guarantee of future performance or success.
				</p>

				<p>
					Trading B2T signals may result in the total loss of the funds that you deposit at a Financial Institution, and you may incur
					losses in excess of your deposits. Leverage can work against you as well as for you, and can lead to large losses as well as
					gains. Moreover, funds held at such Financial Institution are typically not guaranteed or insured against misappropriation or
					the bankruptcy of the Financial Institution. You should only trade with funds that you can afford to lose.
				</p>

				<p>
					Based on the foregoing, you agree that you shall not seek to hold B2T responsible for any losses associated with any trading
					signals provided to you by B2T. In the event that any claims, suits, actions, damages, liabilities, obligations, losses, or
					expenses (including reasonable attorneys’ fees) arise out of or relate to the relationship between B2T and you, and/or any
					trading signals provided by B2T to you, you agree to indemnify and hold B2T harmless therefrom.
				</p>

				<p class="text-center footer-text-st">
					THIS BRIEF STATEMENT CANNOT DISCLOSE ALL THE RISKS AND OTHER ASPECTS OF THE MARKETS. YOU SHOULD CONSULT WITH YOUR FINANCIAL
					ADVISOR PRIOR TO TRADING OF ANY MARKETS.
				</p>
			</div><!-- /.email-footer -->
		</div><!-- /.email-template -->


	</div><!-- /.template-wrapper -->
</body>
</html>
HTML_MESSAGE;

			// $headers  = "From: Born2trading\r\n";
			$headers  = 'From: trading-alerts@born2trading.com' . "\r\n";
			$headers  .= 'Reply-To: trading-alerts@born2trading.com' . "\r\n";
			$headers .= "Content-type: text/html\r\n";
			mail($val->email, "{$currency_name} Updated", $html, $headers);

	        }
	        // -- mail end


			$this->session->set_flashdata('msg', 'Signals updated successfully');
			redirect('admin/manage_signals');

		}

		$data['body'] = $this->load->view('admin/signals/edit_signals', $data, TRUE);
		$this->load->view('admin/common/admin_master', $data);
	}

	public function settings() {
		$data = [];
		$data['get_data'] = $this->db->query("SELECT * FROM settings")->row();
		$id = $data['get_data']->id;

		if( $this->input->post('submit') ) {

			$deposit_bonus_ratio = $this->input->post('deposit_bonus_ratio');
			$bet_cancel_rate = $this->input->post('bet_cancel_rate');
			$withdraw_charge = $this->input->post('withdraw_charge');
			$bet_cancel_time = $this->input->post('bet_cancel_time');

	        $data_arr = array(
	        	'deposit_bonus_ratio' => $deposit_bonus_ratio,
	        	'bet_cancel_rate' => $bet_cancel_rate,
	        	'withdraw_charge' => $withdraw_charge,
	        	'bet_cancel_time' => $bet_cancel_time,
	        	'updated_at'	=> date("Y-m-d H:i:s")
	        );
			$this->db->where('id', $id);
			$this->db->update('settings', $data_arr);
			$this->session->set_flashdata('msg', 'Settings updated successfully');
			redirect('settings');
		}

		$this->load->view('admin/settings', $data);
	}

	// history functions
	public function customer_deposit_history() {
    	$data = array();
		$data['title'] = 'Customer Deposit';
		// $data['get_data'] = $this->db->query("SELECT d.*, c.club_name, u.name, u.username FROM `deposit` AS d INNER JOIN club_users AS c ON d.club_user_id=c.id INNER JOIN users AS u ON d.user_id=u.id ORDER BY id DESC LIMIT 10")->result();
		$data['get_data'] = [];
		if($this->input->post('submit')) {
			$search_data = $this->input->post('search_data');
			if(!empty($search_data)) {
				$data['get_data'] = $this->db->query("SELECT d.*, c.club_name, u.name, u.username FROM `deposit` AS d INNER JOIN club_users AS c ON d.club_user_id=c.id INNER JOIN users AS u ON d.user_id=u.id WHERE
					u.name LIKE '%$search_data%' OR
					c.club_name LIKE '%$search_data%' OR
					d.transaction_id LIKE '%$search_data%' OR
					d.payment_method LIKE '%$search_data%' OR
					d.user_phone LIKE '%$search_data%'
					ORDER BY id DESC LIMIT 10")->result();
			}
		}
        $this->load->view('admin/history/customer_deposit_history', $data);
	}

	public function customer_withdraw_history() {
    	$data = array();
		$data['title'] = 'Customer Withdraw';
		// $data['withdraw_data'] = $this->db->query("SELECT w.*, u.name, c.club_name FROM withdraw_req AS w INNER JOIN users AS u ON w.user_id=u.id INNER JOIN club_users AS c ON w.club_user_id=c.id ORDER BY id DESC")->result();

		$data['withdraw_data'] = [];

		if($this->input->post('submit')) {
			$search_data = $this->input->post('search_data');
			if(!empty($search_data)) {
				$data['withdraw_data'] = $this->db->query("SELECT w.*, u.name, u.username, c.club_name FROM withdraw_req AS w INNER JOIN users AS u ON w.user_id=u.id INNER JOIN club_users AS c ON w.club_user_id=c.id
					WHERE
					w.status NOT IN ('PENDING') AND
					u.name LIKE '%$search_data%' OR
					c.club_name LIKE '%$search_data%' OR
					w.payment_type LIKE '%$search_data%' OR
					w.payment_method LIKE '%$search_data%' OR
					w.from_no LIKE '%$search_data%' OR
					w.account_number LIKE '%$search_data%'
					ORDER BY id DESC")->result();
			}
		}

        $this->load->view('admin/history/customer_withdraw_history', $data);
	}

	public function club_withdraw_history() {
    	$data = array();
		$data['title'] = 'Club Withdraw History';
		// $data['withdraw_data'] = $this->db->query("SELECT cw.*, c.club_name FROM club_withdraw_req AS cw INNER JOIN club_users AS c ON cw.club_user_id=c.id ORDER BY id DESC LIMIT 20")->result();
		$data['withdraw_data'] = [];
		if( $this->input->post('submit') ) {
			$search_data = $this->input->post('search_data');
			if( !empty($search_data) ) {
				$data['withdraw_data'] = $this->db->query("SELECT cw.*, c.club_name FROM club_withdraw_req AS cw INNER JOIN club_users AS c ON cw.club_user_id=c.id
					WHERE
					c.club_name LIKE '%$search_data%' OR
					cw.payment_type LIKE '%$search_data%' OR
					cw.payment_method LIKE '%$search_data%' OR
					cw.from_no LIKE '%$search_data%' OR
					cw.account_number LIKE '%$search_data%'
					ORDER BY id DESC")->result();
			}
		}
        // $this->load->view('admin/history/club_withdraw_history', $data);
        $this->load->view('admin/club/club_withdraw_history', $data);
	}

	public function match_bit_coin_history() {
		$data = array();
		$data['title'] = 'Match Bit Coin';
		// $query_string = "SELECT mbc.*, mopd.option_title, mopd.match_id, mopd.match_option_id, mp.match_option_title, m.league_title, m.title, s.name FROM matchbit_coin AS mbc INNER JOIN match_option_details AS mopd ON mbc.match_bit_id=mopd.id INNER JOIN match_option AS mp ON mopd.match_option_id=mp.id INNER JOIN matchname AS m ON mopd.match_id=m.id INNER JOIN sportscategory AS s ON m.sportscategory_id=s.id ORDER BY mbc.id DESC LIMIT 100";
		// $data['match_bit'] = $this->db->query($query_string)->result();

		$data['match_bit'] = [];

		if( $this->input->post('submit') ) {
			$search_data = $this->input->post('search_data');
			if( !empty($search_data) ) {
				$query_string = "SELECT mbc.*, mopd.option_title, mopd.match_id, mopd.match_option_id, mp.match_option_title, m.league_title, m.title, s.name FROM matchbit_coin AS mbc INNER JOIN match_option_details AS mopd ON mbc.match_bit_id=mopd.id INNER JOIN match_option AS mp ON mopd.match_option_id=mp.id INNER JOIN matchname AS m ON mopd.match_id=m.id INNER JOIN sportscategory AS s ON m.sportscategory_id=s.id
					WHERE
					mopd.option_title LIKE '%$search_data%' OR
					mp.match_option_title LIKE '%$search_data%' OR
					m.league_title LIKE '%$search_data%' OR
					m.title LIKE '%$search_data%' OR
					s.name LIKE '%$search_data%' OR
					mbc.bet_status LIKE '%$search_data%' OR
					mbc.username LIKE '%$search_data%'
					ORDER BY mbc.id DESC";
				$data['match_bit'] = $this->db->query($query_string)->result();
			}
		}

    	$this->load->view('admin/history/match_bit_coin_history', $data);
	}

	public function complain_history() {
    	$data = array();
		$data['title'] = 'Customer Complain';
		// $data['complain_data'] = $this->db->query("SELECT c.*, u.name FROM complain AS c INNER JOIN users AS u ON c.user_id=u.id ORDER BY id DESC LIMIT 10")->result();

		$data['complain_data'] = [];

		if( $this->input->post('submit') ) {
			$search_data = $this->input->post('search_data');
			if( !empty($search_data) ) {
				$data['complain_data'] = $this->db->query("SELECT c.*, u.name FROM complain AS c INNER JOIN users AS u ON c.user_id=u.id
					WHERE
					u.name LIKE '%$search_data%' OR
					c.subject LIKE '%$search_data%' OR
					c.message LIKE '%$search_data%' OR
					c.reply LIKE '%$search_data%' OR
					c.complain_to LIKE '%$search_data%'
					ORDER BY id DESC LIMIT 10")->result();
			}
		}

        $this->load->view('admin/history/complain_history', $data);
	}

	public function balance_transfer_history() {

    	$data = array();
		$data['title'] = 'Customer Balance Transfer';
		// $data['transfer_data'] = $this->db->query("SELECT * FROM balance_transfer ORDER BY id DESC LIMIT 20")->result();
		$data['transfer_data'] = [];

		if( $this->input->post('submit') ) {
			$search_data = $this->input->post('search_data');
			if( !empty($search_data) ) {
				$data['transfer_data'] = $this->db->query("SELECT * FROM balance_transfer
					WHERE
					transfer_to_name LIKE '%$search_data%' OR
					transfer_by_name LIKE '%$search_data%' OR
					amount LIKE '%$search_data%' OR
					created_at LIKE '%$search_data%'
					ORDER BY id DESC")->result();
			}
		}

        $this->load->view('admin/history/balance_transfer_history', $data);

	}

	public function datewise_report() {
    	$data = array();
		$data['deposit'] = 0;
		$data['user_withdraw'] = 0;
		$data['club_withdraw'] = 0;
		$data['total_withdraw'] = 0;
		$data['balance'] = 0;

		if( $this->input->post('submit') ) {
			$start_date = $this->input->post('start_date');
			$end_date = $this->input->post('end_date');
			if( !empty($start_date) && !empty($end_date) ) {

				$start_date = $start_date . " 00:00:00";
				$end_date = $end_date . " 23:59:59";

				$total_deposit = 0;
				$total_user_withdraw = 0;
				$total_club_withdraw = 0;

				$deposit = $this->db->query("SELECT SUM(`amount`) AS total_deposit FROM deposit WHERE created_at BETWEEN '{$start_date}' AND '{$end_date}' AND status='SUCCESS'")->row();
				if(!empty($deposit->total_deposit)) {
					$total_deposit = $deposit->total_deposit;
				}

				$user_withdraw = $this->db->query("SELECT SUM(`amount`) AS withdraw FROM withdraw_req WHERE created_at BETWEEN '{$start_date}' AND '{$end_date}' AND status='SUCCESS'")->row();
				if(!empty($user_withdraw->withdraw)) {
					$total_user_withdraw = $user_withdraw->withdraw;
				}


				$club_withdraw = $this->db->query("SELECT SUM(`amount`) AS withdraw FROM club_withdraw_req WHERE created_at BETWEEN '{$start_date}' AND '{$end_date}' AND status='SUCCESS'")->row();
				if(!empty($club_withdraw->withdraw)) {
					$total_club_withdraw = $club_withdraw->withdraw;
				}

				$data['deposit'] = $total_deposit;
				$data['user_withdraw'] = $total_user_withdraw;
				$data['club_withdraw'] = $total_club_withdraw;
				$data['total_withdraw'] = $total_user_withdraw + $total_club_withdraw;
				$data['balance'] = $data['deposit'] - $data['total_withdraw'];
			}
		}

        $this->load->view('admin/history/datewise_report', $data);
	}

	public function get_bet_options_for_result() {
		if( !empty($_POST) ) {
			$bet_id = $_POST['hidden_bet_id'];
			$data = $this->db->query("SELECT id, option_title, option_coin FROM match_option_details WHERE match_option_id='{$bet_id}'")->result();
			$string = "";
			foreach($data as $val) {
				$string .= "<option value='".$val->id."'>$val->option_title</option>";
			}
			echo $string;
		}
	}

	public function set_bet_winner_old() {
		$red = $_SERVER['HTTP_REFERER'];
		$bet_id = $this->uri->segment(3);
		$bet_option_id = $this->uri->segment(4);

		$data = $this->db->query("SELECT * FROM matchbit_coin WHERE match_bit_id='{$bet_option_id}'")->result();

		// -- update match option status
		$this->db->query("UPDATE match_option SET status=3 WHERE id='{$bet_id}'");

		// - update match option details status
		$this->db->query("UPDATE match_option_details SET status=2 WHERE id='{$bet_option_id}'");
		$this->db->query("UPDATE match_option_details SET status=3 WHERE match_option_id='{$bet_id}' AND id !='{$bet_option_id}'");

		if( !empty($data) ) {

			foreach($data as $val) {
				$ids = $val->match_bit_id;
				// - update match bit coin
				$this->db->query("UPDATE matchbit_coin SET bet_status='WIN' WHERE match_bit_id='{$val->match_bit_id}' AND bet_status='MATCH_RUNNING'");

				// -- insert data in my coin
				$user_balance = get_user_current_balance($val->user_id);
				$cur_balance = $user_balance + $val->total_coin;
		        $data_arr2 = array(
		        	'user_id'			=> $val->user_id,
		        	'club_id'			=> $val->club_id,
					'coin'				=> $val->total_coin,
					'current_balance'	=> $cur_balance,
					'coin_type'			=> 'BETWIN',
					'method'			=> 'GET',
					'bet_option_id'		=> $val->match_bit_id,
					'created_at'		=> date("Y-m-d H:i:s")
		        );
		        $this->db->insert('my_coin', $data_arr2);
			}
			// -- update match lost status to the match bit coin table
			$this->db->query("UPDATE matchbit_coin SET bet_status='LOST'
				WHERE
				match_bet_option_id='{$bet_id}' AND
				bet_status='MATCH_RUNNING' AND
				match_bit_id NOT IN ('".$ids."')");
			$this->session->set_flashdata('msg', 'Result done successfully');
		}
		else {
			// -- update match lost status to the match bit coin table
			$this->db->query("
				UPDATE matchbit_coin SET bet_status='LOST'
				WHERE
				match_bet_option_id='{$bet_id}'
				AND bet_status='MATCH_RUNNING'
			");
			//$this->session->set_flashdata('error', 'No user bet in this answer');
			$this->session->set_flashdata('msg', 'Result done successfully');
		}


		
		redirect($red);
	}

	public function set_bet_winner() {

		$data = [];
		$data['error_msg'] = "";
		$data['success_msg'] = "";

		$bet_id = $_POST['bet_id'];
		$bet_option_id = $_POST['bet_option_id'];

		$data = $this->db->query("SELECT * FROM matchbit_coin WHERE match_bit_id='{$bet_option_id}' AND bet_status='MATCH_RUNNING'")->result();

		// -- update match option status
		$this->db->query("UPDATE match_option SET status=3 WHERE id='{$bet_id}'");

		// - update match option details status
		$this->db->query("UPDATE match_option_details SET status=2 WHERE id='{$bet_option_id}'");
		$this->db->query("UPDATE match_option_details SET status=3 WHERE match_option_id='{$bet_id}' AND id !='{$bet_option_id}'");

		if( !empty($data) ) {

			foreach($data as $val) {
				$ids = $val->match_bit_id;
				// - update match bit coin
				$this->db->query("UPDATE matchbit_coin SET bet_status='WIN' WHERE match_bit_id='{$val->match_bit_id}' AND bet_status='MATCH_RUNNING'");

				// -- insert data in my coin
				$user_balance = get_user_current_balance($val->user_id);
				$cur_balance = $user_balance + $val->total_coin;
		        $data_arr2 = array(
		        	'user_id'			=> $val->user_id,
		        	'club_id'			=> $val->club_id,
					'coin'				=> $val->total_coin,
					'current_balance'	=> $cur_balance,
					'coin_type'			=> 'BETWIN',
					'method'			=> 'GET',
					'bet_option_id'		=> $val->match_bit_id,
					'created_at'		=> date("Y-m-d H:i:s")
		        );
		        $this->db->insert('my_coin', $data_arr2);
			}
			// -- update match lost status to the match bit coin table
			$this->db->query("UPDATE matchbit_coin SET bet_status='LOST'
				WHERE
				match_bet_option_id='{$bet_id}' AND
				bet_status='MATCH_RUNNING' AND
				match_bit_id NOT IN ('".$ids."')");
				$data['error'] = 0;
				$data['success_msg'] = "Result done successfully";
				echo json_encode($data);
				exit();

		}
		else {
			// -- update match lost status to the match bit coin table
			$this->db->query("
				UPDATE matchbit_coin SET bet_status='LOST'
				WHERE
				match_bet_option_id='{$bet_id}'
				AND bet_status='MATCH_RUNNING'
			");
			$data['error'] = 0;
			$data['success_msg'] = "Result done successfully";
			echo json_encode($data);
			exit();
		}

	}

	public function published_bet_result() {
		$red = $_SERVER['HTTP_REFERER'];

		if( $this->input->post('submit') ) {
			$bet_id = $this->input->post('hidden_bet_id');
			$bet_option_id = $this->input->post('bet_option_id');

			$data = $this->db->query("SELECT * FROM matchbit_coin WHERE match_bit_id='{$bet_option_id}'")->result();

			// -- update match option status
			$this->db->query("UPDATE match_option SET status=3 WHERE id='{$bet_id}'");

			// - update match option details status
			$this->db->query("UPDATE match_option_details SET status=2 WHERE id='{$bet_option_id}'");
			$this->db->query("UPDATE match_option_details SET status=3 WHERE match_option_id='{$bet_id}' AND id !='{$bet_option_id}'");

			foreach($data as $val) {
				$ids = $val->match_bit_id;
				// - update match bit coin
				$this->db->query("UPDATE matchbit_coin SET bet_status='WIN' WHERE match_bit_id='{$val->match_bit_id}' AND bet_status='MATCH_RUNNING'");

				// -- insert data in my coin
				$user_balance = get_user_current_balance($val->user_id);
				$cur_balance = $user_balance + $val->total_coin;
		        $data_arr2 = array(
		        	'user_id'			=> $val->user_id,
		        	'club_id'			=> $val->club_id,
					'coin'				=> $val->total_coin,
					'current_balance'	=> $cur_balance,
					'coin_type'			=> 'BETWIN',
					'method'			=> 'GET',
					'bet_option_id'		=> $val->match_bit_id,
					'created_at'		=> date("Y-m-d H:i:s")
		        );
		        $this->db->insert('my_coin', $data_arr2);
			}

			// -- update match lost status to the match bit coin table
			$this->db->query("UPDATE matchbit_coin SET bet_status='LOST'
				WHERE
				match_bet_option_id='{$bet_id}' AND
				bet_status='MATCH_RUNNING' AND
				match_bit_id NOT IN ('".$ids."')");

			$this->session->set_flashdata('msg', 'Result done successfully');
		}
		redirect($red);
	}

	public function bet_match_cancel() {
		$red = $_SERVER['HTTP_REFERER'];
		$id = $this->uri->segment(3);
		if(!empty($id) && is_numeric($id)) {

			// -- update match option status
			$this->db->query("UPDATE match_option SET status=4 WHERE id='{$id}'");

			$data = $this->db->query("SELECT * FROM matchbit_coin WHERE match_bet_option_id='{$id}'")->result();
			// - update match bit coin
			$this->db->query("UPDATE matchbit_coin SET bet_status='CANCEL_ADMIN' WHERE match_bet_option_id='{$id}'");

			foreach($data as $val) {
				// -- insert data in my coin
				$user_balance = get_user_current_balance($val->user_id);
				$cur_balance = $user_balance + $val->bet_coin;
		        $data_arr2 = array(
		        	'user_id'			=> $val->user_id,
		        	'club_id'			=> $val->club_id,
					'coin'				=> $val->bet_coin,
					'current_balance'	=> $cur_balance,
					'coin_type'			=> 'BETCANCEL',
					'method'			=> 'GET',
					'bet_option_id'		=> $val->match_bit_id,
					'created_at'		=> date("Y-m-d H:i:s")
		        );
		        $this->db->insert('my_coin', $data_arr2);
			}
			$this->session->set_flashdata('msg', 'Match bet cancelled');
		}
		redirect($red);
	}

	public function update_bet_ratio() {
		$data = [];
		$data['error_msg'] = "";
		$data['success_msg'] = "";
		$id = $this->input->post('option_details_id');
		$option_coin = $this->input->post('option_coin');

		$this->db->query("UPDATE match_option_details SET option_coin='{$option_coin}' WHERE id='{$id}' LIMIT 1");

		if($this->db->affected_rows()) {
			$data['error'] = 0;
			$data['success_msg'] = "coin update successfully";
			echo json_encode($data);
			exit();
		}
		else {
			$data['error'] = 1;
			$data['error_msg'] = "coin update failed";
			echo json_encode($data);
			exit();
		}

	}

	public function update_bet_score() {
		$data = [];
		$data['error_msg'] = "";
		$data['success_msg'] = "";
		$id = $this->input->post('match_id');
		$notification = $this->input->post('notification');

		$this->db->query("UPDATE matchname SET notification='{$notification}' WHERE id='{$id}' LIMIT 1");

		if($this->db->affected_rows()) {
			$data['error'] = 0;
			$data['success_msg'] = "score update successfully";
			echo json_encode($data);
			exit();
		}
		else {
			$data['error'] = 1;
			$data['error_msg'] = "score update failed";
			echo json_encode($data);
			exit();
		}

	}

	public function update_match_status() {
		$data = [];
		$data['error_msg'] = "";
		$data['success_msg'] = "";
		$id = $this->input->post('match_id');
		$active_status = $this->input->post('active_status');

		// update bet option
		// $this->db->query("UPDATE match_option SET status=1 WHERE match_id='{$id}'");
		$this->db->query("UPDATE matchname SET active_status='{$active_status}' WHERE id='{$id}' LIMIT 1");

		// -- if admin select match complete then update match option status too
		if($active_status == 4) {
			$this->db->query("UPDATE match_option SET status=0 WHERE match_id='{$id}' AND status=1");
		}

		if($this->db->affected_rows()) {
			$data['error'] = 0;
			$data['success_msg'] = "status update successfully";
			echo json_encode($data);
			exit();
		}
		else {
			$data['error'] = 1;
			$data['error_msg'] = "status update failed";
			echo json_encode($data);
			exit();
		}
	}

	public function update_bet_status() {
		$data = [];
		$data['error_msg'] = "";
		$data['success_msg'] = "";
		$id = $this->input->post('bet_id');
		$status = $this->input->post('status');

		$this->db->query("UPDATE match_option SET status='{$status}' WHERE id='{$id}' LIMIT 1");

		if($this->db->affected_rows()) {
			$data['error'] = 0;
			$data['success_msg'] = "status update successfully";
			echo json_encode($data);
			exit();
		}
		else {
			$data['error'] = 1;
			$data['error_msg'] = "status update failed";
			echo json_encode($data);
			exit();
		}
	}

	public function user_bet_return() {
		$bet_id = $_POST['bet_id'];
		$bet_data = $this->db->query("SELECT * FROM matchbit_coin WHERE id='{$bet_id}'")->row();

		if(!empty($bet_data)) {
			$this->db->query("UPDATE matchbit_coin SET bet_status='BET_RETURN' WHERE id='{$bet_id}' LIMIT 1");
			$bet_coin = ($bet_data->bet_coin*95)/100;

	        // -- add user balance to my_coin
	        $cur_balance = get_user_current_balance($bet_data->user_id) + $bet_coin;
	        $data_arr2 = array(
	        	'user_id'			=> $bet_data->user_id,
	        	'club_id'			=> $bet_data->club_id,
				'coin'				=> $bet_coin,
				'current_balance'	=> $cur_balance,
				'coin_type'			=> 'BET_RETURN',
				'method'			=> 'GET',
				'created_at'		=> date("Y-m-d H:i:s")
	        );
	        $this->db->insert('my_coin', $data_arr2);
			$data['error'] = 0;
			$data['success_msg'] = "Bet returned successfully";
			echo json_encode($data);
			exit();
		}
		else {
			$data['error'] = 1;
			$data['error_msg'] = "Bet return failed";
			echo json_encode($data);
			exit();
		}
	}

	public function update_total_bet_count() {
		$bet_ids = $_POST['bet_id'];
		$data = [];
		$get_data = [];
		foreach($bet_ids as $val) {
			// $query_data = $this->db->query("SELECT COUNT(id) AS total_bet FROM matchbit_coin WHERE match_bet_option_id='{$val}'")->row()->total_bet;
			$query_data = $this->db->query("SELECT SUM(`bet_coin`) AS total_bet FROM matchbit_coin WHERE match_bet_option_id='{$val}'")->row()->total_bet;
			$get_data[$val] = $query_data; 
		}
		if( !empty($get_data) ) {
			$data['error'] = 0;
			$data['success_msg'] = $get_data;
			echo json_encode($data);
			exit();
		}
		else {
			$data['error'] = 1;
			$data['error_msg'] = "Data collection failed";
			echo json_encode($data);
			exit();
		}
	}

	public function update_total_ratio_count() {
		$option_ids = $_POST['bet_option_id'];
		$data = [];
		$get_data = [];
		foreach($option_ids as $val) {
			// $query_data = $this->db->query("SELECT SUM(bet_coin) AS tb, option_coin, bet_coin FROM matchbit_coin WHERE match_bit_id='{$val}'")->row();
			$query_data = $this->db->query("SELECT SUM(`bet_coin`) as tb, ANY_VALUE(option_coin) AS option_coin FROM matchbit_coin WHERE match_bit_id='{$val}';")->row();

			$tb = !empty($query_data->tb) ? $query_data->tb : 0;
			$tr = !empty($query_data->tb) ? $query_data->tb*$query_data->option_coin : 0;
			$string = "TB: " . $tb . " TR: " . $tr;
			$get_data[$val] = $string; 
		}
		if( !empty($get_data) ) {
			$data['error'] = 0;
			$data['success_msg'] = $get_data;
			echo json_encode($data);
			exit();
		}
		else {
			$data['error'] = 1;
			$data['error_msg'] = "Data collection failed";
			echo json_encode($data);
			exit();
		}
	}

	/*public function update_total_ratio_count() {
		$option_ids = $_POST['bet_option_id'];
		$data = [];
		$get_data = [];
		foreach($option_ids as $val) {
			$query_data = $this->db->query("SELECT SUM(`bet_coin`) AS tb FROM matchbit_coin WHERE match_bit_id='{$val}' GROUP BY match_bit_id")->row();

			if( !empty($query_data) ) {

				$tb = !empty($query_data->tb) ? $query_data->tb : 0;
				$tr = !empty($query_data->tb) ? $query_data->tb*$query_data->option_coin : 0;
				$string = "TB: " . $tb . " TR: " . $tr;
				$get_data[$val] = $string;

			}
			else {
				$tb = 0;
				$tr = 0;
				$string = "TB: " . $tb . " TR: " . $tr;
				$get_data[$val] = $string;
			}

			dd($query_data, true);
			$tb = [];
			if( !empty($query_data) ) {
				foreach($query_data as $val) {
					$tb[] = $val->bet_coin;
				}
				$total_tb = array_sum($tb);

				$tb = !empty($total_tb) ? $total_tb : 0;
				$tr = !empty($total_tb) ? $total_tb*$query_data[0]->option_coin : 0;
				$string = "TB: " . $tb . " TR: " . $tr;
			}
			else {

			}


			
		}
		if( !empty($get_data) ) {
			$data['error'] = 0;
			$data['success_msg'] = $get_data;
			echo json_encode($data);
			exit();
		}
		else {
			$data['error'] = 1;
			$data['error_msg'] = "Data collection failed";
			echo json_encode($data);
			exit();
		}
	}*/


	public function deposit_by_admin() {
		$red = $_SERVER['HTTP_REFERER'];
		$user_id = $_POST['user_id'];
		$get_data = $this->db->query("SELECT * FROM users WHERE id='{$user_id}'")->row();
		$amount = $_POST['amount'];

		if( !empty($user_id) && !empty($amount) ) {
	        $data_arr = array(
	        	'user_id' => $user_id,
				'club_user_id'	=> $get_data->club_id,
				'payment_method' => 'admin_deposit',
				'amount' => $amount,
				'deposit_note' => 'Deposit by admin',
				'created_at'	=> date("Y-m-d H:i:s"),
				'status' => 'SUCCESS',
			);
			$this->db->insert('deposit', $data_arr);

			$current_balance = get_user_current_balance($user_id);
			$current_balance = $current_balance + $amount;

	        $data_arr2 = array(
	        	'user_id'			=> $user_id,
	        	'club_id'			=> $get_data->club_id,
	        	'coin'				=> $amount,
	        	'current_balance'	=> $current_balance,
	        	'coin_type'			=> 'DEPOSIT',
	        	'method'			=> 'GET',
	        	'created_at'		=> date('Y-m-d H:i:s')
	        );
	        $this->db->insert('my_coin', $data_arr2);
			$this->session->set_flashdata('msg', 'Deposit successfully done.');
		}
		else {
			$this->session->set_flashdata('error', 'user id and amount is required');
		}
		redirect($red);
	}

	public function final_result_done() {
		$red = $_SERVER['HTTP_REFERER'];
		$id = $this->uri->segment(3);
		$this->db->query("UPDATE matchname SET active_status='8' WHERE id='{$id}' LIMIT 1");
		$this->session->set_flashdata('msg', 'Match finally done!');
		redirect($red);
	}

	public function user_login_by_admin() {
		$user_id = $this->uri->segment(2);
		$red = $_SERVER['HTTP_REFERER'];

		$this->db->select('*');
		$this->db->from('users');
		$this->db->where('id', $user_id);
		$query = $this->db->get();
		$result = $query->row();

		if(!empty($result) && isset($result)) {

			$this->session->set_userdata('cus_data', $result);
			$red = base_url('view_profile');
			$this->session->set_flashdata('msg', 'Login success as admin user!');
			redirect($red);
		}
		else {
			$this->session->set_flashdata('msg', 'Incorrect user');
			redirect($red);
		}

	}

	public function club_login_by_admin() {
		$user_id = $this->uri->segment(2);
		$red = $_SERVER['HTTP_REFERER'];

		$this->db->select('*');
		$this->db->from('club_users');
		$this->db->where('id', $user_id);
		$query = $this->db->get();
		$result = $query->row();

		if(!empty($result) && isset($result)) {

			$this->session->set_userdata('club_user_data', $result);
			$red = base_url('club_profile');
			$this->session->set_flashdata('msg', 'Login success as admin user!');
			redirect($red);

		}
		else {
			$this->session->set_flashdata('msg', 'Incorrect user');
			redirect($red);
		}
	}

	public function delete_club() {
		$id = $this->uri->segment(3);
		$red = $_SERVER['HTTP_REFERER'];

		$this->db->query("DELETE FROM club_users WHERE id='{$id}' LIMIT 1");
		$this->session->set_flashdata('msg', 'Club deleted successfully');
		redirect($red);
	}

	public function delete_match() {
		$id = $this->uri->segment(3);
		$red = $_SERVER['HTTP_REFERER'];

		$bet_data = $this->db->query("SELECT * FROM matchbit_coin WHERE match_id='{$id}'")->result();
		if(!empty($bet_data)) {
			$this->session->set_flashdata('msg', 'You can not delete this match.');
		}
		else {
			$this->db->query("DELETE FROM matchname WHERE id='{$id}' LIMIT 1");
			$this->session->set_flashdata('msg', 'Match deleted successfully');
		}
		redirect($red);
	}

	public function multibets_data() {
		$data = [];
		$data = array();
		$query_string = "SELECT m.*, u.username, c.club_name FROM multibet AS m INNER JOIN users AS u ON m.user_id=u.id INNER JOIN club_users AS c ON m.club_id=c.id";
		$data['get_data'] = $this->db->query($query_string)->result();
		$this->load->view('admin/multibets_data', $data);
	}

	public function ajax_multibet_details() {
		$id = $_POST['option_detail_ids'];
		$user_id = $_POST['user_id'];
		$red = $_SERVER['HTTP_REFERER'];
		$data = [];

		$get_bet = $this->db->query("SELECT * FROM multibet WHERE id='{$id}' AND user_id='{$user_id}'")->row();
		if(empty($get_bet)) {
			redirect($red);
			return;
		}
		$option_ids = $get_bet->option_detail_ids;
		$query_string = "SELECT mbc.*, mopd.option_title, mopd.match_id, mopd.match_option_id, mp.match_option_title, m.league_title, m.title, m.starting_time, m.ending_time, s.name FROM matchbit_coin AS mbc INNER JOIN match_option_details AS mopd ON mbc.match_bit_id=mopd.id INNER JOIN match_option AS mp ON mopd.match_option_id=mp.id INNER JOIN matchname AS m ON mopd.match_id=m.id INNER JOIN sportscategory AS s ON m.sportscategory_id=s.id WHERE mbc.match_bit_id IN({$option_ids}) AND mbc.user_id='{$user_id}' ORDER BY mbc.id DESC";
		$get_data = $this->db->query($query_string)->result();
		$data['get_data'] = $get_data;

		$view_data = $this->load->view('admin/ajax_multibet_details', $data, true);
		$new_data = [];
		$new_data['error'] = 0;
		$new_data['get_data'] = $view_data;
		$new_data['error_msg'] = "";
		echo json_encode($new_data);
	}

	public function set_multibet_result() {
		$red = $_SERVER['HTTP_REFERER'];
		$multibet_id = $_POST['multibet_id'];
		$status = $_POST['status'];

		if(!empty($multibet_id) && !empty($status)) {

			if($status=='WIN') {
				$data = $this->db->query("SELECT * FROM multibet WHERE id='{$multibet_id}'")->row();
				$possible_win = $data->possible_win;
				$current_balance = get_user_current_balance($data->user_id);
				$current_balance = $current_balance + $possible_win;

		        $data_arr = array(
		        	'user_id'			=> $data->user_id,
		        	'club_id'			=> $data->club_id,
		        	'coin'				=> $possible_win,
		        	'current_balance'	=> $current_balance,
		        	'coin_type'			=> 'BETWIN',
		        	'method'			=> 'GET',
		        	'created_at'		=> date('Y-m-d H:i:s')
		        );
		        $this->db->insert('my_coin', $data_arr);
			}

			$this->db->query("UPDATE multibet SET status='{$status}' WHERE id='{$multibet_id}'");
			$this->session->set_flashdata('msg', 'Result published successfully');
			redirect($red);

		}
	}

	// -- new functions
	public function total_user_balance() {

		$data = [];
		$ids = $this->db->query("SELECT DISTINCT user_id FROM my_coin")->result();
		$amount = [];
		foreach($ids as $id) {
		    $coin = $this->db->query("SELECT user_id, current_balance FROM my_coin WHERE user_id='{$id->user_id}' ORDER BY id DESC LIMIT 1")->row();
		    $amount[] = $coin->current_balance;
		    
		}
		$data['total_user_coin'] = round(array_sum($amount), 2);
		$this->load->view("admin/total_user_balance", $data);

	}

	public function change_password() {
		$data = array();

		if($this->input->post('submit')) {
			$old_password = $this->input->post('old_password');
			$password = $this->input->post('new_password');
			$confirm_password = $this->input->post('confirm_password');

			$red = 'change_password/';

			if( strlen($password) < 6 ) {
				$this->session->set_flashdata('error', 'password need atleast 6 character');
				redirect($red);
			}

			if( $confirm_password != $password ) {
				$this->session->set_flashdata('error', 'password is not match with confirm password');
				redirect($red);
			}

			// -- check old password
			$old_password = md5($old_password);
			$chk_data = $this->db->query("SELECT * FROM admin WHERE id='{$_SESSION["admin_access_data"]->id}' AND password='{$old_password}'")->row();

			if( empty($chk_data) ) {
				$this->session->set_flashdata('error', 'old password is not matched');
				redirect($red);
			}

	        $data_arr = array(
	        	'password'		=> md5($password),
	        	'updated_at'	=> date("Y-m-d H:i:s")
	        );
	        $this->db->where('id', $_SESSION['admin_access_data']->id);
	        $this->db->update('admin', $data_arr);
			$this->session->set_flashdata('msg', 'Password changed successfully');
			redirect($red);
		}

		$this->load->view("admin/change_password", $data);
	}

	public function site_report_data() {

    	$data = array();
		$data['total_bet'] = 0;
		$data['total_return'] = 0;
		$data['total_refund'] = 0;
		$data['user_withdraw'] = 0; // total cash out
		$data['club_withdraw'] = 0;
		$data['sponsor_comission'] = 0;
		$data['total_withdraw'] = 0;
		$data['deposit'] = 0;
		$data['balance'] = 0; // total reserve
		$data['profit'] = 0;
		$data['total_win'] = 0;


		if( $this->input->post('submit') ) {
			$start_date = $this->input->post('start_date');
			$end_date = $this->input->post('end_date');
			if( !empty($start_date) && !empty($end_date) ) {

				$start_date = $start_date . " 00:00:00";
				$end_date = $end_date . " 23:59:59";

				$total_deposit = 0;
				$total_user_withdraw = 0;
				$total_club_withdraw = 0;

				$total_bet_q = $this->db->query("SELECT SUM(`bet_coin`) AS total_bet FROM matchbit_coin WHERE created_at BETWEEN '{$start_date}' AND '{$end_date}'")->row();
				if(!empty($total_bet_q)) {
					$data['total_bet'] = $total_bet_q->total_bet;
				}

				$total_return_q = $this->db->query("SELECT SUM(`bet_coin`) AS total_return FROM matchbit_coin WHERE bet_status IN('BET_RETURN') AND created_at BETWEEN '{$start_date}' AND '{$end_date}'")->row();
				if(!empty($total_return_q)) {
					$data['total_return'] = $total_return_q->total_return;
				}

				$total_refund_q = $this->db->query("SELECT SUM(`bet_coin`) AS total_refund FROM matchbit_coin WHERE bet_status IN('CANCEL_ADMIN') AND created_at BETWEEN '{$start_date}' AND '{$end_date}'")->row();
				if(!empty($total_refund_q)) {
					$data['total_refund'] = $total_refund_q->total_refund;
				}
				
				$user_withdraw = $this->db->query("SELECT SUM(`amount`) AS withdraw FROM withdraw_req WHERE created_at BETWEEN '{$start_date}' AND '{$end_date}' AND status='SUCCESS'")->row();
				if(!empty($user_withdraw->withdraw)) {
					$data['user_withdraw'] = $user_withdraw->withdraw;
				}
				
				$club_withdraw = $this->db->query("SELECT SUM(`amount`) AS withdraw FROM club_withdraw_req WHERE created_at BETWEEN '{$start_date}' AND '{$end_date}' AND status='SUCCESS'")->row();
				if(!empty($club_withdraw->withdraw)) {
					$data['club_withdraw'] = $club_withdraw->withdraw;
				}
				
				// sponsor comission 
				$sponsor_com_q = $this->db->query("SELECT SUM(`coin`) AS comission FROM my_coin WHERE coin_type='SPONSOR_COMMISSION' AND created_at BETWEEN '{$start_date}' AND '{$end_date}'")->row();
				if(!empty($sponsor_com_q->comission)) {
					$data['sponsor_comission'] = $sponsor_com_q->comission;
				}
				
				// total withdraw
				$data['total_withdraw'] = $data['user_withdraw'] + $data['club_withdraw'];

				$deposit = $this->db->query("SELECT SUM(`amount`) AS total_deposit FROM deposit WHERE created_at BETWEEN '{$start_date}' AND '{$end_date}' AND status='SUCCESS'")->row();
				if(!empty($deposit->total_deposit)) {
					$data['deposit'] = $deposit->total_deposit;
				}
				
				// balance
				$data['balance'] = $data['deposit'] - $data['total_withdraw']; // total reserve
				
				// total loss
				$total_win = $this->db->query("SELECT SUM(`bet_coin`) AS total_win FROM matchbit_coin WHERE bet_status IN('WIN', 'USER_CANCEL', 'CANCEL_ADMIN', 'BET_RETURN') AND created_at BETWEEN '{$start_date}' AND '{$end_date}'")->row();
				if(!empty($total_win)) {
					$data['total_win'] = $total_win->total_win;
				}
				$data['profit'] = $data['total_bet'] - $data['total_win'];

				
				
			}
		}
		else {

			$start_date = date("Y-m-d") . " 00:00:00";
			$end_date = date("Y-m-d") . " 23:59:59";

			$total_deposit = 0;
			$total_user_withdraw = 0;
			$total_club_withdraw = 0;

			$total_bet_q = $this->db->query("SELECT SUM(`bet_coin`) AS total_bet FROM matchbit_coin WHERE created_at BETWEEN '{$start_date}' AND '{$end_date}'")->row();
			if(!empty($total_bet_q)) {
				$data['total_bet'] = $total_bet_q->total_bet;
			}

			$total_return_q = $this->db->query("SELECT SUM(`bet_coin`) AS total_return FROM matchbit_coin WHERE bet_status IN('BET_RETURN') AND created_at BETWEEN '{$start_date}' AND '{$end_date}'")->row();
			if(!empty($total_return_q)) {
				$data['total_return'] = $total_return_q->total_return;
			}

			$total_refund_q = $this->db->query("SELECT SUM(`bet_coin`) AS total_refund FROM matchbit_coin WHERE bet_status IN('CANCEL_ADMIN') AND created_at BETWEEN '{$start_date}' AND '{$end_date}'")->row();
			if(!empty($total_refund_q)) {
				$data['total_refund'] = $total_refund_q->total_refund;
			}
			
			$user_withdraw = $this->db->query("SELECT SUM(`amount`) AS withdraw FROM withdraw_req WHERE created_at BETWEEN '{$start_date}' AND '{$end_date}' AND status='SUCCESS'")->row();
			if(!empty($user_withdraw->withdraw)) {
				$data['user_withdraw'] = $user_withdraw->withdraw;
			}
			
			$club_withdraw = $this->db->query("SELECT SUM(`amount`) AS withdraw FROM club_withdraw_req WHERE created_at BETWEEN '{$start_date}' AND '{$end_date}' AND status='SUCCESS'")->row();
			if(!empty($club_withdraw->withdraw)) {
				$data['club_withdraw'] = $club_withdraw->withdraw;
			}
			
			// sponsor comission 
			$sponsor_com_q = $this->db->query("SELECT SUM(`coin`) AS comission FROM my_coin WHERE coin_type='SPONSOR_COMMISSION' AND created_at BETWEEN '{$start_date}' AND '{$end_date}'")->row();
			if(!empty($sponsor_com_q->comission)) {
				$data['sponsor_comission'] = $sponsor_com_q->comission;
			}
			
			// total withdraw
			$data['total_withdraw'] = $data['user_withdraw'] + $data['club_withdraw'];

			$deposit = $this->db->query("SELECT SUM(`amount`) AS total_deposit FROM deposit WHERE created_at BETWEEN '{$start_date}' AND '{$end_date}' AND status='SUCCESS'")->row();
			if(!empty($deposit->total_deposit)) {
				$data['deposit'] = $deposit->total_deposit;
			}
			
			// balance
			$data['balance'] = $data['deposit'] - $data['total_withdraw']; // total reserve
			
			// total loss
			$total_win = $this->db->query("SELECT SUM(`bet_coin`) AS total_win FROM matchbit_coin WHERE bet_status IN('WIN', 'USER_CANCEL', 'CANCEL_ADMIN', 'BET_RETURN') AND created_at BETWEEN '{$start_date}' AND '{$end_date}'")->row();
			if(!empty($total_win)) {
				$data['total_win'] = $total_win->total_win;
			}
			$data['profit'] = $data['total_bet'] - $data['total_win'];

		}

        $this->load->view('admin/site_report_data', $data);

	}
	
    public function get_bet_amount_data() {
        $data = [];
        $match_id = $_POST['match_id'];
        $option_id = $_POST['option_id'];
        $query = $this->db->query("SELECT
                (
                    SELECT 
                        COUNT(m.id)
                    FROM 
                        `matchbit_coin` AS m WHERE m.match_bit_id='{$option_id}'
                ) AS `total_bet`,
                (
                    SELECT
                        SUM(m.bet_coin)
                    FROM
                        `matchbit_coin` AS m WHERE m.match_bit_id='{$option_id}'
                ) AS `total_bet_amount`,
                (
                    SELECT
                        SUM(m.total_coin)
                    FROM
                        `matchbit_coin` AS m WHERE m.match_bit_id='{$option_id}'
                ) AS `total_return_amount`,
                (
                    SELECT
                        SUM(m.bet_coin)
                    FROM
                        `matchbit_coin` AS m WHERE m.match_bet_option_id='{$match_id}'
                ) AS `total_bet_this_q`")->row();

        if(!empty($query)) {

            $total_bet = !empty($query->total_bet) ? $query->total_bet : 0;
            $total_bet_amount = !empty($query->total_bet_amount) ? $query->total_bet_amount : 0;
            $total_return_amount = !empty($query->total_return_amount) ? $query->total_return_amount : 0;
            $total_bet_this_q = !empty($query->total_bet_this_q) ? $query->total_bet_this_q : 0;

            $data['error'] = 0;
            $data['success_msg'] = "Total Bet: $total_bet, Total Bet Amount: $total_bet_amount TK, Total Return Amount: $total_return_amount TK, Total Bet On This Question: $total_bet_this_q TK";
            echo json_encode($data);
            exit();
        }
        else {
            $data['error'] = 1;
            $data['error_msg'] = "data collection failed";
            echo json_encode($data);
            exit();
        }
    }

	public function ball_by_ball() {
		$data = [];
		$red = '/ball_by_ball';

		if( $this->input->post('submit') ) {

			// dd($_POST);
			$answers = array_filter($_POST['answers']);
			$ratios = array_filter($_POST['ratios']);
			
			if( empty($answers) || count($answers) < 2 ) {
				redirect($red);
				return;
			}

			if( empty($_POST['ratios']) || count($ratios) < 2 ) {
				redirect($red);
				return;
			}

			$_SESSION['answers_'.$_POST['match_id']] = $answers;
			$_SESSION['ratios_'.$_POST['match_id']] = $ratios;

			$over = $_POST['over'];
			$ball = $_POST['ball'];
			$innings = $_POST['innings'];
			$question_title = ordinal($over)." over, ".ordinal($ball)." ball run (".ordinal($innings)." Batting)";

			$get_serial = $this->db->query("SELECT match_option_serial FROM match_option WHERE match_id='{$_POST['match_id']}' ORDER BY id DESC LIMIT 1")->row();
			if(!empty($get_serial)) {
				$get_serial = $get_serial->match_option_serial + 1;
			}
			else {
				$get_serial = 1;
			}

	        $data_arr = array(
	        	'match_id'				=> $_POST['match_id'],
	        	'match_option_title'	=> $question_title,
	        	'match_option_serial'	=> $get_serial,
	        	'capital'				=> 20000,
	        	'min_bet_limit'			=> 20,
	        	'max_bet_limit'			=> 5000,
	        	'question_type'			=> 'BALL_BY_BALL',
	        	'over'					=> $over,
	        	'ball'					=> $ball,
	        	'innings'				=> $innings,
	        	'created_at'			=> date('Y-m-d H:i:s')
	        );
	        $this->db->insert('match_option', $data_arr);
	        $last_id = $this->db->insert_id();

	        for( $j=0; $j<count($answers); $j++ ) {
		        $data_arr = array(
		        	'match_id'				=> $_POST['match_id'],
		        	'match_option_id'		=> $last_id,
		        	'option_title'			=> $answers[$j],
		        	'option_coin'			=> $_POST['ratios'][$j],
		        	'created_at'			=> date('Y-m-d H:i:s')
		        );
		        $this->db->insert('match_option_details', $data_arr);
	        }
			$this->session->set_flashdata('msg', 'Match question with answer created successfully');
			redirect($red);

		}

		$all_ball_by_ball = $this->db->query("SELECT m.* FROM matchname AS m WHERE m.ball_by_ball='YES' AND m.status IN(1,2) AND m.active_status IN(1,2) AND m.sportscategory_id IN(2,14,15) ORDER BY m.id DESC")->result();
		$data['get_question'] = $all_ball_by_ball;
		// dd($data);
		$this->load->view('admin/liveinplay/ball_by_ball', $data);
	}

	public function over_by_over() {
		$data = [];
		$red = '/over_by_over';

		if( $this->input->post('submit') ) {

			// dd($_POST);
			$answers = array_filter($_POST['answers']);
			if( empty($answers) ) {
				//
			}

			if( empty($_POST['ratios']) ) {
				//
			}

			$over = $_POST['over'];
			// $ball = $_POST['ball'];
			$innings = $_POST['innings'];
			$question_title = ordinal($over)." over run (".ordinal($innings)." Batting)";

			$get_serial = $this->db->query("SELECT match_option_serial FROM match_option WHERE match_id='{$_POST['match_id']}' ORDER BY id DESC LIMIT 1")->row();
			if(!empty($get_serial)) {
				$get_serial = $get_serial->match_option_serial + 1;
			}
			else {
				$get_serial = 1;
			}

	        $data_arr = array(
	        	'match_id'				=> $_POST['match_id'],
	        	'match_option_title'	=> $question_title,
	        	'match_option_serial'	=> $get_serial,
	        	'capital'				=> 20000,
	        	'min_bet_limit'			=> 20,
	        	'max_bet_limit'			=> 5000,
	        	'question_type'			=> 'OVER_BY_OVER',
	        	'over'					=> $over,
	        	// 'ball'					=> $ball,
	        	'innings'				=> $innings,
	        	'created_at'			=> date('Y-m-d H:i:s')
	        );
	        $this->db->insert('match_option', $data_arr);
	        $last_id = $this->db->insert_id();

	        for( $j=0; $j<count($answers); $j++ ) {
		        $data_arr = array(
		        	'match_id'				=> $_POST['match_id'],
		        	'match_option_id'		=> $last_id,
		        	'option_title'			=> $answers[$j],
		        	'option_coin'			=> $_POST['ratios'][$j],
		        	'created_at'			=> date('Y-m-d H:i:s')
		        );
		        $this->db->insert('match_option_details', $data_arr);
	        }
			$this->session->set_flashdata('msg', 'Match question with answer created successfully');
			redirect($red);

		}

		$all_ball_by_ball = $this->db->query("SELECT m.* FROM matchname AS m WHERE m.over_by_over='YES' AND m.status IN(1,2) AND m.active_status IN(1,2) AND m.sportscategory_id IN(2,14,15) ORDER BY m.id DESC")->result();
		$data['get_question'] = $all_ball_by_ball;
		// dd($data);
		$this->load->view('admin/liveinplay/over_by_over', $data);
	}

}