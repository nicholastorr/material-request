<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class C_request extends CI_Controller {
    
    public function __construct() {

        parent::__construct();

        $this->load->model('request/M_request', 'request_model');
        $this->db_op = $this->load->database('odoo_production', TRUE); // op = odoo production
        define('KEY', 'AIzaSyBpuiyEEWl_F1ZfDUnSF6nvU2reGMdot7Q');
    }

    //main function to load material request page
    public function index() {
        
        //everytime index page loads fetch new requests from database
        $this->request_model->create_material_request();

        $pending_requests = $this->request_model->get_pending_request();
        $completed_requests = $this->request_model->get_completed_request();
        $session = $this->session->userdata();

        $data['pending_requests'] = $pending_requests;
        $data['completed_requests'] = $completed_requests;

        //get user data and send to view to know which warehouse user belongs to
        $data['user'] = $session;

        


        $this->load->view('pages/templates/header');
        $this->load->view('pages/request/v_index', $data);
        $this->load->view('pages/templates/footer'); 
       
    }

    
    //display pending and completed requests
    public function view_request($order_id) {

        $data['request'] = $this->request_model->get_request_by_id($order_id);

        $this->load->view('pages/templates/header');
        $this->load->view('pages/request/v_index_request', $data);
        $this->load->view('pages/templates/footer');
    }

    //set status of request to completed
    public function complete_request($order_id) {

        $this->request_model->complete_request($order_id);

        redirect('/request/C_request/index');
    }

    //set status of request to pending
    public function pend_request($order_id) {

        $this->request_model->pend_request($order_id);

        redirect('/request/C_request/index');
    }


    //function to create a new request
    public function get_request_sheet($error) {
        //get material request data from post and send to insert request sheet
        $request['sku'] = $this->input->post('sku');
        $request['good'] = $this->input->post('good');
        $request['order'] = $this->input->post('order');
        $request['comments'] = $this->input->post('comments');
        $request['description'] = $this->input->post('description');
        $request['warehouse'] = $this->input->post('warehouse');
        $request['quantity'] = $this->input->post('qty');
        $request['weight'] = $this->input->post('weight');
        $request['order_id'] = $this->request_model->generate_order_id();

        //insert request into database
        $this->request_model->insert_request($request);

        //on successful request insert, send email to purchasing
        if ($request['sku'] != null) {
            $this->request_model->stringify_request($request);
            $this->request_model->update_weight($request);
        }

        //send user data and product categories to request sheet
        $data['user'] = $this->session->all_userdata();
        $data['categories'] = $this->request_model->get_categories();

        //get error message if exists from header and display on page
        $error = (isset($_GET['error'])) ? $_GET['error'] : null;
        $data['error'] = $error;

        //get warehouse data from header, send to view to know which warehouse user belongs to
        $wh = (isset($_GET['wh'])) ? $_GET['wh'] : null;
        $warehouse = null;
        if (!is_null($wh)) {
            $warehouse = $this->request_model->get_warehouse_by_name($wh);
        }
        $data['warehouse'] = $warehouse;

        //get products available at the warehouse and send to request sheet to fill    
        $data['products'] = $this->request_model->get_products_by_cat($warehouse['warehouse_code']);
        

            $this->load->view('pages/templates/header');
            $this->load->view('pages/request/v_request_sheet', $data);
            $this->load->view('pages/templates/footer');
    }

    //adds new product from optional form in request sheet (admin only)
    public function add_product() {
        //get form validation rules from library
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');

        //set validation rules
        $this->form_validation->set_rules('product_sku', 'Product Sku', 'required');
        $this->form_validation->set_rules('product_warehouse', 'Product Warehouse', 'required');
        $this->form_validation->set_rules('product_category', 'Product Category', 'required');

        //get data from add product form in request sheet
        $product['sku'] = $this->input->post('product_sku');
        $product['warehouse'] = $this->input->post('product_warehouse');
        $product['category'] = $this->input->post('product_category');
        $product['this_warehouse'] = $this->input->post('this_wh');
        $product['category'] = strtolower($product['category']);
        $product['warehouse'] = strtolower($product['warehouse']);

        if ($this->form_validation->run() == FALSE)
            {       
                print('please fill out all fields');
            }

        else
            {
                $this->request_model->add_product($product);
            }    
    }

    //adds new category from optional form in request sheet (admin only)
    public function add_category() {
        //get form validation rules from library
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');

        //set validation rules
        $this->form_validation->set_rules('category', 'Category', 'required');

        //get data from add category form in request sheet
        $category['this_warehouse'] = $this->input->post('this_wh1');
        $category['category'] = $this->input->post('category');
        $category['category'] = strtolower($category['category']);

        if ($this->form_validation->run() == FALSE)
            {       
                print('please fill out category');
            }
        else
            {
                $this->request_model->add_category($category);
            }
    }

    public function delete_product($product_sku) {
        $this->request_model->delete_product($product_sku);

        //refresh page on successful delete
        header('Location: ' . $_SERVER['HTTP_REFERER']);
    }

    public function delete_category($category) {
        $this->request_model->delete_category($category);

        //refresh page on successful delete
        header('Location: ' . $_SERVER['HTTP_REFERER']);
    }


}
