<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class Prakmm1 extends REST_Controller {

    function __construct($config = 'rest') {
        parent::__construct($config);
        $this->load->database();
    }

    //Menampilkan data kontak
    function index_get() {
        // get id from request
        $id = $this->get('id');
        // get password from request
        $password = $this->get('password');

        // if id is empty
        (
            $id == ''
                ?
            $sql = "select user_id, role, nama, alamat, email, password  from akun_pengguna natural join detail_pengguna"
                :
            $sql = "select user_id, role, nama, alamat, email  from akun_pengguna natural join detail_pengguna WHERE user_id='$id' AND password=md5('$password')"
        );

        // execute query
        $kontak = $this->db->query($sql)->result();
        (
            $kontak
                ?
            $this->response($kontak, 200)
                :
            $this->response(array('status' => "userID $id or password $password not found"), 404)
        );
        
    }

    
    function index_post() {
        // get data from request
        $userID = $this->post('username');
        $password = $this->post('password');
        $role = $this->post('role');
        $key = $this->post('key');
        $nama = $this->post('nama');
        $alamat = $this->post('alamat');
        $jenis_kelamin = $this->post('jenis_kelamin');
        $tanggal_lahir = $this->post('tanggal_lahir');
        $email = $this->post('email');

        // query insert
        $insertIntoAkunPengguna = "insert into akun_pengguna (user_id, password, role, keyy) values
                ('$userID', md5('$password'), $role, '$key')
        ";

        $insertIntoDetailPengguna = "insert into detail_pengguna (user_id, nama, alamat, jenis_kelamin, tanggal_lahir, email) values
                ('$userID', '$nama', '$alamat', '$jenis_kelamin', '$tanggal_lahir', '$email')
        ";

        // execute query 
        (
            $this->db->query($insertIntoAkunPengguna) && $this->db->query($insertIntoDetailPengguna)
                ?
            $this->response(array('status' => 'success create data'), 201)
                :
            $this->response(array('status' => 'failed create data'), 400)
            
        );
    }

    function index_put() {
        // get data from request
        $userID = $this->put('username');
        $nama = $this->put('nama');
        $alamat = $this->put('alamat');
        $jenis_kelamin = $this->put('jenis_kelamin');
        $tanggal_lahir = $this->put('tanggal_lahir');
        $email = $this->put('email');

        // query update
        $updateDetailPengguna = "update detail_pengguna set nama='$nama', alamat='$alamat', jenis_kelamin='$jenis_kelamin', tanggal_lahir='$tanggal_lahir', email='$email' where user_id='$userID'
        ";

        // if userID is not exist
        $user = $this->findUserByID($userID, "detail_pengguna");
        if($user == 0){
            $this->response(array('status' => "userID $userID not found"), 404);
            return;
        }

        // execute query
        $update = $this->db->query($updateDetailPengguna);
        (
            $update 
                ? 
            $this->response(array('status' => 'success update data'), 200) 
                : 
            $this->response(array('status' => 'failed update data'), 400)
        ); 
    }

    function index_delete() {
        // get data from request
        $userID = $this->delete('username');

        // query delete
        $queryDeleteAkunPengguna = "delete from akun_pengguna where user_id='$userID'";
        $queryDeleteDetailPengguna = "delete from detail_pengguna where user_id='$userID'";

        // find user by userID
        $user = $this->findUserByID($userID, "detail_pengguna");
        if($user == 0){
            $this->response(array('status' => "userID $userID not found"), 404);
            return;
        }

        // execute query
        $deleteAkunPengguna = $this->db->query($queryDeleteAkunPengguna);
        $deleteDetailPengguna = $this->db->query($queryDeleteDetailPengguna);
        (
            $deleteAkunPengguna && $deleteDetailPengguna
                ?
            $this->response(array('status' => "success delete user with userID = $userID"), 200)
                :
            $this->response(array('status' => "failed, user with userID $userID not found"), 404)
        );
    }

    function findUserByID($userID, $table){
        $sqlFindUserID = "select * from $table where user_id='$userID'";
        
        $findUserID = $this->db->query($sqlFindUserID)->result();

        return count($findUserID);
    }
}
